<?php
namespace App\Traits;

use App\Enum\Role;
use App\Models\Util\MarketingCampaignUser;
use App\Models\Util\UserHelpdesk;

trait FilterAgent
{

     public function scopeFilterAgent($query, $agentId, $companyId, $userId, $userRole, $category, $escalationType = null)
     {
          if (!$agentId && !in_array($userRole, [Role::Admin, Role::BA])) {
               $relation = $category == 'inbound' ? 'view_inbound_teams' : 'view_outbound_teams';
               $agentIdColumnName = "current_agent_id";
               if ($userRole == 'spv_escalation' || str_contains($escalationType, 'escalation')) {
                    $relation = "view_escalation_user_team";
                    $query->where(function ($q) use ($userId, $companyId) {
                         $q->where(function ($qq) use ($userId, $companyId) {
                              $qq->whereIn('tickets.escalation_team_id', function ($qr) use ($userId, $companyId) {
                                   $qr->select('escalation_team_id')
                                        ->from('escalation_team_members')
                                        ->where('user_id', $userId)
                                        ->where('company_id', $companyId)
                                        ->distinct();
                              });
                              $qq->whereNull('tickets.current_agent_id');
                         });
                         $q->orWhere(function ($qq) use ($userId, $companyId) {
                              $qq->whereIn('tickets.current_agent_id', function ($qr) use ($userId, $companyId) {
                                   $qr->select('team_id')
                                        ->from('view_escalation_user_team')
                                        ->where('user_id', $userId)
                                        ->where('company_id', $companyId)
                                        ->distinct();
                              });
                              $qq->whereNotNull('tickets.escalation_team_id');
                         });

                    });
               } else {
                    $query->leftJoin($relation, function ($join) use ($companyId, $userId, $userRole, $relation, $agentIdColumnName) {
                         $join->on("tickets.{$agentIdColumnName}", $relation . ".team_id");
                         $join->where($relation . '.user_id', $userId);
                         $join->where($relation . '.user_role', $userRole);
                         $join->where($relation . '.company_id', $companyId);
                         $join->whereIn($relation . '.team_role', ['agent', 'agent_escalation']);
                    });
                    if ($category == 'inbound') {
                         $userHelpdeskId = UserHelpdesk::query()
                              ->where('user_id', $userId)
                              ->where('company_id', $companyId)
                              ->distinct()
                              ->pluck('helpdesk_id')
                              ->toArray();
                         $query->whereRaw("
                        (
                            CASE
                                WHEN tickets.source = 'From Web Bot' || tickets.source = 'From Whatsapp Bot' THEN EXISTS (
                                    SELECT 1
                                    FROM product_helpdesk join company_helpdesk_categories on company_helpdesk_categories.id=product_helpdesk.helpdesk_id
                                    WHERE helpdesk_id IN ('" . implode("','", $userHelpdeskId) . "') and company_helpdesk_categories.status='active'
                                    AND product_helpdesk.product_id = tickets.product_id
                                )
                                ELSE " . $relation . ".team_id is not null
                            END
                        )
                    ");
                    } else {
                         $userCampaignId = MarketingCampaignUser::query()
                              ->where('user_id', $userId)
                              ->where('company_id', $companyId)
                              ->distinct()
                              ->pluck('marketing_campaign_id')
                              ->toArray();
                         $query->whereRaw("
                        (
                            CASE
                                WHEN tickets.source = 'From Web Bot' || tickets.source = 'From Whatsapp Bot' THEN EXISTS (
                                    SELECT 1
                                    FROM product_campaigns join marketing_campaigns on marketing_campaigns.id=product_campaigns.marketing_campaign_id
                                    WHERE marketing_campaign_id IN ('" . implode("','", $userCampaignId) . "') and marketing_campaigns.status='active'
                                    AND product_campaigns.product_id = tickets.product_id
                                )
                                ELSE " . $relation . ".team_id is not null
                            END
                        )
                    ");
                    }
               }

          }
     }

}
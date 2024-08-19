<?php
namespace App\Service\Utility;

use App\Models\Util\MarketingCampaign;
use Illuminate\Support\Facades\DB;

class MarketingCampaignService
{
     public function __construct(
          private $model = MarketingCampaign::class,
     ) {
     }

     public function findAllCampaignUser($user)
     {
          // Todo : filter by their own campaign
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalation_type = $user->escalation_type;
          return $this->model::query()
               ->where('company_id', $companyId)
               ->where('status', 'active')
               ->select(['id', 'name'])
               ->get();
     }

     public function findAllEscalationUser($user, $type)
     {
          // Todo : filter by their own campaign
          $companyId = $user->company_id;
          $userId = $user->id;
          $escalation_type = $user->escalation_type;
          $escalationId = [];

          if (str_contains($escalation_type, 'escalation')) {
               $escalationId = DB::table('escalation_team_members')
                    ->where('company_id', $companyId)
                    ->where('user_id', $userId)
                    ->where('category', $type)
                    ->pluck('escalation_team_id');
          }
          return DB::table('escalation_teams')
               ->where('company_id', $companyId)
               ->when(str_contains($escalation_type, 'escalation'), fn($query) => $query->whereIn('id', $escalationId))
               ->select(['id', 'name'])
               ->where('category', $type)
               ->where('status', 'active')
               ->latest()
               ->get();
     }

}
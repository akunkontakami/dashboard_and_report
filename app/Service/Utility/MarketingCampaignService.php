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
          $escalationType = $user->escalation_type || $userRole;
          return $this->model::query()
               ->where('company_id', $companyId)
               ->where('status', 'active')
               ->select(['id', 'name'])
               ->get();
     }
     
     public function findAllCampaignUserDashboard($user)
     {
          // Todo : filter by their own campaign
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          return $this->model::query()
               ->where('company_id', $companyId)
               ->where('status', 'active')
               ->select(['id', 'name'])
               ->get();
     }

     public function findAllCampaignUserDashboardMarketing($user)
     {
          // Todo : filter by their own campaign
          $companyId = $user->company_id;
	     $userId = $user->id;
          $userRole = $user->role;
          // dd($userRole->value);
          $escalationType = $user->escalation_type || $userRole;
          // \DB::enableQueryLog();
          
          return $this->model::query()
          ->select('marketing_campaigns.id', 'marketing_campaigns.name')
          ->leftJoin('user_marketing_campaigns', 'user_marketing_campaigns.marketing_campaign_id', '=', 'marketing_campaigns.id')
          ->leftJoin('company_users', function ($join) {
               $join->on('company_users.user_id', '=', 'user_marketing_campaigns.user_id')
                    ->whereColumn('company_users.company_id', 'user_marketing_campaigns.company_id');
          })
          ->join('product_campaigns', 'marketing_campaigns.id', '=', 'product_campaigns.marketing_campaign_id')
          ->join('company_products', 'product_campaigns.product_id', '=', 'company_products.id')
          ->where('marketing_campaigns.status', 'active')
           ->when($userRole->value === 'SPV' || $userRole->value === 'spv', fn($q) => $q->where('user_marketing_campaigns.user_id', $userId))
          ->groupBy('marketing_campaigns.id')
          ->orderByDesc('marketing_campaigns.created_at')
          ->get();

          // dd(\DB::getQueryLog());
     }

     public function findAllEscalationUser($user, $type)
     {
          // Todo : filter by their own campaign
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $escalationId = [];

          if (str_contains($escalationType, 'escalation')) {
               $escalationId = DB::table('escalation_team_members')
                    ->where('company_id', $companyId)
                    ->where('user_id', $userId)
                    ->where('category', $type)
                    ->pluck('escalation_team_id');
          }
          return DB::table('escalation_teams')
               ->where('company_id', $companyId)
               ->when(str_contains($escalationType, 'escalation'), fn($query) => $query->whereIn('id', $escalationId))
               ->select(['id', 'name'])
               ->where('category', $type)
               ->where('status', 'active')
               ->latest()
               ->get();
     }

}


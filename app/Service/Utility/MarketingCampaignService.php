<?php
namespace App\Service\Utility;
use App\Models\Util\MarketingCampaign;

class MarketingCampaignService{
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

}
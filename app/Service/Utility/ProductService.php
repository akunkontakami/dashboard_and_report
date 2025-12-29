<?php
namespace App\Service\Utility;

use App\Models\Util\Product;

class ProductService
{
     public function __construct(
          private $model = Product::class,
     ) {
     }

     public function findAllProductUser($user, $type)
     {
          // Todo : filter by their own campaign
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          return $this->model::query()
               ->where('company_id', $companyId)
               ->where('type', $type)
               ->where('category', 'general')
               ->select(['id', 'name'])
               ->get();
     }

     public function findAllProductUserDashboard($user, $type)
     {
          // Todo : filter by their own campaign
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          // dd($userRole);
          $escalationType = $user->escalation_type || $userRole;
          // \DB::enableQueryLog();
          
          return \DB::table('marketing_campaigns')
          ->select('company_products.id', 'company_products.name')
          ->leftJoin('user_marketing_campaigns', 'user_marketing_campaigns.marketing_campaign_id', '=', 'marketing_campaigns.id')
          ->leftJoin('company_users', function ($join) {
               $join->on('company_users.user_id', '=', 'user_marketing_campaigns.user_id')
                    ->where('company_users.company_id', '=', \DB::raw('user_marketing_campaigns.company_id'));
          })
          ->join('product_campaigns', 'marketing_campaigns.id', '=', 'product_campaigns.marketing_campaign_id')
          ->join('company_products', 'product_campaigns.product_id', '=', 'company_products.id')
          ->where('marketing_campaigns.status', 'active')
          ->when($userRole->value === 'SPV' || $userRole->value === 'spv', fn($q) => $q->where('user_marketing_campaigns.user_id', $userId))
          ->whereNull('marketing_campaigns.deleted_at')
          ->groupBy('company_products.id')
          ->orderByDesc('marketing_campaigns.created_at')
          ->get();
          
          // dd(\DB::getQueryLog());

     }
}
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
}
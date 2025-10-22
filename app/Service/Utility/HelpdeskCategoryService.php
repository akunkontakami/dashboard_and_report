<?php
namespace App\Service\Utility;

use App\Models\Util\HelpdeskCategory;

class HelpdeskCategoryService
{

     public function __construct(
          public $model = HelpdeskCategory::class,
     ) {
     }

     public function findAllHelpdeskUser($user)
     {
          // Todo : filter by their own data 
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          
          return $this->model::query()
               ->with([
                    'sub' => function ($query) {
                         $query->where('status', 'active');
                         $query->select(['id', 'name', 'parent_id', 'status', 'is_hidden']);
                    }
               ])
               ->where('is_kontakkami', false)
               ->whereNull('parent_id')
               ->where('company_id', $companyId)
               ->where('status', 'active')
               ->oldest('sorting')
               ->select('id', 'parent_id', 'name', 'status', 'is_hidden')
               ->get();
     }
}
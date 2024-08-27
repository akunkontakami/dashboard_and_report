<?php
namespace App\Service\Utility;

use DB;

class UtilityService
{
     public function findAllSumResponseTimeSla($companyId, $category)
     {
          $query = DB::select("SELECT 
          sum(
               TIME_TO_SEC(JSON_UNQUOTE(JSON_EXTRACT(critical, '$.response'))) + 
               TIME_TO_SEC(JSON_UNQUOTE(JSON_EXTRACT(hight, '$.response'))) + 
               TIME_TO_SEC(JSON_UNQUOTE(JSON_EXTRACT(medium, '$.response'))) + 
               TIME_TO_SEC(JSON_UNQUOTE(JSON_EXTRACT(low, '$.response'))) 
          ) / 60 duration_minute
          FROM `company_sla_settings` 
          where company_id=:company_id and category=:category", [
               "company_id" => $companyId,
               "category" => $category
          ]);

          return @$query[0]?->duration_minute ?: 0;
     }

     public function findAllSumResolutionTimeSla($companyId, $category)
     {
          $query = DB::select("SELECT 
          sum(
               TIME_TO_SEC(JSON_UNQUOTE(JSON_EXTRACT(critical, '$.resolution'))) + 
               TIME_TO_SEC(JSON_UNQUOTE(JSON_EXTRACT(hight, '$.resolution'))) + 
               TIME_TO_SEC(JSON_UNQUOTE(JSON_EXTRACT(medium, '$.resolution'))) + 
               TIME_TO_SEC(JSON_UNQUOTE(JSON_EXTRACT(low, '$.resolution'))) 
          ) / 60 duration_minute
          FROM `company_sla_settings` 
          where company_id=:company_id and category=:category", [
               "company_id" => $companyId,
               "category" => $category
          ]);

          return @$query[0]?->duration_minute ?: 0;
     }
}
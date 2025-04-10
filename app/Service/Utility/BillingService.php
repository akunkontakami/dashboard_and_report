<?php
namespace App\Service\Utility;
use App\Models\Util\Billing;

class BillingService
{
     public function __construct(
          public $model = Billing::class
     ) {
     }

     public function findReportItemBilling($billing)
     {
          $items = ['ticket_list'];
          $additionalReportItems = ['call_tracking','agent_activity','call_agent'];
          $onlineAccounts = $billing->online_account;
          foreach($additionalReportItems as $additional){
               if(@$onlineAccounts[$additional]){
                    $items[] = $additional;
               }
          }
          return $items;
     }

     public function findActiveCompanyBilling($companyId)
     {
          return $this->model::query()
               ->where('company_id', $companyId)
               ->available(date('Y-m'))
               ->whereIn('status', ['active', 'non_active'])
               ->where('category', 'current')
               ->orderBy('status', 'asc')
               ->select([
                    'plan_name',
                    'plan_type',
                    'expired_at',
                    'balance',
                    'agent_spv_limit',
                    'qc_am_escalation_limit',
                    'comm_agent_limit',
                    'status',
                    'id',
                    'summary',
                    'additional',
                    'available_from',
                    'available_until',
                    'online_account'
               ])
               ->first();
     }
}
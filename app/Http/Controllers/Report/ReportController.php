<?php
namespace App\Http\Controllers\Report;

use App\Enum\Role;
use App\Http\Resources\Report\TicketListReportResource;
use App\Service\Ticket\ReportTicketService;
use App\Service\Ticket\TicketService;
use App\Service\Utility\BillingService;
use App\Service\Utility\CompanyUserService;
use App\Service\Utility\HelpdeskCategoryService;
use App\Service\Utility\MarketingCampaignService;
use Illuminate\Http\Request;
use Inertia\Inertia;

trait ReportController
{
     public function __construct(
          private HelpdeskCategoryService $helpdeskCategoryService,
          private MarketingCampaignService $marketingCampaignService,
          private TicketService $ticketService,
          private CompanyUserService $companyUserService
     ) {
     }
     public function index(Request $request, BillingService $billingService, $category)
     {
          $billing = $request->RequestBilling;
          return Inertia::render("Report/Index", [
               'billing' => [
                    'report_items' => $billingService->findReportItemBilling($billing),
                    'can_view' => $billing->expired_at >= date('Y-m-d H:i:s'),
               ],
               'category' => $category,
               'type' => $this->type,
               'queueLog' => null,
               'filter' => $this->filterProperties($category)
          ]);
     }

     public function datatable(Request $request,$category){
          return $this->getDataTable($request,$category,$request->get('limit',10));
     }


     private function getDataTable(Request $request,$category,$paginate = true){
          $user = user();
          $items = [];
          if ($category === 'ticket-list') {
               $data = (new ReportTicketService)->findAllTicketListReportData(
                    user : $user,
                    filter : $request->get('filter',[]),
                    search : $request->search,
                    type : $this->type,
                    paginate : $paginate
               );
               $items = TicketListReportResource::collection($data);
          }

          return $items;
     }
     private function filterProperties($category)
     {
          // Todo : filter all query data whit user own relation
          $user = user();
          $helpdesk = [];
          $status = [];
          $spv = [];
          $agent = [];
          $productList = [];
          $escalations = [];
          if ($category == 'ticket-list') {
               $helpdesk = $this->type === 'inbound'
                    ? $this->helpdeskCategoryService->findAllHelpdeskUser($user)
                    : $this->marketingCampaignService->findAllCampaignUser($user);
               $productList = $this->ticketService->findAllProductTicket($user, $this->type);
               $escalations = $this->marketingCampaignService->findAllEscalationUser($user,$this->type);
          }
          if (in_array($category, ["call-tracking", "ticket-list"])) {
               $status = $this->ticketService->findAllStatusTicketWithColor($user, $this->type);
          } else if ($category == 'call-agent') {
               $status = ['Incoming Call', 'Outgoing Call', 'Missed Call', 'Callback', 'Outgoing Campaign'];
          }

          if (in_array($user->role, [Role::BA, Role::Admin, Role::AM])) {
               $spv = $this->companyUserService->findAllUserTeam($user, $this->type, 'spv');
               $agent = $this->companyUserService->findAllUserTeam($user, $this->type, 'agent');
          }


          return [
               "helpdesk" => $helpdesk,
               "status" => $status,
               "spv" => $spv,
               "agent" => $agent,
               "productList" => $productList,
               "escalations" => $escalations
          ];
     }
}
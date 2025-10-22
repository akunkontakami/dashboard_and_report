<?php
namespace App\Http\Controllers\Report;

use App\Enum\Role;
use App\Helpers\ExportExcel;
use App\Http\Resources\Report\AgentActivityReportResource;
use App\Http\Resources\Report\CallAgentReportResource;
use App\Http\Resources\Report\CallTrackingReportResource;
use App\Http\Resources\Report\TicketListReportResource;
use App\Jobs\Report\DonwloadFormTicketPdfReport;
use App\Models\Account\Company;
use App\Models\Util\QueueActionLog;
use App\Service\Ticket\ReportTicketService;
use App\Service\Ticket\TicketService;
use App\Service\Utility\BillingService;
use App\Service\Utility\CompanyUserService;
use App\Service\Utility\HelpdeskCategoryService;
use App\Service\Utility\MarketingCampaignService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

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
          $reportItems = $billingService->findReportItemBilling($billing);
          $categoryKey = str_replace('-','_',$category);
          if(!in_array($categoryKey,$reportItems)){
               abort(401);
          }
          return Inertia::render("Report/Index", [
               'billing' => [
                    'report_items' => $reportItems,
                    'can_view' => $billing ? $billing->expired_at >= date('Y-m-d H:i:s') : false,
               ],
               'category' => $category,
               'type' => $this->type,
               'queueLog' => $this->findQueueLog($category),
               'filter' => $this->filterProperties($category)
          ]);
     }

     public function datatable(Request $request, $category)
     {
          return $this->getDataTable($request, $category, $request->get('limit', 10));
     }

     public function export(Request $request, $category)
     {
          $user = user();
          $company = Company::where('id', $user->company_id)->first();
          $additionalData = [];
          $data = $this->getDataTable($request, $category, null)->resolve();
          $props = match ($category) {
               'ticket-list' => [
                    'exports.reports.ticket-list',
                    '"Report Ticket List'
               ],
               'call-tracking' => [
                    'exports.reports.call-tracking',
                    'Report Call Tracking'
               ],
               'agent-activity' => [
                    'exports.reports.agent-activity',
                    'Report Agent Activity'
               ],
               'call-agent' => [
                    'exports.reports.call-agent',
                    'Report Call Agent'
               ],
          };
          $viewName = @$props[0];
          $fileName = @$props[1];
          if (!$viewName || !$fileName) {
               return null;
          }

          if ($category == 'call-tracking') {
               $additionalData = [
                    'status' => $this->getCallTrackingTicketStatus($user),
               ];
          }
          $filter = $request->get('filter', []);
          $filename = $fileName . " " . $company->name . " " . @$filter['created_start'] . " - " . @$filter['created_end'];
          return Excel::download(new ExportExcel([
               'view' => $viewName,
               'data' => [
                    'items' => $data,
                    'type' => $this->type,
                    ...$additionalData
               ],
          ]), "{$filename}.xlsx", \Maatwebsite\Excel\Excel::XLSX, [
               'filename' => "{$filename}.xlsx",
          ]);
     }


     public function exportFormTicket(Request $request, ReportTicketService $reportTicketService)
     {
          $user = user();
          $company = $user->company;
          $type = $request->type ?: 'excel';
          $filter = $request->get('filter', []);
          $data = $reportTicketService->findAllFormTicketListReportData(
               user: $user,
               type: $this->type,
               filter: $filter,
               search: $request->get('search', ''),
          );
          if ($type == 'pdf') {
               DonwloadFormTicketPdfReport::dispatch($data, $filter, $company, $this->type);
               return "process";
          } else {
               $filename = "Report Ticket Form";
               $filename = $filename . " " . $company->name . " " . @$filter['created_start'] . " - " . @$filter['created_end'];
               return Excel::download(new ExportExcel([
                    'view' => "exports.reports.ticket-form",
                    'data' => [
                         'items' => $data,
                         'type' => $type
                    ],
               ]), "{$filename}.xlsx", \Maatwebsite\Excel\Excel::XLSX, [
                    'filename' => "{$filename}.xlsx",
               ]);
          }
     }

     public function exportChatTicket(Request $request, ReportTicketService $reportTicketService)
     {
          $user = user();
          $company = $user->company;
          $filename = "Report Call Ticket";
          $data = $reportTicketService->findAllChatTicketListReportData(
               user: $user,
               type: $this->type,
               filter: $request->get('filter', []),
               search: $request->get('search', ''),
          );

          $filter = $request->get('filter', []);
          $filename = $filename . " " . $company  ->name . " " . @$filter['created_start'] . " - " . @$filter['created_end'];

          return Excel::download(new ExportExcel([
               'view' => "exports.reports.ticket-chat",
               'data' => [
                    'items' => $data,
               ],
          ]), "{$filename}.xlsx", \Maatwebsite\Excel\Excel::XLSX, [
               'filename' => "{$filename}.xlsx",
          ]);
     }

     public function downloadFormTicketPdf(Request $request, $queueId)
     {
          $queueLog = QueueActionLog::query()
               ->where('company_id', user()->company_id)
               ->where('id', $queueId)
               ->latest()
               ->firstOrFail();
          $file = $queueLog->data['file'];
          $queueLog->delete();
          return response()->download(storage_path("app/{$file}"))->deleteFileAfterSend(true);;
     }

     private function getDataTable(Request $request, $category, $paginate = 10)
     {
          $user = user();
          $service = new ReportTicketService;
          $items = [];
          if ($category === 'ticket-list') {
               $data = $service->findAllTicketListReportData(
                    user: $user,
                    filter: $request->get('filter', []),
                    search: $request->search,
                    type: $this->type,
                    paginate: $paginate
               );
               $items = TicketListReportResource::collection($data);
          }

          if ($category == 'call-tracking') {
               $data = $service->findAllCallTrackingReportData(
                    user: $user,
                    filter: $request->get('filter', []),
                    search: $request->search,
                    type: $this->type,
                    paginate: $paginate
               );
               $items = CallTrackingReportResource::collection($data);
          }

          if ($category == 'agent-activity') {
               $data = $service->findAllAgentActivityReportData(
                    user: $user,
                    filter: $request->get('filter', []),
                    search: $request->search,
                    type: $this->type,
                    paginate: $paginate
               );
               $items = AgentActivityReportResource::collection($data);
          }

          if ($category == 'call-agent') {
               $data = $service->findAllCallAgentReportData(
                    user: $user,
                    filter: $request->get('filter', []),
                    search: $request->search,
                    type: $this->type,
                    paginate: $paginate
               );
               $items = CallAgentReportResource::collection($data);
          }

          return $items;
     }
     private function filterProperties($category)
     {
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
               $escalations = $this->marketingCampaignService->findAllEscalationUser($user, $this->type);
          }
          if ($category == 'ticket-list') {
               $status = $this->ticketService->findAllStatusTicketWithColor($user, $this->type);
          } else if ($category == 'call-tracking') {
               $status = $this->getCallTrackingTicketStatus($user);
          } else if ($category == 'call-agent') {
               // $status = ['Incoming Call', 'Outgoing Call', 'Missed Call', 'Callback', 'Outgoing Campaign'];
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

     private function findQueueLog($category)
     {
          if ($category != 'ticket-list') {
               return null;
          }
          $queueLogAction = "report-ticket-form-pdf-{$this->type}";
          $queueLog = QueueActionLog::query()
               ->where('company_id', user()->company_id)
               ->where('action', $queueLogAction)
               ->latest()
               ->select(['id', 'done_at', 'created_at'])
               ->first();

          if ($queueLog) {
               $hourdiff = round((strtotime(date('Y-m-d H:i:s')) - strtotime($queueLog->created_at)) / 3600, 1);
               if ($hourdiff >= 2) {
                    $queueLog->delete();
                    $queueLog = null;
               }
          }
          return $queueLog;
     }
     private function getCallTrackingTicketStatus($user)
     {
          if ($this->type == 'outbound') {
               $status = $this->ticketService->findAllStatusTicketWithColor($user, $this->type);
          } else {
               $status = $this->ticketService->findAllInboundStatus($user->company_id);
          }
          return $status;
     }
}
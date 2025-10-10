<?php
namespace App\Service\Ticket;

use App\Enum\Role;
use App\Models\Ticket\AgentActivity;
use App\Models\Ticket\CallAgent;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketHistory;
use Illuminate\Support\Facades\DB;

class ReportTicketService
{
     public function __construct(
          private $model = Ticket::class,
     ) {
     }

     public function findAllTicketListReportData($user, $filter, $search, $type, $paginate = null)
     {
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;

          $created_start = @$filter['created_start'];
          $created_end = @$filter['created_end'];
          $modify_start = @$filter['modify_start'];
          $modify_end = @$filter['modify_end'];
          $origins = @$filter['origins'];
          $category = @$filter['category'];
          $helpdesk_id = @$filter['helpdesk_id'];
          $status = @$filter['status'];
          $agent_id = @$filter['agent_id'];
          $spv_id = @$filter['spv_id'];
          $product_name = @$filter['product_name'];
          $last_handle = @$filter['last_handle'];
          $escalation_id = @$filter['escalation_id'];
          $priority = @$filter['priority'];
          $division_sla = @$filter['division_sla'];
          $ticket_sla = @$filter['ticket_sla'];
          $campaign_id = @$filter['campaign_id'];
          $new_data = @$filter['new_data'];
          $response_time = @$filter['response_time'];
          $broadcast_response = strtolower(@$filter['broadcast_response'] ?: '');

          $relations = [];
          if ((!$created_start && !$created_end) && (!$modify_start && !$modify_end)) {
               return [];
          }
          if ($type === 'inbound') {
               $relations = ['helpdesk:id,name'];
          } else {
               $relations = ['campaign:id,name'];
          }


          $query = $this->model::query()
               ->with([
                    'product:id,name',
                    'subject:id,name',
                    'escalationTeam:id,name',
                    'agent' => fn($query) => $query->where('company_users.company_id', $companyId)->select(['users.id', 'company_users.name', 'company_users.code']),
                    'spv' => fn($query) => $query->where('company_users.company_id', $companyId)->select(['users.id', 'company_users.name']),
                    ...$relations
               ])
               ->leftJoin('company_customer_contacts as c', function ($join) {
                    $join->on('c.customer_id', 'tickets.customer_id');
                    $join->on('c.company_id', 'tickets.company_id');
               })
               ->leftJoin("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->leftJoin('users as u', function ($join) {
                    $join->on('u.id', 'tickets.customer_id');
                    $join->where('u.role', 'customer');
               })
               ->filterBroadcasted($broadcast_response)
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->where('tickets.is_bucket', 0)
               ->when($created_start && $created_end, fn($query) => $query->whereBetween('tickets.created_at', [$created_start . " 00:00:00", $created_end . " 23:59:59"]))
               ->when($modify_start && $modify_end, fn($query) => $query->whereBetween('tickets.ticket_date', [$modify_start . " 00:00:00", $modify_end . " 23:59:59"]))
               ->when($origins, fn($query) => $query->whereIn('tickets.source', $origins))
               ->when($status, fn($query) => $query->whereIn('tickets.status', $status))
               ->when($category, fn($query) => $query->whereIn('tickets.product_category', $category))
               ->when($agent_id, fn($filter) => $filter->whereIn('tickets.current_agent_id', $agent_id))
               ->when($spv_id, fn($filter) => $filter->whereIn('tickets.spv_id', $spv_id))
               ->filterAgent($agent_id, $companyId, $userId, $userRole, $type, $escalationType)
               ->when($helpdesk_id, function ($query) use ($helpdesk_id) {
                    $query->whereIn('tickets.helpdesk_id', $helpdesk_id);
               })
               ->when($campaign_id, fn($query) => $query->whereIn('tickets.marketing_campaign_id', $campaign_id))
               ->when($product_name, fn($query) => $query->whereIn('tickets.product_name', $product_name))
               ->when($priority, fn($query) => $query->whereIn('tickets.priority', $priority))
               ->when($last_handle, fn($query) => $query->whereIn('tickets.last_agent_id', $last_handle))
               ->when($escalation_id, fn($query) => $query->whereIn('tickets.escalation_team_id', $escalation_id))
               ->when($new_data, fn($query) => $query->where('tickets.status', 'New'))
               ->filterTicketSla($ticket_sla)
               ->filterDivisionSla($division_sla)
               ->filterResponseTimeSla($response_time)
               ->search($search)
               ->select([
                    'tickets.ticket_date as updated_at',
                    'tickets.created_at',
                    'tickets.source as call_origin',
                    'tickets.ticket_number',
                    'tickets.customer_name',
                    'tickets.product_category',
                    'tickets.product_name',
                    'tickets.product_id',
                    'tickets.status',
                    'tickets.current_agent_id',
                    'tickets.spv_id',
                    'tickets.subject_id',
                    'tickets.priority',
                    'tickets.sla_resolution_time',
                    'tickets.sla_response_time',
                    'tickets.sla_division',
                    'tickets.escalation_team_id',
                    'tickets.status_id',
                    'tickets.status_table',
                    'st.status_category',
                    'tickets.note',
                    'tickets.remark',
                    'tickets.helpdesk_id',
                    'tickets.marketing_campaign_id',
                    'tickets.is_broadcasted',
                    'tickets.outbound_data_upload_bucket_id',
                    DB::raw("ifnull(c.email,u.email) as customer_email")
               ])
               ->groupBy([
                    'tickets.id',
               ])
               ->orderByRaw('tickets.ticket_date desc,tickets.outbound_data_upload_bucket_id asc,tickets.id asc');
          return $paginate ? $query->paginate($paginate) : $query->get();
     }


     public function findAllCallTrackingReportData($user, $type, $filter, $search, $paginate = null)
     {
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;

          $created_start = @$filter['created_start'];
          $created_end = @$filter['created_end'];
          $spv_id = @$filter['spv_id'];
          $agent_id = @$filter['agent_id'];
          if (!$created_start && !$created_end) {
               return [];
          }
          $totalCustomer = "count(distinct tickets.customer_id) as total_customer";
          if ($type == 'outbound') {
               $totalCustomer = "count(distinct tickets.customer_name) as total_customer";
          }

          $query = $this->model::query()
               ->with([
                    'ticketStatus' => function ($query) use ($type, $companyId, $agent_id, $created_start, $created_end) {
                         $query->whereBetween('ticket_date', [$created_start . " 00:00:00", $created_end . " 23:59:59"]);
                         $query->where('type', $type);
                         $query->where('company_id', $companyId);
                         $query->when($agent_id, fn($filter) => $filter->whereIn('current_agent_id', $agent_id));
                    }
               ])
               ->join('company_users as agent', function ($join) use ($companyId) {
                    $join->on('agent.user_id', 'tickets.current_agent_id');
                    $join->where('agent.company_id', $companyId);
               })
               ->join('company_users as spv', function ($join) use ($companyId) {
                    $join->on('spv.user_id', 'tickets.spv_id');
                    $join->where('spv.company_id', $companyId);
               })
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->when($search, function ($query) use ($search) {
                    $query->where('agent.name', 'like', "%{$search}%");
                    $query->orWhere('spv.name', 'like', "%{$search}%");
               })
               ->whereBetween('tickets.ticket_date', [$created_start . " 00:00:00", $created_end . " 23:59:59"])
               ->when($agent_id, fn($filter) => $filter->whereIn('current_agent_id', $agent_id))
               ->when($spv_id, fn($filter) => $filter->whereIn('spv_id', $spv_id))
               ->filterAgent($agent_id, $companyId, $userId, $userRole, $type, $escalationType)
               ->select([
                    'tickets.type',
                    'tickets.company_id',
                    'tickets.current_agent_id',
                    'spv_id',
                    'agent.name as agent_name',
                    'agent.code as agent_code',
                    'spv.name as spv_name',
                    DB::raw('count(distinct tickets.id) total_ticket'),
                    DB::raw($totalCustomer),
               ])
               ->orderByRaw('count(distinct tickets.id) desc')
               ->groupBy([
                    'tickets.company_id',
                    'tickets.current_agent_id',
                    'tickets.spv_id',
                    'tickets.type',
                    'agent.name',
                    'agent.code',
                    'spv.name'
               ]);

          return $paginate ? $query->paginate($paginate) : $query->get();
     }

     public function findAllAgentActivityReportData($user, $filter, $search, $type, $paginate)
     {
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;

          $created_start = @$filter['created_start'];
          $created_end = @$filter['created_end'];
          $agent_id = @$filter['agent_id'];
          $roles = @$filter['roles'];
          if (!$created_start && !$created_end) {
               return [];
          }

          $query = AgentActivity::query()
               ->where('view_report_agent_activity.company_id', $companyId)
               ->filterAgent($userRole, $userId, $companyId, $type, $escalationType)
               ->when($search, function ($query) use ($search) {
                    $query->where('view_report_agent_activity.name', 'like', "%{$search}%");
               })
               ->whereBetween('date', [$created_start, $created_end])
               ->select([
                    'view_report_agent_activity.*',
                    DB::raw("'{$type}' as type_category")
               ])
               ->when($agent_id, fn($query) => $query->whereIn('user_id', $agent_id))
               ->when($roles, fn($query) => $query->whereIn('role', $roles))
               ->groupBy('view_report_agent_activity.user_id');

          return $paginate ? $query->paginate($paginate) : $query->get();
     }


     public function findAllCallAgentReportData($user, $filter, $search, $type, $paginate)
     {
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;

          $created_start = @$filter['created_start'];
          $created_end = @$filter['created_end'];
          $spv_id = @$filter['spv_id'];
          $agent_id = @$filter['agent_id'];
          if (!$created_start && !$created_end) {
               return [];
          }

          $query = CallAgent::query()
               ->filterAgent($userRole, $userId, $companyId, $type, $escalationType)
               ->joinSpv($userRole, $companyId, $type, $escalationType)
               ->where('view_report_call_agent.company_id', $companyId)
               ->whereBetween('view_report_call_agent.date', [$created_start, $created_end])
               ->when($agent_id, fn($filter) => $filter->whereIn('agent_id', $agent_id))
               ->when($type == 'inbound', fn($query) => $query->whereNull('view_report_call_agent.sip'))
               ->when($type == 'outbound', fn($query) => $query->whereNotNull('view_report_call_agent.sip'))
               ->when($search, function ($query) use ($search) {
                    $query->where('view_report_call_agent.agent_name', 'like', "%{$search}%");
               })
               ->select(
                    'view_report_call_agent.*',
                    'company_users.name as spv_name',
                    'company_users.code as spv_code',
                    DB::raw("'{$type}' as type_filter")
               )
               ->groupBy([
                    'view_report_call_agent.agent_id',
                    'view_report_call_agent.date',
               ])
               ->orderBy('date', 'desc');
          return $paginate ? $query->paginate($paginate) : $query->get();
     }


     public function findAllFormTicketListReportData($user, $type, $filter, $search)
     {
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;

          $created_start = @$filter['created_start'];
          $created_end = @$filter['created_end'];
          $modify_start = @$filter['modify_start'];
          $modify_end = @$filter['modify_end'];
          $origins = @$filter['origins'];
          $category = @$filter['category'];
          $helpdesk_id = @$filter['helpdesk_id'];
          $campaign_id = @$filter['campaign_id'];
          $status = @$filter['status'];
          $spv_id = @$filter['spv_id'];
          $agent_id = @$filter['agent_id'];

          if ((!$created_start && !$created_end) && (!$modify_start && !$modify_end)) {
               return [];
          }



          $relations = [
               'lastHistory.forms'
          ];
          if ($type == 'outbound') {
               $relations = [
                    ...$relations,
                    ...[
                         'lastHistory.insured',
                         'lastHistory.beneficiary'
                    ]
               ];
          }
          return $this->model::query()
               ->with($relations)
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->where('tickets.is_bucket', 0)
               ->filterAgent($userRole, $userId, $companyId, $type, $escalationType)
               ->when($created_start && $created_end, fn($query) => $query->whereBetween('tickets.created_at', [$created_start . " 00:00:00", $created_end . " 23:59:59"]))
               ->when($modify_start && $modify_end, fn($query) => $query->whereBetween('tickets.ticket_date', [$modify_start . " 00:00:00", $modify_end . " 23:59:59"]))
               ->when(
                    $search,
                    fn($q) => $q->where(function ($query) use ($search) {
                         $query->where('tickets.product_name', 'like', "%{$search}%");
                         $query->orWhere('tickets.customer_name', 'like', "%{$search}%");
                         $query->orWhere('tickets.ticket_number', 'like', "%{$search}%");
                    })
               )
               ->when($origins, fn($query) => $query->whereIn('tickets.source', $origins))
               ->when($status, fn($query) => $query->whereIn('tickets.status', $status))
               ->when($category, fn($query) => $query->whereIn('tickets.product_category', $category))
               ->when($agent_id, fn($filter) => $filter->whereIn('tickets.current_agent_id', $agent_id))
               ->when($spv_id, fn($filter) => $filter->whereIn('tickets.spv_id', $spv_id))
               ->when($helpdesk_id, function ($query) use ($helpdesk_id) {
                    $query->whereIn('tickets.helpdesk_id', $helpdesk_id);
               })
               ->when($campaign_id, function ($query) use ($campaign_id) {
                    $query->whereIn('tickets.marketing_campaign_id', $campaign_id);
               })
               ->select([
                    'tickets.id',
                    'tickets.created_at',
                    'tickets.number_id',
                    'tickets.ticket_number',
                    'tickets.customer_name',
               ])
               ->orderBy('tickets.created_at', 'desc')
               ->get()
               ->map(function ($items) {
                    if ($items->lastHistory) {
                         $items->form = $items->lastHistory?->forms->groupBy('form_category')->map(function ($row) {
                              return $row->groupBy('group_name')
                                   ->map(function ($group) {
                                        $fields = $group
                                             ->sortBy('sorting')
                                             ->each(function ($row) {
                                                  if ($row->input_type == 'file') {
                                                       $row->content = $row->content && $row->content!='' ? asset($row->content) : '';
                                                  }
                                             })
                                             ->values();
                                        return $fields;
                                   });
                         });
                         $items->insured = $items->lastHistory?->insured;
                         $items->beneficiary = $items->lastHistory?->beneficiary;
                         unset($items->lastHistory->forms);
                         unset($items->lastHistory->insured);
                         unset($items->lastHistory->beneficiary);
                    } else {
                         $items->form = [];
                         $items->insured = [];
                         $items->beneficiary = [];
                    }
                    return $items;
               });
     }



     public function findAllChatTicketListReportData($user, $type, $filter, $search)
     {
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          
          $created_start = @$filter['created_start'];
          $created_end = @$filter['created_end'];
          $modify_start = @$filter['modify_start'];
          $modify_end = @$filter['modify_end'];
          $origins = @$filter['origins'];
          $category = @$filter['category'];
          $helpdesk_id = @$filter['helpdesk_id'];
          $campaign_id = @$filter['campaign_id'];
          $status = @$filter['status'];
          $spv_id = @$filter['spv_id'];
          $agent_id = @$filter['agent_id'];
          
          if ((!$created_start && !$created_end) && (!$modify_start && !$modify_end)) {
               return [];
          }


          return TicketHistory::query()
               ->with([
                    'messages', 
                    // 'wa', 
                    'comments'
               ])
               ->join('tickets', 'tickets.id', 'ticket_histories.ticket_id')
               ->leftJoin('company_users', function ($join) {
                    $join->on('company_users.user_id', 'ticket_histories.agent_id');
                    $join->on('company_users.company_id', 'ticket_histories.company_id');
               })
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->where('tickets.is_bucket', 0)
               ->when($created_start && $created_end, fn($query) => $query->whereBetween('tickets.created_at', [$created_start . " 00:00:00", $created_end . " 23:59:59"]))
               ->when($modify_start && $modify_end, fn($query) => $query->whereBetween('tickets.ticket_date', [$modify_start . " 00:00:00", $modify_end . " 23:59:59"]))
               ->when(
                    $search,
                    fn($q) => $q->where(function ($query) use ($search) {
                         $query->where('tickets.product_name', 'like', "%{$search}%");
                         $query->orWhere('tickets.customer_name', 'like', "%{$search}%");
                         $query->orWhere('tickets.ticket_number', 'like', "%{$search}%");
                    })
               )
               ->filterAgent($userRole, $userId, $companyId, $type, $escalationType)
               ->when($origins, fn($query) => $query->whereIn('tickets.source', $origins))
               ->when($status, fn($query) => $query->whereIn('tickets.status', $status))
               ->when($category, fn($query) => $query->whereIn('tickets.product_category', $category))
               ->when($agent_id, fn($filter) => $filter->whereIn('tickets.current_agent_id', $agent_id))
               ->when($spv_id, fn($filter) => $filter->whereIn('tickets.spv_id', $spv_id))
               ->when($helpdesk_id, function ($query) use ($helpdesk_id) {
                    $query->whereIn('tickets.helpdesk_id', $helpdesk_id);
               })
               ->when($campaign_id, function ($query) use ($campaign_id) {
                    $query->whereIn('tickets.marketing_campaign_id', $campaign_id);
               })
               ->select([
                    'tickets.ticket_date as created_at',
                    'ticket_histories.created_at as history_date',
                    'tickets.number_id',
                    'tickets.ticket_number',
                    'tickets.customer_name',
                    'ticket_histories.note',
                    'ticket_histories.remark',
                    'ticket_histories.id',
                    'ticket_histories.ticket_id',
                    'ticket_histories.chat_id',
                    'ticket_histories.status',
                    'company_users.name as agent_name',
                    'ticket_histories.inbound_whatsapp_id'
               ])
               ->orderBy('ticket_histories.created_at', 'desc')
               ->get()
               ->map(function ($items) {
                    $items->comments = $items->comments?->groupBy('type');
                    return $items;
               });
     }

}
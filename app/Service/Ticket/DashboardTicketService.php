<?php
namespace App\Service\Ticket;

use App\Helpers\Yellow;
use App\Models\Ticket\Ticket;
use App\Models\Util\Call;
use App\Models\Util\Rating;
use Illuminate\Support\Facades\DB;

class DashboardTicketService
{
     public function __construct(
          private $model = Ticket::class,
          private $call = Call::class,
          private $rating = Rating::class,
     ) {
     }


     public function findNewTicketByDate($user, $dateToday, $dateYesterday, $type)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $result = $this->model::query()
               ->leftJoin("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->select([
                    'tickets.id',
                    DB::raw("date(created_at) as date")
               ])
               ->filterByCompanyTypeDateRangeAndSource($companyId, $type, [$dateToday, $dateYesterday])
               ->where(function ($query) {
                    $query->whereIn("tickets.status", ['New', 'From Bot', 'From Whatsapp Bot', 'From Web Bot']);
                    $query->orWhere('st.status_category', "New");
               })
               ->get();

          $today = $result->where('date', $dateToday)->count();
          $yesterday = $result->where('date', $dateYesterday)->count();
          return [
               'today' => $today,
               'yesterday' => $today - $yesterday
          ];
     }

     public function findUnassignedEnquiryTicketByDate($user, $dateToday, $dateYesterday, $type)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $sourceTicket = ['From Email', 'From Whatsapp', 'From Facebook', 'From Instagram'];
          $result = $this->model::query()
               ->select([
                    'tickets.id',
                    DB::raw("date(created_at) as date")
               ])
               ->filterByCompanyTypeDateRangeAndSource($companyId, $type, [$dateToday, $dateYesterday], $sourceTicket)
               ->whereNull('tickets.current_agent_id')
               ->get();

          $today = $result->where('date', $dateToday)->count();
          $yesterday = $result->where('date', $dateYesterday)->count();
          return [
               'today' => $today,
               'yesterday' => $today - $yesterday
          ];
     }

     public function findUnassignedTicketByDate($user, $dateToday, $dateYesterday, $type)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $sourceTicket = ['From Whatsapp Bot', 'From Web Bot'];
          $result = $this->model::query()
               ->select([
                    'tickets.id',
                    DB::raw("date(created_at) as date")
               ])
               ->filterByCompanyTypeDateRangeAndSource($companyId, $type, [$dateToday, $dateYesterday], $sourceTicket)
               ->whereNull('tickets.current_agent_id')
               ->get();

          $today = $result->where('date', $dateToday)->count();
          $yesterday = $result->where('date', $dateYesterday)->count();
          return [
               'today' => $today,
               'yesterday' => $today - $yesterday
          ];
     }

     public function findOpenTicketByDate($user, $dateToday, $dateYesterday, $type)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $result = $this->model::query()
               ->leftJoin("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->select([
                    'tickets.id',
                    DB::raw("date(created_at) as date")
               ])
               ->filterByCompanyTypeDateRangeAndSource($companyId, $type, [$dateToday, $dateYesterday])
               ->where(function ($query) {
                    $query->where("tickets.status", 'Open');
                    $query->orWhere('st.status_category', "Open");
               })
               ->get();

          $today = $result->where('date', $dateToday)->count();
          $yesterday = $result->where('date', $dateYesterday)->count();
          return [
               'today' => $today,
               'yesterday' => $today - $yesterday
          ];
     }

     public function findSolvedTicketByDate($user, $dateToday, $dateYesterday, $type)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $result = $this->model::query()
               ->leftJoin("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->select([
                    'tickets.id',
                    DB::raw("date(created_at) as date")
               ])
               ->filterByCompanyTypeDateRangeAndSource($companyId, $type, [$dateToday, $dateYesterday])
               ->where(function ($query) {
                    $query->where("tickets.status", 'Solved');
                    $query->orWhere('st.status_category', "Solved");
               })
               ->get();

          $today = $result->where('date', $dateToday)->count();
          $yesterday = $result->where('date', $dateYesterday)->count();
          return [
               'today' => $today,
               'yesterday' => $today - $yesterday
          ];
     }

     public function findEscalatedTicketByDate($user, $dateToday, $dateYesterday, $type)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $result = $this->model::query()
               ->select([
                    'tickets.id',
                    DB::raw("date(created_at) as date")
               ])
               ->filterByCompanyTypeDateRangeAndSource($companyId, $type, [$dateToday, $dateYesterday])
               ->whereNotNull('escalation_team_id')
               ->get();

          $today = $result->where('date', $dateToday)->count();
          $yesterday = $result->where('date', $dateYesterday)->count();
          return [
               'today' => $today,
               'yesterday' => $today - $yesterday
          ];
     }

     public function findTopSolvedClosedTicketAgent($user, $dates, $type,$top = 10,$filter = [])
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $campaignId = @$filter['campaign_id'];
          $productId = @$filter['product_id'];

          $result = $this->model::query()
               ->leftJoin("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->join("company_users", function ($join) {
                    $join->on("company_users.company_id", "tickets.company_id");
                    $join->on("company_users.user_id", "tickets.current_agent_id");
               })
               ->select([
                    'tickets.current_agent_id',
                    'company_users.name',
                    'company_users.profile',
                    DB::raw("sum(case when st.status_category in ('Closed','Solved') or tickets.status in ('Closed','Auto Closed','Solved') then 1 else 0 end) as total"),
                    DB::raw("sum(case when st.status_category='Open' then 1 else 0 end) as open"),
               ])
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->whereNull('tickets.marketing_campaign_id')
               ->when($campaignId,fn($query)=>$query->where('tickets.marketing_campaign_id',$campaignId))
               ->when($productId,fn($query)=>$query->where('tickets.product_id',$productId))
               ->whereRaw("date(tickets.ticket_date) between ? and ?", [$dates[0], $dates[count($dates) - 1]])
               ->where(function ($query) {
                    $query->whereIn('st.status_category', ["Solved", "Closed"]);
                    $query->orWhereIn('tickets.status', ["Solved", "Auto Closed"]);
               })
               ->groupBy(["tickets.current_agent_id", "company_users.name","company_users.profile"])
               ->orderBy("total", "desc")
               ->take($top)
               ->get();
          $items = $result->toArray();
          $totalData = $result->count();
          if ($totalData < $top && $totalData && $type=='inbound') {
               $appends = collect(range(1, $top - $totalData))->map(fn($row) => [
                    'current_agent_id' => null,
                    'name' => "#",
                    'total' => 0
               ]);
               $items = [
                    ...$items,
                    ...$appends
               ];
          }
          return $items;
     }

     public function findAllFirstResponseTime($user, $dates, $totalResponseSlaTime, $type)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $result = $this->model::query()
               ->leftJoin("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->selectRaw("
                    date(created_at) date,
                    count(tickets.id) as total_ticket,
                    sum(case when st.status_category='Closed' or tickets.status='Closed' or tickets.status='Auto Closed' then 1 else 0 end) as total_closed 
               ")
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->whereRaw("date(tickets.created_at) between ? and ?", [$dates[0], $dates[count($dates) - 1]])
               ->groupByRaw("date(created_at)")
               ->get();
          return collect($dates)->map(function ($date) use ($result, $totalResponseSlaTime) {
               $frt = 0;
               $ticket = $result->where('date', $date)->first();
               if ($ticket) {
                    $totalTicket = $ticket->total_ticket * $totalResponseSlaTime;
                    if ($ticketClosed = $ticket->total_closed) {
                         $frt = round($totalTicket / $ticketClosed);
                    }
               }
               return [
                    'date' => date('d-m-Y', strtotime($date)),
                    'frt' => $frt,
                    'label' => Yellow::minuteToSla($frt)
               ];
          });
     }

     public function findAllFirstResolutionTime($user, $dates, $type)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $result = $this->model::query()
               ->leftJoin("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->selectRaw("
                    date(created_at) date,
                    count(tickets.id) as total_ticket,
                    sum(case when st.status_category='Closed' or tickets.status='Closed' or tickets.status='Auto Closed' then TIMESTAMPDIFF(MINUTE,created_at,ticket_date) else 0 end) as duration_closed  
               ")
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->whereRaw("date(tickets.created_at) between ? and ?", [$dates[0], $dates[count($dates) - 1]])
               ->groupByRaw("date(created_at)")
               ->get();
          return collect($dates)->map(function ($date) use ($result) {
               $frt = 0;
               $ticket = $result->where('date', $date)->first();
               if ($ticket) {
                    $totalTicket = $ticket->total_ticket;
                    if ($durationClosed = $ticket->duration_closed) {
                         $frt = round($durationClosed / $totalTicket);
                    }
               }
               return [
                    'date' => date('d-m-Y', strtotime($date)),
                    'frt' => $frt,
                    'label' => Yellow::minuteToSla($frt)
               ];
          });
     }

     public function countAllTicketByCategoryStatus($user, $dates, $type)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $result = $this->model::query()
               ->leftJoin("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->select([
                    DB::raw("
                         (
                              case 
                              when st.status_category is null and tickets.`status`='Auto Closed' then 'Closed'
                              when st.status_category is  null then 'New' 
                              else st.status_category end
                         ) as status_category
                    "),
                    DB::raw("count(tickets.id) as total")
               ])
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->whereRaw("date(tickets.created_at) between ? and ?", [$dates[0], $dates[count($dates) - 1]])
               ->groupByRaw("
                    case 
                    when st.status_category is null and tickets.`status`='Auto Closed' then 'Closed'
                    when st.status_category is  null then 'New' 
                    else st.status_category end
               ")
               ->get();
          return $result;
     }

     public function findAllDailyTicketCategory($user, $dates, $type)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $result = $this->model::query()
               ->leftJoin("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->selectRaw("
                    date(created_at) date,
                    sum(case when st.status_category='Closed' or tickets.status='Auto Closed' then 1 else 0 end) as closed,
                    sum(case when st.status_category='New' or tickets.status='New' then 1 else 0 end) as new,
                    sum(case when st.status_category='Solved' then 1 else 0 end) as solved,
                    sum(case when st.status_category='Open' then 1 else 0 end) as open
               ")
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->whereRaw("date(tickets.created_at) between ? and ?", [$dates[0], $dates[count($dates) - 1]])
               ->groupByRaw("date(created_at)")
               ->get();
          return collect($dates)->map(function ($date) use ($result) {
               $ticket = $result->where('date', $date)->first();
               return [
                    'date' => date('d-m-Y', strtotime($date)),
                    'new' => intval($ticket?->new ?: 0),
                    'closed' => intval($ticket?->closed ?: 0),
                    'solved' => intval($ticket?->solved ?: 0),
                    'open' => intval($ticket?->open ?: 0),
               ];
          });
     }

     public function findAllTicketCategoryBySource($user, $dates, $type)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          return $this->model::query()
               ->select([
                    'tickets.source',
                    'tickets.call_id',
                    'tickets.chat_id',
                    DB::raw("count(distinct tickets.id) as total_ticket")
               ])
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->whereRaw("date(tickets.created_at) between ? and ?", [$dates[0], $dates[count($dates) - 1]])
               ->groupByRaw("tickets.source,tickets.call_id,tickets.chat_id")
               ->get();
     }

     public function findAllMissedCall($user, $dates)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          return $this->call::query()
               ->select([
                    DB::raw("count(distinct calls.id) as total")
               ])
               ->where('calls.company_id', $companyId)
               ->where("calls.category", "Missed Call")
               ->whereRaw("date(calls.created_at) between ? and ?", [$dates[0], $dates[count($dates) - 1]])
               ->get()
               ->sum('total');
     }

     public function findAllCsatRating($user, $dates)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $csat = $this->rating::query()
               ->fromRaw("
                    ratings,
                    JSON_TABLE(csat_rating, '$.ratings[*]'
                    COLUMNS (
                         rating INT PATH '$.rating'
                    )
                    ) AS rt
               ")
               ->select([
                    "rt.rating",
                    DB::raw("count(*) as total")
               ])
               ->where('ratings.company_id', $companyId)
               ->whereRaw("date(ratings.created_at) between ? and ?", [$dates[0], $dates[count($dates) - 1]])
               ->groupBy('rt.rating')
               ->get();

          $goodRating = $csat->whereIn('rating', [4, 5])->sum('total');
          $badRating = $csat->whereIn('rating', [1, 2, 3])->sum('total');
          $allRating = $csat->where('rating', '!=', 0)->sum('total');
          if ($allRating > 0) {
               $goodRating = $goodRating / $allRating * 100;
               $badRating = $badRating / $allRating * 100;
          }
          return [
               'good' => round($goodRating),
               'bad' => round($badRating),
               'total' => $allRating,
          ];
     }

     public function findAllTicketByStatusCategory($user, $dates, $type,$filter = [])
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $campaignId = @$filter['campaign_id'];
          $productId = @$filter['product_id'];

          return $this->model::query()
               ->leftJoin("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->select([
                    'tickets.status',
                    DB::raw("
                         (
                              case 
                              when st.status_category is null and tickets.`status`='Auto Closed' then 'Closed'
                              when st.status_category is  null then 'New' 
                              else st.status_category end
                         ) as status_category
                    "),
                    DB::raw("count(tickets.status) as total")
               ])
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->when($campaignId,fn($query)=>$query->where('tickets.marketing_campaign_id',$campaignId))
               ->when($productId,fn($query)=>$query->where('tickets.product_id',$productId))
               ->whereRaw("date(tickets.ticket_date) between ? and ?", [$dates[0], $dates[count($dates) - 1]])
               ->groupByRaw("tickets.status,st.status_category")
               ->orderByRaw("count(tickets.status) desc")
               ->get();
     }

     public function findAllTicketOutboundMarketingCampaign($user, $dates, $type, $filter)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $marketingCampaign = @$filter['campaign_id'];
          $productId = @$filter['product_id'];

          $subJoinTicketHistory = DB::table('ticket_histories')
               ->selectRaw("ticket_id,count(id) as call_attempt")
               ->groupBy("ticket_id");

          return $this->model::query()
               ->leftJoin("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->leftJoinSub($subJoinTicketHistory, "h", "h.ticket_id", "tickets.id")
               ->select([
                    DB::raw("count(distinct tickets.id) as data_size"),
                    DB::raw("sum(h.call_attempt) as call_attempt"),
                    DB::raw("sum(case when st.status_category='Closed' or tickets.status='Closed' or tickets.status='Auto Closed' then 1 else 0 end) as close_deal"),
                    DB::raw("sum(case when st.status_category is null or tickets.status='New' then 1 else 0 end) as utilized"),
               ])
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->whereNull('tickets.escalation_team_id')
               ->when($marketingCampaign,fn($query)=>$query->where('tickets.marketing_campaign_id', $marketingCampaign))
               ->when($productId,fn($query)=>$query->where('tickets.product_id', $productId))
               ->whereRaw("date(tickets.ticket_date) between ? and ?", [$dates[0], $dates[count($dates) - 1]])
               ->first();
     }

     public function findAllTicketOutboundMarketingCampaignDataSize($user, $type, $filter)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalationType = $user->escalation_type || $userRole;
          $marketingCampaign = @$filter['campaign_id'];
          $productId = @$filter['product_id'];

          $subJoinTicketHistory = DB::table('ticket_histories')
               ->selectRaw("ticket_id,count(id) as call_attempt")
               ->groupBy("ticket_id");

          return $this->model::query()
               ->leftJoin("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->join('marketing_campaigns','marketing_campaigns.id','tickets.marketing_campaign_id')
               ->leftJoinSub($subJoinTicketHistory, "h", "h.ticket_id", "tickets.id")
               ->select([
                    DB::raw("count(distinct tickets.id) as data_size"),
                    DB::raw("sum(h.call_attempt) as call_attempt"),
                    DB::raw("sum(case when st.status_category='Closed' or tickets.status='Closed' or tickets.status='Auto Closed' then 1 else 0 end) as close_deal"),
                    DB::raw("sum(case when st.status_category is null or tickets.status='New' then 1 else 0 end) as utilized"),
               ])
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->where('marketing_campaigns.status', 'active')
               ->whereNull('tickets.escalation_team_id')
               ->when($marketingCampaign,fn($query)=>$query->where('tickets.marketing_campaign_id', $marketingCampaign))
               ->when($productId,fn($query)=>$query->where('tickets.product_id', $productId))
               ->first();
     }

}
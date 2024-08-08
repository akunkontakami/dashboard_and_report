<?php
namespace App\Service\Ticket;

use App\Helpers\Yellow;
use App\Models\Ticket\Ticket;
use Illuminate\Support\Facades\DB;

class DashboardTicketService
{
     public function __construct(
          private $model = Ticket::class
     ) {
     }


     public function findNewTicketByDate($user, $dateToday, $dateYesterday, $type)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalation_type = $user->escalation_type;
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
          $escalation_type = $user->escalation_type;
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
          $escalation_type = $user->escalation_type;
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
          $escalation_type = $user->escalation_type;
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
          $escalation_type = $user->escalation_type;
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
          $escalation_type = $user->escalation_type;
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

     public function findTopTenSolvedClosedTicketAgent($user, $date, $type)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalation_type = $user->escalation_type;
          $result = $this->model::query()
               ->join("view_status_table_mapper as st", function ($join) {
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
                    DB::raw("count(tickets.id) as total")
               ])
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->whereRaw('date(tickets.created_at) = ?', $date)
               ->where(function ($query) {
                    $query->orWhereIn('st.status_category', ["Solved", "Closed"]);
               })
               ->groupBy(["tickets.current_agent_id", "company_users.name"])
               ->orderBy("total", "desc")
               ->take(10)
               ->get();

          $items = $result->toArray();
          $totalData = $result->count();
          if ($totalData < 10 && $totalData) {
               $appends = collect(range(1, 10 - $totalData))->map(fn($row) => [
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
          $escalation_type = $user->escalation_type;
          $result = $this->model::query()
               ->join("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->selectRaw("
                    date(created_at) date,
                    count(tickets.id) as total_ticket,
                    sum(case when st.status_category='Closed' then 1 else 0 end) as total_closed 
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
          $escalation_type = $user->escalation_type;
          $result = $this->model::query()
               ->join("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->selectRaw("
                    date(created_at) date,
                    count(tickets.id) as total_ticket,
                    sum(case when st.status_category='Closed' then TIMESTAMPDIFF(MINUTE,created_at,ticket_date) else 0 end) as duration_closed  
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
          $escalation_type = $user->escalation_type;
          $result = $this->model::query()
               ->join("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->select([
                    "st.status_category",
                    DB::raw("count(tickets.id) as total")
               ])
               ->where('tickets.company_id', $companyId)
               ->where('tickets.type', $type)
               ->whereRaw("date(tickets.created_at) between ? and ?", [$dates[0], $dates[count($dates) - 1]])
               ->groupBy('st.status_category')
               ->get();
          return $result;
     }

     public function findAllDailyTicketCategory($user, $dates, $type)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalation_type = $user->escalation_type;
          $result =  $this->model::query()
               ->join("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->selectRaw("
                    date(created_at) date,
                    sum(case when st.status_category='Closed' then 1 else 0 end) as closed,
                    sum(case when st.status_category='New' then 1 else 0 end) as new,
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

}
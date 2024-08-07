<?php
namespace App\Service\Ticket;

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
          if ($totalData < 10) {
               $appends = collect(range(1, 10 - $totalData))->map(fn($row)=>[
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
}
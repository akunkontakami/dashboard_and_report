<?php
namespace App\Service\Ticket;

use App\Models\Ticket\Ticket;
use Illuminate\Support\Facades\DB;

class TicketService
{
     public function __construct(
          private $model = Ticket::class
     ) {
     }


     public function findNewTicketByDate($user, $dateToday, $dateYesterday)
     {
          // Todo : filter by spv, spv esca, am, am esca user
          $companyId = $user->company_id;
          $userId = $user->id;
          $userRole = $user->role;
          $escalation_type = $user->escalation_type;
          $result =  $this->model::query()
               ->leftJoin("view_status_table_mapper as st", function ($join) {
                    $join->on("st.id", "tickets.status_id");
                    $join->on("st.table_name", "tickets.status_table");
               })
               ->select([
                    'tickets.id',
                    DB::raw("date(created_at) as date")
               ])
               ->where('tickets.company_id', $companyId)
               ->whereRaw('date(created_at) in (?,?)', [$dateToday, $dateYesterday])
               ->where(function ($query) {
                    $query->whereIn("tickets.status", ['New', 'From Bot', 'From Whatsapp Bot', 'From Web Bot']);
                    $query->orWhere('st.status_category', "New");
               })
               ->get();

          $today = $result->where('date',$dateToday)->count();
          $yesterday = $result->where('date',$dateYesterday)->count();
          return [
               'today' => $today,
               'yesterday' => $today - $yesterday
          ];
     }
}
<?php
namespace App\Http\Controllers\Dashboard\Data;

use App\Helpers\Yellow;
use App\Service\Ticket\DashboardTicketService;
use Illuminate\Http\Request;

trait InboundKpiData
{
     public function kpiTicketStatus(Request $request, DashboardTicketService $dashboardTicketService)
     {
          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
          $tickets = $dashboardTicketService->countAllTicketByCategoryStatus($user, $dates, 'inbound');
          return collect([
               [
                    "name" => "New",
                    "color" => "#FF605C"
               ],[
                    "name" => "Open",
                    "color" => "#FFBD44"
               ],[
                    "name" => "Solved",
                    "color" => "#26BFF0"
               ],[
                    "name" => "Closed",
                    "color" => "#00CA95"
               ]
          ])->map(function($category) use($tickets){
               $ticket = $tickets->where('status_category',$category['name'])->first();
               return [
                    ...$category,
                    'total' => $ticket?->total ?: 0
               ];
          });
     }

     public function kpiSlaTime(Request $request)
     {

     }

     public function kpiTicketActivity(Request $request)
     {

     }

     public function kpiSlaChart(Request $request)
     {

     }

     public function kpiTicketChannel(Request $request)
     {

     }

     public function kpiVoicePstn(Request $request)
     {

     }

     public function kpiWebCall(Request $request)
     {

     }

     public function kpiCsat(Request $request)
     {

     }
     public function kpiTicketStatusChart(Request $request)
     {

     }
}
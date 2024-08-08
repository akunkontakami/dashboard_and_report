<?php
namespace App\Http\Controllers\Dashboard\Data;

use App\Helpers\Yellow;
use App\Service\Ticket\DashboardTicketService;
use App\Service\Utility\UtilityService;
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
               ],
               [
                    "name" => "Open",
                    "color" => "#FFBD44"
               ],
               [
                    "name" => "Solved",
                    "color" => "#26BFF0"
               ],
               [
                    "name" => "Closed",
                    "color" => "#00CA95"
               ]
          ])->map(function ($category) use ($tickets) {
               $ticket = $tickets->where('status_category', $category['name'])->first();
               return [
                    ...$category,
                    'total' => $ticket?->total ?: 0
               ];
          });
     }

     public function kpiSlaTime(Request $request, UtilityService $utilityService, DashboardTicketService $dashboardTicketService)
     {
          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
          $totalDay = count($dates);

          $totalResponseSlaTime = $utilityService->findAllSumResponseTimeSla($user->company_id, 'inbound');
          $responseTime = $dashboardTicketService->findAllFirstResponseTime($user, $dates, $totalResponseSlaTime, 'inbound')->sum('frt');
          $resolutionTime = $dashboardTicketService->findAllFirstResolutionTime($user, $dates, 'inbound')->sum('frt');
          return [
               [
                    "name" => "average first response time",
                    "total" => Yellow::minuteToSla(round($responseTime / $totalDay))
               ],
               [
                    "name" => "average first resolution time",
                    "total" => Yellow::minuteToSla(round($resolutionTime / $totalDay))
               ]
          ];
     }

     public function kpiTicketActivity(Request $request, DashboardTicketService $dashboardTicketService)
     {
          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
          return $dashboardTicketService->findAllDailyTicketCategory($user, $dates, 'inbound');
     }

     public function kpiSlaChart(Request $request, UtilityService $utilityService, DashboardTicketService $dashboardTicketService)
     {
          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));

          $totalResponseSlaTime = $utilityService->findAllSumResponseTimeSla($user->company_id, 'inbound');
          
          $responseTime = $dashboardTicketService->findAllFirstResponseTime($user, $dates, $totalResponseSlaTime, 'inbound');
          $resolutionTime = $dashboardTicketService->findAllFirstResolutionTime($user, $dates, 'inbound');
          return $responseTime->map(function ($response) use ($resolutionTime) {
               $resolution = $resolutionTime->where('date', $response['date'])->first();
               return [
                    'date' => $response['date'],
                    'response' => (object) [
                         'value' => $response['frt'],
                         'label' => $response['label'],
                    ],
                    'resolution' => (object) [
                         'value' => $resolution['frt'],
                         'label' => $resolution['label'],
                    ]
               ];
          });
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
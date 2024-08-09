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

     public function kpiTicketChannel(Request $request, DashboardTicketService $dashboardTicketService)
     {
          $channels = [
               [
                    "label" => "Voice PSTN",
                    "color" => "#F94144",
                    "source" => ['From SIP', 'From Incoming SIP']
               ],
               [
                    "label" => "Web Call",
                    "color" => "#2D9CDB",
                    "source" => ['From Web']
               ],
               [
                    "label" => "Web Chat",
                    "color" => "#F8961E",
                    "source" => ['From Web']
               ],
               [
                    "label" => "Web Bot",
                    "color" => "#F9C74F",
                    "source" => ['From Web Bot']
               ],
               [
                    "label" => "Whatsapp",
                    "color" => "#90BE6D",
                    "source" => ['From Whatsapp']
               ],
               [
                    "label" => "Whatsapp Bot",
                    "color" => "#F3722C",
                    "source" => ['From Whatsapp Bot']
               ],
               [
                    "label" => "Email",
                    "color" => "#7A7E80",
                    "source" => ['From Email']
               ],
               [
                    "label" => "Instagram",
                    "color" => "#E99C00",
                    "source" => ['From Instagram']
               ],
               [
                    "label" => "Facebook",
                    "color" => "#E4BEBE",
                    "source" => ['From Facebook']
               ]
          ];


          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));

          $tickets = $dashboardTicketService->findAllTicketCategoryBySource($user, $dates, 'inbound');
          return collect($channels)->map(function ($channel) use ($tickets) {
               $ticket = $tickets->whereIn('source', $channel['source'])
                    ->when($channel['label'] == "Web Call", fn($query) => $query->whereNotNull('call_id'))
                    ->when($channel['label'] == "Web Chat", fn($query) => $query->whereNotNull('chat_id'));
               return [
                    ...$channel,
                    'total' => $ticket->sum('total_ticket')
               ];
          });
     }

     public function kpiVoicePstn(Request $request, DashboardTicketService $dashboardTicketService)
     {
     }

     public function kpiWebCall(Request $request, DashboardTicketService $dashboardTicketService)
     {
          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
          $missedCall =  $dashboardTicketService->findAllMissedCall($user,$dates);
          return [$missedCall];
     }

     public function kpiCsat(Request $request, DashboardTicketService $dashboardTicketService)
     {
          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
          $csat =  $dashboardTicketService->findAllCsatRating($user,$dates);
          return $csat;
     }
     public function kpiTicketStatusChart(Request $request)
     {

     }
}
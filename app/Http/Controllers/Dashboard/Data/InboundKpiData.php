<?php
namespace App\Http\Controllers\Dashboard\Data;

use App\Helpers\YeastarApi;
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

          $totalResponseSlaTime = 0;//$utilityService->findAllSumResponseTimeSla($user->company_id, 'inbound');
          $totalResolutionSlaTime = 0;//$utilityService->findAllSumResolutionTimeSla($user->company_id, 'inbound');
          $responseTime = $dashboardTicketService->findAllFirstResponseTime($user, $dates, $totalResponseSlaTime, 'inbound')->sum('frt');
          $resolutionTime = $dashboardTicketService->findAllFirstResolutionTime($user, $dates,$totalResolutionSlaTime, 'inbound')->sum('frt');
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

          $totalResponseSlaTime = 0;//$utilityService->findAllSumResponseTimeSla($user->company_id, 'inbound');
          $totalResolutionSlaTime = 0;//$utilityService->findAllSumResolutionTimeSla($user->company_id, 'inbound');

          $responseTime = $dashboardTicketService->findAllFirstResponseTime($user, $dates, $totalResponseSlaTime, 'inbound');
          $resolutionTime = $dashboardTicketService->findAllFirstResolutionTime($user, $dates,$totalResolutionSlaTime, 'inbound');
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
               ],
               [
                    "label" => "Kontakami",
                    "color" => "#3942B7",
                    "source" => ['From Kontakami']
               ],
               [
                    "label" => "Walk-In",
                    "color" => "#C6BD48",
                    "source" => ['From Walk-In']
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
          
          $user = user();
          $companyId = $user->company_id;
          $yeastarApi = new YeastarApi;

          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
          $startDate = date('d/m/Y',strtotime($dates[0])). " 00:00:00";
          $endDate = date('d/m/Y',strtotime($dates[count($dates)-1]))." 23:59:59";
          
          // return [$startDate,$endDate];
          return $yeastarApi->getAbaddonMissedCall('9a8e167e-e49b-403e-8a00-421cd13ab24b',$startDate,$endDate);
     }

     public function kpiWebCall(Request $request, DashboardTicketService $dashboardTicketService)
     {
          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
          $missedCall = $dashboardTicketService->findAllMissedCall($user, $dates);
          return [$missedCall];
     }

     public function kpiCsat(Request $request, DashboardTicketService $dashboardTicketService)
     {
          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
          return $dashboardTicketService->findAllCsatRating($user, $dates);
     }
     public function kpiTicketStatusChart(Request $request, DashboardTicketService $dashboardTicketService)
     {
          $colors  = [
               "New" => "#FF605C",
               "Open" => "#FFBD44",
               "Solved" => "#26C0F1",
               "Closed" => "#00CA4E"
          ];
          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
          return $dashboardTicketService->findAllTicketByStatusCategory($user, $dates, 'inbound')->map(function($row) use($colors){
               $color = @$colors[$row->status_category] ?: '#FF605C';
               return [
                    ...$row->toArray(),
                    'color' => $color,
               ];
          });
     }
}
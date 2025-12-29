<?php
namespace App\Http\Controllers\Dashboard\Data;

use App\Helpers\YeastarApi;
use App\Helpers\Yellow;
use App\Models\Util\Call;
use App\Service\Ticket\DashboardTicketService;
use App\Service\Utility\UtilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

trait OutboundKpiData
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

     public function kpiSlaTimeSalescall(Request $request, UtilityService $utilityService, DashboardTicketService $dashboardTicketService)
     {
          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
          // $totalDay = count($dates);
          //  $dates = array(date('2025-11-20'));
          // $totalResponseSlaTime = 0;//$utilityService->findAllSumResponseTimeSla($user->company_id, 'inbound');
          // $totalResolutionSlaTime = 0;//$utilityService->findAllSumResolutionTimeSla($user->company_id, 'inbound');
          // $responseTime = $dashboardTicketService->findAllFirstResponseTime($user, $dates, $totalResponseSlaTime, 'inbound')->sum('frt');
          // $resolutionTime = $dashboardTicketService->findAllFirstResolutionTime($user, $dates,$totalResolutionSlaTime, 'inbound')->sum('frt');
          
          $startDate = $dates[0];
          $endDate   = $dates[count($dates) - 1];
          $type = 'outbound';
          $url = config('services.API_PBX_URL_V2');
          $response = Http::get("{$url}/recording/index", [
               'direction' => $type == 'inbound' ? 1 : 2,
               'start_time' => $startDate,
               'end_time' => $endDate,
               'anumber' => '',
          ])->json();
           $data = @$response ?: [];
          $sessionKeys = collect(@$data['data'] ?: [])->pluck('session_key');
          $agentExt = collect(@$data['data'] ?: [])->pluck('agent_ext');

          $totalOutgoingCalls = $dashboardTicketService->findOutgoingCallsSalescall($user, $dates,'outbound');
          $items = [];
          // dd($totalOutgoingCalls);
          $records = @$data['data'] ?: [];      // data dari API PBX
          $calls   = $totalOutgoingCalls;       // data dari query calls/tickets
          // untuk memastikan ext + anumber selalu menghasilkan ticket yang sama
          $ticketCache = [];   // key: "ext|number" => ticket number
          $companyCache = [];  // key: "ext|number" => company
          $agentCache   = [];  // key: "ext|number" => agent

          foreach ($records as &$row) {

               $ext    = $row['agent_ext'];
               $number = $row['anumber'];
               $key    = $ext . '|' . $number;

               // ===== 1. Jika sudah pernah ditentukan ticket untuk kombinasi ini =====
               if (isset($ticketCache[$key])) {

                    $row['ticketNumber'] = $ticketCache[$key];
                    $row['companyName']  = $companyCache[$key];
                    $row['userid']       = $agentCache[$key];
                    $row['ext']          = $ext;

                    $items[] = $row;
                    continue;
               }

               // ===== 2. Belum ada → tentukan ticket-nya =====
               $call = null;

               // A. Paling akurat → pakai session_key dulu
               if (!empty($row['session_key'])) {
                    $call = $calls->firstWhere('sip', $row['session_key']);
               }

               // B. Jika tidak ada → cari berdasarkan ext + number
               if (!$call) {
                    $call = $calls
                         ->where('extension_number', $ext)
                         ->where('destination', $number)
                         ->first();
               }

               // C. Jika masih tidak ada → cari waktu terdekat
               if (!$call) {
                    $start = strtotime($row['start_time']);

                    $possible = $calls
                         ->where('extension_number', $ext)
                         ->where('destination', $number)
                         ->values();

                    if ($possible->count() > 0) {
                         $call = $possible->sortBy(function($c) use ($start) {
                              return abs(strtotime($c->start_time ?? '0') - $start);
                         })->first();
                    }
               }

               // ===== DEFAULT =====
               $company      = '-';
               $ticketNumber = '-';
               $agentName    = 'Agent';

               // ===== 3. Jika call ketemu → pakai datanya =====
               if ($call) {
                    $ticketNumber = $call->ticket_number    ?? '-';
                    $company      = $call->company          ?? '-';
                    $agentName    = $call->agent_name       ?? 'Agent';
               }

               // ===== 4. Cache agar ext+number selalu pakai ticket yang sama =====
               $ticketCache[$key]  = $ticketNumber;
               $companyCache[$key] = $company;
               $agentCache[$key]   = $agentName;

               // ===== Mapping output =====
               $row['ticketNumber'] = $ticketNumber;
               $row['companyName']  = $company;
               $row['userid']       = $agentName;
               $row['ext']          = $ext;

               $items[] = $row;
          }
          // dd($items);



         $totalOutgoingCallsTotal = collect($items)
               ->filter(fn($i) => !empty($i['ticketNumber']) && $i['ticketNumber'] != '-')
               ->count();

          $totalOutgoingCallsDuration = collect($items)
          ->filter(fn($i) => !empty($i['ticketNumber']) && $i['ticketNumber'] != '-')
          ->sum('duration');

          $totalTicket = collect($items)
               ->filter(fn($i) => !empty($i['ticketNumber']) && $i['ticketNumber'] !== '-')
               ->unique('ticketNumber')     // hanya ambil ticketNumber unik
               ->count();
          
          //  dd($totalOutgoingCallsTotal, $totalOutgoingCallsDuration);
          // $totalFrequencyperlead = $dashboardTicketService->findFrequencyperleadSalescall($user, $dates,'outbound');
          

          // $data = $dashboardTicketService->findAllTicketOutboundSalesCall($user, $dates,'outbound');
          // $totalattemp = $dashboardTicketService->findAllTicketByStatusCategorysalescall($user, $dates, 'outbound');
          //  dd($totalOutgoingCallsDuration);
            // dd($totalOutgoingCallsTotal);
          // $call_attempt = $data?->call_attempt ?: 0;
          
          $totalTicket   = (float) ($totalTicket ?? 0);
          $totalCalls    = (float) ($totalOutgoingCallsTotal ?? 0);
          $totalTime = (float) ($totalOutgoingCallsDuration / $totalOutgoingCallsTotal ?? 0);
          $totalTime = gmdate("H:i:s", $totalTime);

        //   dd($totalFrequencyperlead->total);
          return [
               [
                    "name" => "Outgoing calls",
                    "total" => $totalCalls
               ],
               [
                    "name" => "Avg outgoing call time",
                    "total" => $totalTime
               ],
               [
                    "name" => "Frequency per lead",
                    "total" => round($totalCalls / $totalTicket, 2)
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

     public function kpiVoicePstn(Request $request, YeastarApi $yeastarApi)
     {

          $user = user();
          $companyId = $user->company_id;

          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
          $startDate = date('d/m/Y',strtotime($dates[0])). " 00:00:00";
          $endDate = date('d/m/Y',strtotime($dates[count($dates)-1]))." 23:59:59";

          return $yeastarApi->getAbaddonMissedCall($companyId,$startDate,$endDate);
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

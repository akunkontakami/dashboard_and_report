<?php
namespace App\Http\Controllers\Dashboard\Data;
use App\Helpers\YeastarApi;
use App\Helpers\Yellow;
use App\Service\Ticket\DashboardTicketServiceOutbond;
use App\Service\Utility\UtilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

trait OutboundLiveDailyData
{
     public function liveDailyCard2(Request $request, DashboardTicketServiceOutbond $dashboardTicketService)
     {
         $currentDate = now();
         $user = user();
         $today = $currentDate->clone()->format('Y-m-d');
         $yesterday = $currentDate->subDays(1)->format('Y-m-d');
        //  $today = $currentDate->clone()->format('Y-09-17');
        //  $yesterday = $currentDate->subDays(1)->format('Y-09-01');
        //   dd($yesterday);
        //  dd('halo');



          $new = $dashboardTicketService->findNewTicketByDate($user, $today, $yesterday, 'inbound');
          $unassignedEnquire = $dashboardTicketService->findUnassignedEnquiryTicketByDate($user, $today, $yesterday, 'inbound');
          $unassignedTicket = $dashboardTicketService->findUnassignedTicketByDate($user, $today, $yesterday, 'inbound');
          $open = $dashboardTicketService->findOpenTicketByDate($user, $today, $yesterday, 'inbound');
          $solved = $dashboardTicketService->findSolvedTicketByDate($user, $today, $yesterday, 'inbound');
          $escaalted = $dashboardTicketService->findEscalatedTicketByDate($user, $today, $yesterday, 'inbound');

          return [
               [
                    "label" => "Outgoing Calls",
                    ...$new
               ],
               [
                    "label" => "Avg outgoing call time",
                    ...$unassignedEnquire
               ],
               [
                    "label" => "Frequency per lead",
                    ...$unassignedTicket
               ],

          ];
     }

     public function liveDailyTicketSolved2(Request $request, DashboardTicketServiceOutbond $dashboardTicketService)
     {

          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
          //  $currentDate = now()->format('2025-11-20');
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
          $CallOutgoingGroupName = $dashboardTicketService->findTopSolvedClosedTicketAgentSalescall($user, $dates, "outbound", 10);
          // dd($CallOutgoingGroupName);
           $items = [];
          // dd($totalOutgoingCalls);
          $records = @$data['data'] ?: [];      // data dari API PBX
          $calls   = $CallOutgoingGroupName;       // data dari query calls/tickets
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
                    $row['name']         = $nameCache[$key];   // <-- tambahkan name
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
               $name         = '-';   // <-- tambahkan default name

               // ===== 3. Jika call ketemu → pakai datanya =====
               if ($call) {
                    $ticketNumber = $call->ticket_number    ?? '-';
                    $company      = $call->company          ?? '-';
                    $agentName    = $call->agent_name       ?? 'Agent';
                    $name         = $call->name              ?? '-';   // <-- ambil name dari $call
               }

               // ===== 4. Cache agar ext+number selalu pakai ticket yang sama =====
               $ticketCache[$key]  = $ticketNumber;
               $companyCache[$key] = $company;
               $agentCache[$key]   = $agentName;
               $nameCache[$key]    = $name;   // <-- cache name

               // ===== Mapping output =====
               $row['ticketNumber'] = $ticketNumber;
               $row['companyName']  = $company;
               $row['userid']       = $agentName;
               $row['name']         = $name;   // <-- tambahkan name ke row
               $row['ext']          = $ext;

               $items[] = $row;
          }
         // $items = array hasil akhir yang kamu punya (array index 0 sampai 5)
               $collection = collect($items);

               // Group data berdasarkan name + agent_ext (bukan anumber)
               $grouped = $collection
               ->groupBy(function ($row) {
                    return $row['name'].'|'.$row['agent_ext'];
               })
               ->map(function ($rows) {
                    return [
                         'total'        => $rows->count(),
                         'name'         => $rows->first()['name'],
                         'agent_ext'    => $rows->first()['agent_ext'],
                         'anumber'      => $rows->first()['anumber'],
                         'ticketNumber' => $rows->first()['ticketNumber'] ?? '-',
                         'companyName'  => $rows->first()['companyName'] ?? '-',
                         'profile'      => $rows->first()['profile'] ?? '-',
                    ];
               })
               // 🔥 FILTER → hilangkan jika name "-" DAN ticketNumber "-"
               ->filter(function ($row) {
                    return !($row['name'] === '-' && $row['ticketNumber'] === '-');
               })
               ->sortByDesc('total')
               ->values(); // reset index

               return $grouped;

     }

     public function liveDailyTicketSolvedClosed(Request $request, DashboardTicketServiceOutbond $dashboardTicketService)
     {

          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
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
          // $currentDate = now()->format('2025-11-20');
          $CallOutgoingGroupName= $dashboardTicketService->findTopSolvedClosedTicketAgentClosedSalescall($user, $dates, "outbound", 10);
          // dd($CallOutgoingGroupName);
           $items = [];
          // dd($totalOutgoingCalls);
          $records = @$data['data'] ?: [];      // data dari API PBX
          $calls   = $CallOutgoingGroupName;       // data dari query calls/tickets
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
                    $row['name']         = $nameCache[$key];   // <-- tambahkan name
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
               $name         = '-';   // <-- tambahkan default name

               // ===== 3. Jika call ketemu → pakai datanya =====
               if ($call) {
                    $ticketNumber = $call->ticket_number    ?? '-';
                    $company      = $call->company          ?? '-';
                    $agentName    = $call->agent_name       ?? 'Agent';
                    $name         = $call->name              ?? '-';   // <-- ambil name dari $call
               }

               // ===== 4. Cache agar ext+number selalu pakai ticket yang sama =====
               $ticketCache[$key]  = $ticketNumber;
               $companyCache[$key] = $company;
               $agentCache[$key]   = $agentName;
               $nameCache[$key]    = $name;   // <-- cache name

               // ===== Mapping output =====
               $row['ticketNumber'] = $ticketNumber;
               $row['companyName']  = $company;
               $row['userid']       = $agentName;
               $row['name']         = $name;   // <-- tambahkan name ke row
               $row['ext']          = $ext;

               $items[] = $row;
          }
         // $items = array hasil akhir yang kamu punya (array index 0 sampai 5)
               $collection = collect($items);

               // Group data berdasarkan name + agent_ext (bukan anumber)
               $grouped = $collection
               ->groupBy(function ($row) {
                    return $row['name'].'|'.$row['agent_ext'];
               })
               ->map(function ($rows) {
                    return [
                         'total'        => $rows->count(),
                         'name'         => $rows->first()['name'],
                         'agent_ext'    => $rows->first()['agent_ext'],
                         'anumber'      => $rows->first()['anumber'],
                         'ticketNumber' => $rows->first()['ticketNumber'] ?? '-',
                         'companyName'  => $rows->first()['companyName'] ?? '-',
                         'profile'      => $rows->first()['profile'] ?? '-',
                    ];
               })
               // 🔥 FILTER → hilangkan jika name "-" DAN ticketNumber "-"
               ->filter(function ($row) {
                    return !($row['name'] === '-' && $row['ticketNumber'] === '-');
               })
               ->sortByDesc('total')
               ->values(); // reset index

               return $grouped;
         
          // return $dashboardTicketService->findTopSolvedClosedTicketAgentClosedSalescall($user, $dates, "outbound", 10);
     }

     public function liveDailyTicketSolvedAverage(Request $request, DashboardTicketServiceOutbond $dashboardTicketService)
     {

          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
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
          // $currentDate = now()->format('2025-11-20');
          $CallOutgoingGroupAverage = $dashboardTicketService->findTopSolvedClosedTicketAgentAverageSalescall($user, $dates, "outbound", 10);
          // dd($CallOutgoingGroupAverage);
           $items = [];
          // dd($totalOutgoingCalls);
          $records = @$data['data'] ?: [];      // data dari API PBX
          $calls   = $CallOutgoingGroupAverage;       // data dari query calls/tickets
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
                    $row['name']         = $nameCache[$key];   // <-- tambahkan name
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
               $name         = '-';   // <-- tambahkan default name

               // ===== 3. Jika call ketemu → pakai datanya =====
               if ($call) {
                    $ticketNumber = $call->ticket_number    ?? '-';
                    $company      = $call->company          ?? '-';
                    $agentName    = $call->agent_name       ?? 'Agent';
                    $name         = $call->name              ?? '-';   // <-- ambil name dari $call
               }

               // ===== 4. Cache agar ext+number selalu pakai ticket yang sama =====
               $ticketCache[$key]  = $ticketNumber;
               $companyCache[$key] = $company;
               $agentCache[$key]   = $agentName;
               $nameCache[$key]    = $name;   // <-- cache name

               // ===== Mapping output =====
               $row['ticketNumber'] = $ticketNumber;
               $row['companyName']  = $company;
               $row['userid']       = $agentName;
               $row['name']         = $name;   // <-- tambahkan name ke row
               $row['ext']          = $ext;

               $items[] = $row;
          }
         // $items = array hasil akhir yang kamu punya (array index 0 sampai 5)
               $collection = collect($items);

               // Group data berdasarkan name + agent_ext (bukan anumber)
               $grouped = $collection
               ->groupBy(function ($row) {
                    return $row['name'].'|'.$row['agent_ext'];
               })
               ->map(function ($rows) {
                    return [
                         'total'        => $rows->count(),
                         'name'         => $rows->first()['name'],
                         'agent_ext'    => $rows->first()['agent_ext'],
                         'anumber'      => $rows->first()['anumber'],
                         'ticketNumber' => $rows->first()['ticketNumber'] ?? '-',
                         'companyName'  => $rows->first()['companyName'] ?? '-',
                         'profile'      => $rows->first()['profile'] ?? '-',
                    ];
               })
               // 🔥 FILTER → hilangkan jika name "-" DAN ticketNumber "-"
               ->filter(function ($row) {
                    return !($row['name'] === '-' && $row['ticketNumber'] === '-');
               })
               ->sortByDesc('total')
               ->values(); // reset index

               return $grouped;
     }

     public function liveDailyFirstResponseTime(Request $request, UtilityService $utilityService, DashboardTicketServiceOutbond $dashboardTicketService)
     {
          $user = user();
          $todayDate = now();
          $dates = Yellow::createRangeInterval($todayDate, -6); // to get only 7 day last
          $totalResponseSlaTime = 0;//$utilityService->findAllSumResponseTimeSla($user->company_id, 'inbound');
          $tickets = $dashboardTicketService->findAllFirstResponseTime($user, $dates, $totalResponseSlaTime, 'inbound');
          return $tickets;
     }

     public function liveDailyFirstResolutionTime(Request $request, UtilityService $utilityService, DashboardTicketServiceOutbond $dashboardTicketService)
     {
          $user = user();
          $todayDate = now();
          $dates = Yellow::createRangeInterval($todayDate, -6); // to get only 7 day last
          $totalResolutionSlaTime = 0;//$utilityService->findAllSumResolutionTimeSla($user->company_id, 'inbound');
          $tickets = $dashboardTicketService->findAllFirstResolutionTime($user, $dates, $totalResolutionSlaTime, 'inbound');
          return $tickets;
     }
}

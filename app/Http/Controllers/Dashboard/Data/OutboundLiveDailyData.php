<?php
namespace App\Http\Controllers\Dashboard\Data;
use App\Helpers\YeastarApi;
use App\Helpers\Yellow;
use App\Service\Ticket\DashboardTicketServiceOutbond;
use App\Service\Utility\UtilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
        //   $currentDate = now()->format('2024-03-26');
          return $dashboardTicketService->findTopSolvedClosedTicketAgentSalescall($user, $dates, "outbound", 10);
     }

     public function liveDailyTicketSolvedClosed(Request $request, DashboardTicketServiceOutbond $dashboardTicketService)
     {

          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
        //   $currentDate = now()->format('2024-03-26');
          return $dashboardTicketService->findTopSolvedClosedTicketAgentClosedSalescall($user, $dates, "outbound", 10);
     }

     public function liveDailyTicketSolvedAverage(Request $request, DashboardTicketServiceOutbond $dashboardTicketService)
     {

          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
        //   $currentDate = now()->format('2024-03-26');
          return $dashboardTicketService->findTopSolvedClosedTicketAgentAverageSalescall($user, $dates, "outbound", 10);
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

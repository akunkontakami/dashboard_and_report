<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\Yellow;
use App\Http\Controllers\Controller;
use App\Service\Ticket\DashboardTicketService;
use App\Service\Utility\UtilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class InboundDashboardController extends Controller
{
    public function index(Request $request, DashboardTicketService $dashboardTicketService, $type)
    {
        return Inertia::render("Dashboard/Inbound/Index", [
            "type" => $type
        ]);
    }

    public function liveDailyCard(Request $request, DashboardTicketService $dashboardTicketService)
    {
        $currentDate = now();
        $user = user();
        $today = $currentDate->clone()->format('Y-m-d');
        $yesterday = $currentDate->subDays(1)->format('Y-m-d');


        $new = $dashboardTicketService->findNewTicketByDate($user, $today, $yesterday, 'inbound');
        $unassignedEnquire = $dashboardTicketService->findUnassignedEnquiryTicketByDate($user, $today, $yesterday, 'inbound');
        $unassignedTicket = $dashboardTicketService->findUnassignedTicketByDate($user, $today, $yesterday, 'inbound');
        $open = $dashboardTicketService->findOpenTicketByDate($user, $today, $yesterday, 'inbound');
        $solved = $dashboardTicketService->findSolvedTicketByDate($user, $today, $yesterday, 'inbound');
        $escaalted = $dashboardTicketService->findEscalatedTicketByDate($user, $today, $yesterday, 'inbound');

        return [
            [
                "label" => "New",
                ...$new
            ],
            [
                "label" => "Unassigned Enquiry",
                ...$unassignedEnquire
            ],
            [
                "label" => "Unassigned Ticket",
                ...$unassignedTicket
            ],
            [
                "label" => "Open",
                ...$open
            ],
            [
                "label" => "Solved",
                ...$solved
            ],
            [
                "label" => "Escalated",
                ...$escaalted
            ]
        ];
    }

    public function liveDailyTicketSolved(Request $request, DashboardTicketService $dashboardTicketService)
    {
        $currentDate = now();
        $user = user();
        return $dashboardTicketService->findTopTenSolvedClosedTicketAgent($user, $currentDate->format('Y-m-d'), "inbound");
    }

    public function liveDailyFirstResponseTime(Request $request, UtilityService $utilityService, DashboardTicketService $dashboardTicketService)
    {
        $user = user();
        $todayDate = now();
        $dates = Yellow::createRangeInterval($todayDate, -6); // to get only 7 day last
        $totalResponseSlaTime = $utilityService->findAllSumResponseTimeSla($user->company_id, 'inbound');
        $tickets = $dashboardTicketService->findAllFirstResponseTime($user, $dates, $totalResponseSlaTime, 'inbound');
        return $tickets;
    }

    public function liveDailyFirstResolutionTime(Request $request, DashboardTicketService $dashboardTicketService)
    {
        $user = user();
        $todayDate = now();
        $dates = Yellow::createRangeInterval($todayDate, -6); // to get only 7 day last
        $tickets = $dashboardTicketService->findAllFirstResolutionTime($user, $dates, 'inbound');
        return $tickets;
    }
}

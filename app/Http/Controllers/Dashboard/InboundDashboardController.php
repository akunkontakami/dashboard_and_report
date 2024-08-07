<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Service\Ticket\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class InboundDashboardController extends Controller
{
    public function index(Request $request, TicketService $ticketService, $type)
    {
        return Inertia::render("Dashboard/Inbound/Index", [
            "type" => $type
        ]);
    }

    public function liveDailyCard(Request $request, TicketService $ticketService)
    {
        $currentDate = Carbon::parse("2024-08-01"); // Todo : change to now
        $user = user();
        $companyId = $user->company_id;
        $today = $currentDate->clone()->format('Y-m-d');
        $yesterday = $currentDate->subDays(1)->format('Y-m-d');


        $new = $ticketService->findNewTicketByDate($user, $today, $yesterday, 'inbound');
        $unassignedEnquire = $ticketService->findUnassignedEnquiryTicketByDate($user,$today,$yesterday,'inbound');
        $unassignedTicket = $ticketService->findUnassignedTicketByDate($user,$today,$yesterday,'inbound');
        $open = $ticketService->findOpenTicketByDate($user, $today, $yesterday, 'inbound');
        $solved = $ticketService->findSolvedTicketByDate($user, $today, $yesterday, 'inbound');
        $escaalted = $ticketService->findEscalatedTicketByDate($user, $today, $yesterday, 'inbound');

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

    public function liveDailyTicketSolved(Request $request)
    {
        return [];
    }

    public function liveDailyFirstResponseTime(Request $request)
    {
        return [];
    }

    public function liveDailyFirstResolutionTime(Request $request)
    {
        return [];
    }
}

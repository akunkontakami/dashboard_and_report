<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Service\Ticket\TicketService;
use Illuminate\Http\Request;
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
        $user = user();
        $companyId = $user->company_id;
        $today = date('Y-m-d');
        $yesterday = now()->subDays(1)->format('Y-m-d');
        
        
        $new = $ticketService->findNewTicketByDate($user, $today, $yesterday);

        return [
            [
                "label" => "New",
                ...$new
            ],
            [
                "label" => "Unassigned Enquiry",
                "today" => 30,
                "yesterday" => -5
            ],
            [
                "label" => "Unassigned Ticket",
                "today" => 30,
                "yesterday" => -5
            ],
            [
                "label" => "Open",
                "today" => 30,
                "yesterday" => -5
            ],
            [
                "label" => "Solved",
                "today" => 30,
                "yesterday" => -5
            ],
            [
                "label" => "Escalated",
                "today" => 30,
                "yesterday" => -5
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

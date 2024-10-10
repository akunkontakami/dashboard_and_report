<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dashboard\Data\InboundTeamPerformance;
use App\Http\Controllers\Dashboard\Data\InboundKpiData;
use App\Http\Controllers\Dashboard\Data\InboundLiveDailyData;
use App\Service\Ticket\DashboardTicketService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InboundDashboardController extends Controller
{
    use InboundLiveDailyData,InboundKpiData,InboundTeamPerformance;
    public function index(Request $request, DashboardTicketService $dashboardTicketService, $type)
    {
        return Inertia::render("Dashboard/Inbound/Index", [
            "type" => $type
        ]);
    }

}

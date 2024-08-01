<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OutboundDashboardController extends Controller
{
    public function index(Request $request, $type)
    {
        return Inertia::render("Dashboard/Outbound/Index", [
            "type" => $type
        ]);
    }
}

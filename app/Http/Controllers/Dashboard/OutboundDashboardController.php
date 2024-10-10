<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dashboard\Data\OutboundCampaignData;
use App\Service\Utility\MarketingCampaignService;
use App\Service\Utility\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Dashboard\Data\OutboundLiveDailyData;
use App\Http\Controllers\Dashboard\Data\OutboundKpiData;

class OutboundDashboardController extends Controller
{
    use OutboundCampaignData,OutboundLiveDailyData,OutboundKpiData;
    public function index(Request $request,ProductService $productService,MarketingCampaignService $marketingCampaignService, $type)
    {
        $user = user();
        $campaigns = $type=='marketing-campaign' ? $marketingCampaignService->findAllCampaignUser($user) : []; // marketing-campaign
        $products = $type=='product' ? $productService->findAllProductUser($user,'outbound') : [];
        $salescalls = $type=='salescalls' ? $marketingCampaignService->findAllCampaignUser($user) : []; // marketing-campaign

        return Inertia::render("Dashboard/Outbound/Index", [
            "type" => $type,
            'salescalls' => $salescalls,
            'campaigns' => $campaigns,
            'products' => $products
        ]);
    }


}

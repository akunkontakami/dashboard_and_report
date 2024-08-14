<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dashboard\Data\OutboundCampaignData;
use App\Http\Controllers\Dashboard\Data\OutboundProductData;
use App\Service\Utility\MarketingCampaignService;
use App\Service\Utility\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OutboundDashboardController extends Controller
{
    use OutboundCampaignData, OutboundProductData;
    public function index(Request $request,ProductService $productService,MarketingCampaignService $marketingCampaignService, $type)
    {
        $user = user();
        $campaigns = $type=='marketing-campaign' ? $marketingCampaignService->findAllCampaignUser($user) : []; // marketing-campaign
        $products = $type=='product' ? $productService->findAllProductUser($user,'outbound') : [];

        return Inertia::render("Dashboard/Outbound/Index", [
            "type" => $type,
            'campaigns' => $campaigns,
            'products' => $products
        ]);
    }
}

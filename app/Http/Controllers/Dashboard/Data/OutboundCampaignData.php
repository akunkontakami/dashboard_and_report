<?php
namespace App\Http\Controllers\Dashboard\Data;

use App\Helpers\Yellow;
use App\Service\Ticket\DashboardTicketService;
use Illuminate\Http\Request;

trait OutboundCampaignData
{
     public function chartCampaign(Request $request, DashboardTicketService $dashboardTicketService)
     {
          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));

          $data = $dashboardTicketService->findAllTicketOutboundMarketingCampaign($user, $dates, 'outbound', [
               'campaign_id' => $request->campaign_id
          ]);

          $data_size = $data?->data_size ?: 0;
          $call_attempt = $data?->call_attempt ?: 0;
          $close_deal = $data?->close_deal ?: 0;
          $higher = [
               "label" => "Data Size",
               "total" => $data_size
          ];
          if ($call_attempt > $data_size) {
               $higher = [
                    "label" => "Call Attempt",
                    "total" => $call_attempt
               ];
          }
          if ($close_deal > $call_attempt) {
               $higher = [
                    "label" => "Close Deals",
                    "total" => $close_deal
               ];
          }
          return [
               'items' => [
                    [
                         'label' => "Data Size",
                         "value" => $data_size,
                         'color' => '#26C0F1'
                    ],
                    [
                         'label' => "Call Attempt",
                         'value' => $call_attempt,
                         'color' => '#FFBD44'
                    ],
                    [
                         'label' => "Close Deals",
                         "value" => $close_deal,
                         'color' => '#00CA4E'
                    ]
               ],
               'higher' => $higher
          ];
     }

     public function topClosedCampaignAgent(Request $request, DashboardTicketService $dashboardTicketService)
     {
          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
          return $dashboardTicketService->findTopSolvedClosedTicketAgent($user, $dates, "outbound", 5, [
               'campaign_id' => $request->campaign_id
          ]);
     }

     public function ticketByTypeCampaign(Request $request,DashboardTicketService $dashboardTicketService)
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
          return $dashboardTicketService->findAllTicketByStatusCategory($user, $dates, 'inbound',[
               'campaign_id' => $request->campaign_id
          ])->map(function($row) use($colors){
               $color = @$colors[$row->status_category] ?: '#FF605C';
               return [
                    ...$row->toArray(),
                    'color' => $color,
               ];
          });
     }
}
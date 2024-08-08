<?php
namespace App\Http\Controllers\Dashboard\Data;

use App\Helpers\Yellow;
use Illuminate\Http\Request;

trait InboundKpiData
{
     public function kpiTicketStatus(Request $request)
     {
          $user = user();
          $currentDate = now();
          $dates = Yellow::getDateRangeByPeriod($currentDate, $request->get('periode', 'today'));
          return $dates;
     }

     public function kpiSlaTime(Request $request)
     {

     }

     public function kpiTicketActivity(Request $request)
     {

     }

     public function kpiSlaChart(Request $request)
     {

     }

     public function kpiTicketChannel(Request $request)
     {

     }

     public function kpiVoicePstn(Request $request)
     {

     }

     public function kpiWebCall(Request $request)
     {

     }

     public function kpiCsat(Request $request)
     {

     }
     public function kpiTicketStatusChart(Request $request)
     {

     }
}
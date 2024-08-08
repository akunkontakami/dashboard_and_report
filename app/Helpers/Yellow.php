<?php
namespace App\Helpers;

use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\Storage;

class Yellow
{
     public static function createRangeInterval($start, $total)
     {
          $end = $start->clone()->addDays($total);
          $startDate = $start > $end ? $end : $start;
          $endDate = $end < $start ? $start : $end;

          $dates = [];
          $interval = new DateInterval('P1D');
          $realEnd = new DateTime($endDate);
          $realEnd->add($interval);
          $period = new DatePeriod(new DateTime($startDate), $interval, $realEnd);
          foreach ($period as $date) {
               $dates[] = $date->format('Y-m-d');
          }
          return $dates;
     }

     public static function minuteToSla($minutes)
     {
          try {
               $hours = floor($minutes / 60);
               $minutes = $minutes - ($hours * 60);
               $hours = str_pad($hours, 2, "0",STR_PAD_LEFT);
               $minutes = str_pad($minutes, 2, "0",STR_PAD_LEFT);
               return "{$hours}h:{$minutes}min";
          } catch (\Exception $e) {
               return "00h:00min";
          }
     }

     public static function getDateRangeByPeriod($startDate, $periode)
     {
          // Periode List : today, last-7-days , last-30-days,
          $periodeInterval = [
               'today' => 0,
               'last-7-days' => -6,
               'last-30-days' => -29
          ];

          $periode = @$periodeInterval[$periode] ?: 'today';
          return self::createRangeInterval($startDate, $periode);
     }
}
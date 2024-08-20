<?php
namespace App\Helpers;

use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

     public static function getDurationSlaTimer($objectTimer, $endOrNow)
     {
          $solvedDuration = @$objectTimer['solved_duration'] ?: null;
          if ($solvedDuration) {
               $type = '';
               if (Str::contains($solvedDuration, '-')) {
                    $type = '-';
                    $solvedDuration = str_replace('-', '', $solvedDuration);
               }
               list($hours, $minutes) = explode(':', $solvedDuration);
               $hours = str_pad($hours,2,"0",STR_PAD_LEFT);
               $minutes = str_pad($minutes,2,"0",STR_PAD_LEFT);
               return $type . "{$hours}h{$minutes}min";
          }

          $now = Carbon::parse($endOrNow->copy());
          $startAt = Carbon::parse(@$objectTimer['start_at']);
          $endAt = Carbon::parse(@$objectTimer['end_at']);
          // $solvedAt = Carbon::parse(@$objectTimer['solved_at']);
          $startDuration = @$objectTimer['duration'] ?: null;

          if ($startAt && $startDuration && $now < $startAt) {
               list($hours, $minutes) = explode(':', $startDuration);

               $hours = str_pad($hours,2,"0",STR_PAD_LEFT);
               $minutes = str_pad($minutes,2,"0",STR_PAD_LEFT);
               return "{$hours}h{$minutes}min";
          }

          if ($now < $endAt) {
               // Countdown
               $diff = $now->diffInMinutes($endAt);
               $hours = intval($diff / 60);
               $minutes = intval(fmod($diff, 60));
               $hours = sprintf('%02d', $hours);
               $minutes = sprintf('%02d', $minutes);


               $hours = str_pad($hours,2,"0",STR_PAD_LEFT);
               $minutes = str_pad($minutes,2,"0",STR_PAD_LEFT);
               return "{$hours}h{$minutes}min";
          } else {
               // Countup
               $diff = $endAt->diffInMinutes($now);
               $hours = intval($diff / 60);
               $minutes = intval(fmod($diff, 60));
               $hours = sprintf('%02d', $hours);
               $minutes = sprintf('%02d', $minutes);
               $type = $diff == 0 ? '' : '-';


               $hours = str_pad($hours,2,"0",STR_PAD_LEFT);
               $minutes = str_pad($minutes,2,"0",STR_PAD_LEFT);
               return $type . "{$hours}h{$minutes}min";
          }
     }

     public static function secondsToHoursMinutes($value)
     {
          if ($value) {
               if ($value <= 0)
                    $value = 0;
               $sec = intval($value, 10);
               $hours = floor($sec / 3600);
               $minutes = floor(($sec - $hours * 3600) / 60);
               $seconds = $sec - $hours * 3600 - $minutes * 60;
               if ($hours < 10) {
                    $hours = '0' . $hours;
               }
               if ($minutes < 10) {
                    $minutes = '0' . $minutes;
               }
               if ($seconds < 10) {
                    $seconds = '0' . $seconds;
               }
               return $hours . ':' . $minutes . ':' . $seconds; // Return is HH : MM : SS
          }
          return '00:00:00';
     }
}
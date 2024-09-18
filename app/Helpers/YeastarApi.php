<?php
namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class YeastarApi
{
     public function getToken()
     {
          return Cache::remember("yeastar-api-token", 1500, function () {
               $url = config('services.yeastar.url');
               $result = Http::asJson()
                    ->withOptions(["verify" => false])
                    ->post("{$url}/get_token", [
                         'username' => config('services.yeastar.username'),
                         'password' => config('services.yeastar.password'),
                    ]);
               return @$result['access_token'];
          });
     }


     public function getQueueList($token)
     {
          $url = config('services.yeastar.url');
          $result = Http::asJson()
               ->withOptions(["verify" => false])
               ->get("{$url}/queue/list", [
                    'access_token' => $token,
               ]);
          return collect(@$result['queue_list'] ?: []);
     }
     public function getAbaddonMissedCall($companyId, $startDate, $endDate)
     {
          $token = $this->getToken();
          $queueId = Cache::remember("yeastar-queue-list-{$companyId}", 600, function () use ($token, $companyId) {
               return $this->getQueueList($token)
                    ->filter(function ($row) use ($companyId) {
                         return str_contains(@$row['name'], $companyId);
                    })
                    ->pluck('id')
                    ->toArray();
          });

          $queueId = implode(",", $queueId ?: []);

          $keySlug = Str::slug($startDate . $endDate);
          return Cache::remember("yeaster-abandoned-missed-data-{$keySlug}-{$companyId}", 60, function () use ($token, $startDate, $endDate, $queueId) {
               $url = config('services.yeastar.url');
               $result = Http::asJson()
                    ->withOptions(["verify" => false])
                    ->get("{$url}/call_report/list", [
                         'access_token' => $token,
                         'type' => 'queueperformance',
                         'queue_id_list' => $queueId,
                         'start_time' => $startDate,
                         'end_time' => $endDate,
                    ]);
               if ($queueList = @$result['queue_performance_list']) {
                    $queueList = collect($queueList ?: []);
                    $missed = $queueList->sum('missed_rate');
                    $abandoned = $queueList->sum('abandoned_rate');
                    return [
                         'abandoned' => $abandoned,
                         'missed_call' => $missed
                    ];
               }
               return [
                    'abandoned' => 0,
                    'missed_call' => 0
               ];
          });

     }
}
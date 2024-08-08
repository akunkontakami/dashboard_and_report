<?php

use App\Http\Controllers\Auth\AuthLoginController;
use App\Http\Controllers\Dashboard\InboundDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthLoginController::class)
     ->prefix('login')
     ->as('auth.login.')
     ->group(function () {
          Route::post('/store', 'store')->name('store');
     });

Route::middleware(['me-auth', 'idle-logout'])
     ->group(function () {
          Route::get('/logout', [AuthLoginController::class, 'logout'])->name('auth.logout');


          Route::controller(InboundDashboardController::class)
               ->as('dashboard.inbound.data.')
               ->prefix("dashboard/inbound")
               ->group(function () {
                    Route::get('live-daily/card', 'liveDailyCard')->name('live-daily.card');
                    Route::get('live-daily/ticket-solved', 'liveDailyTicketSolved')->name('live-daily.ticket-solved');
                    Route::get('live-daily/first-response-time', 'liveDailyFirstResponseTime')->name('live-daily.first-response-time');
                    Route::get('live-daily/first-resolution-time', 'liveDailyFirstResolutionTime')->name('live-daily.first-resolution-time');

                    
                    Route::get('kpi/ticket-status', 'kpiTicketStatus')->name('kpi.ticket-status');
                    Route::get('kpi/sla-time', 'kpiSlaTime')->name('kpi.sla-time');
                    Route::get('kpi/ticket-activity', 'kpiTicketActivity')->name('kpi.ticket-activity');
                    Route::get('kpi/sla-chart', 'kpiSlaChart')->name('kpi.sla-chart');
                    Route::get('kpi/ticket-channel', 'kpiTicketChannel')->name('kpi.ticket-channel');
                    Route::get('kpi/voice-pstn', 'kpiVoicePstn')->name('kpi.voice-pstn');
                    Route::get('kpi/web-call', 'kpiWebCall')->name('kpi.web-call');
                    Route::get('kpi/csat', 'kpiCsat')->name('kpi.csat');
                    Route::get('kpi/ticket-status/chart', 'kpiTicketStatusChart')->name('kpi.ticket-status/chart');
               });
     });
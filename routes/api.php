<?php

use App\Http\Controllers\Auth\AuthLoginController;
use App\Http\Controllers\Dashboard\InboundDashboardController;
use App\Http\Controllers\Dashboard\OutboundDashboardController;
use App\Http\Controllers\Report\InboundReportController;
use App\Http\Controllers\Report\OutboundReportController;
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
                    Route::get('kpi/ticket-status/chart', 'kpiTicketStatusChart')->name('kpi.ticket-status.chart');

                    Route::get('team-performance/ticket-status', 'kpiTicketStatus2')->name('team-performance.ticket-status2');
                    Route::get('team-performance/ticket-status/chart', 'kpiTicketStatusChart2')->name('team-performance.ticket-status.chart2');
                    Route::get('team-performance/ticket-channel', 'kpiTicketChannelTeamPerformance')->name('team-performance.ticket-channel');
                    Route::get('team-performance/web-call', 'kpiWebCall2')->name('team-performance.web-call');
                    Route::get('team-performance/csat', 'kpiCsat2')->name('team-performance.csat');

                    Route::get('team-performance/top-closed', 'topClosedCampaignAgentTeamPerformance')->name('team-performance.top-closed');

                    Route::get('escalation/ticket-status', 'kpiTicketStatusEscalation')->name('escalation.ticket-status');
                    Route::get('escalation/ticket-channel', 'kpiTicketChannelEscalation')->name('escalation.ticket-channel');
                    Route::get('escalation/web-call', 'kpiWebCallEscalation')->name('escalation.web-call');
                    Route::get('escalation/top-closed', 'topClosedCampaignAgentEscalation')->name('escalation.top-closed');
               });

          Route::controller(OutboundDashboardController::class)
               ->as('dashboard.outbound.data.')
               ->prefix("dashboard/outbound")
               ->group(function () {
                    Route::get('live-daily/ticket-solved', 'liveDailyTicketSolved2')->name('live-daily.ticket-solved2');
                    Route::get('salescall/ticket-Closed', 'liveDailyTicketSolvedClosed')->name('salescall.agentclosed');
                    Route::get('salescall/ticket-Average', 'liveDailyTicketSolvedAverage')->name('salescall.agentaverage');
                    Route::get('live-daily/card', 'liveDailyCard2')->name('live-daily.card2');
                    Route::get('campaign/chart-campaign', 'chartCampaign')->name('campaign.chart-campaign');
                    Route::get('campaign/top-closed', 'topClosedCampaignAgent')->name('team-performance.top-closed');
                    Route::get('campaign/ticket-by-type', 'ticketByTypeCampaign')->name('campaign.ticket-by-type');
                    Route::get('salescall/sla-time', 'kpiSlaTimeSalescall')->name('salescall.sla-time');

               });


          Route::controller(InboundReportController::class)
               ->as('report.inbound.')
               ->prefix('/report/inbound/{category}')
               ->group(function () {
                    Route::get('data-table', 'datatable')->name('data-table');
                    Route::post('export', 'export')->name('export');
               });

          Route::controller(OutboundReportController::class)
               ->as('report.outbound.')
               ->prefix('/report/outbound/{category}')
               ->group(function () {
                    Route::get('data-table', 'datatable')->name('data-table');
                    Route::post('export', 'export')->name('export');
               });

          Route::controller(InboundReportController::class)
               ->as('report.inbound.')
               ->prefix('/report/inbound/ticket')
               ->group(function () {
                    Route::post('export-form', 'exportFormTicket')->name('ticket.export-form');
                    Route::post('export-chat', 'exportChatTicket')->name('ticket.export-chat');
                    Route::get('download-form/{queue_id}', 'downloadFormTicketPdf')->name('ticket.export-form.download');
               });
          Route::controller(OutboundReportController::class)
               ->as('report.outbound.')
               ->prefix('/report/outbound/ticket')
               ->group(function () {
                    Route::post('export-form', 'exportFormTicket')->name('ticket.export-form');
                    Route::post('export-chat', 'exportChatTicket')->name('ticket.export-chat');
               });
     });

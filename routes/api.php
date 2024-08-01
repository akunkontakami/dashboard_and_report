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
               ->as('dashboard.inbound.data')
               ->prefix("dashboard/inbound")
               ->group(function () {
                    Route::get('live-daily/card', 'liveDailyCard')->name('live-daily.card');
                    Route::get('live-daily/ticket-solved', 'liveDailyTicketSolved')->name('live-daily.ticket-solved');
                    Route::get('live-daily/first-response-time', 'liveDailyFirstResponseTime')->name('live-daily.first-response-time');
                    Route::get('live-daily/first-resolution-time', 'liveDailyFirstResolutionTime')->name('live-daily.first-resolution-time');
               });
     });
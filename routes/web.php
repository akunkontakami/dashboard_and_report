<?php

use App\Http\Controllers\Auth\AuthLoginController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\InboundDashboardController;
use App\Http\Controllers\Dashboard\OutboundDashboardController;
use App\Http\Controllers\Report\InboundReportController;
use App\Http\Controllers\Report\OutboundReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect("/","dashboard/inbound/live-daily");
/**
 * Authentication Route
 */
Route::controller(AuthLoginController::class)
     ->prefix('login')
     ->as('auth.login.')
     ->middleware(['idle-logout:false'])
     ->group(function () {
          Route::get('/', 'index')->name('index');
     });

Route::middleware(['me-auth', 'idle-logout'])
     ->group(function () {
          Route::get('/logout', [AuthLoginController::class, 'logout'])->name('auth.logout');

          Route::get("/dashboard/inbound/{type}", [InboundDashboardController::class, "index"])->name("dashboard.inbound.index")->whereIn('type', ['live-daily', 'kpi','team-performance','sla','escalation-performance']);
          Route::get("/dashboard/outbound/{type}", [OutboundDashboardController::class, "index"])->name("dashboard.outbound.index")->whereIn('type', ['marketing-campaign', 'product', 'salescalls','dashboard-productivity']);

          Route::get('/report/inbound/{category}', [InboundReportController::class, 'index'])->name('report.inbound.index');
          Route::get('/report/outbound/{category}', [OutboundReportController::class, 'index'])->name('report.outbound.index');
     });

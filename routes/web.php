<?php

use App\Http\Controllers\Auth\AuthLoginController;
use App\Http\Controllers\Dashboard\DashboardController;
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

          Route::get("/", [DashboardController::class, "index"])->name("index");
          
     });
<?php

use App\Http\Controllers\Auth\AuthLoginController;
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
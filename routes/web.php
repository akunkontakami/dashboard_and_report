<?php

use App\Http\Controllers\Auth\AuthLoginController;
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
Route::controller(AuthForgotPasswordController::class)
     ->prefix('forgot-password')
     ->as('auth.forgot-password.')
     ->group(function () {
          Route::get('/', 'index')->name('index');
          Route::get('/verification/{token}', 'verification')->name('otp-verification');
          Route::get('/change-password/{token}', 'changePassword')->name('change-password');
     });

<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
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

Route::prefix('admin')->group(function () {
   Route::middleware('guest')->group(function () {
      Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.view');
      Route::post('/login', [AuthController::class, 'login'])->name('login');
   });

   Route::middleware('auth')->group(function () {
      Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
      Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
   });
});
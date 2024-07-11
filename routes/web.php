<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\LecturerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\ResearchFieldController;

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
      // Login
      Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.view');
      Route::post('/login', [AuthController::class, 'login'])->name('login');

      // Forgot Password
      Route::get('/lupa-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
      Route::post('/lupa-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');

      // Reset Password
      Route::get('/reset-password', [AuthController::class, 'showPasswordResetForm'])->name('password.reset');
      Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
   });

   Route::middleware('auth')->group(function () {
      Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
      Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

      // News
      Route::get('/berita', [NewsController::class, 'index'])->name('news');
      Route::get('/berita/tambah', [NewsController::class, 'create'])->name('news.create');
      Route::post('/berita/tambah', [NewsController::class, 'store'])->name('news.store');
      Route::get('/berita/{slug}/edit', [NewsController::class, 'edit'])->name('news.edit');
      Route::put('/berita/{slug}/edit', [NewsController::class, 'update'])->name('news.update');
      Route::delete('/berita/{slug}/delete', [NewsController::class, 'delete'])->name('news.delete');

      // Lecturer
      Route::get('/dosen', [LecturerController::class, 'index'])->name('lecturers');
      Route::get('/dosen/{id}', [LecturerController::class, 'findLecturer'])->name('lecturers.find');
      Route::post('/dosen', [LecturerController::class, 'store'])->name('lecturers.store');
      Route::put('/dosen/{id}', [LecturerController::class, 'update'])->name('lecturers.update');
      Route::delete('/dosen/{id}', [LecturerController::class, 'delete'])->name('lecturers.delete');
   });
});

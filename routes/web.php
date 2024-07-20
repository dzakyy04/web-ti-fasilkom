<?php

use App\Http\Controllers\Admin\AnnouncementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\LecturerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\GraduateController;
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

      // Announcements
      Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcements');
      Route::get('/pengumuman/tambah', [AnnouncementController::class, 'create'])->name('announcements.create');
      Route::post('/pengumuman/tambah', [AnnouncementController::class, 'store'])->name('announcements.store');
      Route::get('/pengumuman/{slug}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
      Route::put('/pengumuman/{slug}/edit', [AnnouncementController::class, 'update'])->name('announcements.update');
      Route::delete('/pengumuman/{slug}/delete', [AnnouncementController::class, 'delete'])->name('announcements.delete');


      // Lecturer
      Route::get('/dosen', [LecturerController::class, 'index'])->name('lecturers');
      Route::get('/dosen/{id}', [LecturerController::class, 'findLecturer'])->name('lecturers.find');
      Route::post('/dosen', [LecturerController::class, 'store'])->name('lecturers.store');
      Route::put('/dosen/{id}', [LecturerController::class, 'update'])->name('lecturers.update');
      Route::delete('/dosen/{id}', [LecturerController::class, 'delete'])->name('lecturers.delete');

      // Facility
      Route::get('/sarana-prasarana', [FacilityController::class, 'index'])->name('facilities');
      Route::get('/sarana-prasarana/{id}', [FacilityController::class, 'findFacility'])->name('facilities.find');
      Route::post('/sarana-prasarana', [FacilityController::class, 'store'])->name('facilities.store');
      Route::put('/sarana-prasarana/{id}', [FacilityController::class, 'update'])->name('facilities.update');
      Route::delete('/sarana-prasarana/{id}', [FacilityController::class, 'delete'])->name('facilities.delete');

      // Kompetensi Utama
      Route::get('/kompetensi-utama', [GraduateController::class, 'indexMainCompetency'])->name('graduates.main-competencies');
      Route::get('/kompetensi-utama/{id}', [GraduateController::class, 'findGraduatesMainCompetency'])->name('graduates.main-competencies.find');
      Route::post('/kompetensi-utama', [GraduateController::class, 'storeMainCompetency'])->name('graduates.main-competencies.store');
      Route::put('/kompetensi-utama/{id}', [GraduateController::class, 'updateMainCompetency'])->name('graduates.main-competencies.update');
      Route::delete('/kompetensi-utama/{id}', [GraduateController::class, 'deleteMainCompetency'])->name('graduates.main-competencies.delete');

      // Kompetensi Pendukung
      Route::get('/kompetensi-pendukung', [GraduateController::class, 'indexSupportCompetency'])->name('graduates.support-competencies');
      Route::get('/kompetensi-pendukung/{id}', [GraduateController::class, 'findGraduatesSupportCompetency'])->name('graduates.support-competencies.find');
      Route::post('/kompetensi-pendukung', [GraduateController::class, 'storeSupportCompetency'])->name('graduates.support-competencies.store');
      Route::put('/kompetensi-pendukung/{id}', [GraduateController::class, 'updateSupportCompetency'])->name('graduates.support-competencies.update');
      Route::delete('/kompetensi-pendukung/{id}', [GraduateController::class, 'deleteSupportCompetency'])->name('graduates.support-competencies.delete');
   });
});

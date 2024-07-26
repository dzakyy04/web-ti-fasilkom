<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AnnouncementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\LecturerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\GraduateCompetencyController;
use App\Http\Controllers\Admin\GraduateController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\LeaderController;
use App\Http\Controllers\Admin\MainCompetencyController;
use App\Http\Controllers\Admin\SupportCompetencyController;

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
      Route::get('/berita/{slug}', [NewsController::class, 'findNews'])->name('news.find');
      Route::post('/berita/tambah', [NewsController::class, 'store'])->name('news.store');
      Route::get('/berita/{slug}/edit', [NewsController::class, 'edit'])->name('news.edit');
      Route::put('/berita/{slug}/edit', [NewsController::class, 'update'])->name('news.update');
      Route::delete('/berita/{slug}/delete', [NewsController::class, 'delete'])->name('news.delete');

      // Announcements
      Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcements');
      Route::get('/pengumuman/tambah', [AnnouncementController::class, 'create'])->name('announcements.create');
      Route::get('/pengumuman/{slug}', [AnnouncementController::class, 'findAnnouncements'])->name('announcements.find');
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

      // Main Competency
      Route::get('/kompetensi-utama', [MainCompetencyController::class, 'index'])->name('main-competencies');
      Route::get('/kompetensi-utama/{id}', [MainCompetencyController::class, 'findMainCompetency'])->name('main-competencies.find');
      Route::post('/kompetensi-utama', [MainCompetencyController::class, 'store'])->name('main-competencies.store');
      Route::put('/kompetensi-utama/{id}', [MainCompetencyController::class, 'update'])->name('main-competencies.update');
      Route::delete('/kompetensi-utama/{id}', [MainCompetencyController::class, 'delete'])->name('main-competencies.delete');

      // Kompetensi Pendukung
      Route::get('/kompetensi-pendukung', [SupportCompetencyController::class, 'index'])->name('support-competencies');
      Route::get('/kompetensi-pendukung/{id}', [SupportCompetencyController::class, 'findSupportCompetency'])->name('support-competencies.find');
      Route::post('/kompetensi-pendukung', [SupportCompetencyController::class, 'store'])->name('support-competencies.store');
      Route::put('/kompetensi-pendukung/{id}', [SupportCompetencyController::class, 'update'])->name('support-competencies.update');
      Route::delete('/kompetensi-pendukung/{id}', [SupportCompetencyController::class, 'delete'])->name('support-competencies.delete');

      // Kompetensi Lulusan
      Route::get('/kompetensi-lulusan', [GraduateCompetencyController::class, 'index'])->name('graduate-competencies');
      Route::get('/kompetensi-lulusan/{id}', [GraduateCompetencyController::class, 'findGraduateCompetency'])->name('graduate-competencies.find');
      Route::post('/kompetensi-lulusan', [GraduateCompetencyController::class, 'store'])->name('graduate-competencies.store');
      Route::put('/kompetensi-lulusan/{id}', [GraduateCompetencyController::class, 'update'])->name('graduate-competencies.update');
      Route::delete('/kompetensi-lulusan/{id}', [GraduateCompetencyController::class, 'delete'])->name('graduate-competencies.delete');

      // Struktur Organisasi Admin
      Route::get('/struktur-organisasi/admin', [AdminController::class, 'index'])->name('admins');
      Route::get('/struktur-organisasi/admin/{id}', [AdminController::class, 'findAdmin'])->name('admins.find');
      Route::post('/struktur-organisasi/admin', [AdminController::class, 'store'])->name('admins.store');
      Route::put('/struktur-organisasi/admin/{id}', [AdminController::class, 'update'])->name('admins.update');
      Route::delete('/struktur-organisasi/admin/{id}', [AdminController::class, 'delete'])->name('admins.delete');

      // Struktur Organisasi Pimpinan
      Route::get('/struktur-organisasi/pimpinan', [LeaderController::class, 'index'])->name('leaders');
      Route::get('/struktur-organisasi/pimpinan/{id}', [LeaderController::class, 'findLeader'])->name('leaders.find');
      Route::post('/struktur-organisasi/pimpinan', [LeaderController::class, 'store'])->name('leaders.store');
      Route::put('/struktur-organisasi/pimpinan/{id}', [LeaderController::class, 'update'])->name('leaders.update');
      Route::delete('/struktur-organisasi/pimpinan/{id}', [LeaderController::class, 'delete'])->name('leaders.delete');
   });
});

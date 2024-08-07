<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\CurriculumController;
use App\Http\Controllers\Admin\ProfileGalleryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\LecturerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\GraduateCompetencyController;
use App\Http\Controllers\Admin\InformationController;
use App\Http\Controllers\Admin\InformationGalleryController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\LeaderController;
use App\Http\Controllers\Admin\MainCompetencyController;
use App\Http\Controllers\Admin\MissionController;
use App\Http\Controllers\Admin\SliderGalleryController;
use App\Http\Controllers\Admin\SupportCompetencyController;
use App\Http\Controllers\Admin\VisionController;

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
      Route::get('/sarana-prasarana/session/{id}', [FacilityController::class, 'sessionFacility'])->name('facilities.session');
      Route::post('/sarana-prasarana', [FacilityController::class, 'store'])->name('facilities.store');
      Route::put('/sarana-prasarana/{id}', [FacilityController::class, 'update'])->name('facilities.update');
      Route::delete('/sarana-prasarana/{id}', [FacilityController::class, 'delete'])->name('facilities.delete');

      // // Main Competency
      // Route::get('/kompetensi-utama', [MainCompetencyController::class, 'index'])->name('main-competencies');
      // Route::get('/kompetensi-utama/{id}', [MainCompetencyController::class, 'findMainCompetency'])->name('main-competencies.find');
      // Route::get('/kompetensi-utama/session/{id}', [MainCompetencyController::class, 'sessionMainCompetency'])->name('main-competencies.session');
      // Route::post('/kompetensi-utama', [MainCompetencyController::class, 'store'])->name('main-competencies.store');
      // Route::put('/kompetensi-utama/{id}', [MainCompetencyController::class, 'update'])->name('main-competencies.update');
      // Route::delete('/kompetensi-utama/{id}', [MainCompetencyController::class, 'delete'])->name('main-competencies.delete');

      // // Kompetensi Pendukung
      // Route::get('/kompetensi-pendukung', [SupportCompetencyController::class, 'index'])->name('support-competencies');
      // Route::get('/kompetensi-pendukung/{id}', [SupportCompetencyController::class, 'findSupportCompetency'])->name('support-competencies.find');
      // Route::get('/kompetensi-pendukung/session/{id}', [SupportCompetencyController::class, 'sessionSupportCompetency'])->name('support-competencies.session');
      // Route::post('/kompetensi-pendukung', [SupportCompetencyController::class, 'store'])->name('support-competencies.store');
      // Route::put('/kompetensi-pendukung/{id}', [SupportCompetencyController::class, 'update'])->name('support-competencies.update');
      // Route::delete('/kompetensi-pendukung/{id}', [SupportCompetencyController::class, 'delete'])->name('support-competencies.delete');

      // // Kompetensi Lulusan
      // Route::get('/kompetensi-lulusan', [GraduateCompetencyController::class, 'index'])->name('graduate-competencies');
      // Route::get('/kompetensi-lulusan/{id}', [GraduateCompetencyController::class, 'findGraduateCompetency'])->name('graduate-competencies.find');
      // Route::get('/kompetensi-lulusan/session/{id}', [GraduateCompetencyController::class, 'sessionGraduateCompetency'])->name('graduate-competencies.session');
      // Route::post('/kompetensi-lulusan', [GraduateCompetencyController::class, 'store'])->name('graduate-competencies.store');
      // Route::put('/kompetensi-lulusan/{id}', [GraduateCompetencyController::class, 'update'])->name('graduate-competencies.update');
      // Route::delete('/kompetensi-lulusan/{id}', [GraduateCompetencyController::class, 'delete'])->name('graduate-competencies.delete');

      // Struktur Organisasi Admin
      Route::get('/struktur-organisasi/admin', [AdminController::class, 'index'])->name('admins');
      Route::get('/struktur-organisasi/admin/{id}', [AdminController::class, 'findAdmin'])->name('admins.find');
      Route::get('/struktur-organisasi/admin/session/{id}', [AdminController::class, 'sessionAdmin'])->name('admins.session');
      Route::post('/struktur-organisasi/admin', [AdminController::class, 'store'])->name('admins.store');
      Route::put('/struktur-organisasi/admin/{id}', [AdminController::class, 'update'])->name('admins.update');
      Route::delete('/struktur-organisasi/admin/{id}', [AdminController::class, 'delete'])->name('admins.delete');

      // Struktur Organisasi Pimpinan
      Route::get('/struktur-organisasi/pimpinan', [LeaderController::class, 'index'])->name('leaders');
      Route::get('/struktur-organisasi/pimpinan/{id}', [LeaderController::class, 'findLeader'])->name('leaders.find');
      Route::get('/struktur-organisasi/pimpinan/session/{id}', [LeaderController::class, 'sessionLeader'])->name('leaders.session');
      Route::post('/struktur-organisasi/pimpinan', [LeaderController::class, 'store'])->name('leaders.store');
      Route::put('/struktur-organisasi/pimpinan/{id}', [LeaderController::class, 'update'])->name('leaders.update');
      Route::delete('/struktur-organisasi/pimpinan/{id}', [LeaderController::class, 'delete'])->name('leaders.delete');

      // Informasi Jurusan
      Route::get('/informasi-jurusan/informasi', [InformationController::class, 'index'])->name('informations');
      Route::get('/informasi-jurusan/informasi/{id}', [InformationController::class, 'findInformation'])->name('informations.find');
      Route::get('/informasi-jurusan/session/{id}', [InformationController::class, 'sessionInformation'])->name('informations.session');
      Route::post('/informasi-jurusan/informasi', [InformationController::class, 'store'])->name('informations.store');
      Route::put('/informasi-jurusan/informasi/{id}', [InformationController::class, 'update'])->name('informations.update');
      Route::delete('/informasi-jurusan/informasi/{id}', [InformationController::class, 'delete'])->name('informations.delete');

      // Informasi Jurusan Visi
      Route::get('/informasi-jurusan/visi', [VisionController::class, 'index'])->name('visions');
      Route::get('/informasi-jurusan/visi/{id}', [VisionController::class, 'findVision'])->name('visions.find');
      Route::get('/informasi-jurusan/visi/session/{id}', [VisionController::class, 'sessionVision'])->name('visions.session');
      Route::post('/informasi-jurusan/visi', [VisionController::class, 'store'])->name('visions.store');
      Route::put('/informasi-jurusan/visi/{id}', [VisionController::class, 'update'])->name('visions.update');
      Route::delete('/informasi-jurusan/visi/{id}', [VisionController::class, 'delete'])->name('visions.delete');

      // Informasi Jurusan Misi
      Route::get('/informasi-jurusan/misi', [MissionController::class, 'index'])->name('missions');
      Route::get('/informasi-jurusan/misi/{id}', [MissionController::class, 'findMission'])->name('missions.find');
      Route::get('/informasi-jurusan/misi/session/{id}', [MissionController::class, 'sessionMission'])->name('missions.session');
      Route::post('/informasi-jurusan/misi', [MissionController::class, 'store'])->name('missions.store');
      Route::put('/informasi-jurusan/misi/{id}', [MissionController::class, 'update'])->name('missions.update');
      Route::delete('/informasi-jurusan/misi/{id}', [MissionController::class, 'delete'])->name('missions.delete');

      // Gsleri Slider
      Route::get('/galeri/slider', [SliderGalleryController::class, 'index'])->name('slider-galleries');
      Route::post('/galeri/slider', [SliderGalleryController::class, 'store'])->name('slider-galleries.store');
      Route::put('/galeri/slider/{id}', [SliderGalleryController::class, 'update'])->name('slider-galleries.update');
      Route::delete('/galeri/slider/{id}', [SliderGalleryController::class, 'delete'])->name('slider-galleries.delete');
      Route::get('/galeri/slider/{id}', [SliderGalleryController::class, 'findSliderGallery'])->name('slider-galleries.find');
      Route::get('/galeri/slider/session/{id}', [SliderGalleryController::class, 'sessionSliderGallery'])->name('slider-galleries.session');

      // Gsleri Informasi
      Route::get('/galeri/informasi', [InformationGalleryController::class, 'index'])->name('information-galleries');
      Route::post('/galeri/informasi', [InformationGalleryController::class, 'store'])->name('information-galleries.store');
      Route::put('/galeri/informasi/{id}', [InformationGalleryController::class, 'update'])->name('information-galleries.update');
      Route::delete('/galeri/informasi/{id}', [InformationGalleryController::class, 'delete'])->name('information-galleries.delete');
      Route::get('/galeri/informasi/{id}', [InformationGalleryController::class, 'findInformationGallery'])->name('information-galleries.find');
      Route::get('/galeri/informasi/session/{id}', [InformationGalleryController::class, 'sessionInformationGallery'])->name('information-galleries.session');

      // Gsleri Profil
      Route::get('/galeri/profil', [ProfileGalleryController::class, 'index'])->name('profile-galleries');
      Route::post('/galeri/profil', [ProfileGalleryController::class, 'store'])->name('profile-galleries.store');
      Route::put('/galeri/profil/{id}', [ProfileGalleryController::class, 'update'])->name('profile-galleries.update');
      Route::delete('/galeri/profil/{id}', [ProfileGalleryController::class, 'delete'])->name('profile-galleries.delete');
      Route::get('/galeri/profil/{id}', [ProfileGalleryController::class, 'findProfileGallery'])->name('profile-galleries.find');
      Route::get('/galeri/profil/session/{id}', [ProfileGalleryController::class, 'sessionProfileGallery'])->name('profile-galleries.session');

      // Kurikulum`
      Route::get('/kurikulum', [CurriculumController::class, 'index'])->name('curriculums');
      Route::post('/kurikulum', [CurriculumCOntroller::class, 'store'])->name('curriculums.store');
      Route::put('/kurikulum/{id}', [CurriculumCOntroller::class, 'update'])->name('curriculums.update');
      Route::delete('/kurikulum/{id}', [CurriculumCOntroller::class, 'delete'])->name('curriculums.delete');
      Route::get('/kurikulum/{id}', [CurriculumCOntroller::class, 'findCurriculum'])->name('curriculums.find');
      Route::get('/kurikulum/session/{id}', [CurriculumCOntroller::class, 'sessionCurriculum'])->name('curriculums.session');

   });
});

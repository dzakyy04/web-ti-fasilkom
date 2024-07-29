<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\CompetencyController;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\InformationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\LecturerController;
use App\Http\Controllers\Api\OrganizationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/dosen', [LecturerController::class, 'getAll']);
Route::get('/dosen/{id}', [LecturerController::class, 'getById']);

Route::get('/berita', [NewsController::class, 'getAll']);
Route::get('/berita/{slug}', [NewsController::class, 'getBySlug']);
Route::get('/pengumuman', [AnnouncementController::class, 'getAll']);
Route::get('/pengumuman/{slug}', [AnnouncementController::class, 'getBySlug']);

Route::get('/sarana-prasarana', [FacilityController::class, 'getAll']);
Route::get('/struktur-jabatan', [OrganizationController::class, 'getAll']);
Route::get('/kompetensi', [CompetencyController::class, 'getAll']);
Route::get('/informasi-jurusan', [InformationController::class, 'getAll']);


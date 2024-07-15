<?php

use App\Http\Controllers\Api\AnnouncementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\LecturerController;

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

Route::get('/lecturers', [LecturerController::class, 'getAll']);
Route::get('/lecturers/{id}', [LecturerController::class, 'getById']);

Route::get('/news', [NewsController::class, 'getAll']);
Route::get('/news/{slug}', [NewsController::class, 'getBySlug']);
Route::get('/announcement', [AnnouncementController::class, 'getAll']);
Route::get('/announcement/{slug}', [AnnouncementController::class, 'getBySlug']);

<?php

use App\Http\Controllers\AcademicTrackController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\TitleController;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/register', [AuthController::class, 'register'])->middleware('guest:sanctum');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest:sanctum');
Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::apiResource('timetable', TimetableController::class);
Route::apiResource('professor', ProfessorController::class);
Route::apiResource('classroom', ClassRoomController::class);
Route::apiResource('course', CourseController::class);
Route::apiResource('level', LevelController::class);
Route::apiResource('subject', SubjectController::class);
Route::apiResource('title', TitleController::class);
Route::apiResource('academic_track', AcademicTrackController::class);

Route::patch('/subject/{subjectId}/link/academicTrack/{academictrackId}', [SubjectController::class, 'link']);

Route::get('/generate/{timetableId}', [TimetableController::class, 'generate']);
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\TitleController;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Graph;
use App\Models\Professor;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', [AuthController::class, 'register'])->middleware('guest:sanctum');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest:sanctum');
Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::apiResource('timetable', TimetableController::class)->middleware('auth:sanctum');
Route::apiResource('professor', ProfessorController::class)->middleware('auth:sanctum');
Route::apiResource('classroom', ClassRoomController::class)->middleware('auth:sanctum');
Route::apiResource('course', CourseController::class)->middleware('auth:sanctum');
Route::apiResource('level', LevelController::class)->middleware('auth:sanctum');
Route::apiResource('subject', SubjectController::class)->middleware('auth:sanctum');
Route::apiResource('title', TitleController::class)->middleware('auth:sanctum');

Route::patch('/subject/{subjectId}/link/level/{levelId}', [SubjectController::class, 'link'])->middleware('auth:sanctum');


Route::get('/graph', function(Request $request) {
    $courses = Course::all();

    $classrooms = ClassRoom::all()->all();
    $g = new Graph();

    foreach($courses as $course){
        $g->addVertex($course);
    }

    return dd($g->colorize($classrooms));
});
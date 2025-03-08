<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TitleController;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Graph;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', [AuthController::class, 'register'])->middleware('guest:sanctum');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest:sanctum');
Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::apiResource('professor', ProfessorController::class)->middleware('auth:sanctum');
Route::apiResource('classroom', ClassRoomController::class)->middleware('auth:sanctum');
Route::apiResource('course', CourseController::class)->middleware('auth:sanctum');
Route::apiResource('level', LevelController::class)->middleware('auth:sanctum');
Route::apiResource('subject', SubjectController::class)->middleware('auth:sanctum');
Route::apiResource('title', TitleController::class)->middleware('auth:sanctum');


Route::get('/graph', function(Request $request) {
    $courses = Course::all();
    $classrooms = ClassRoom::all();
    $g = new Graph();

    foreach($courses as $course){
        $g->addVertex($course);
    }

    return $g->colorize($classrooms);
});
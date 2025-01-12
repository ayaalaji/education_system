<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MaterialController;

// ---------------------- Auth Routes ---------------------- //
// Auth for (students)
Route::post('/login/user', [AuthController::class, 'login'])->defaults('guard', 'api');
Route::post('register/user', [AuthController::class, 'register']);
Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout/user', [AuthController::class, 'logout'])->defaults('guard', 'api');
    Route::post('/refresh/user', [AuthController::class, 'refresh'])->defaults('guard', 'api');
});

// Auth for (teachers)
Route::post('/login/teacher', [AuthController::class, 'login'])->defaults('guard', 'teacher-api');
Route::middleware(['auth:teacher-api'])->group(function () {
    Route::post('/logout/teacher', [AuthController::class, 'logout'])->defaults('guard', 'teacher-api');
    Route::post('/refresh/teacher', [AuthController::class, 'refresh'])->defaults('guard', 'teacher-api');
});

// ---------------------- Role Routes ---------------------- //
Route::controller(RoleController::class)->group(function () {
    Route::get('roles', 'index');
    Route::post('role', 'store');
    Route::get('role/{role}', 'show');
    Route::put('role/{role}', 'update');
    Route::delete('role/{role}', 'destroy');
});

// ---------------------- User Routes ---------------------- //
Route::controller(UserController::class)->group(function () {
    Route::get('users', 'index');
    Route::post('user', 'store');
    Route::get('users/{user}', 'show');
    Route::put('users/{user}', 'update');
    Route::delete('users/{user}', 'destroy');
});

// ---------------------- Teacher Routes ---------------------- //
Route::controller(TeacherController::class)->group(function () {
    Route::get('/teachers', 'index');
    Route::post('/teachers', 'store');
    Route::get('/teachers/{teacher}', 'show');
    Route::put('/teachers/{teacher}', 'update');
    Route::delete('/teachers/{teacher}', 'destroy');
});

// ---------------------- Material Routes ---------------------- //
Route::controller(MaterialController::class)->group(function () {
    Route::get('/materials', 'index');
    Route::post('/materials', 'store');
    Route::get('/materials/{material}', 'show');
    Route::put('/materials/{material}', 'update');
    Route::delete('/materials/{material}', 'destroy');
});

// ---------------------- Category Routes ---------------------- //
Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index');
    Route::post('/categories', 'store');
    Route::get('/categories/{category}', 'show');
    Route::put('/categories/{category}', 'update');
    Route::delete('/categories/{category}', 'destroy');
});

// ---------------------- Course Routes ---------------------- //
Route::controller(CourseController::class)->group(function () {
    Route::get('/courses', 'index');

    // Middleware for ensuring the teacher is responsible for the course
    Route::middleware(['auth:teacher-api', 'course.teacher'])->group(function () {
        Route::post('/courses', 'store');
        Route::put('/courses/{course}', 'update');
        Route::delete('/courses/{course}', 'destroy');
    });

    Route::get('/courses/{course}', 'show');
});

// ---------------------- Task Routes ---------------------- //
Route::middleware(['auth:api', 'task.user'])->group(function () {
    Route::controller(TaskController::class)->group(function () {
        Route::get('task', 'index');
        Route::get('task/{task}', 'show');
        Route::post('task', 'store');
        Route::put('task/{task}', 'update');
        Route::delete('task/{task}', 'destroy');
    });
});

// Route to add a note for a specific user on a task
Route::post('/tasks/{taskId}/users/{userId}/add-note', [TaskController::class, 'addNote'])
    ->middleware(['auth:api', 'course.teacher']);

// Route to delete a note for a specific user on a task
Route::delete('/tasks/{taskId}/users/{userId}/delete-note', [TaskController::class, 'deleteNote'])
    ->middleware(['auth:api', 'course.teacher']);

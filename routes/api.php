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
    Route::get('roles', 'index')->middleware('permission:show_role'); // Get a list of all roles
    Route::post('role', 'store')->middleware('permission:add_role'); // Create a new role
    Route::get('role/{role}', 'show')->middleware('permission:show_role'); // Get details of a specific role
    Route::put('role/{role}', 'update')->middleware('permission:update_role'); // Update an existing role
    Route::delete('role/{role}', 'destroy')->middleware('permission:delete_role'); // Delete a specific role
});

// ---------------------- User Routes ---------------------- //
Route::controller(UserController::class)->group(function () {
    Route::get('users', 'index')->middleware('permission:show_user'); // Get a list of all users
    Route::post('user', 'store')->middleware('permission:add_user'); // Create a new user
    Route::get('users/{user}', 'show')->middleware('permission:show_user'); // Get details of a specific user
    Route::put('users/{user}', 'update')->middleware('permission:update_user'); // Update an existing user
    Route::delete('users/{user}', 'destroy')->middleware('permission:delete_user'); // Delete a specific user
});

// ---------------------- Teacher Routes ---------------------- //
Route::controller(TeacherController::class)->group(function () {
    Route::get('/teachers', 'index')->middleware('permission:show_teacher'); // Get a list of all teachers
    Route::post('/teachers', 'store')->middleware('permission:add_teacher'); // Create a new teacher
    Route::get('/teachers/{teacher}', 'show')->middleware('permission:show_teacher'); // Get details of a specific teacher
    Route::put('/teachers/{teacher}', 'update')->middleware('permission:update_teacher'); // Update an existing teacher
    Route::delete('/teachers/{teacher}', 'destroy')->middleware('permission:delete_teacher'); // Delete a specific teacher
});

// ---------------------- Material Routes ---------------------- //
Route::middleware('permission:access_materials')->group(function(){
    Route::controller(MaterialController::class)->group(function () {
        Route::get('/materials', 'index');
        Route::get('/materials/{material}', 'show');
        Route::middleware(['course.teacher','auth:teacher-api'])->group(function(){
            Route::post('/materials', 'store');
            Route::put('/materials/{material}', 'update');
            Route::delete('/materials/{material}', 'destroy');
        });

    });
});


// ---------------------- Category Routes ---------------------- //
Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index')->middleware('permission:show_category'); // Get a list of all categories
    Route::post('/categories', 'store')->middleware('permission:add_category'); // Create a new category
    Route::get('/categories/{category}', 'show')->middleware('permission:show_category'); // Get details of a specific category
    Route::put('/categories/{category}', 'update')->middleware('permission:update_category'); // Update an existing category
    Route::delete('/categories/{category}', 'destroy')->middleware('permission:delete_category'); // Delete a specific category
});

// ---------------------- Course Routes ---------------------- //
Route::controller(CourseController::class)->group(function () {
    Route::get('/courses', 'index');

    // Middleware for ensuring the teacher is responsible for the course
    Route::middleware(['auth:teacher-api', 'course.teacher'])->group(function () {
        Route::post('/courses', 'store')->middleware('permission:add_course'); // Create a new course
        Route::put('/courses/{course}', 'update')->middleware('permission:update_course'); // Update an existing course
        Route::delete('/courses/{course}', 'destroy')->middleware('permission:delete_course'); // Delete a specific course
        Route::put('/courses/{course}/updateStartDate', 'updateStartDate')->middleware('permission:set_course_start_time'); // Update course start date
        Route::put('/courses/{course}/updateEndDate', 'updateEndDate')->middleware('permission:set_course_end_time'); // Update course end date
        Route::put('/courses/{course}/updateStartRegisterDate', 'updateStartRegisterDate')->middleware('permission:set_registration_start_time'); // Update course registration start date
        Route::put('/courses/{course}/updateEndRegisterDate', 'updateEndRegisterDate')->middleware('permission:set_registration_end_time'); // Update course registration end date
    });

    Route::get('/courses/{course}', 'show');
});

// ---------------------- Task Routes ---------------------- //
Route::middleware( ['auth:teacher-api','course.teacher'])->group(function () {
    Route::controller(TaskController::class)->group(function () {
        Route::get('task', 'index');
        Route::get('task/{task}', 'show');
        Route::post('task', 'store');
        Route::put('task/{task}', 'update');
        Route::delete('task/{task}', 'destroy');
        // Route to add a note for a specific user on a task
        Route::post('/tasks/{taskId}/users/{userId}/add-note', [TaskController::class, 'addNote']);

        // Route to delete a note for a specific user on a task
        Route::delete('/tasks/{taskId}/users/{userId}/delete-note', [TaskController::class, 'deleteNote']);
    });
});
Route::post('task/{task}/attachments',[TaskController::class,'uploadTask'])->middleware(['task.user','auth:api']);


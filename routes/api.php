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
use App\Http\Controllers\Api\NoteController;

// ---------------------- Auth Routes ---------------------- //
Route::prefix('auth')->group(function () {
    // Auth for Students
    Route::post('/login/user', [AuthController::class, 'login'])->defaults('guard', 'api');
    Route::post('register/user', [AuthController::class, 'register']);

    Route::middleware(['auth:api'])->group(function () {
        Route::post('/logout/user', [AuthController::class, 'logout'])->defaults('guard', 'api');
        Route::post('/refresh/user', [AuthController::class, 'refresh'])->defaults('guard', 'api');
    });

    // Auth for Teachers
    Route::post('/login/teacher', [AuthController::class, 'login'])->defaults('guard', 'teacher-api');
    Route::middleware(['auth:teacher-api'])->group(function () {
        Route::post('/logout/teacher', [AuthController::class, 'logout'])->defaults('guard', 'teacher-api');
        Route::post('/refresh/teacher', [AuthController::class, 'refresh'])->defaults('guard', 'teacher-api');
    });
});

// ---------------------- Role Routes ---------------------- //
Route::controller(RoleController::class)->prefix('roles')->middleware('auth:teacher-api')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store')->middleware('permission:add_role');
    Route::get('/{role}', 'show')->middleware('permission:show_role');
    Route::put('/{role}', 'update')->middleware('permission:update_role');
    Route::delete('/{role}', 'destroy')->middleware('permission:delete_role');
});

// ---------------------- User Routes ---------------------- //
Route::controller(UserController::class)->prefix('users')->middleware('auth:teacher-api')->group(function () {
    Route::get('/', 'index')->middleware('permission:show_user');
    Route::post('/', 'store')->middleware('permission:add_user');
    Route::get('/{user}', 'show')->middleware('permission:show_user');
    Route::put('/{user}', 'update')->middleware('permission:update_user');
    Route::delete('/{user}', 'destroy')->middleware('permission:delete_user_temporary');

    Route::delete('/{user}/forcedelete', 'forceDeleteUser')->middleware('permission:delete_user');
    Route::get('/{user}/restore', 'restoreUser')->middleware('permission:restore_user');
    Route::get('/trashed', 'getAllUserTrashed')->middleware('permission:get_trashed_user');
});

// ---------------------- Teacher Routes ---------------------- //
Route::controller(TeacherController::class)->prefix('teachers')->middleware('auth:teacher-api')->group(function () {
    Route::get('/', 'index')->middleware('permission:show_teacher');
    Route::post('/', 'store')->middleware('permission:add_teacher');
    Route::get('/{teacher}', 'show')->middleware('permission:show_teacher');
    Route::put('/{teacher}', 'update')->middleware('permission:update_teacher');
    Route::delete('/{teacher}', 'soft_delete')->middleware('permission:delete_teacher_temporary');
    Route::get('/restore/{id}', 'restore')->middleware('permission:restore_teacher');
    Route::delete('/forceDelete/{id}', 'force_delete')->middleware('permission:delete_teacher');
});

// ---------------------- Material Routes ---------------------- //
Route::controller(MaterialController::class)->prefix('materials')->middleware('auth:teacher-api')->group(function () {
    Route::get('/', 'index')->middleware('permission:show_teacher');
    Route::get('/{material}', 'show');

    Route::middleware('course.teacher')->group(function () {
        Route::post('/', 'store');
        Route::put('/{material}', 'update');
        Route::delete('/{material}', 'destroy');
    });
});

// ---------------------- Category Routes ---------------------- //
Route::controller(CategoryController::class)->prefix('categories')->middleware('auth:teacher-api')->group(function () {
    Route::get('/', 'index')->middleware('permission:show_category');
    Route::post('/', 'store')->middleware('permission:add_category');
    Route::get('/{category}', 'show')->middleware('permission:show_category');
    Route::put('/{category}', 'update')->middleware('permission:update_category');
    Route::delete('/{category}', 'destroy')->middleware('permission:delete_category_temporary');

    Route::get('/trashed', 'trashed')->middleware('permission:getTrashed');
    Route::post('/{id}/restore', 'restore')->middleware('permission:restore_category');
    Route::delete('/{id}/force-delete', 'forceDelete')->middleware('permission:delete_category');
});

// ---------------------- Shared Middleware Group ---------------------- //
Route::middleware(['auth:teacher-api', 'course.teacher'])->group(function () {
    // ---------------------- Course Routes ---------------------- //
    Route::controller(CourseController::class)->prefix('courses')->group(function () {
        Route::post('/', 'store')->middleware('permission:add_course');
        Route::put('/{course}', 'update')->middleware('permission:update_course');
        Route::delete('/{course}', 'destroy')->middleware('permission:delete_course_temporary');

        Route::put('/{course}/updateStartDate', 'updateStartDate')->middleware('permission:set_course_start_time');
        Route::put('/{course}/updateEndDate', 'updateEndDate')->middleware('permission:set_course_end_time');
        Route::put('/{course}/updateStartRegisterDate', 'updateStartRegisterDate')->middleware('permission:set_registration_start_time');
        Route::put('/{course}/updateEndRegisterDate', 'updateEndRegisterDate')->middleware('permission:set_registration_end_time');
        Route::put('/{course}/updatestatus', 'updateStatus')->middleware('permission:change_the_status_of_course');
        Route::post('/{course}/addUser', 'addUser')->middleware('permission:add_user_to_course');

        Route::delete('/{course}/forcedelete', 'forceDeleteCourse')->middleware('permission:delete_course');
        Route::get('/{course}/restore', 'restoreCourse')->middleware('permission:restore_course');
        Route::get('/trashed', 'getAllTrashed')->middleware('permission:get_trashed_corse');
    });

    // ---------------------- Task Routes ---------------------- //
    Route::controller(TaskController::class)->prefix('tasks')->group(function () {
        Route::get('/', 'index');
        Route::get('/{task}', 'show');
        Route::post('/', 'store');
        Route::put('/{task}', 'update');
        Route::delete('/{task}', 'destroy');
        Route::post('/{task}/forcedelete', 'forceDeleteForTask');
        Route::post('/{task}/restore', 'restoreTask');
    });


});

Route::middleware(['auth:teacher-api'])->group(function () {

    // ---------------------- Note Routes ---------------------- //
    Route::controller(NoteController::class)->prefix('notes')->group(function () {
        Route::post('/{taskId}/users/{userId}/add-note', 'addNote');
        Route::delete('/{taskId}/users/{userId}/delete-note', 'deleteNote');
});

});
// ---------------------- Task Attachment Routes ---------------------- //
Route::controller(TaskController::class)->prefix('tasks')->group(function () {
    Route::post('/{task}/attachments', 'uploadTask')->middleware(['task.user', 'auth:api']);
});

<?php

use App\Models\Course;
use App\Exports\CourseReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\PaypalController;


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
    Route::get('/trashed', 'getAllUserTrashed')->middleware('permission:get_trashed_user');
    Route::get('/', 'index')->middleware('permission:show_user');
    Route::post('/', 'store')->middleware('permission:add_user');
    Route::get('/{user}', 'show')->middleware('permission:show_user');
    Route::put('/{user}', 'update')->middleware('permission:update_user');
    Route::delete('/{user}', 'destroy')->middleware('permission:delete_user_temporary');

    Route::delete('/{user}/forcedelete', 'forceDeleteUser')->middleware('permission:delete_user');
    Route::get('/{user}/restore', 'restoreUser')->middleware('permission:restore_user');
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
Route::controller(MaterialController::class)->prefix('materials')->group(function () {
    Route::get('/trashed',  'getAllTrashed')->middleware(['permission:get_all_trashed','auth:teacher-api']);
    Route::get('/', 'index');
    Route::get('/{material}', 'show');

    Route::middleware(['auth:teacher-api'])->group(function () {
        
        Route::post('/', 'store')->middleware('permission:add_material');
        // Force Delete
        Route::delete('/{id}/forcedelete',  'force_delete')->middleware('permission:delete_material');
        Route::get('/{material}/restore',  'restoreMaterial')->middleware('permission:restore_material');
           //get trash all materials
        Route::middleware('material.teacher')->group(function () {
            Route::put('/{material}', 'update')->middleware('permission:update_material');
            Route::delete('/{material}', 'destroy')->middleware('permission:delete_material_temporary');
        });
    });
});
// ---------------------- Category Routes ---------------------- //
Route::controller(CategoryController::class)->prefix('categories')->middleware('auth:teacher-api')->group(function () {
    Route::get('/trashed', 'trashed')->middleware('permission:getTrashed');
    Route::get('/', 'index')->middleware('permission:show_category');
    Route::post('/', 'store')->middleware('permission:add_category');
    Route::get('/{category}', 'show')->middleware('permission:show_category');
    Route::put('/{category}', 'update')->middleware('permission:update_category');
    Route::delete('/{category}', 'destroy')->middleware('permission:delete_category_temporary');

    Route::post('/{id}/restore', 'restore')->middleware('permission:restore_category');
    Route::delete('/{id}/force-delete', 'forceDelete')->middleware('permission:delete_category');
});

// ---------------------- Course Routes ---------------------- //
Route::controller(CourseController::class)->group(function () {
    Route::get('/courses', 'index');
    Route::get('/courses/{course}','show');

    // Middleware for ensuring the teacher is responsible for the course
    Route::middleware(['auth:teacher-api'])->group(function () {
        Route::post('/courses', 'store')
                ->middleware('permission:add_course'); // Create a new course
        Route::put('/courses/{course}', 'update')
                ->middleware(['permission:update_course','course.teacher']); // Update an existing course
        Route::delete('/courses/{course}', 'destroy')
                ->middleware(['permission:delete_course_temporary','course.teacher']); // Delete a specific course
        Route::put('/courses/{course}/updateStartDate', 'updateStartDate')
                ->middleware('permission:set_course_start_time'); // Update course start date
        Route::put('/courses/{course}/updateEndDate', 'updateEndDate')
                ->middleware('permission:set_course_end_time'); // Update course end date
        Route::put('/courses/{course}/updateStartRegisterDate', 'updateStartRegisterDate')
                ->middleware('permission:set_registration_start_time'); // Update course registration start date
        Route::put('/courses/{course}/updateEndRegisterDate', 'updateEndRegisterDate')
                ->middleware('permission:set_registration_end_time'); // Update course registration end date
        Route::put('/courses/{course}/updatestatus', 'updateStatus')
                ->middleware('permission:change_the_status_of_course'); //Update the course status
        Route::post('/courses/{course}/addUser','addUser')
                ->middleware(['permission:add_user_to_course','course.teacher']);//Add user to course

        Route::delete('/courses/{course}/forcedelete', 'forceDeleteCourse')
                ->middleware('permission:delete_course');
        Route::get('courses/{course}/restore', 'restoreCourse')
                ->middleware('permission:restore_course');
        Route::get('/courses-trashed', 'getAllTrashed')
                ->middleware('permission:get_trashed_corse');

    });
});


// ---------------------- Task Routes ---------------------- //
Route::middleware( ['auth:teacher-api','task.teacher'])->group(function () {
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

//-----------------  For Export ---------------------------//
Route::middleware('auth:teacher-api')->group(function () {
    Route::controller(ExportController::class)->group(function () {
        Route::get('/tasks-overDueUserExport',  'exportUsersWithOverdueTasks')->middleware('permission:export_users_with_overdue_tasks');
        Route::get('/tasks/{taskId}/export',  'generateExcel')->middleware('permission:export_task_note');
        Route::get('/courses/{course}/export', 'exportCourseReport')->middleware('permission:export_course_report');
        Route::get('/export-education-system','exportEducationSystem')->middleware('permission:export_category_course');
   
    });
   
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


// Route::post('/courses/{course}/register', [PaypalController::class, 'registerToCourse'])->middleware('auth:api');

Route::post('/paypal/create-order', [PayPalController::class, 'createOrder']);
Route::post('/paypal/capture-order', [PayPalController::class, 'captureOrder']);
Route::get('/paypal/cancel', [PayPalController::class, 'cancelOrder']);
Route::get('/paypal/success', [PayPalController::class, 'successOrder']);

// Route::post('/paypal/register-to-course', [PaypalController::class, 'registerToCourse']);
//     Route::post('/paypal/capture-order', [PaypalController::class, 'captureOrder']);
//     Route::get('/paypal/cancel-order', [PaypalController::class, 'cancelOrder']);
Route::post('paypal/authenticate', [PayPalController::class, 'authenticate']); // للحصول على Access Token
Route::post('paypal/create-order', [PayPalController::class, 'createOrder']); // لإنشاء طلب
Route::post('paypal/capture-payment/{orderId}', [PayPalController::class, 'capturePayment']); // للتقاط الدفع
Route::get('paypal/show-order/{orderId}', [PayPalController::class, 'showOrder']); // لعرض تفاصيل الطلب
Route::get('paypal/success', [PayPalController::class, 'successTransaction']); // مسار النجاح
Route::get('paypal/cancel', [PayPalController::class, 'cancelTransaction']); // مسار الإلغاء


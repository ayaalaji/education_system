<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\PushNotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


//Auth for(students)
Route::post('/login/user', [AuthController::class, 'login'])->defaults('guard', 'api');
Route::post('register/user', [AuthController::class, 'register']);
Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout/user', [AuthController::class, 'logout'])->defaults('guard', 'api');
    Route::post('/refresh/user', [AuthController::class, 'refresh'])->defaults('guard', 'api');
});

//Auth for (teachers)
Route::post('/login/teacher', [AuthController::class, 'login'])->defaults('guard', 'teacher-api');
Route::middleware(['auth:teacher-api'])->group(function () {
    Route::post('/logout/teacher', [AuthController::class, 'logout'])->defaults('guard', 'teacher-api');
    Route::post('/refresh/teacher', [AuthController::class, 'refresh'])->defaults('guard', 'teacher-api');
});



//////// Role ////////
Route::controller(RoleController::class)->group(function () {
    Route::get('roles', 'index');
    Route::post('role', 'store');
    Route::get('role/{role}', 'show');
    Route::put('role/{role}', 'update');
    Route::delete('role/{role}', 'destroy');
});
///////User/////////
Route::controller(UserController::class)->group(function () {
    Route::get('users', 'index');
    Route::post('user', 'store');
    Route::get('users/{user}', 'show');
    Route::put('users/{user}', 'update');
    Route::delete('users/{user}', 'destroy');
});

///////Teacher////////
Route::controller(TeacherController::class)->group(function () {
    Route::get('/teachers', 'index');
    Route::post('/teachers', 'store');
    Route::get('/teachers/{teacher}', 'show');
    Route::put('/teachers/{teacher}', 'update');
    Route::delete('/teachers/{teacher}', 'destroy');
});

///////Material////////
Route::controller(MaterialController::class)->group(function () {
    Route::get('/materials', 'index');
    Route::post('/materials', 'store');
    Route::get('/materials/{material}', 'show');
    Route::put('/materials/{material}', 'update');
    Route::delete('/materials/{material}', 'destroy');
});

////////////Category///////////
Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index');
    Route::post('/categories', 'store');
    Route::get('/categories/{category}', 'show');
    Route::put('/categories/{category}', 'update');
    Route::delete('/categories/{category}', 'destroy');
});

/////////Courses/////////////
Route::controller(CourseController::class)->group(function () {
    Route::get('/courses', 'index');


    Route::post('/courses', 'store');
    //    ->middleware('permission:');

    Route::get('/courses/{course}', 'show');
    //    ->middleware('permission:');

    Route::put('/courses/{course}', 'update');
    //    ->middleware('permission:');

    Route::delete('/courses/{course}', 'destroy');
    //    ->middleware('permission:');

    //---------------------------------------

    /**
     * Force delete and Restore
     */

    Route::delete('/courses/{course}/forcedelete', 'forceDeleteCourse');
    //    ->middleware('permission:');


    Route::get('courses/{course}/restore', 'restoreCourse');
    //    ->middleware('permission:');


    Route::get('/courses-trashed', 'getAllTrashed');
    //    ->middleware('permission:');


    //-----------------------------------------


    Route::put('/courses/{course}/updatestatus', 'updateStatus');
    // ->middleware('permission:');

    /**
     * start and end date  of the course
     */

    Route::put('/courses/{course}/updateStartDate', 'updateStartDate');
    // ->middleware('permission:');

    Route::put('/courses/{course}/updateEndDate', 'updateEndDate');
    // ->middleware('permission:');

    //................
    /**
     * start and end registaer date of the course
     */
    Route::put('/courses/{course}/updateStartRegisterDate', 'updateStartRegisterDate');
    // ->middleware('permission:');

    //..................

    Route::put('/courses/{course}/updateEndRegisterDate', 'updateEndRegisterDate');
    // ->middleware('permission:');


    Route::post('/courses/{course}/addUser',[CourseController::class,'addUser']);
    // ->middleware('permission:');

});




Route::middleware('course.teacher')->controller(TaskController::class)->group(function () {

    Route::get('task', 'index');

    Route::get('task/{task}', 'show');
    
    Route::post('task', 'store');
    
    Route::put('task/{task}', 'update');
   
    Route::delete('task/{task}', 'destroy');
});

Route::post('/task/{task}/attachments', [TaskController::class, 'uploadTask'])->defaults('guard', 'api');
Route::post('test',[MaterialController::class,'store']);

// Route to add a note for a specific user on a task
Route::post('/tasks/{taskId}/users/{userId}/add-note', [TaskController::class, 'addNote']);

// Route to delete a note for a specific user on a task
Route::delete('/tasks/{taskId}/users/{userId}/delete-note', [TaskController::class, 'deleteNote']);

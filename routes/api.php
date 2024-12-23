<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TeacherController;
use Illuminate\Support\Facades\Auth;

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
Route::post('register/user',[AuthController::class, 'register']);
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

Route::controller(TeacherController::class)->group(function () {
    Route::get('/teachers', 'index');
    Route::post('/teachers', 'store');
    Route::get('/teachers/{teacher}', 'show');
    Route::put('/teachers/{teacher}', 'update');
    Route::delete('/teachers/{teacher}', 'destroy');
});

////////////Category///////////
Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index'); 
    Route::post('/categories', 'store'); 
    Route::get('/categories/{category}', 'show'); 
    Route::put('/categories/{category}', 'update'); 
    Route::delete('/categories/{category}', 'destroy'); 
});

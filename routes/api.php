<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TeacherController;

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

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
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


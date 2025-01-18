<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;




Route::get('/create-transaction', [PaymentController::class, 'createTransaction'])->name('createTransaction');
Route::post('/process-transaction', [PaymentController::class, 'processTransaction'])->name('processTransaction');
Route::get('/success-transaction', [PaymentController::class, 'successTransaction'])->name('successTransaction');
Route::get('/cancel-transaction', [PaymentController::class, 'cancelTransaction'])->name('cancelTransaction');

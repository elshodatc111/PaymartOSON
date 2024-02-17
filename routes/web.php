<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OsonPaymartController;

use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});



Route::post('/create-transaction', [PaymentController::class, 'createTransaction'])->name('paymart');
Route::get('/chekkinTransaction/{tr_id}', [PaymentController::class, 'chekkinTransaction'])->name('chekkinTransaction');


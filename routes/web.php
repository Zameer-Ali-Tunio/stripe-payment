<?php

use App\Http\Controllers\DisputeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RefundController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::resource('home', InvoiceController::class);
    Route::resource('disputes', DisputeController::class);
    Route::resource('refunds', RefundController::class);
    Route::get('success_payment', [InvoiceController::class, "successPayment"]);
    Route::get('cancel_payment', [InvoiceController::class, "cancelPayment"]);
});

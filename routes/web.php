<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VoucherController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/vouchers', [VoucherController::class, "index"])->name('vouchers-index')->middleware(['can:view-voucher']);
    Route::delete('/vouchers/{id}', [VoucherController::class, "destroy"])->name('vouchers-destroy')->middleware(['can:delete-voucher']);
    Route::get('/vouchers/create', [VoucherController::class, "create"])->name('vouchers-create')->middleware(['can:create-voucher']);
    Route::post('/vouchers', [VoucherController::class, "store"])->name('vouchers-store')->middleware(['can:create-voucher']);
    Route::get('/vouchers/export/{id}', [VoucherController::class, "export"])->name('vouchers-export')->middleware(['can:export']);
});



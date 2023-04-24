<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
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

    Route::get('/groups', [GroupController::class, "index"])->name('groups-index')->middleware(['can:group-crud']);
    Route::delete('/groups/{id}', [GroupController::class, "destroy"])->name('groups-destroy')->middleware(['can:group-crud']);
    Route::get('/groups/create', [GroupController::class, "create"])->name('groups-create')->middleware(['can:group-crud']);
    Route::post('/groups', [GroupController::class, "store"])->name('groups-store')->middleware(['can:group-crud']);
    Route::get('/groups/edit/{id}', [GroupController::class, "edit"])->name('groups-edit')->middleware(['can:group-crud']);
    Route::patch('/groups/{id}', [GroupController::class, "update"])->name('groups-update')->middleware(['can:group-crud']);
    
    Route::get('/groups/admin/{id}', [GroupController::class, "getAdmin"])->name('groups-admin')->middleware(['can:assign-group-admin']);
    Route::patch('/groups/admin/assign/{id}', [GroupController::class, "assignAdmin"])->name('groups-assign-admin')->middleware(['can:assign-group-admin']);
    Route::get('/groups/admin/create/{id}', [GroupController::class, "createNewAdmin"])->name('groups-new-admin')->middleware(['can:assign-group-admin']);
    Route::post('/groups/admin/store/{id}', [GroupController::class, "storeNewAdmin"])->name('groups-admin-add')->middleware(['can:assign-group-admin']);
});



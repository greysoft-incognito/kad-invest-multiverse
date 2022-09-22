<?php

use App\Http\Controllers\v1\Admin\AdminController;
use App\Http\Controllers\v1\Admin\ReservationController;
use App\Http\Controllers\v1\Admin\SpacesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::post('configuration', [AdminController::class, 'saveSettings']);

    Route::apiResource('/spaces', SpacesController::class, ['as' => 'spaces']);
    Route::name('spaces.')->prefix('spaces')->controller(SpacesController::class)->group(function () {
        Route::get('/reservations/{status}', [ReservationController::class, 'all'])->name('all');
        Route::name('reservations.')->prefix('{space}/reservations')->controller(ReservationController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{reservation}', 'show')->name('show');
            Route::put('/{reservation}/status', 'status')->name('status');
        });
    });
});
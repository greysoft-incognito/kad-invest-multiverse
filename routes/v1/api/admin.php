<?php

use App\Http\Controllers\v1\Admin\AdminController;
use App\Http\Controllers\v1\Admin\SpacesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::post('configuration', [AdminController::class, 'saveSettings']);

    Route::name('spaces.')->prefix('spaces')->controller(SpacesController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
    });
});
<?php

use App\Http\Controllers\v1\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::post('configuration', [AdminController::class, 'saveSettings']);
});
<?php

use App\Http\Controllers\v1\Admin\AdminController;
use App\Http\Controllers\v1\Admin\ReservationController;
use App\Http\Controllers\v1\Admin\SpacesController;
use App\Http\Controllers\v1\Manage\FormController as SuFormController;
use App\Http\Controllers\v1\Manage\FormDataController as SuFormDataController;
use App\Http\Controllers\v1\Manage\FormFieldController as SuFormFieldController;
use App\Http\Controllers\v1\Manage\FormInfoController;
use App\Http\Controllers\v1\Manage\UsersController;
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

    Route::apiResource('forms', SuFormController::class);
    Route::apiResource('form-infos/{form}', FormInfoController::class)->parameter('{form}', 'info');
    Route::get('form-fields', [SuFormFieldController::class, 'all'])->name('all');
    Route::post('form-fields/{form}/multiple', [SuFormFieldController::class, 'multiple'])->name('multiple');
    Route::apiResource('form-fields/{form}', SuFormFieldController::class)->parameters(['{form}' => 'field']);
    Route::get('form-data/all', [SuFormDataController::class, 'all'])->name('all');
    Route::get('form-data/stats', [SuFormDataController::class, 'stats'])->name('stats');
    Route::apiResource('form-data/{form}', SuFormDataController::class)->parameters(['{form}' => 'id']);
    Route::apiResource('users', UsersController::class);
});

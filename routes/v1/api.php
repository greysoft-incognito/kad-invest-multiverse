<?php

use App\Http\Controllers\v1\Guest\FormController;
use App\Http\Controllers\v1\Guest\FormDataController;
use App\Http\Controllers\v1\Guest\FormFieldController;
use App\Http\Controllers\v1\Manage\FormController as SuFormController;
use App\Http\Controllers\v1\Manage\FormDataController as SuFormDataController;
use App\Http\Controllers\v1\Manage\FormFieldController as SuFormFieldController;
use App\Http\Controllers\v1\Manage\UsersController;
use Illuminate\Support\Facades\Route;

header('SameSite:  None');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::name('home.')->group(function () {
    // Route::get('/get/settings', 'settings')->name('settings');
    Route::apiResource('get/forms', FormController::class)->only(['index', 'show']);
    Route::get('get/form-fields/form/{form}', [FormFieldController::class, 'form']);
    Route::apiResource('get/form-fields', FormFieldController::class)->parameters(['form-fields' => 'id'])->only(['index', 'show']);
    Route::apiResource('get/form-data/{form_id}', FormDataController::class)->parameters(['{form_id}' => 'id'])->only(['store', 'update', 'show']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::name('admin.')->prefix('admin')->middleware(['admin'])->group(function () {
        Route::apiResource('forms', SuFormController::class)->only(['index', 'show']);
        Route::get('form-fields/form/{form}', [SuFormFieldController::class, 'form']);
        Route::apiResource('get/form-fields', SuFormFieldController::class)->parameters(['form-fields' => 'id']);
        Route::get('form-data/all', [SuFormDataController::class, 'all'])->name('all');
        Route::apiResource('form-data/{form_id}', SuFormDataController::class)->parameters(['{form_id}' => 'id']);
        Route::apiResource('users', UsersController::class);
    });

    Route::name('manage.')->prefix('manage')->group(function () {
        Route::apiResource('forms', SuFormController::class)->only(['index', 'show']);
        Route::get('form-fields/form/{form}', [SuFormFieldController::class, 'form']);
        Route::apiResource('get/form-fields', SuFormFieldController::class)->parameters(['form-fields' => 'id']);
        Route::get('form-data/all', [SuFormDataController::class, 'all'])->name('all');
        Route::apiResource('form-data/{form_id}', SuFormDataController::class)->parameters(['{form_id}' => 'id']);
    });

    Route::get('/playground', function () {
        return (new Shout())->viewable();
    })->name('playground');
});

require __DIR__.'/auth.php';

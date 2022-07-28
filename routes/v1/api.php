<?php

use App\Http\Controllers\v1\Guest\FormController;
use App\Http\Controllers\v1\Guest\FormDataController;
use App\Http\Controllers\v1\Guest\FormFieldController;
use App\Http\Controllers\v1\Manage\FormController as SuFormController;
use App\Http\Controllers\v1\Manage\FormDataController as SuFormDataController;
use App\Http\Controllers\v1\Manage\FormFieldController as SuFormFieldController;
use App\Http\Controllers\v1\Manage\FormInfoController;
use App\Http\Controllers\v1\Manage\UsersController;
use App\Services\AppInfo;
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
    Route::get('/', function () {
        return [
            'Welcome to the GreyMultiverse API v1' => AppInfo::basic(),
        ];
    });
    // Route::get('/get/settings', 'settings')->name('settings');
    Route::apiResource('get/forms', FormController::class)->only(['index', 'show']);
    Route::get('get/form-fields/form/{form}', [FormFieldController::class, 'form']);
    Route::apiResource('get/form-fields', FormFieldController::class)->parameters(['form-fields' => 'id'])->only(['index', 'show']);
    Route::apiResource('get/form-data/{form}', FormDataController::class)->parameters(['{form}' => 'id'])->only(['store', 'update', 'show']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::name('admin.')->prefix('admin')->middleware(['admin'])->group(function () {
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

    Route::name('manage.')->prefix('manage')->group(function () {
        Route::apiResource('forms', SuFormController::class)->only(['index', 'show']);
        Route::get('form-fields', [SuFormFieldController::class, 'all'])->name('all');
        Route::apiResource('form-fields/{form}', SuFormFieldController::class)
            ->parameters(['{form}' => 'field'])->except(['store', 'update', 'destroy']);
        Route::get('form-data/all', [SuFormDataController::class, 'all'])->name('all');
        Route::get('form-data/stats/{form}', [SuFormDataController::class, 'stats'])->name('stats');
        Route::apiResource('form-data/{form}', SuFormDataController::class)->parameters(['{form}' => 'id']);
    });

    Route::get('/playground', function () {
        return (new Shout())->viewable();
    })->name('playground');
});

require __DIR__.'/auth.php';
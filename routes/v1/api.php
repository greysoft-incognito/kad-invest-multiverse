<?php

use App\Http\Controllers\v1\ScanHistoryController;
use App\Http\Controllers\v1\Manage\FormController as SuFormController;
use App\Http\Controllers\v1\Manage\FormDataController as SuFormDataController;
use App\Http\Controllers\v1\Manage\FormFieldController as SuFormFieldController;
use App\Http\Controllers\v1\Manage\FormInfoController;
use App\Http\Controllers\v1\Manage\UsersController;
use App\Services\AppInfo;
use Illuminate\Support\Facades\File;
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
// dd(File::exists(base_path('routes/v1/api.php')));
// Load Extra Routes
if (file_exists(base_path('routes/v1/api'))) {
    array_filter(File::files(base_path('routes/v1/api')), function ($file) {
        if ($file->getExtension() === 'php') {
            require_once $file->getPathName();
        }
    });
}

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

    Route::name('user.')->prefix('user')->group(function () {
        // Load user's scan history
        Route::get('scan-history', [ScanHistoryController::class, 'index'])->name('scan-history');
        // schow scan history
        Route::get('scan-history/{scan}', [ScanHistoryController::class, 'show'])->name('scan-history.show');
    });

    Route::name('manage.')->prefix('manage')->group(function () {
        Route::apiResource('forms', SuFormController::class)->only(['index', 'show']);
        Route::get('form-fields', [SuFormFieldController::class, 'all'])->name('all');
        Route::apiResource('form-fields/{form}', SuFormFieldController::class)
            ->parameters(['{form}' => 'field'])->except(['store', 'update', 'destroy']);
        Route::get('form-data/all', [SuFormDataController::class, 'all'])->name('all');
        Route::post('form-data/qr', [SuFormDataController::class, 'decodeQr'])->name('decode.qr');
        Route::get('form-data/stats/{form}', [SuFormDataController::class, 'stats'])->name('stats');
        Route::apiResource('form-data/{form}', SuFormDataController::class)->parameters(['{form}' => 'id']);
    });

    Route::get('/playground', function () {
        return (new Shout())->viewable();
    })->name('playground');
});

require __DIR__.'/auth.php';
<?php

use App\Http\Controllers\v1\Admin\ReservationController;
use App\Http\Controllers\v1\Manage\FormController as SuFormController;
use App\Http\Controllers\v1\Manage\FormDataController as SuFormDataController;
use App\Http\Controllers\v1\Manage\FormFieldController as SuFormFieldController;
use App\Http\Controllers\v1\ScanHistoryController;
use Illuminate\Support\Facades\Broadcast;
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

// Broadcast::routes(['middleware' => ['auth:sanctum']]);

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
        Route::post('qr/form-data', [SuFormDataController::class, 'decodeQr'])->name('decode.qr');
        Route::post('qr/reservation-data', [ReservationController::class, 'decodeQr'])->name('decode.reservation.qr');
        Route::get('form-data/stats/{form}', [SuFormDataController::class, 'stats'])->name('stats');
        Route::apiResource('form-data/{form}', SuFormDataController::class)->parameters(['{form}' => 'id']);
    });

    Route::get('/playground', function () {
        return (new Shout())->viewable();
    })->name('playground');
});

require __DIR__.'/auth.php';

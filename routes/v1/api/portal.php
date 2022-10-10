<?php

use App\Http\Controllers\v1\Admin\Portal\BlogController as AdminBlogController;
use App\Http\Controllers\v1\Admin\Portal\CardController;
use App\Http\Controllers\v1\Admin\Portal\PortalController as AdminPortalController;
use App\Http\Controllers\v1\Admin\Portal\PortalPageController as AdminPortalPageController;
use App\Http\Controllers\v1\Admin\Portal\SectionController;
use App\Http\Controllers\v1\Admin\Portal\SlidersController;
use App\Http\Controllers\v1\Portal\BlogController;
use App\Http\Controllers\v1\Portal\PortalController;
use App\Http\Controllers\v1\Portal\PortalPageController;
use App\Http\Controllers\v1\Portal\PortalUserController;
use App\Models\v1\Portal\LearningPath;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::apiResource('/portals', AdminPortalController::class);
    Route::name('portals.')->prefix('portals/{portal}')->group(function () {
        Route::apiResource('/blogs', AdminBlogController::class);
        Route::apiResource('/cards', CardController::class);
        Route::apiResource('/pages', AdminPortalPageController::class);
        Route::apiResource('/sections', SectionController::class);
        Route::apiResource('/sliders', SlidersController::class);
    });
});

Route::apiResource('/portals', PortalController::class)->only(['index', 'show']);
Route::name('portals.')->prefix('portals/{portal}')->group(function () {
    Route::apiResource('/blogs', BlogController::class)->only(['index', 'show']);
    Route::get('/pages/index', [PortalPageController::class, 'showIndex'])->name('pages.show.index');
    Route::apiResource('/pages', PortalPageController::class)->only(['index', 'show']);
    Route::apiResource('/learning/paths', LearningPath::class, ['as' => 's'])->only(['index', 'show']);

    Route::controller(PortalUserController::class)->group(function () {
        Route::post('/register', 'register')->name('register');
        Route::post('/login', 'login')->name('login');
        Route::get('/user', 'show')->name('user.show')->middleware('auth:sanctum');
        // Route::post('/user', 'update')->name('user.update');
        // Route::post('/user/password', 'updatePassword')->name('user.update.password');
    });
});
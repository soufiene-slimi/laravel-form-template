<?php

use Illuminate\Support\Facades\Route;
use SoufieneSlimi\LaravelFormTemplate\Http\Controllers\AnalyticsController;

Route::group(['middleware' => config('laravel-form-template.middleware', ['web', 'auth'])], function () {
    Route::get('/template-analytics', [AnalyticsController::class, 'index'])->name('template.analytics.index');
    Route::get('/template-analytics/{id}', [AnalyticsController::class, 'show'])->name('template.analytics.show');
});


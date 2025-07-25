<?php

use App\Http\Controllers\CountryController;
use App\Http\Controllers\ScraperController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::get('countries/options', [CountryController::class, 'options']);
    Route::apiResource('countries', CountryController::class);

    Route::get('stores/options', [StoreController::class, 'options']);
    Route::apiResource('stores', StoreController::class);

    Route::post('scrapers/scrape/{scraperId}', [ScraperController::class, 'scrape']);
    Route::post('scrapers/set-active/{scraper}', [ScraperController::class, 'setActive']);
    Route::apiResource('scrapers', ScraperController::class);
});

<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\FamilyController;
use App\Http\Controllers\Api\PackagingController;
use App\Http\Controllers\Api\PriceHistoryController;
use App\Http\Controllers\Api\SubFamilyController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\VatController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Families
    Route::apiResource('families', FamilyController::class);

    // Sub Families
    Route::apiResource('sub-families', SubFamilyController::class);

    // Units
    Route::apiResource('units', UnitController::class);

    // Packagings
    Route::apiResource('packagings', PackagingController::class);

    // VATs
    Route::apiResource('vats', VatController::class);

    // Articles
    Route::apiResource('articles', ArticleController::class);
    Route::get('articles/search', [ArticleController::class, 'search']);

    // Suppliers
    Route::apiResource('suppliers', SupplierController::class);

    // Price Histories
    Route::apiResource('price-histories', PriceHistoryController::class);
    Route::get('price-histories/current', [PriceHistoryController::class, 'currentPrices']);
    Route::get('articles/{article}/price-histories', [PriceHistoryController::class, 'byArticle']);
});

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\ArticleController;
use App\Http\Controllers\Web\FamilyController;
use App\Http\Controllers\Web\PackagingController;
use App\Http\Controllers\Web\PriceHistoryController;
use App\Http\Controllers\Web\SubFamilyController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\UnitController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\VatController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Families
    Route::resource('families', FamilyController::class);

    // Sub Families
    Route::resource('sub-familles', SubFamilyController::class);

    // Units
    Route::resource('units', UnitController::class);

    // Packagings
    Route::resource('packagings', PackagingController::class);

    // VATs
    Route::resource('vats', VatController::class);

    // Suppliers
    Route::resource('suppliers', SupplierController::class);

    // Articles
    Route::resource('articles', ArticleController::class);
    Route::post('articles/{article}/add-price', [ArticleController::class, 'addPrice'])->name('articles.add-price');
    Route::get('articles/{article}/price-histories', [ArticleController::class, 'priceHistories'])->name('articles.price-histories');
    Route::post('articles/{article}/set-current-supplier', [ArticleController::class, 'setCurrentSupplier'])->name('articles.set-current-supplier');

    // Admin only routes
    Route::middleware(['admin'])->group(function () {
        // Users
        Route::resource('users', UserController::class);
    });
});

// Require auth routes
require __DIR__.'/auth.php';

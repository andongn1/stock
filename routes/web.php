<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\ArticleController;
use App\Http\Controllers\Web\EntryController;
use App\Http\Controllers\Web\FamilyController;
use App\Http\Controllers\Web\InventoryController;
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
    Route::post('articles/{article}/update-selling-price', [ArticleController::class, 'updateSellingPrice'])->name('articles.update-selling-price');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventories.index');
    Route::get('/inventory/search', [InventoryController::class, 'search'])->name('inventories.search');
    Route::get('/inventory/article/{article}', [InventoryController::class, 'create'])->name('inventories.create');
    Route::post('/inventory/article/{article}', [InventoryController::class, 'store'])->name('inventories.store');
    Route::get('/inventory/article/{article}/confirm-duplicate', [InventoryController::class, 'confirmDuplicate'])->name('inventories.confirm-duplicate');
    Route::post('/inventory/article/{article}/process-duplicate', [InventoryController::class, 'processDuplicate'])->name('inventories.process-duplicate');
    Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventories.destroy');

    // Entries (Stock Entries)
    Route::get('/entries', [EntryController::class, 'index'])->name('entries.index');
    Route::get('/entries/search', [EntryController::class, 'search'])->name('entries.search');
    Route::get('/entries/article/{article}', [EntryController::class, 'create'])->name('entries.create');
    Route::post('/entries/article/{article}', [EntryController::class, 'store'])->name('entries.store');
    Route::delete('/entries/{entry}', [EntryController::class, 'destroy'])->name('entries.destroy');

    // Admin only routes
    Route::middleware(['admin'])->group(function () {
        // Users
        Route::resource('users', UserController::class);
    });
});

// Require auth routes
require __DIR__.'/auth.php';

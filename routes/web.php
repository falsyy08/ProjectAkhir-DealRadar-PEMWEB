<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use App\Services\GameDealService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request, GameDealService $dealService) {
    $sections = $dealService->getHomepageSections();
    $hasFilter = $request->filled('q') || $request->filled('genre') || $request->filled('store');

    $searchResults = $hasFilter
        ? $dealService->searchDeals($request->q ?? '', $request->store, $request->genre)
        : [];

    return view('welcome', array_merge($sections, [
        'searchResults' => $searchResults,
    ]));
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [WishlistController::class, 'index'])->name('dashboard');
    Route::post('/wishlists', [WishlistController::class, 'store'])->name('wishlists.store');
    Route::put('/wishlists/{wishlist}', [WishlistController::class, 'update'])->name('wishlists.update');
    Route::delete('/wishlists/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlists.destroy');
    Route::post('/wishlists/import-excel', [WishlistController::class, 'importExcel'])->name('wishlists.import');
    Route::get('/wishlists/export-excel', [WishlistController::class, 'exportExcel'])->name('wishlists.export.excel');
    Route::get('/wishlists/export-pdf', [WishlistController::class, 'exportPdf'])->name('wishlists.export.pdf');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
<?php

// routes/api.php
use App\Models\Wishlist;
use App\Services\GameDealService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/v1/deals/check-budget', function (Request $request, GameDealService $service) {
    $targetIdr = (float) $request->query('target_idr', 150000);
    $deals = collect($service->searchDeals($request->query('title', '')))
        ->filter(fn ($deal) => $deal['sale_price_idr'] <= $targetIdr)
        ->values();

    return response()->json([
        'kurs_idr' => $service->getUsdToIdrRate(),
        'target_budget_idr' => $targetIdr,
        'total_matched' => $deals->count(),
        'data' => $deals,
    ]);
});
<?php

namespace App\Imports;

use App\Models\PriceLog;
use App\Models\Wishlist;
use App\Services\GameDealService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WishlistImport implements ToModel, WithHeadingRow
{
    public function model(array $row): Model|array|null
    {
        $title = trim((string) ($row['judul_game'] ?? ''));
        if ($title === '') {
            return null;
        }

        $dealService = app(GameDealService::class);
        $rate = $dealService->getUsdToIdrRate();
        $deals = $dealService->searchDeals($title);
        $bestDeal = $deals[0] ?? null;

        $normalUsd = (float) ($bestDeal['normal_price_usd'] ?? 0);
        $saleUsd = (float) ($bestDeal['sale_price_usd'] ?? 0);
        $gameId = (string) ($bestDeal['gameID'] ?? '0');
        $dealId = $bestDeal['dealID'] ?? null;
        $cheapestUsd = $gameId !== '0' ? $dealService->getCheapestEverUsd($gameId) : $saleUsd;

        $rawTarget = $row['target_budget_idr'] ?? null;
        $targetIdr = is_numeric($rawTarget) && (float) $rawTarget > 0
            ? (float) $rawTarget
            : round($saleUsd * $rate);

        $currentIdr = $saleUsd * $rate;
        $storePref = trim((string) ($row['toko_preferensi'] ?? $row['toko'] ?? ''));
        $specsNote = $row['catatan_singkat'] ?? $row['catatan_spesifikasi'] ?? null;

        $wishlist = Wishlist::create([
            'user_id' => Auth::id(),
            'cheapshark_game_id' => $gameId,
            'deal_id' => $dealId,
            'title' => $bestDeal['title'] ?? $title,
            'thumb_url' => $bestDeal['thumb'] ?? null,
            'store_name' => $storePref !== '' ? $storePref : ($bestDeal['store_name'] ?? 'Steam'),
            'normal_price_usd' => $normalUsd,
            'sale_price_usd' => $saleUsd,
            'cheapest_ever_usd' => $cheapestUsd > 0 ? $cheapestUsd : $saleUsd,
            'target_price_idr' => $targetIdr,
            'status' => ($currentIdr > 0 && $currentIdr <= $targetIdr) ? 'buy_now' : 'wait',
            'specs_note' => ($specsNote !== '-' && $specsNote !== '') ? $specsNote : null,
        ]);

        PriceLog::create([
            'wishlist_id' => $wishlist->id,
            'recorded_price_usd' => $saleUsd,
            'exchange_rate_idr' => $rate,
        ]);

        return $wishlist;
    }
}
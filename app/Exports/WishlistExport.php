<?php

namespace App\Exports;

use App\Models\Wishlist;
use App\Services\GameDealService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WishlistExport implements FromCollection, WithHeadings, WithMapping
{
    protected float $rate;

    public function __construct()
    {
        $this->rate = app(GameDealService::class)->getUsdToIdrRate();
    }

    public function collection(): Collection
    {
        return Wishlist::where('user_id', Auth::id())->get();
    }

    public function headings(): array
    {
        return [
            'Judul Game',
            'Toko',
            'Harga Normal (USD)',
            'Harga Promo (USD)',
            'Estimasi Promo (IDR)',
            'Historical Low (USD)',
            'Target Budget (IDR)',
            'Status',
            'Catatan Spesifikasi',
        ];
    }

    public function map($wishlist): array
    {
        return [
            $wishlist->title,
            $wishlist->store_name,
            $wishlist->normal_price_usd,
            $wishlist->sale_price_usd,
            round($wishlist->sale_price_usd * $this->rate),
            $wishlist->cheapest_ever_usd,
            $wishlist->target_price_idr,
            strtoupper($wishlist->status),
            strip_tags($wishlist->specs_note ?? '-'),
        ];
    }
}
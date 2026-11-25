<?php

namespace App\Http\Controllers;

use App\Exports\WishlistExport;
use App\Imports\WishlistImport;
use App\Models\PriceLog;
use App\Models\Wishlist;
use App\Services\GameDealService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class WishlistController extends Controller
{
    public function index(Request $request, GameDealService $dealService)
    {
        $rate = $dealService->getUsdToIdrRate();
        $genres = $dealService->getGenres();
        $wishlists = Wishlist::where('user_id', Auth::id())->latest()->get();

        $hasFilter = $request->filled('q') || $request->filled('genre') || $request->filled('store');
        $searchResults = $hasFilter
            ? $dealService->searchDeals($request->q ?? '', $request->store, $request->genre)
            : [];

        return view('dashboard', compact('wishlists', 'searchResults', 'rate', 'genres'));
    }

    public function store(Request $request, GameDealService $dealService)
    {
        $request->validate([
            'cheapshark_game_id' => 'required|string',
            'title' => 'required|string',
            'normal_price_usd' => 'required|numeric',
            'sale_price_usd' => 'required|numeric',
            'target_price_idr' => 'required|numeric|min:0',
        ]);

        $rate = $dealService->getUsdToIdrRate();
        $cheapestUsd = $dealService->getCheapestEverUsd($request->cheapshark_game_id);
        $currentIdr = $request->sale_price_usd * $rate;
        $status = ($currentIdr <= $request->target_price_idr) ? 'buy_now' : 'wait';

        $wishlist = Wishlist::create([
            'user_id' => Auth::id(),
            'cheapshark_game_id' => $request->cheapshark_game_id,
            'deal_id' => $request->deal_id,
            'title' => $request->title,
            'thumb_url' => $request->thumb_url,
            'store_name' => $request->store_name ?? 'Steam',
            'normal_price_usd' => $request->normal_price_usd,
            'sale_price_usd' => $request->sale_price_usd,
            'cheapest_ever_usd' => $cheapestUsd > 0 ? $cheapestUsd : $request->sale_price_usd,
            'target_price_idr' => $request->target_price_idr,
            'status' => $status,
            'specs_note' => $request->specs_note,
        ]);

        PriceLog::create([
            'wishlist_id' => $wishlist->id,
            'recorded_price_usd' => $request->sale_price_usd,
            'exchange_rate_idr' => $rate,
        ]);

        return back()->with('success', 'Game berhasil ditambahkan ke Wishlist!');
    }

    public function update(Request $request, Wishlist $wishlist, GameDealService $dealService)
    {
        abort_if($wishlist->user_id !== Auth::id(), 403);

        $rate = $dealService->getUsdToIdrRate();
        $currentIdr = $wishlist->sale_price_usd * $rate;
        $status = $request->status ?? (($currentIdr <= $request->target_price_idr) ? 'buy_now' : 'wait');

        $wishlist->update([
            'target_price_idr' => $request->target_price_idr,
            'status' => $status,
            'specs_note' => $request->specs_note,
        ]);

        return back()->with('success', 'Target budget & catatan spesifikasi diperbarui!');
    }

    public function destroy(Wishlist $wishlist)
    {
        abort_if($wishlist->user_id !== Auth::id(), 403);
        $wishlist->delete();

        return back()->with('success', 'Game dihapus dari Wishlist.');
    }

    public function exportExcel()
    {
        return Excel::download(new WishlistExport, 'DealRadar_Wishlist_Budget.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate(['file_excel' => 'required|mimes:xlsx,xls,csv']);
        Excel::import(new WishlistImport, $request->file('file_excel'));

        return back()->with('success', 'Daftar game dari Excel berhasil diimpor & disinkronkan!');
    }

    public function exportPdf(GameDealService $dealService)
    {
        $rate = $dealService->getUsdToIdrRate();
        $wishlists = Wishlist::where('user_id', Auth::id())->get();
        $buyNowList = $wishlists->where('status', 'buy_now');
        $totalEstimatedIdr = $buyNowList->sum(fn ($item) => $item->sale_price_usd * $rate);

        $pdf = Pdf::loadView('pdf.budget_plan', compact('wishlists', 'buyNowList', 'rate', 'totalEstimatedIdr'));
        return $pdf->download('Gaming_Wishlist_Budget_Plan.pdf');
    }
}
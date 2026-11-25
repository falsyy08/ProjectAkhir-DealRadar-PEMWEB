<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DealRadar — Multi-Store PC Game Deal Aggregator & Smart Budget Planner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bevel-card {
            clip-path: polygon(0 0, calc(100% - 26px) 0, 100% 26px, 100% 100%, 26px 100%, 0 calc(100% - 26px));
        }
        .bevel-thumb {
            clip-path: polygon(0 0, calc(100% - 14px) 0, 100% 14px, 100% 100%, 14px 100%, 0 calc(100% - 14px));
        }
    </style>
</head>
<body class="bg-[#070709] text-white font-sans antialiased selection:bg-orange-500 selection:text-white">

    <!-- ==================== BAGIAN ATAS: HERO & HOT GAME DISCOUNT ==================== -->
    <div class="relative overflow-hidden bg-[#070709] pb-16">
        <div class="pointer-events-none absolute -top-28 left-1/2 -translate-x-1/2 w-[780px] h-[360px] rounded-full bg-gradient-to-tr from-orange-600/45 via-red-600/30 to-amber-500/20 blur-3xl"></div>

        <!-- Navbar / Header -->
        <header class="relative z-10 max-w-6xl mx-auto px-6 pt-8 pb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <a href="{{ route('home') }}" class="text-4xl md:text-5xl font-black tracking-tight uppercase text-white">
                    DEAL <span class="text-orange-500">RADAR</span>
                </a>
                <p class="text-xs md:text-sm tracking-widest text-gray-300 uppercase mt-1 max-w-md">
                    Multi-Store PC Game Deal Aggregator & Smart Budget Planner
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs text-amber-300 font-medium">
                    Kurs Hari Ini: 1 USD = Rp {{ number_format($rate ?? 16000, 0, ',', '.') }}
                </span>

                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-orange-600 hover:bg-orange-500 text-sm font-bold transition shadow-lg shadow-orange-600/30">
                        Dashboard Wishlist
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-sm font-semibold transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-orange-600 hover:bg-orange-500 text-sm font-bold transition">
                        Daftar
                    </a>
                @endauth
            </div>
        </header>

        @if(session('success'))
        <div class="relative z-10 max-w-4xl mx-auto px-6 mb-4">
            <div class="p-3 rounded-xl bg-emerald-600/20 border border-emerald-500/40 text-emerald-300 text-xs font-semibold text-center">
                {{ session('success') }}
            </div>
        </div>
        @endif

        <!-- Search Bar, Filter Tipe Game & Filter Toko -->
        <section class="relative z-10 max-w-5xl mx-auto px-6 mt-4 mb-6">
            <form action="{{ route('home') }}" method="GET" class="flex flex-col md:flex-row gap-2 bg-white/10 backdrop-blur-md p-2 rounded-xl border border-white/15">
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari judul game PC (atau kosongkan jika pilih tipe game)..."
                       class="flex-1 bg-transparent border-0 text-white placeholder-gray-400 focus:ring-0 text-sm px-3 py-2">

                <select name="genre" class="bg-[#141419] border border-white/15 text-gray-200 text-sm rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                    <option value="">Semua Tipe Game</option>
                    @foreach($genres ?? [] as $slug => $label)
                        <option value="{{ $slug }}" {{ request('genre') == $slug ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="store" class="bg-[#141419] border border-white/15 text-gray-200 text-sm rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                    <option value="">Semua Toko Resmi</option>
                    <option value="1" {{ request('store') == '1' ? 'selected' : '' }}>Steam</option>
                    <option value="25" {{ request('store') == '25' ? 'selected' : '' }}>Epic Games Store</option>
                    <option value="7" {{ request('store') == '7' ? 'selected' : '' }}>GOG</option>
                    <option value="11" {{ request('store') == '11' ? 'selected' : '' }}>Humble Store</option>
                    <option value="15" {{ request('store') == '15' ? 'selected' : '' }}>Fanatical</option>
                </select>

                <button type="submit" class="px-6 py-2.5 bg-orange-600 hover:bg-orange-500 text-white font-bold text-sm rounded-lg transition cursor-pointer">
                    Cari Promo
                </button>
            </form>

            <!-- Tombol Cepat Kategori Tipe Game -->
            <div class="flex flex-wrap items-center justify-center gap-2 mt-3">
                <span class="text-[11px] text-gray-400 mr-1">Tipe Populer:</span>
                @foreach($genres ?? [] as $slug => $label)
                    <a href="{{ route('home', ['genre' => $slug]) }}"
                       class="px-3 py-1 rounded-full text-xs font-semibold border transition {{ request('genre') === $slug ? 'bg-orange-600 border-orange-500 text-white' : 'bg-white/5 border-white/10 text-gray-300 hover:border-orange-500/50 hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </section>

        <!-- Keterangan Sumber Harga & Konversi Kurs -->
        <div class="relative z-10 max-w-5xl mx-auto px-6 mb-10">
            <div class="flex items-start sm:items-center gap-2.5 px-4 py-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-xs text-amber-200">
                <span class="text-amber-400 font-bold text-sm leading-none">ⓘ</span>
                <p>
                    Seluruh harga normal maupun promo bersumber dari katalog global <strong>CheapShark (USD)</strong> dan telah dikonversi otomatis ke Rupiah menggunakan kurs harian <strong>Frankfurter API (1 USD = Rp {{ number_format($rate ?? 16000, 0, ',', '.') }})</strong>.
                </p>
            </div>
        </div>

        <!-- Hasil Pencarian Live (Muncul jika user mencari judul, genre, atau toko) -->
        @if(request()->filled('q') || request()->filled('genre') || request()->filled('store'))
        <section class="relative z-10 max-w-6xl mx-auto px-6 mb-14">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-orange-400">
                    Hasil Pencarian
                    @if(request()->filled('q')) "{{ request('q') }}" @endif
                    @if(request()->filled('genre') && isset($genres[request('genre')])) • Tipe: {{ $genres[request('genre')] }} @endif
                    ({{ count($searchResults ?? []) }} penawaran ditemukan)
                </h2>
                <a href="{{ route('home') }}" class="text-xs text-gray-400 hover:text-white">Reset Pencarian ✕</a>
            </div>

            @if(count($searchResults ?? []) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($searchResults as $deal)
                @php
                    $normalIdr = $deal['normal_price_idr'] ?? round($deal['normal_price_usd'] * ($rate ?? 16000));
                @endphp
                <div class="bg-[#121217] border border-white/10 rounded-xl p-4 flex flex-col justify-between hover:border-orange-500/50 transition">
                    <div class="flex gap-3">
                        <img src="{{ $deal['thumb'] }}" alt="{{ $deal['title'] }}" class="w-24 h-14 object-cover rounded-lg bg-gray-800 shrink-0">
                        <div class="min-w-0">
                            <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-orange-600/20 text-orange-400 border border-orange-500/30">
                                {{ $deal['store_name'] }} • -{{ $deal['savings'] }}%
                            </span>
                            <h3 class="font-bold text-sm text-white truncate mt-1" title="{{ $deal['title'] }}">
                                {{ $deal['title'] }}
                            </h3>
                            <div class="mt-1 flex flex-col">
                                @if($normalIdr > $deal['sale_price_idr'])
                                <span class="text-xs text-gray-400 line-through">
                                    Rp {{ number_format($normalIdr, 0, ',', '.') }}
                                </span>
                                @endif
                                <span class="text-base font-extrabold text-emerald-400">
                                    Rp {{ number_format($deal['sale_price_idr'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-white/10 flex items-center gap-2">
                        <a href="{{ $deal['redirect_url'] }}" target="_blank" rel="noopener noreferrer"
                           class="flex-1 text-center py-2 px-3 rounded-lg bg-orange-600 hover:bg-orange-500 text-xs font-bold text-white transition">
                            Beli di {{ $deal['store_name'] }} ↗
                        </a>

                        @auth
                        <form action="{{ route('wishlists.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="cheapshark_game_id" value="{{ $deal['gameID'] }}">
                            <input type="hidden" name="deal_id" value="{{ $deal['dealID'] }}">
                            <input type="hidden" name="title" value="{{ $deal['title'] }}">
                            <input type="hidden" name="thumb_url" value="{{ $deal['thumb'] }}">
                            <input type="hidden" name="store_name" value="{{ $deal['store_name'] }}">
                            <input type="hidden" name="normal_price_usd" value="{{ $deal['normal_price_usd'] }}">
                            <input type="hidden" name="sale_price_usd" value="{{ $deal['sale_price_usd'] }}">
                            <input type="hidden" name="target_price_idr" value="{{ $deal['sale_price_idr'] }}">
                            <button type="submit" class="py-2 px-3 rounded-lg bg-white/10 hover:bg-white/20 text-xs font-semibold text-white transition cursor-pointer">
                                + Wishlist
                            </button>
                        </form>
                        @else
                        <a href="{{ route('login') }}" class="py-2 px-3 rounded-lg bg-white/10 hover:bg-white/20 text-xs font-semibold text-gray-300">
                            + Wishlist
                        </a>
                        @endauth
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-6 rounded-xl bg-white/5 text-center text-sm text-gray-400">
                Tidak ada penawaran diskon ditemukan untuk kriteria pencarian tersebut.
            </div>
            @endif
        </section>
        @endif

        <!-- Section: HOT GAME DISCOUNT -->
        <section class="relative z-10 max-w-6xl mx-auto px-6">
            <div class="text-center mb-8">
                <h2 class="text-xl md:text-2xl font-extrabold tracking-wider uppercase">HOT GAME DISCOUNT</h2>
                <div class="mt-2 mx-auto w-full max-w-2xl h-[2px] bg-orange-900/50 relative flex justify-center">
                    <span class="w-28 h-1 bg-orange-500 -mt-[1px] rounded-full"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-7">
                @forelse($featured ?? [] as $item)
                @php
                    $itemNormalIdr = $item['normal_price_idr'] ?? round($item['normal_price_usd'] * ($rate ?? 16000));
                @endphp
                <div class="group flex flex-col items-center">
                    <div class="w-full bg-gradient-to-br from-orange-500/40 to-red-600/20 p-[2px] bevel-card transition transform group-hover:-translate-y-1">
                        <div class="bg-[#13131a] bevel-card overflow-hidden">
                            <div class="relative h-44 bg-gray-900">
                                <img src="{{ $item['thumb'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition duration-300">
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded bg-orange-600 text-xs font-black tracking-wide shadow">
                                    -{{ $item['savings'] }}%
                                </span>
                                <span class="absolute bottom-3 right-3 px-2.5 py-1 rounded bg-black/80 text-xs font-semibold text-gray-200">
                                    {{ $item['store_name'] }}
                                </span>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-base text-white truncate" title="{{ $item['title'] }}">{{ $item['title'] }}</h3>
                                <div class="mt-2 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-400 line-through">Rp {{ number_format($itemNormalIdr, 0, ',', '.') }}</p>
                                        <p class="text-lg font-black text-emerald-400">Rp {{ number_format($item['sale_price_idr'], 0, ',', '.') }}</p>
                                    </div>
                                    <a href="{{ $item['redirect_url'] }}" target="_blank" rel="noopener noreferrer"
                                       class="px-3.5 py-2 rounded-lg bg-orange-600 hover:bg-orange-500 text-xs font-bold text-white transition">
                                        Beli di {{ $item['store_name'] }} ↗
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-300 font-medium">
                        Diskon {{ $item['savings'] }}% • Metacritic: {{ $item['metacritic'] }}
                    </p>
                </div>
                @empty
                <div class="col-span-3 text-center text-sm text-gray-400 py-8">
                    Sedang memuat katalog promo dari CheapShark API... (Pastikan koneksi internet aktif)
                </div>
                @endforelse
            </div>
        </section>
    </div>

    <!-- FLOATING CART BUTTON -->
    <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
       title="Buka Dashboard Wishlist & Budget Planner"
       class="fixed right-0 top-1/2 -translate-y-1/2 z-50 flex items-center gap-2 bg-gray-200 hover:bg-white text-gray-900 pl-4 pr-3 py-3 rounded-l-full shadow-2xl border-2 border-orange-500 transition transform hover:scale-105">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <span class="hidden sm:inline text-xs font-extrabold uppercase tracking-tight">Wishlist</span>
    </a>

    <!-- ==================== BAGIAN BAWAH: 3 KOLOM RANKING (HOT DEAL, TRENDING, BEST DEAL) ==================== -->
    <div class="bg-gradient-to-b from-[#160404] via-[#3d0808] to-[#1f0303] border-t border-red-900/40 py-14">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-3 gap-10">

            @php
                $columns = [
                    ['title' => 'HOT DEAL', 'items' => $hot_deals ?? []],
                    ['title' => 'TRENDING GAMES', 'items' => $trending ?? []],
                    ['title' => 'BEST DEAL', 'items' => $best_deals ?? []],
                ];
            @endphp

            @foreach($columns as $col)
            <div>
                <h3 class="text-center text-lg font-black tracking-wider uppercase text-white mb-6 pb-2 border-b border-white/15">
                    {{ $col['title'] }}
                </h3>

                <div class="space-y-4">
                    @foreach($col['items'] as $index => $game)
                    @php
                        $gameNormalIdr = $game['normal_price_idr'] ?? round($game['normal_price_usd'] * ($rate ?? 16000));
                    @endphp
                    <div class="flex items-center gap-3 bg-black/30 hover:bg-black/50 p-2.5 rounded-xl border border-white/10 transition">
                        <span class="w-6 text-lg font-black text-white/90 text-center shrink-0">
                            {{ $index + 1 }}.
                        </span>

                        <img src="{{ $game['thumb'] }}" alt="{{ $game['title'] }}"
                             class="w-20 h-12 object-cover bg-gray-800 bevel-thumb shrink-0">

                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-bold text-white truncate" title="{{ $game['title'] }}">
                                {{ $game['title'] }}
                            </h4>
                            <div class="mt-0.5">
                                @if($gameNormalIdr > $game['sale_price_idr'])
                                    <span class="text-[11px] text-gray-400 line-through mr-1">
                                        Rp {{ number_format($gameNormalIdr, 0, ',', '.') }}
                                    </span>
                                @endif
                                <span class="text-xs font-extrabold text-emerald-400">
                                    Rp {{ number_format($game['sale_price_idr'], 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] font-semibold text-orange-400">(-{{ $game['savings'] }}%)</span>
                            </div>
                            <div class="mt-1 flex items-center gap-2">
                                <a href="{{ $game['redirect_url'] }}" target="_blank" rel="noopener noreferrer"
                                   class="text-[11px] font-bold text-amber-300 hover:text-amber-200 underline">
                                    Beli di {{ $game['store_name'] }} ↗
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>

        <footer class="max-w-6xl mx-auto px-6 mt-14 pt-6 border-t border-white/10 text-center text-xs text-gray-400 space-y-1">
            <p><strong>DealRadar</strong> • Multi-Store PC Game Deal Aggregator & Smart Budget Planner</p>
            <p class="text-gray-500">
                Catatan: Harga asli menggunakan mata uang USD (CheapShark API) yang dikonversi ke IDR secara real-time berdasarkan kurs Frankfurter API.
            </p>
        </footer>
    </div>

</body>
</html>
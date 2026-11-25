<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DealRadar') }} — Autentikasi Akun</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Potongan sudut miring (beveled card) serasi dengan halaman utama */
        .bevel-card {
            clip-path: polygon(0 0, calc(100% - 26px) 0, 100% 26px, 100% 100%, 26px 100%, 0 calc(100% - 26px));
        }
    </style>
</head>
<body class="bg-[#070709] text-white font-sans antialiased selection:bg-orange-500 selection:text-white">
    <div class="relative min-h-screen flex flex-col justify-center items-center px-4 py-10 overflow-hidden bg-[#070709]">
        <!-- Efek Cahaya Gradien Oranye-Merah di Atas -->
        <div class="pointer-events-none absolute -top-28 left-1/2 -translate-x-1/2 w-[780px] h-[360px] rounded-full bg-gradient-to-tr from-orange-600/45 via-red-600/30 to-amber-500/20 blur-3xl"></div>

        <!-- Efek Pendaran Halus di Bawah -->
        <div class="pointer-events-none absolute -bottom-32 left-1/2 -translate-x-1/2 w-[620px] h-[240px] rounded-full bg-gradient-to-tr from-red-900/30 via-orange-700/15 to-transparent blur-3xl"></div>

        <!-- Header Brand Logo -->
        <div class="relative z-10 text-center mb-7">
            <a href="{{ route('home') }}" class="inline-block text-4xl sm:text-5xl font-black tracking-tight uppercase text-white">
                DEAL <span class="text-orange-500">RADAR</span>
            </a>
            <p class="text-[11px] sm:text-xs tracking-widest text-gray-300 uppercase mt-1.5">
                Multi-Store PC Game Deal Aggregator & Smart Budget Planner
            </p>
        </div>

        <!-- Kartu Form dengan Bingkai Gradien & Sudut Bevel -->
        <div class="relative z-10 w-full max-w-md bg-gradient-to-br from-orange-500/45 via-red-600/25 to-white/10 p-[2px] bevel-card shadow-2xl shadow-orange-950/40">
            <div class="bg-[#121217] bevel-card px-7 py-8 sm:px-8 sm:py-9">
                {{ $slot }}
            </div>
        </div>

        <!-- Tautan Kembali ke Beranda -->
        <div class="relative z-10 mt-6 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400 hover:text-orange-400 transition">
                <span>←</span> Kembali ke Beranda Promo
            </a>
        </div>
    </div>
</body>
</html>
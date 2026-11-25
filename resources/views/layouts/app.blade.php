<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DealRadar') }} — Smart Budget Dashboard</title>

        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

        <!-- Tailwind CSS & Alpine.js CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- jQuery, DataTables, TinyMCE WYSIWYG & Chart.js -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <style>
            /* Penyesuaian warna DataTables agar serasi dengan tema gelap */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                color: #d1d5db !important;
                font-size: 0.75rem;
                margin-bottom: 0.75rem;
            }
            .dataTables_wrapper .dataTables_filter input,
            .dataTables_wrapper .dataTables_length select {
                background-color: #18181f !important;
                border: 1px solid rgba(255, 255, 255, 0.15) !important;
                color: #ffffff !important;
                border-radius: 0.5rem;
                padding: 0.35rem 0.65rem;
            }
            table.dataTable tbody tr {
                background-color: #121217 !important;
                color: #f3f4f6 !important;
            }
            table.dataTable tbody tr:hover {
                background-color: #1c1c24 !important;
            }
            table.dataTable tbody td {
                border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                color: #d1d5db !important;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background: #ea580c !important;
                border-color: #ea580c !important;
                color: #ffffff !important;
                border-radius: 0.375rem;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#070709] text-white selection:bg-orange-500 selection:text-white">
        <div class="min-h-screen bg-[#070709] relative overflow-x-hidden">
            <!-- Efek Pendaran Gradien Oranye-Merah di Atas -->
            <div class="pointer-events-none absolute -top-24 left-1/2 -translate-x-1/2 w-[780px] h-[280px] rounded-full bg-gradient-to-tr from-orange-600/30 via-red-600/20 to-amber-500/10 blur-3xl"></div>

            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="relative z-10 bg-[#101015]/80 backdrop-blur-md border-b border-white/10">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="relative z-10">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
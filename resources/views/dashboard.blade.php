<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-black text-xl tracking-wide uppercase text-white">
                    WISHLIST & <span class="text-orange-500">SMART BUDGET</span> DASHBOARD
                </h2>
                <p class="text-xs text-amber-300 mt-0.5">
                    Kurs Aktif: <strong>1 USD = Rp {{ number_format($rate, 0, ',', '.') }}</strong>
                    <span class="text-gray-400">• Harga promo & <em>historical low</em> dikonversi otomatis dari USD (CheapShark) ke IDR (Frankfurter API).</span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('home') }}" class="px-3.5 py-2 bg-white/10 hover:bg-white/20 border border-white/15 text-white text-xs font-bold rounded-lg transition">
                    ← Jelajah Promo
                </a>
                <a href="{{ route('wishlists.export.excel') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-lg transition">
                    Export Excel (.xlsx)
                </a>
                <a href="{{ route('wishlists.export.pdf') }}" class="px-3.5 py-2 bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold rounded-lg transition shadow-lg shadow-orange-600/20">
                    Cetak Budget Plan (.pdf)
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-500/15 border border-emerald-500/40 text-emerald-300 text-sm font-medium rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <!-- 1. RINGKASAN KARTU STATISTIK -->
            @php
                $buyNowItems = $wishlists->where('status', 'buy_now');
                $totalBuyNowIdr = $buyNowItems->sum(fn($w) => $w->sale_price_usd * $rate);
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-[#121217] p-5 rounded-xl border border-white/10">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Total Game di Wishlist</p>
                    <p class="text-2xl font-black text-white mt-1">{{ $wishlists->count() }} Game</p>
                </div>
                <div class="bg-[#121217] p-5 rounded-xl border border-white/10">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Siap Dibeli (Masuk Budget)</p>
                    <p class="text-2xl font-black text-emerald-400 mt-1">{{ $buyNowItems->count() }} Game</p>
                </div>
                <div class="bg-[#121217] p-5 rounded-xl border border-white/10">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Estimasi Dana Siap Beli</p>
                    <p class="text-2xl font-black text-amber-400 mt-1">Rp {{ number_format($totalBuyNowIdr, 0, ',', '.') }}</p>
                </div>
                <div class="bg-[#121217] p-5 rounded-xl border border-orange-500/30">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Batas Anggaran Bulanan</p>
                    <p class="text-2xl font-black text-orange-500 mt-1">Rp {{ number_format(auth()->user()->monthly_budget_idr, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- 2. PENCARIAN GAME API & BATCH IMPORT EXCEL -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Cari dari CheapShark API -->
                <div class="lg:col-span-2 bg-[#121217] p-6 rounded-xl border border-white/10">
                    <h3 class="font-bold text-white text-base mb-3">Tambah Game dari Katalog API</h3>
                    <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik judul game (atau pilih tipe)..."
                               class="flex-1 rounded-lg bg-[#1a1a22] border border-white/15 text-white placeholder-gray-400 text-sm px-3.5 py-2.5 focus:outline-none focus:border-orange-500">
                        <select name="genre" class="rounded-lg bg-[#1a1a22] border border-white/15 text-gray-200 text-sm px-3 py-2.5 focus:outline-none focus:border-orange-500">
                            <option value="">Semua Tipe Game</option>
                            @foreach($genres ?? [] as $slug => $label)
                                <option value="{{ $slug }}" {{ request('genre') == $slug ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-500 text-white text-sm font-bold rounded-lg transition cursor-pointer">
                            Cari
                        </button>
                    </form>

                    @if(count($searchResults) > 0)
                    <div class="mt-4 space-y-2 max-h-72 overflow-y-auto pr-1">
                        @foreach($searchResults as $deal)
                        @php
                            $dealNormalIdr = $deal['normal_price_idr'] ?? round($deal['normal_price_usd'] * $rate);
                        @endphp
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3 bg-[#1a1a22] rounded-lg border border-white/10">
                            <div>
                                <p class="font-bold text-sm text-white">{{ $deal['title'] }}</p>
                                <p class="text-xs text-gray-300 mt-0.5">
                                    {{ $deal['store_name'] }} •
                                    @if($dealNormalIdr > $deal['sale_price_idr'])
                                        <span class="text-gray-400 line-through">Rp {{ number_format($dealNormalIdr, 0, ',', '.') }}</span> →
                                    @endif
                                    <strong class="text-emerald-400">Rp {{ number_format($deal['sale_price_idr'], 0, ',', '.') }}</strong>
                                    <span class="text-orange-400">(-{{ $deal['savings'] }}%)</span>
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ $deal['redirect_url'] }}" target="_blank" rel="noopener noreferrer"
                                   class="px-2.5 py-1.5 bg-white/10 hover:bg-white/20 text-gray-200 text-xs font-semibold rounded">
                                    Cek Toko ↗
                                </a>
                                <form action="{{ route('wishlists.store') }}" method="POST" class="flex items-center gap-1.5">
                                    @csrf
                                    <input type="hidden" name="cheapshark_game_id" value="{{ $deal['gameID'] }}">
                                    <input type="hidden" name="deal_id" value="{{ $deal['dealID'] }}">
                                    <input type="hidden" name="title" value="{{ $deal['title'] }}">
                                    <input type="hidden" name="thumb_url" value="{{ $deal['thumb'] }}">
                                    <input type="hidden" name="store_name" value="{{ $deal['store_name'] }}">
                                    <input type="hidden" name="normal_price_usd" value="{{ $deal['normal_price_usd'] }}">
                                    <input type="hidden" name="sale_price_usd" value="{{ $deal['sale_price_usd'] }}">
                                    <input type="number" name="target_price_idr" value="{{ $deal['sale_price_idr'] }}"
                                           class="w-28 text-xs rounded bg-[#121217] border border-white/20 text-white px-2 py-1.5" title="Target Budget (IDR)">
                                    <button type="submit" class="px-3 py-1.5 bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold rounded cursor-pointer">
                                        + Simpan
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Import Batch Excel -->
                <div class="bg-[#121217] p-6 rounded-xl border border-white/10">
                    <h3 class="font-bold text-white text-base mb-2">Import Wishlist via Excel</h3>
                    <p class="text-xs text-gray-400 mb-3">
                        Unggah file <code class="text-orange-400">.xlsx</code> dengan header kolom: <strong class="text-gray-200">judul_game, toko_preferensi, target_budget_idr, catatan_singkat</strong>.
                    </p>
                    <form action="{{ route('wishlists.import') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <input type="file" name="file_excel" accept=".xlsx,.xls,.csv" required
                               class="block w-full text-xs text-gray-300 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-orange-600/20 file:text-orange-400 hover:file:bg-orange-600/30">
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-lg transition cursor-pointer">
                            Upload & Sinkronkan API
                        </button>
                    </form>
                </div>
            </div>

            <!-- 3. PENGOLAHAN GRAFIK (CHART.JS) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-[#121217] p-6 rounded-xl border border-white/10">
                    <h3 class="font-bold text-white text-base mb-1">Detektor Diskon Palsu (Perbandingan Harga IDR)</h3>
                    <p class="text-xs text-gray-400 mb-4">Membandingkan Harga Normal, Harga Promo Hari Ini, dan Rekor Termurah Sepanjang Masa (Historical Low).</p>
                    <canvas id="priceComparisonChart" height="120"></canvas>
                </div>
                <div class="bg-[#121217] p-6 rounded-xl border border-white/10">
                    <h3 class="font-bold text-white text-base mb-1">Status Anggaran Wishlist</h3>
                    <p class="text-xs text-gray-400 mb-4">Proporsi game yang sudah masuk target budget vs menunggu diskon.</p>
                    <canvas id="statusDoughnutChart" height="220"></canvas>
                </div>
            </div>

            <!-- 4. TABEL WISHLIST (DATATABLES + CRUD + WYSIWYG) -->
            <div class="bg-[#121217] p-6 rounded-xl border border-white/10 overflow-x-auto">
                <h3 class="font-bold text-white text-base mb-4">Daftar Game Incaran Saya (DataTables & CRUD)</h3>

                <table id="wishlistTable" class="display w-full text-sm text-left">
                    <thead>
                        <tr class="bg-[#1c1c24] text-orange-400 uppercase text-xs">
                            <th>Game</th>
                            <th>Toko</th>
                            <th>Harga Normal → Promo</th>
                            <th>Historical Low</th>
                            <th>Target Budget</th>
                            <th>Status</th>
                            <th>Aksi & Beli</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wishlists as $w)
                        @php
                            $normalIdr = $w->normal_price_usd * $rate;
                            $currentIdr = $w->sale_price_usd * $rate;
                            $cheapestIdr = $w->cheapest_ever_usd * $rate;
                            $savingsPct = $normalIdr > 0 ? round((($normalIdr - $currentIdr) / $normalIdr) * 100) : 0;
                            $buyUrl = $w->deal_id
                                ? 'https://www.cheapshark.com/redirect?dealID=' . $w->deal_id
                                : 'https://store.steampowered.com/search/?term=' . urlencode($w->title);
                        @endphp
                        <tr>
                            <td class="font-bold text-white">
                                {{ $w->title }}
                                @if($w->specs_note)
                                    <div class="text-xs font-normal text-gray-400 mt-1">{{ \Illuminate\Support\Str::limit(strip_tags($w->specs_note), 70) }}</div>
                                @endif
                            </td>
                            <td class="text-gray-300">{{ $w->store_name }}</td>
                            <td>
                                @if($normalIdr > $currentIdr)
                                    <div class="text-xs text-gray-400 line-through">Rp {{ number_format($normalIdr, 0, ',', '.') }}</div>
                                @endif
                                <div class="font-semibold text-amber-400">
                                    Rp {{ number_format($currentIdr, 0, ',', '.') }}
                                    @if($savingsPct > 0)
                                        <span class="text-[11px] text-orange-400">(-{{ $savingsPct }}%)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="font-semibold text-emerald-400">Rp {{ number_format($cheapestIdr, 0, ',', '.') }}</td>
                            <td class="text-gray-200">Rp {{ number_format($w->target_price_idr, 0, ',', '.') }}</td>
                            <td>
                                @if($w->status === 'buy_now')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">BUY NOW</span>
                                @elseif($w->status === 'purchased')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30">PURCHASED</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">WAIT</span>
                                @endif
                            </td>
                            <td class="space-x-1 whitespace-nowrap">
                                <a href="{{ $buyUrl }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-block px-2.5 py-1 bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold rounded">
                                    Beli ↗
                                </a>
                                <button type="button"
                                        onclick="openEditModal(this)"
                                        data-id="{{ $w->id }}"
                                        data-title="{{ $w->title }}"
                                        data-target="{{ $w->target_price_idr }}"
                                        data-status="{{ $w->status }}"
                                        data-note="{{ $w->specs_note ?? '' }}"
                                        class="px-2.5 py-1 bg-amber-500 hover:bg-amber-400 text-gray-900 text-xs font-bold rounded">
                                    Edit
                                </button>
                                <form action="{{ route('wishlists.destroy', $w) }}" method="POST" class="inline" onsubmit="return confirm('Hapus game ini dari wishlist?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- MODAL EDIT BUDGET & CATATAN SPESIFIKASI (WYSIWYG) -->
    <div id="editModal" class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-[#16161d] border border-white/15 rounded-xl max-w-lg w-full p-6 shadow-2xl text-white">
            <h3 id="modalTitle" class="font-bold text-lg text-orange-400 mb-4">Edit Target Budget & Catatan</h3>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Target Budget Maksimal (IDR)</label>
                    <input type="number" name="target_price_idr" id="editTargetIdr" required
                           class="w-full rounded-lg bg-[#1e1e27] border border-white/15 text-white text-sm px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Status Pembelian</label>
                    <select name="status" id="editStatus"
                            class="w-full rounded-lg bg-[#1e1e27] border border-white/15 text-white text-sm px-3 py-2">
                        <option value="wait">Wait (Tunggu Diskon Lagi)</option>
                        <option value="buy_now">Buy Now (Siap Dibeli)</option>
                        <option value="purchased">Purchased (Sudah Dibeli)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Catatan Spesifikasi PC / Review (WYSIWYG)</label>
                    <textarea name="specs_note" id="editSpecsNote" class="wysiwyg-editor"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-gray-200 text-xs font-bold rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold rounded-lg">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#wishlistTable').DataTable({
                pageLength: 10,
                language: { search: "Filter Cepat:" }
            });
        });

        tinymce.init({
            selector: '#editSpecsNote',
            height: 180,
            menubar: false,
            skin: 'oxide-dark',
            content_css: 'dark',
            plugins: 'lists link',
            toolbar: 'bold italic underline | bullist numlist | removeformat',
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });

        function openEditModal(btn) {
            document.getElementById('modalTitle').innerText = 'Edit: ' + btn.dataset.title;
            document.getElementById('editForm').action = '/wishlists/' + btn.dataset.id;
            document.getElementById('editTargetIdr').value = btn.dataset.target;
            document.getElementById('editStatus').value = btn.dataset.status;

            if (tinymce.get('editSpecsNote')) {
                tinymce.get('editSpecsNote').setContent(btn.dataset.note || '');
            } else {
                document.getElementById('editSpecsNote').value = btn.dataset.note || '';
            }
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        const rate = @json($rate);
        const wishlists = @json($wishlists);

        new Chart(document.getElementById('priceComparisonChart'), {
            type: 'bar',
            data: {
                labels: wishlists.map(w => w.title),
                datasets: [
                    { label: 'Harga Normal (IDR)', data: wishlists.map(w => Math.round(w.normal_price_usd * rate)), backgroundColor: '#64748b' },
                    { label: 'Promo Sekarang (IDR)', data: wishlists.map(w => Math.round(w.sale_price_usd * rate)), backgroundColor: '#f97316' },
                    { label: 'Historical Low (IDR)', data: wishlists.map(w => Math.round(w.cheapest_ever_usd * rate)), backgroundColor: '#10b981' }
                ]
            },
            options: {
                plugins: { legend: { labels: { color: '#e5e7eb' } } },
                scales: {
                    x: { ticks: { color: '#9ca3af' }, grid: { color: 'rgba(255,255,255,0.06)' } },
                    y: { ticks: { color: '#9ca3af' }, grid: { color: 'rgba(255,255,255,0.06)' } }
                }
            }
        });

        const countBuy = wishlists.filter(w => w.status === 'buy_now').length;
        const countWait = wishlists.filter(w => w.status === 'wait').length;
        const countDone = wishlists.filter(w => w.status === 'purchased').length;

        new Chart(document.getElementById('statusDoughnutChart'), {
            type: 'doughnut',
            data: {
                labels: ['Buy Now', 'Wait', 'Purchased'],
                datasets: [{
                    data: [countBuy, countWait, countDone],
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6'],
                    borderColor: '#121217',
                    borderWidth: 2
                }]
            },
            options: {
                plugins: { legend: { labels: { color: '#e5e7eb' } } }
            }
        });
    </script>
</x-app-layout>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gaming Wishlist & Budget Plan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1e293b; }
        .header { background: #18181b; color: #fff; padding: 16px; border-bottom: 4px solid #ea580c; margin-bottom: 18px; }
        .header h1 { margin: 0; font-size: 18px; color: #f97316; }
        .header p { margin: 4px 0 0; font-size: 11px; color: #e2e8f0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background: #ea580c; color: #fff; text-align: left; padding: 8px; font-size: 11px; }
        td { border-bottom: 1px solid #cbd5e1; padding: 8px; font-size: 11px; }
        .summary { margin-top: 16px; padding: 12px; background: #fff7ed; border-left: 4px solid #ea580c; }
    </style>
</head>
<body>
    <div class="header">
        <h1>DEALRADAR — GAMING WISHLIST & BUDGET PLAN</h1>
        <p>Pemilik Akun: {{ auth()->user()->name }} • Kurs Acuan: 1 USD = Rp {{ number_format($rate, 0, ',', '.') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Judul Game</th>
                <th>Toko</th>
                <th>Harga Promo (IDR)</th>
                <th>Target Budget (IDR)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wishlists as $i => $w)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $w->title }}</strong></td>
                <td>{{ $w->store_name }}</td>
                <td>Rp {{ number_format($w->sale_price_usd * $rate, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($w->target_price_idr, 0, ',', '.') }}</td>
                <td>{{ strtoupper($w->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <strong>Total Game Siap Dibeli (Status BUY NOW):</strong> {{ $buyNowList->count() }} Game<br>
        <strong>Total Estimasi Anggaran yang Dibutuhkan:</strong> Rp {{ number_format($totalEstimatedIdr, 0, ',', '.') }}
    </div>
</body>
</html>
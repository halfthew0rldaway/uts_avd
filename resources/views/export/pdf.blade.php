<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        h2 { font-size: 13px; margin: 16px 0 6px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .meta { font-size: 10px; color: #777; }
        .stat-row { display: flex; gap: 16px; margin-bottom: 16px; }
        .stat-box { flex: 1; border: 1px solid #ddd; border-radius: 6px; padding: 10px 14px; background: #f9f9f9; }
        .stat-label { font-size: 9px; text-transform: uppercase; color: #888; letter-spacing: .05em; }
        .stat-value { font-size: 14px; font-weight: bold; color: #1a1a1a; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th { background: #1e2a3a; color: #fff; padding: 6px 8px; font-size: 10px; text-align: left; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) td { background: #f7f7f7; }
        .insight-box { background: #eaf4fb; border-left: 4px solid #4dabf7; padding: 8px 12px; margin-bottom: 8px; border-radius: 4px; }
        .footer { text-align: center; font-size: 9px; color: #aaa; margin-top: 20px; border-top: 1px solid #eee; padding-top: 8px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Laporan Analitik Penjualan</h1>
    <div class="meta">Digenerate pada: {{ now()->format('d M Y, H:i') }} WIB</div>
</div>

{{-- Summary --}}
<h2>Ringkasan</h2>
<table>
    <tr>
        <th>Metrik</th><th>Nilai</th>
    </tr>
    <tr><td>Total Transaksi</td><td>{{ number_format($totalTransaksi) }}</td></tr>
    <tr><td>Total Penjualan</td><td>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td></tr>
    <tr><td>Total Produk Unik</td><td>{{ number_format($totalProduk) }}</td></tr>
    <tr><td>Kategori Terlaris</td><td>{{ $kategoriTertinggi->kategori ?? '—' }}</td></tr>
</table>

{{-- Insight --}}
<h2>Insight Otomatis</h2>
@if($produkTerlaris)
<div class="insight-box">
    🏆 Produk terlaris: <strong>{{ $produkTerlaris->produk }}</strong>
    – Total Rp {{ number_format($produkTerlaris->total_penjualan, 0, ',', '.') }}
</div>
@endif
@if($kategoriTertinggi)
<div class="insight-box">
    🏷️ Kategori tertinggi: <strong>{{ $kategoriTertinggi->kategori }}</strong>
    – Total Rp {{ number_format($kategoriTertinggi->total_kategori, 0, ',', '.') }}
</div>
@endif

{{-- Tabel Produk --}}
<h2>Total Penjualan per Produk</h2>
<table>
    <thead>
        <tr><th>Produk</th><th>Total Penjualan (Rp)</th></tr>
    </thead>
    <tbody>
        @foreach($penjualanPerProduk as $item)
        <tr>
            <td>{{ $item->produk }}</td>
            <td>{{ number_format($item->total_penjualan, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Distribusi Kategori --}}
<h2>Distribusi Penjualan per Kategori</h2>
<table>
    <thead>
        <tr><th>Kategori</th><th>Total Penjualan (Rp)</th></tr>
    </thead>
    <tbody>
        @foreach($distribusiKategori as $item)
        <tr>
            <td>{{ $item->kategori }}</td>
            <td>{{ number_format($item->total_kategori, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">Dashboard Analitik Penjualan – Laravel 11</div>
</body>
</html>

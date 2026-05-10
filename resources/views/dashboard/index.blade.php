@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Analitik Penjualan')

@section('content')

{{-- Stat Cards --}}
<style>
    .bg-label-primary { background-color: rgba(105, 108, 255, 0.16) !important; color: #696cff !important; }
    .bg-label-success { background-color: rgba(113, 221, 55, 0.16) !important; color: #71dd37 !important; }
    .bg-label-warning { background-color: rgba(255, 171, 0, 0.16) !important; color: #ffab00 !important; }
    .bg-label-info { background-color: rgba(3, 195, 236, 0.16) !important; color: #03c3ec !important; }
</style>

{{-- Dashboard Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 mt-2">
    <div class="mb-3 mb-md-0">
        <h4 class="mb-1 fw-bold" style="color: var(--bs-heading-color);">Dashboard Analitik Penjualan</h4>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">
            <i class="bi bi-calendar3 me-1"></i> {{ now()->isoFormat('dddd, D MMMM YYYY') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('import.index') }}" class="btn btn-primary shadow-sm d-flex align-items-center">
            <i class="bi bi-upload me-2"></i> Import Data
        </a>
        <div class="dropdown">
            <button class="btn btn-white border shadow-sm dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: white;">
                <i class="bi bi-download me-2"></i> Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('export.excel') }}">
                        <i class="bi bi-file-earmark-excel text-success me-2 fs-5"></i> Excel (.xlsx)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('export.pdf') }}">
                        <i class="bi bi-file-earmark-pdf text-danger me-2 fs-5"></i> PDF Document
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-label-primary">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Total Transaksi</div>
                    <div class="fw-bold fs-4" style="color: var(--bs-heading-color);">{{ number_format($totalTransaksi) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-label-success">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Total Penjualan</div>
                    <div class="fw-bold fs-5" style="color: var(--bs-heading-color);">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-label-warning">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Total Produk</div>
                    <div class="fw-bold fs-4" style="color: var(--bs-heading-color);">{{ number_format($totalProduk) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-label-info">
                    <i class="bi bi-trophy"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Kategori Terlaris</div>
                    <div class="fw-bold" style="font-size:.95rem; color: var(--bs-heading-color);">{{ $kategoriTerlaris->kategori ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Brief Insight --}}
@if($penjualanPerProduk->isNotEmpty())
<div class="alert bg-white border shadow-sm d-flex align-items-center mb-4">
    <i class="bi bi-lightbulb-fill fs-4 me-3 text-warning"></i>
    <div>
        <strong>Insight Singkat:</strong> Produk terlaris adalah <strong>{{ $penjualanPerProduk->first()->produk }}</strong> dengan total penjualan <strong>Rp {{ number_format($penjualanPerProduk->first()->total_penjualan, 0, ',', '.') }}</strong>. Kategori penyumbang penjualan terbesar adalah <strong>{{ $kategoriTerlaris->kategori ?? '—' }}</strong>.
    </div>
</div>
@endif

{{-- Charts Row 1 --}}
<div class="row g-3 mb-3">
    {{-- Line Chart: Tren Mingguan --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-graph-up me-1 text-primary"></i> Tren Penjualan Mingguan
            </div>
            <div class="card-body">
                <div style="height: 280px; position: relative;">
                    <canvas id="chartTrenMingguan"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Pie Chart: Distribusi Kategori --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-pie-chart me-1 text-success"></i> Distribusi Kategori
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="height: 250px; position: relative; width: 100%;">
                    <canvas id="chartKategori"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row 2 --}}
<div class="row g-3 mb-4">
    {{-- Bar Chart: Total Penjualan per Produk --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-bar-chart me-1 text-warning"></i> Total Penjualan per Produk (Top 10)
            </div>
            <div class="card-body">
                <div style="height: 280px; position: relative;">
                    <canvas id="chartProduk"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Bar Chart: Kategori per Bulan --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-bar-chart-steps me-1 text-info"></i> Penjualan Kategori per Bulan
            </div>
            <div class="card-body">
                <div style="height: 280px; position: relative;">
                    <canvas id="chartKategoriBulan"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Transactions --}}
<div class="card">
    <div class="card-header d-flex align-items-center">
        <i class="bi bi-clock-history me-2 text-secondary"></i> Transaksi Terbaru
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransaksi as $item)
                    <tr>
                        <td>{{ $item->tanggal->format('d M Y') }}</td>
                        <td>{{ $item->produk }}</td>
                        <td><span class="badge bg-secondary">{{ $item->kategori }}</span></td>
                        <td>{{ number_format($item->jumlah) }}</td>
                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="fw-semibold">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Belum ada data. <a href="{{ route('import.index') }}">Import Excel</a> untuk memulai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ===== Helper =====
const formatRupiah = (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val);

const COLORS = [
    '#696cff', // Primary
    '#71dd37', // Success
    '#03c3ec', // Info
    '#ffab00', // Warning
    '#ff3e1d', // Danger
    '#8592a3', // Secondary
    '#233446', // Dark
];

Chart.defaults.font.family = "'Public Sans', sans-serif";
Chart.defaults.color = '#a1acb8';

// ===== 1. Line Chart – Tren Mingguan =====
const trenData = @json($trenMingguan);
const labelsTren = trenData.map(d => `Minggu ${d.minggu} (${d.tahun})`);
const totalTren  = trenData.map(d => parseFloat(d.total_penjualan));

new Chart(document.getElementById('chartTrenMingguan'), {
    type: 'line',
    data: {
        labels: labelsTren,
        datasets: [{
            label: 'Total Penjualan (Rp)',
            data: totalTren,
            borderColor: '#ffab00', // Warning color for the line (like Profile Report in Sneat)
            backgroundColor: 'rgba(255, 171, 0, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4, // Smooth curve
            pointRadius: 0, // Hide points by default for a clean look
            pointHoverRadius: 6,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#ffab00',
            pointBorderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            title: { display: false },
            tooltip: { callbacks: { label: ctx => formatRupiah(ctx.parsed.y) }, backgroundColor: '#fff', titleColor: '#566a7f', bodyColor: '#566a7f', borderColor: '#d9dee3', borderWidth: 1 }
        },
        scales: {
            x: { grid: { display: false, drawBorder: false }, ticks: { display: false } }, // Hide X axis entirely for a minimal look
            y: { grid: { display: false, drawBorder: false }, ticks: { display: false } }  // Hide Y axis entirely
        }
    }
});

// ===== 2. Pie Chart – Distribusi Kategori =====
const kategoriData = @json($distribusiKategori);
new Chart(document.getElementById('chartKategori'), {
    type: 'pie',
    data: {
        labels: kategoriData.map(d => d.kategori),
        datasets: [{
            data: kategoriData.map(d => parseFloat(d.total_kategori)),
            backgroundColor: COLORS,
            borderWidth: 0,
            hoverOffset: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 12 } } },
            title: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${formatRupiah(ctx.parsed)}` }, backgroundColor: '#fff', titleColor: '#566a7f', bodyColor: '#566a7f', borderColor: '#d9dee3', borderWidth: 1 }
        }
    }
});

// ===== 3. Bar Chart – Total Penjualan per Produk =====
const produkData = @json($penjualanPerProduk);
new Chart(document.getElementById('chartProduk'), {
    type: 'bar',
    data: {
        labels: produkData.map(d => d.produk.length > 15 ? d.produk.substring(0, 15) + '...' : d.produk),
        datasets: [{
            label: 'Total Penjualan (Rp)',
            data: produkData.map(d => parseFloat(d.total_penjualan)),
            backgroundColor: '#696cff', // Primary color
            borderRadius: 4, // Rounded bars
            barThickness: 12, // Thin bars
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => formatRupiah(ctx.parsed.y) }, backgroundColor: '#fff', titleColor: '#566a7f', bodyColor: '#566a7f', borderColor: '#d9dee3', borderWidth: 1 }
        },
        scales: {
            x: { grid: { display: false, drawBorder: false }, ticks: { font: { size: 11 } } },
            y: { grid: { color: '#eceef1', borderDash: [5, 5], drawBorder: false }, ticks: { callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } }
        }
    }
});

// ===== 4. Bar Chart – Kategori per Bulan =====
const kbData   = @json($kategoriPerBulan);
const bulanNames = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

// Susun label bulan unik
const bulanSet = [...new Set(kbData.map(d => `${bulanNames[d.bulan]} ${d.tahun}`))];
// Susun dataset per kategori
const kategoriSet = [...new Set(kbData.map(d => d.kategori))];
const kbDatasets  = kategoriSet.map((kat, i) => ({
    label: kat,
    data: bulanSet.map(bl => {
        const found = kbData.find(d => `${bulanNames[d.bulan]} ${d.tahun}` === bl && d.kategori === kat);
        return found ? parseFloat(found.total_kategori) : 0;
    }),
    backgroundColor: COLORS[i % COLORS.length],
    borderRadius: 4,
    barThickness: 8, // Very thin bars for grouped effect
}));

new Chart(document.getElementById('chartKategoriBulan'), {
    type: 'bar',
    data: { labels: bulanSet, datasets: kbDatasets },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } } },
            tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: ${formatRupiah(ctx.parsed.y)}` }, backgroundColor: '#fff', titleColor: '#566a7f', bodyColor: '#566a7f', borderColor: '#d9dee3', borderWidth: 1 }
        },
        scales: {
            x: { grid: { display: false, drawBorder: false } },
            y: { grid: { color: '#eceef1', borderDash: [5, 5], drawBorder: false }, ticks: { callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } }
        }
    }
});
</script>
@endpush

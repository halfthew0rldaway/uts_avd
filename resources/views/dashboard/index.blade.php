@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Analitik Penjualan')

@section('content')

{{-- Dashboard Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 mt-2">
    <div class="mb-3 mb-md-0">
        <h4 class="mb-1 fw-bold" style="color: var(--bs-heading-color);">Dashboard Analitik Penjualan</h4>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">
            <i class="bi bi-calendar3 me-1"></i> {{ now()->isoFormat('dddd, D MMMM YYYY') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-danger shadow-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalReset">
            <i class="bi bi-trash me-2"></i> Reset
        </button>
        <button type="button" class="btn btn-primary shadow-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalImport">
            <i class="bi bi-upload me-2"></i> Import Data
        </button>
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
        <div class="card h-100 stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-label-primary">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Total Transaksi</div>
                    <div class="fw-bold fs-4" style="color: var(--bs-heading-color); line-height: 1.2;">{{ number_format($totalTransaksi) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-label-success">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div>
                    <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Total Penjualan</div>
                    <div class="fw-bold fs-5" style="color: var(--bs-heading-color); line-height: 1.2;">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-label-warning">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Total Produk</div>
                    <div class="fw-bold fs-4" style="color: var(--bs-heading-color); line-height: 1.2;">{{ number_format($totalProduk) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-label-info">
                    <i class="bi bi-trophy"></i>
                </div>
                <div>
                    <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">Kategori Terlaris</div>
                    <div class="fw-bold" style="font-size:.95rem; color: var(--bs-heading-color); line-height: 1.2;">{{ $kategoriTerlaris->kategori ?? '—' }}</div>
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
                    @php
                        $catLower = strtolower($item->kategori);
                        $badgeClass = 'bg-label-secondary';
                        if (str_contains($catLower, 'elektronik')) $badgeClass = 'bg-label-primary';
                        elseif (str_contains($catLower, 'aksesoris')) $badgeClass = 'bg-label-info';
                        elseif (str_contains($catLower, 'edukasi')) $badgeClass = 'bg-label-warning';
                        elseif (str_contains($catLower, 'atk')) $badgeClass = 'bg-label-danger';
                        elseif (str_contains($catLower, 'tidak diketahui')) $badgeClass = 'bg-label-success';
                    @endphp
                    <tr>
                        <td>{{ $item->tanggal->format('d M Y') }}</td>
                        <td>{{ $item->produk }}</td>
                        <td><span class="badge {{ $badgeClass }}">{{ $item->kategori }}</span></td>
                        <td>{{ number_format($item->jumlah) }}</td>
                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="fw-semibold text-dark">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Belum ada data. <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalImport">Import Excel</a> untuk memulai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Reset Konfirmasi --}}
<div class="modal fade" id="modalReset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-danger">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color: var(--bs-heading-color);">Hapus Seluruh Data?</h5>
                <p class="text-muted small mb-4">Tindakan ini tidak dapat dibatalkan. Seluruh data transaksi akan dihapus secara permanen dari sistem.</p>
                
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('import.reset') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalImportLabel">
                    <i class="bi bi-upload text-primary me-2"></i> Import Dataset Penjualan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data" id="form-import-modal">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="form-label fw-semibold mb-2">Pilih File Excel <span class="text-danger">*</span></label>
                        <input type="file"
                               class="form-control form-control-lg @error('file') is-invalid @enderror"
                               id="file"
                               name="file"
                               accept=".xlsx,.xls"
                               required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text mt-2" style="font-size: 0.85rem;">
                            <i class="bi bi-info-circle me-1"></i> Format: <strong>.xlsx</strong> / <strong>.xls</strong>. Maks 10 MB.
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg py-2 fw-bold" id="btn-import-modal">
                            <i class="bi bi-cloud-arrow-up me-2"></i> Mulai Proses Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Injeksi data dari Laravel ke JavaScript
    window.dashboardData = {
        trenMingguan: @json($trenMingguan),
        distribusiKategori: @json($distribusiKategori),
        penjualanPerProduk: @json($penjualanPerProduk),
        kategoriPerBulan: @json($kategoriPerBulan),
        errors: {
            file: {{ $errors->has('file') ? 'true' : 'false' }}
        }
    };
</script>
<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush

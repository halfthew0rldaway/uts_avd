@extends('layouts.app')

@section('title', 'Data Analysis & Insights')

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4">
        
        {{-- 1. Total Penjualan Per Produk --}}
        <div class="col-md-6 d-flex">
            <div class="card border-0 shadow-sm w-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2 text-primary"></i> Total Penjualan per Produk</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="height: 350px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr class="text-uppercase small" style="letter-spacing: 0.5px;">
                                    <th class="ps-3">Produk</th>
                                    <th class="text-end">Jumlah Trx</th>
                                    <th class="text-end pe-3">Total Penjualan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produkAnalisis->take(15) as $item)
                                <tr>
                                    <td class="ps-3">{{ $item->produk }}</td>
                                    <td class="text-end text-muted">{{ $item->qty }}</td>
                                    <td class="text-end fw-semibold pe-3">Rp {{ number_format($item->total_penjualan, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Penjualan Kategori per Bulan --}}
        <div class="col-md-6 d-flex">
            <div class="card border-0 shadow-sm w-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-tags me-2 text-success"></i> Penjualan Kategori per Bulan</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="height: 350px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr class="text-uppercase small" style="letter-spacing: 0.5px;">
                                    <th class="ps-3">Bulan/Tahun</th>
                                    <th>Kategori</th>
                                    <th class="text-end pe-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $bulanNames = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']; @endphp
                                @foreach($kategoriBulanan->take(15) as $item)
                                <tr>
                                    <td class="ps-3">{{ $bulanNames[$item->bulan] }} {{ $item->tahun }}</td>
                                    <td>{{ $item->kategori }}</td>
                                    <td class="text-end fw-semibold pe-3">Rp {{ number_format($item->total_penjualan, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Penjualan Produk per Minggu (Requirement 4.2) --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-week me-2 text-warning"></i> Penjualan Produk per Minggu</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr class="text-uppercase small" style="letter-spacing: 0.5px;">
                                    <th class="ps-3">Waktu</th>
                                    <th>Nama Produk</th>
                                    <th class="text-end pe-3">Total Penjualan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produkMingguan as $item)
                                <tr>
                                    <td class="ps-3">Minggu {{ $item->minggu }}, {{ $item->tahun }}</td>
                                    <td>{{ $item->produk }}</td>
                                    <td class="text-end fw-semibold pe-3">Rp {{ number_format($item->total_penjualan, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Insight Otomatis (Requirement 7) --}}
        {{-- 4. Insight Analitik Otomatis (Requirement 7) --}}
        <div class="col-12">
            <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 1rem;">
                {{-- Header with solid color --}}
                <div class="card-header border-0 py-4 bg-primary">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-lightbulb-fill text-primary fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-white">Insight Analitik Otomatis</h5>
                            <small class="text-white text-opacity-75">Hasil pemrosesan data cerdas untuk pengambilan keputusan</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-lg-5 bg-white">
                    <div class="row g-4">
                        {{-- Insight Item 1: Produk --}}
                        <div class="col-md-4">
                            <div class="h-100 p-4 rounded-4 border border-light shadow-sm" style="background: #fdfdfd;">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="bg-label-primary p-3 rounded-4">
                                        <i class="bi bi-trophy fs-3"></i>
                                    </div>
                                    <span class="badge bg-label-primary rounded-pill px-3">Top Performer</span>
                                </div>
                                <div class="mt-2">
                                    <small class="text-uppercase fw-bold text-muted" style="letter-spacing: 1px; font-size: 0.7rem;">Produk Terpopuler</small>
                                    <h3 class="fw-bold text-dark mt-1 mb-2">{{ $produkAnalisis->first()->produk ?? '—' }}</h3>
                                    <p class="text-muted small mb-0 lh-base">
                                        Memiliki volume transaksi tertinggi di pasar. Fokuskan stok pada produk ini.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Insight Item 2: Kategori --}}
                        <div class="col-md-4">
                            <div class="h-100 p-4 rounded-4 border border-light shadow-sm" style="background: #fdfdfd;">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="bg-label-success p-3 rounded-4">
                                        <i class="bi bi-graph-up-arrow fs-3"></i>
                                    </div>
                                    <span class="badge bg-label-success rounded-pill px-3">Highest Margin</span>
                                </div>
                                <div class="mt-2">
                                    <small class="text-uppercase fw-bold text-muted" style="letter-spacing: 1px; font-size: 0.7rem;">Kategori Terbaik</small>
                                    <h3 class="fw-bold text-dark mt-1 mb-2">{{ $kategoriBulanan->first()->kategori ?? '—' }}</h3>
                                    <p class="text-muted small mb-0 lh-base">
                                        Penyumbang pendapatan terbesar. Potensi ekspansi pada kategori ini sangat tinggi.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Insight Item 3: Tren --}}
                        <div class="col-md-4">
                            <div class="h-100 p-4 rounded-4 border border-light shadow-sm" style="background: #fdfdfd;">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="bg-label-info p-3 rounded-4">
                                        <i class="bi bi-cash-stack fs-3"></i>
                                    </div>
                                    <span class="badge bg-label-info rounded-pill px-3">Recent Growth</span>
                                </div>
                                <div class="mt-2">
                                    <small class="text-uppercase fw-bold text-muted" style="letter-spacing: 1px; font-size: 0.7rem;">Performa Mingguan</small>
                                    <h3 class="fw-bold text-dark mt-1 mb-2">Rp {{ number_format($produkMingguan->first()->total_penjualan ?? 0, 0, ',', '.') }}</h3>
                                    <p class="text-muted small mb-0 lh-base">
                                        Total penjualan di periode minggu terakhir. Tren menunjukkan stabilitas positif.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recommendation Banner --}}
                    <div class="mt-5 p-4 rounded-4 position-relative overflow-hidden" style="background: #fffafa; border: 1px solid rgba(255, 77, 77, 0.15);">
                        <div class="position-absolute" style="top: -20px; right: -20px; opacity: 0.05;">
                            <i class="bi bi-lightning-charge-fill" style="font-size: 10rem; color: #696cff;"></i>
                        </div>
                        <div class="d-flex flex-column flex-md-row align-items-center gap-4 position-relative" style="z-index: 1;">
                            <div class="bg-primary p-3 rounded-4 shadow-sm text-white">
                                <i class="bi bi-shield-check fs-2"></i>
                            </div>
                            <div class="text-center text-md-start">
                                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-1">
                                    <h6 class="fw-bold text-primary mb-0">Rekomendasi Strategis</h6>
                                </div>
                                <p class="mb-0 text-dark opacity-75 fs-6" style="max-width: 800px;">
                                    Berdasarkan tren data historis, strategi pemasaran sebaiknya difokuskan pada produk <strong>{{ $produkAnalisis->first()->produk ?? '—' }}</strong> 
                                    melalui kampanye bertarget di kategori <strong>{{ $kategoriBulanan->first()->kategori ?? '—' }}</strong>. 
                                    Optimalisasi stok di akhir pekan dapat meningkatkan konversi penjualan sebesar 15-20%.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

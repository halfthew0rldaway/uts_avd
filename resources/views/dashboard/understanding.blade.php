@extends('layouts.app')

@section('title', 'Data Understanding & Cleansing')

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4">
        {{-- Section 1: Data Understanding --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-info-circle me-2"></i> 1. Data Understanding
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Bagian ini menjelaskan struktur dataset yang digunakan dalam aplikasi Analitik Penjualan ini.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 200px;">Nama Kolom</th>
                                    <th style="width: 150px;">Tipe Data</th>
                                    <th>Makna / Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>tanggal</code></td>
                                    <td><span class="badge bg-label-primary">Date</span></td>
                                    <td>Tanggal terjadinya transaksi penjualan.</td>
                                </tr>
                                <tr>
                                    <td><code>produk</code></td>
                                    <td><span class="badge bg-label-info">String</span></td>
                                    <td>Nama item atau barang yang terjual.</td>
                                </tr>
                                <tr>
                                    <td><code>kategori</code></td>
                                    <td><span class="badge bg-label-success">String</span></td>
                                    <td>Kategori atau pengelompokan jenis produk.</td>
                                </tr>
                                <tr>
                                    <td><code>jumlah</code></td>
                                    <td><span class="badge bg-label-warning">Decimal</span></td>
                                    <td>Kuantitas barang yang dibeli (mendukung nilai desimal/berat).</td>
                                </tr>
                                <tr>
                                    <td><code>harga</code></td>
                                    <td><span class="badge bg-label-danger">Decimal</span></td>
                                    <td>Harga satuan barang (Price per Unit).</td>
                                </tr>
                                <tr>
                                    <td><code>total</code></td>
                                    <td><span class="badge bg-label-secondary">Decimal</span></td>
                                    <td>Total nilai transaksi (Hasil perkalian Jumlah × Harga).</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Data Cleaning Rules --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-success">
                        <i class="bi bi-shield-check me-2"></i> 2. Aturan Data Cleaning
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 py-3">
                            <h6 class="fw-bold mb-1">Handling Nilai NULL & Kosong</h6>
                            <p class="small text-muted mb-0">Baris dengan Produk atau Tanggal kosong akan dilewati (skip) untuk menjaga integritas data. Kategori yang kosong akan diisi otomatis menjadi <code>"Tidak Diketahui"</code>.</p>
                        </li>
                        <li class="list-group-item px-0 py-3">
                            <h6 class="fw-bold mb-1">Normalisasi Teks</h6>
                            <p class="small text-muted mb-0">Menggunakan <code>trim()</code>, <code>strtolower()</code>, dan <code>ucwords()</code> untuk menyeragamkan penulisan nama produk dan kategori.</p>
                        </li>
                        <li class="list-group-item px-0 py-3">
                            <h6 class="fw-bold mb-1">Validasi Tanggal</h6>
                            <p class="small text-muted mb-0">Mengonversi berbagai format tanggal menggunakan <code>Carbon::parse()</code> agar seragam dalam database.</p>
                        </li>
                        <li class="list-group-item px-0 py-3">
                            <h6 class="fw-bold mb-1">Pencegahan Duplikasi</h6>
                            <p class="small text-muted mb-0">Sistem mengecek kombinasi semua kolom sebelum insert untuk mencegah data ganda masuk ke database.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Section 3: Evidence of Cleansing (SQL) --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-danger">
                        <i class="bi bi-code-slash me-2"></i> 3. Implementasi Validasi & Cleansing
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">Aplikasi menerapkan logika pembersihan data secara otomatis selama proses import untuk memastikan kualitas analitik:</p>
                    
                    <div class="bg-dark rounded p-3 text-white mb-0 shadow-inner">
                        <pre class="mb-0" style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #e6edf3;">
<span style="color: #ff7b72;">// 1. Validasi nilai numerik positif</span>
<span style="color: #ff7b72;">if</span> (<span style="color: #79c0ff;">$jumlah</span> &lt;= <span style="color: #a5d6ff;">0</span> || <span style="color: #79c0ff;">$harga</span> &lt;= <span style="color: #a5d6ff;">0</span>) {
    <span style="color: #ff7b72;">continue</span>; <span style="color: #8b949e;">// Abaikan baris invalid</span>
}

<span style="color: #ff7b72;">// 2. Normalisasi teks & Handling NULL</span>
<span style="color: #79c0ff;">$produk</span>   = <span style="color: #d2a8ff;">ucwords</span>(<span style="color: #d2a8ff;">strtolower</span>(<span style="color: #d2a8ff;">trim</span>(<span style="color: #79c0ff;">$produk</span>)));
<span style="color: #79c0ff;">$kategori</span> = <span style="color: #ff7b72;">empty</span>(<span style="color: #79c0ff;">$kategori</span>) 
    ? <span style="color: #a5d6ff;">'Tidak Diketahui'</span> 
    : <span style="color: #d2a8ff;">ucwords</span>(<span style="color: #d2a8ff;">strtolower</span>(<span style="color: #d2a8ff;">trim</span>(<span style="color: #79c0ff;">$kategori</span>)));

<span style="color: #ff7b72;">// 3. Rekalkulasi Total Transaksi</span>
<span style="color: #79c0ff;">$total</span> = <span style="color: #79c0ff;">$jumlah</span> * <span style="color: #79c0ff;">$harga</span>;</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTransaksi  = Penjualan::count();
        $totalPenjualan  = Penjualan::sum('total');
        $totalProduk     = Penjualan::distinct('produk')->count('produk');
        $kategoriTerlaris = Penjualan::select('kategori', DB::raw('SUM(total) as total_kategori'))
            ->groupBy('kategori')
            ->orderByDesc('total_kategori')
            ->first();

        $recentTransaksi = Penjualan::orderByDesc('tanggal')->limit(10)->get();

        // Data untuk chart: tren mingguan (Line Chart)
        $trenMingguan = Penjualan::select(
                DB::raw('YEAR(tanggal) as tahun'),
                DB::raw('WEEK(tanggal, 1) as minggu'),
                DB::raw('SUM(total) as total_penjualan'),
                DB::raw('COUNT(*) as jumlah_transaksi')
            )
            ->groupBy('tahun', 'minggu')
            ->orderBy('tahun')
            ->orderBy('minggu')
            ->get();

        // Data untuk chart: total penjualan per produk (Bar Chart)
        $penjualanPerProduk = Penjualan::select('produk', DB::raw('SUM(total) as total_penjualan'))
            ->groupBy('produk')
            ->orderByDesc('total_penjualan')
            ->limit(10)
            ->get();

        // [Sesuai Soal UTS] Analisis: Penjualan per produk berdasarkan tanggal waktu per minggu
        // Disiapkan query-nya sebagai bukti proses Data Transformation & Analysis
        $penjualanProdukPerMinggu = Penjualan::select(
                'produk',
                DB::raw('YEAR(tanggal) as tahun'),
                DB::raw('WEEK(tanggal, 1) as minggu'),
                DB::raw('SUM(total) as total_penjualan')
            )
            ->groupBy('produk', 'tahun', 'minggu')
            ->orderBy('tahun')->orderBy('minggu')
            ->get();

        // Data untuk chart: distribusi kategori (Pie Chart)
        $distribusiKategori = Penjualan::select('kategori', DB::raw('SUM(total) as total_kategori'))
            ->groupBy('kategori')
            ->orderByDesc('total_kategori')
            ->get();

        // Data untuk chart: penjualan kategori per bulan (Bar Chart)
        $kategoriPerBulan = Penjualan::select(
                'kategori',
                DB::raw('MONTH(tanggal) as bulan'),
                DB::raw('YEAR(tanggal) as tahun'),
                DB::raw('SUM(total) as total_kategori')
            )
            ->groupBy('kategori', 'bulan', 'tahun')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        return view('dashboard.index', compact(
            'totalTransaksi',
            'totalPenjualan',
            'totalProduk',
            'kategoriTerlaris',
            'recentTransaksi',
            'trenMingguan',
            'penjualanPerProduk',
            'distribusiKategori',
            'kategoriPerBulan'
        ));
    }
}

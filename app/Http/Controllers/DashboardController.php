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
                DB::raw('YEARWEEK(tanggal, 1) as period'),
                DB::raw('SUM(total) as total_penjualan'),
                DB::raw('COUNT(*) as jumlah_transaksi')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(function($item) {
                // Format "202401" -> "2024-W01"
                $year = substr($item->period, 0, 4);
                $week = substr($item->period, 4);
                $item->minggu = $week;
                $item->tahun = $year;
                $item->label = $year . '-W' . $week;
                return $item;
            });

        // Data untuk chart: total penjualan per produk (Bar Chart)
        $penjualanPerProduk = Penjualan::select('produk', DB::raw('SUM(total) as total_penjualan'))
            ->groupBy('produk')
            ->orderByDesc('total_penjualan')
            ->limit(10)
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

    public function understanding()
    {
        return view('dashboard.understanding');
    }

    public function insight()
    {
        // 1. Total penjualan keseluruhan per produk
        $produkAnalisis = Penjualan::select('produk', DB::raw('SUM(total) as total_penjualan'), DB::raw('COUNT(*) as qty'))
            ->groupBy('produk')
            ->orderByDesc('total_penjualan')
            ->get();

        // 2. Penjualan produk per minggu
        $produkMingguan = Penjualan::select(
                'produk',
                DB::raw('YEARWEEK(tanggal, 1) as period'),
                DB::raw('SUM(total) as total_penjualan')
            )
            ->groupBy('produk', 'period')
            ->orderBy('period', 'desc')
            ->get()
            ->map(function($item) {
                $year = substr($item->period, 0, 4);
                $week = substr($item->period, 4);
                $item->minggu = $week;
                $item->tahun = $year;
                return $item;
            });

        // 3. Tren Kategori per Bulan
        $kategoriBulanan = Penjualan::select(
                'kategori',
                DB::raw('MONTH(tanggal) as bulan'),
                DB::raw('YEAR(tanggal) as tahun'),
                DB::raw('SUM(total) as total_penjualan')
            )
            ->groupBy('kategori', 'bulan', 'tahun')
            ->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')
            ->get();

        return view('dashboard.insight', compact('produkAnalisis', 'produkMingguan', 'kategoriBulanan'));
    }
}

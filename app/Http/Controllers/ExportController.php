<?php

namespace App\Http\Controllers;

use App\Exports\PenjualanExport;
use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function excel()
    {
        return Excel::download(new PenjualanExport(), 'laporan_penjualan.xlsx');
    }

    public function pdf()
    {
        $totalTransaksi  = Penjualan::count();
        $totalPenjualan  = Penjualan::sum('total');
        $totalProduk     = Penjualan::distinct('produk')->count('produk');

        $penjualanPerProduk = Penjualan::select('produk', DB::raw('SUM(total) as total_penjualan'))
            ->groupBy('produk')
            ->orderByDesc('total_penjualan')
            ->get();

        $distribusiKategori = Penjualan::select('kategori', DB::raw('SUM(total) as total_kategori'))
            ->groupBy('kategori')
            ->orderByDesc('total_kategori')
            ->get();

        $produkTerlaris = $penjualanPerProduk->first();

        $kategoriTertinggi = Penjualan::select('kategori', DB::raw('SUM(total) as total_kategori'))
            ->groupBy('kategori')
            ->orderByDesc('total_kategori')
            ->first();

        $pdf = Pdf::loadView('export.pdf', compact(
            'totalTransaksi',
            'totalPenjualan',
            'totalProduk',
            'penjualanPerProduk',
            'distribusiKategori',
            'produkTerlaris',
            'kategoriTertinggi'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan_penjualan.pdf');
    }
}

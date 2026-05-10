<?php

namespace App\Exports;

use App\Models\Penjualan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PenjualanExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Penjualan::orderBy('tanggal')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Tanggal', 'Produk', 'Kategori', 'Jumlah', 'Harga', 'Total', 'Dibuat', 'Diperbarui'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->tanggal->format('Y-m-d'),
            $row->produk,
            $row->kategori,
            $row->jumlah,
            $row->harga,
            $row->total,
            $row->created_at,
            $row->updated_at,
        ];
    }
}

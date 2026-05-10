<?php

namespace App\Imports;

use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PenjualanImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;
    public int $skipped  = 0;
    public array $errors = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 karena baris 1 adalah heading

            // --- Parsing tanggal ---
            try {
                $tanggal = Carbon::parse($row['tanggal'])->toDateString();
            } catch (\Exception $e) {
                $this->errors[] = "Baris {$rowNumber}: Format tanggal tidak valid ({$row['tanggal']}), baris dilewati.";
                $this->skipped++;
                continue;
            }

            // --- Null handling: produk kosong → hapus data ---
            $produk = trim((string) ($row['produk'] ?? ''));
            if ($produk === '') {
                $this->errors[] = "Baris {$rowNumber}: Kolom produk kosong, baris dilewati.";
                $this->skipped++;
                continue;
            }

            // --- Normalisasi teks ---
            $produk    = ucwords(strtolower(trim($produk)));
            $kategori  = ucwords(strtolower(trim((string) ($row['kategori'] ?? ''))));
            $kategori  = $kategori !== '' ? $kategori : 'Tidak Diketahui';

            // --- Null handling: numerik ---
            $jumlah = (int) ($row['jumlah'] ?? 0);
            $harga  = (float) ($row['harga']  ?? 0);

            // --- Validasi total: selalu hitung ulang ---
            $total = $jumlah * $harga;

            // --- Cegah duplikasi data ---
            $exists = Penjualan::where('tanggal',   $tanggal)
                ->where('produk',    $produk)
                ->where('kategori',  $kategori)
                ->where('jumlah',    $jumlah)
                ->where('harga',     $harga)
                ->exists();

            if ($exists) {
                $this->skipped++;
                continue;
            }

            Penjualan::create([
                'tanggal'  => $tanggal,
                'produk'   => $produk,
                'kategori' => $kategori,
                'jumlah'   => $jumlah,
                'harga'    => $harga,
                'total'    => $total,
            ]);

            $this->imported++;
        }

        // --- Query cleansing pasca-import: perbaiki total yang salah ---
        \DB::table('penjualan')
            ->whereRaw('total != jumlah * harga')
            ->update(['total' => \DB::raw('jumlah * harga')]);
    }
}

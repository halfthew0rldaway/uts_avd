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
        \DB::transaction(function () use ($rows) {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 karena baris 1 adalah heading

                // --- 1. Pengecekan Tanggal (Wajib Valid & Tidak Masa Depan) ---
                try {
                    if (empty($row['tanggal']) || strtolower(trim($row['tanggal'])) === 'null') {
                        throw new \Exception("Tanggal kosong/null");
                    }
                    $tanggal = \Carbon\Carbon::parse($row['tanggal']);
                    
                    if ($tanggal->isFuture()) {
                        throw new \Exception("Tanggal masa depan");
                    }
                    
                    $tanggalStr = $tanggal->toDateString();
                } catch (\Exception $e) {
                    $this->errors[] = "Baris {$rowNumber}: Tanggal tidak valid atau masa depan, baris dilewati.";
                    $this->skipped++;
                    continue;
                }

                // --- 2. Pengecekan Produk (Wajib Ada) ---
                $produk = trim((string) ($row['produk'] ?? ''));
                if ($produk === '' || strtolower($produk) === 'null') {
                    $this->errors[] = "Baris {$rowNumber}: Kolom produk kosong, baris dilewati.";
                    $this->skipped++;
                    continue;
                }
                $produk = ucwords(strtolower($produk));

                // --- 3. Pengecekan Numerik (Wajib Positif & Mendukung Desimal) ---
                $jumlah = (float) ($row['jumlah'] ?? 0);
                $harga  = (float) ($row['harga'] ?? 0);

                if ($jumlah <= 0 || $harga <= 0) {
                    $this->errors[] = "Baris {$rowNumber}: Jumlah/Harga harus > 0, baris dilewati.";
                    $this->skipped++;
                    continue;
                }

                // --- 4. Pengecekan Kategori (Repair: Isi Default) ---
                $kategori = trim((string) ($row['kategori'] ?? ''));
                if ($kategori === '' || strtolower($kategori) === 'null') {
                    $kategori = "Tidak Diketahui";
                } else {
                    $kategori = ucwords(strtolower($kategori));
                }

                // --- 5. Perhitungan Total (Kalkulasi Ulang Agar Akurat) ---
                $total = $jumlah * $harga;

                // --- 6. Pencegahan Duplikasi (Strict De-duplication) ---
                $exists = Penjualan::where([
                    'tanggal'  => $tanggalStr,
                    'produk'   => $produk,
                    'kategori' => $kategori,
                    'jumlah'   => $jumlah,
                    'harga'    => $harga,
                ])->exists();

                if ($exists) {
                    $this->skipped++;
                    continue;
                }

                Penjualan::create([
                    'tanggal'  => $tanggalStr,
                    'produk'   => $produk,
                    'kategori' => $kategori,
                    'jumlah'   => $jumlah,
                    'harga'    => $harga,
                    'total'    => $total,
                ]);

                $this->imported++;
            }
        });

        // --- Query cleansing pasca-import (Safety Net) ---
        \DB::table('penjualan')
            ->whereRaw('total != jumlah * harga')
            ->update(['total' => \DB::raw('jumlah * harga')]);
    }
}

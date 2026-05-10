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

            // --- 1. Pengecekan Tanggal (Wajib Valid) ---
            try {
                if (empty($row['tanggal']) || strtolower(trim($row['tanggal'])) === 'null') {
                    throw new \Exception("Tanggal kosong/null");
                }
                $tanggal = Carbon::parse($row['tanggal'])->toDateString();
            } catch (\Exception $e) {
                $this->errors[] = "Baris {$rowNumber}: Format tanggal tidak valid atau kosong, baris dilewati.";
                $this->skipped++;
                continue;
            }

            // --- 2. Pengecekan Produk (Wajib Ada) ---
            $produk = trim((string) ($row['produk'] ?? ''));
            if ($produk === '' || strtolower($produk) === 'null') {
                $this->errors[] = "Baris {$rowNumber}: Kolom produk kosong/null, baris dilewati.";
                $this->skipped++;
                continue;
            }
            $produk = ucwords(strtolower($produk));

            // --- 3. Pengecekan Kategori (Wajib Ada) ---
            $kategori = trim((string) ($row['kategori'] ?? ''));
            if ($kategori === '' || strtolower($kategori) === 'null') {
                $this->errors[] = "Baris {$rowNumber}: Kolom kategori kosong/null, baris dilewati.";
                $this->skipped++;
                continue;
            }
            $kategori = ucwords(strtolower($kategori));

            // --- 4. Pengecekan Numerik (Jumlah & Harga Wajib Valid) ---
            $jumlahRaw = trim((string) ($row['jumlah'] ?? ''));
            $hargaRaw  = trim((string) ($row['harga'] ?? ''));

            if ($jumlahRaw === '' || strtolower($jumlahRaw) === 'null' || !is_numeric($jumlahRaw) || (int)$jumlahRaw <= 0) {
                $this->errors[] = "Baris {$rowNumber}: Kolom jumlah tidak valid/kosong, baris dilewati.";
                $this->skipped++;
                continue;
            }

            if ($hargaRaw === '' || strtolower($hargaRaw) === 'null' || !is_numeric($hargaRaw) || (float)$hargaRaw <= 0) {
                $this->errors[] = "Baris {$rowNumber}: Kolom harga tidak valid/kosong, baris dilewati.";
                $this->skipped++;
                continue;
            }

            $jumlah = (int) $jumlahRaw;
            $harga  = (float) $hargaRaw;

            // --- 5. Perhitungan Total (Cleansing Total) ---
            $total = $jumlah * $harga;

            // --- 6. Pencegahan Duplikasi ---
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

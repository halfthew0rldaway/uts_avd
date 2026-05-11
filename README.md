# Dashboard Analitik Penjualan – UTS AVD

Proyek dashboard analitik dan visualisasi data penjualan ini disusun guna memenuhi persyaratan tugas **Ujian Tengah Semester (UTS)** pada mata kuliah **Analitik dan Visualisasi Data (AVD)**.

**Disusun Oleh:**
- **Nama:** Wisnu Widya Pradana
- **NIM:** 411231088
- **Instansi:** Universitas Dian Nusantara (UNDIRA)

---

## Landasan Teknologi

Proyek ini dikembangkan dengan mengintegrasikan berbagai teknologi modern untuk memastikan performa optimal, integritas data, serta estetika visual yang profesional:

### Sisi Belakang (Backend)
- **Kerangka Kerja:** Laravel 11 (PHP 8.2+)
- **Basis Data:** MySQL / MariaDB (Mesin Penyimpanan InnoDB)
- **Impor Data:** [Maatwebsite/Laravel-Excel](https://laravel-excel.com/) (mengintegrasikan transaksi basis data atomik)
- **Mesin Ekspor:** Laravel-Excel (XLSX) dan Barryvdh/Laravel-DomPDF (PDF)

### Sisi Depan (Frontend)
- **Tema:** [Sneat Admin Template](https://github.com/themeselection/sneat-bootstrap-html-laravel-admin-template-free) (Bootstrap 5 yang telah disesuaikan)
- **Tipografi:** 'Inter' (Utama) dan 'JetBrains Mono' (Potongan Kode)
- **Visualisasi:** [Chart.js v4](https://www.chartjs.org/) (Grafik Responsif dan Interaktif)
- **Ikon:** Bootstrap Icons v1.11.3

---

## Fitur Utama

### 1. Pipa Pembersihan Data (Data Cleansing Pipeline)
Sistem menerapkan mekanisme pembersihan data otomatis yang ketat selama proses impor berlangsung:
- **Atomisitas Data:** Implementasi `DB::transaction` menjamin integritas data; transaksi akan dibatalkan sepenuhnya apabila terjadi kesalahan pada berkas, sehingga mencegah data masuk secara parsial.
- **Validasi Ketat:** Sistem secara otomatis mengabaikan baris data yang memiliki nilai produk atau tanggal kosong, kuantitas atau harga bernilai negatif, serta tanggal yang melampaui waktu saat ini.
- **Perbaikan Otomatis:** Kategori yang kosong akan diisi dengan label "Tidak Diketahui" dan teks akan dinormalisasi menggunakan format kapitalisasi standar.
- **Kalkulasi Ulang:** Kolom total dihitung kembali secara internal berdasarkan perkalian jumlah dan harga untuk menjamin akurasi matematis.

### 2. Analitik Berpresisi Tinggi
- **Logika Agregasi:** Penggunaan fungsi `YEARWEEK(tanggal, 1)` memastikan pengelompokan tren mingguan tetap konsisten dan tidak terpecah saat terjadi pergantian tahun.
- **Wawasan Multi-Dimensi:** Penyajian analisis performa berdasarkan Produk Terlaris (Top 10), Distribusi Kategori per Bulan, dan Tren Transaksi secara waktu nyata (real-time).
- **Sistem Wawasan Otomatis:** Dashboard menyediakan ringkasan naratif otomatis mengenai pencapaian performa penjualan tertinggi.

### 3. Pelaporan Profesional
- **Ekspor Excel:** Menyediakan data mentah yang telah melalui proses pembersihan sehingga siap untuk diolah kembali.
- **Ekspor PDF:** Menghasilkan dokumen laporan formal dengan tata letak tabel yang rapi dan sistematis.

---

## Panduan Instalasi

1. **Pemasangan Dependensi**
   ```bash
   composer install
   ```

2. **Konfigurasi Lingkungan (Environment)**
   - Salin berkas `.env.example` menjadi `.env`.
   - Atur konfigurasi pada bagian `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.
   - Jalankan perintah `php artisan key:generate`.

3. **Migrasi Basis Data**
   ```bash
   php artisan migrate
   ```

4. **Menjalankan Aplikasi**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui peramban pada alamat: `http://127.0.0.1:8000`

---

## Aturan Pembersihan Data (Struktur Data)

| Kolom | Tipe Data | Logika Pembersihan |
| :--- | :--- | :--- |
| **tanggal** | Date | Penguraian otomatis; baris diabaikan jika kosong atau merupakan tanggal masa depan. |
| **produk** | String | Penghapusan spasi berlebih dan normalisasi teks; baris diabaikan jika kosong. |
| **kategori** | String | Diberi nilai bawaan "Tidak Diketahui" apabila kolom kosong. |
| **jumlah** | Decimal | Harus bernilai positif; mendukung nilai desimal untuk akurasi presisi. |
| **harga** | Decimal | Harus bernilai positif. |
| **total** | Decimal | Dihitung ulang melalui rumus: `jumlah * harga`. |

---
*Proyek ini dikembangkan sebagai bukti kompetensi dalam pengolahan, pembersihan, dan visualisasi data menggunakan kerangka kerja Laravel.*

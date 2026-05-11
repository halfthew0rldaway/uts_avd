# Dashboard Analitik Penjualan

Aplikasi web analitik dan visualisasi data penjualan, dibangun menggunakan **Laravel 11** dan **Bootstrap 5**. Project ini merupakan pemenuhan tugas Ujian Tengah Semester (UTS) mata kuliah **Analitik dan Visualisasi Data**.

## Fitur Utama Sesuai Ketentuan UTS

1. **Data Cleaning & Import (`App\Imports\PenjualanImport`)**
   - Import dataset dari format Excel (`.xlsx`, `.csv`).
   - Penolakan (Skip) baris data yang mengandung nilai `Null`, kosong, atau tidak lengkap.
   - Perbaikan dan validasi format tanggal secara otomatis.
   - Penyeragaman teks pada nama Produk dan Kategori.
   - Perhitungan ulang dan validasi otomatis nilai total (`jumlah * harga`).

2. **Data Transformation & Analysis (`App\Http\Controllers\DashboardController`)**
   - Agregasi nilai menggunakan fungsi `SUM`, `COUNT`, dan `GROUP BY`.
   - Analisis total penjualan keseluruhan per produk.
   - Analisis penjualan per produk berdasarkan waktu per minggu.
   - Analisis penjualan per kategori tiap bulan.
   - Analisis tren penjualan berdasarkan waktu dan jumlah transaksi.

3. **Data Visualization (`Chart.js`)**
   - Line Chart: Tren Penjualan Mingguan.
   - Bar Chart: Total Penjualan per Produk (Top 10).
   - Bar Chart: Penjualan Kategori per Bulan.
   - Pie Chart: Distribusi Kategori Penjualan.

4. **Export Data**
   - Export ke format **Excel** menggunakan library `Maatwebsite/Laravel-Excel`.
   - Export ke format **PDF** menggunakan library `Barryvdh/Laravel-Dompdf`.

## Persyaratan Sistem

- PHP >= 8.2
- Composer
- MySQL / MariaDB

## Langkah Instalasi & Setup

1. **Persiapan Direktori**
   Pastikan Anda berada di direktori aplikasi Laravel (tempat file `artisan` berada).

2. **Install Dependensi PHP**
   Jalankan perintah berikut pada terminal:
   ```bash
   composer install
   ```

3. **Setup Environment Variabel**
   Ubah nama file `.env.example` menjadi `.env` atau jalankan perintah:
   ```bash
   cp .env.example .env
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Setup Database**
   - Buat database baru di MySQL dengan nama `uts_avd` (atau nama lain).
   - Buka file `.env`, lalu atur koneksi database:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=uts_avd
     DB_USERNAME=root
     DB_PASSWORD=
     ```

6. **Jalankan Migrasi Database**
   ```bash
   php artisan migrate
   ```

7. **Jalankan Server Lokal**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser pada alamat: `http://127.0.0.1:8000`

## Cara Pengujian Aplikasi

1. Buka `http://127.0.0.1:8000` di browser.
2. Klik tombol **Import Data** di pojok kanan atas layar Dashboard.
3. Modal akan muncul. Pilih dan unggah file dataset Excel/CSV yang diberikan. Sistem otomatis melakukan *cleansing* (mengabaikan baris dengan data yang hilang/null/salah).
4. Setelah proses selesai, Dashboard akan memuat ulang dan menampilkan grafik hasil analisis terbaru.
5. Uji fitur **Export** (Excel & PDF) menggunakan tombol di kanan atas layar Dashboard.

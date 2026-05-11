# Dashboard Analitik Penjualan – UTS AVD

Proyek dashboard analitik dan visualisasi data penjualan ini disusun guna memenuhi persyaratan tugas **Ujian Tengah Semester (UTS)** pada mata kuliah **Analitik dan Visualisasi Data (AVD)**.

**Disusun Oleh:**
- **Nama:** Wisnu Widya Pradana
- **NIM:** 411231088
- **Instansi:** Universitas Dian Nusantara (UNDIRA)

---

## Tech Stack

Proyek ini dikembangkan dengan mengintegrasikan berbagai teknologi untuk memastikan performa optimal, integritas data, serta estetika visual yang profesional:

### Backend
- **Framework:** Laravel 11 (PHP 8.2+)
- **Database:** MySQL / MariaDB (InnoDB Storage Engine)
- **Data Import:** [Maatwebsite/Laravel-Excel](https://laravel-excel.com/) (mengintegrasikan atomic database transactions)
- **Export Engines:** Laravel-Excel (XLSX) dan Barryvdh/Laravel-DomPDF (PDF)

### Frontend
- **Theme:** [Sneat Admin Template](https://github.com/themeselection/sneat-bootstrap-html-laravel-admin-template-free) (Customized Bootstrap 5)
- **Typography:** 'Inter' (Primary) dan 'JetBrains Mono' (Code Snippets)
- **Visualisasi:** [Chart.js v4](https://www.chartjs.org/) (Responsive & Interactive Charts)
- **Icons:** Bootstrap Icons v1.11.3

---

## Fitur Utama

### 1. Data Cleansing Pipeline
Sistem menerapkan mekanisme pembersihan data otomatis yang ketat selama proses impor berlangsung:
- **Data Atomicity:** Implementasi `DB::transaction` menjamin integritas data; transaksi akan dibatalkan sepenuhnya apabila terjadi kesalahan pada file, sehingga mencegah data masuk secara parsial.
- **Strict Validation:** Sistem secara otomatis mengabaikan baris data yang memiliki nilai produk atau tanggal kosong, kuantitas atau harga bernilai negatif, serta tanggal yang melampaui waktu saat ini (future date).
- **Auto-Repair:** Kategori yang kosong akan diisi dengan label "Tidak Diketahui" dan teks akan dinormalisasi menggunakan format kapitalisasi standar (Ucwords).
- **Recalculation:** Kolom total dihitung kembali secara internal berdasarkan perkalian jumlah dan harga untuk menjamin akurasi matematis 100%.

### 2. High-Fidelity Analytics
- **Aggregation Logic:** Penggunaan fungsi `YEARWEEK(tanggal, 1)` memastikan pengelompokan tren mingguan tetap konsisten dan tidak terpecah saat terjadi pergantian tahun.
- **Multi-Dimensional Insight:** Analisis performa berdasarkan Produk Terlaris (Top 10), Distribusi Kategori per Bulan, dan Tren Transaksi secara real-time.
- **Auto-Insight System:** Dashboard menyediakan ringkasan naratif otomatis mengenai pencapaian performa penjualan tertinggi.

### 3. Professional Reporting
- **Excel Export:** Menyajikan data mentah yang telah melalui proses cleansing sehingga siap untuk diolah kembali.
- **PDF Export:** Menghasilkan dokumen laporan formal dengan tata letak tabel yang rapi dan sistematis.

---

## Langkah Instalasi

1. **Install Dependensi**
   ```bash
   composer install
   ```

2. **Setup Environment**
   - Salin file `.env.example` menjadi `.env`.
   - Konfigurasi `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.
   - Jalankan perintah `php artisan key:generate`.

3. **Migrasi Database**
   ```bash
   php artisan migrate
   ```

4. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser pada alamat: `http://127.0.0.1:8000`

---

## Aturan Cleansing Data (Struktur Data)

| Kolom | Tipe Data | Logika Cleansing |
| :--- | :--- | :--- |
| **tanggal** | Date | Parse otomatis; baris diabaikan jika kosong atau future date. |
| **produk** | String | Trim dan normalisasi teks; baris diabaikan jika kosong. |
| **kategori** | String | Diberi nilai bawaan "Tidak Diketahui" apabila kolom kosong. |
| **jumlah** | Decimal | Harus bernilai positif; mendukung nilai desimal untuk presisi tinggi. |
| **harga** | Decimal | Harus bernilai positif. |
| **total** | Decimal | Dihitung ulang melalui rumus: `jumlah * harga`. |

---
*Proyek ini dikembangkan sebagai bukti kompetensi dalam pengolahan, pembersihan, dan visualisasi data menggunakan framework Laravel.*

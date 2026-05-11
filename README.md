# Dashboard Analitik Penjualan – UTS AVD

Project dashboard analitik dan visualisasi data penjualan yang dibangun untuk memenuhi tugas **Ujian Tengah Semester (UTS)** mata kuliah **Analitik dan Visualisasi Data (AVD)**.

**Disusun Oleh:**
- **Nama:** Wisnu Widya Pradana
- **NIM:** 411231088
- **Kampus:** Universitas Dian Nusantara (UNDIRA)

---

## 🚀 Tech Stack

Project ini dikembangkan menggunakan kombinasi teknologi modern untuk menjamin performa, keamanan data, dan estetika visual yang premium:

### Backend (The Engine)
- **Framework:** Laravel 11 (PHP 8.2+)
- **Database:** MySQL / MariaDB (InnoDB Engine)
- **Data Import:** [Maatwebsite/Laravel-Excel](https://laravel-excel.com/) (dengan integrasi atomic database transactions)
- **Export Engines:** Laravel-Excel (XLSX) & Barryvdh/Laravel-DomPDF (PDF)

### Frontend (The Interface)
- **Theming:** Modern Premium Admin Template (Customized Bootstrap 5)
- **Main Color:** Solid Reddish-Orange Theme (`#ff4d4d`)
- **Typography:** 'Public Sans' (Google Fonts)
- **Visuals:** [Chart.js v4](https://www.chartjs.org/) (Responsive & Interactive Charts)
- **Icons:** Bootstrap Icons v1.11.3

---

## 🛠️ Fitur Unggulan

### 1. Robust Data Cleansing Pipeline
Sistem memiliki mekanisme pembersihan data otomatis yang ketat saat proses import:
- **Atomisitas Data:** Menggunakan `DB::transaction`, menjamin data tidak akan masuk "setengah-setengah" jika terjadi kegagalan file.
- **Strict Validation:** Mengabaikan (skip) baris dengan produk/tanggal kosong, kuantitas/harga negatif (outliers), atau tanggal di masa depan.
- **Auto-Repair:** Kategori kosong otomatis diisi sebagai "Tidak Diketahui" dan teks dinormalisasi menggunakan format `Ucwords`.
- **Recalculation:** Menghitung ulang kolom `total` (`jumlah * harga`) secara internal untuk menjamin akurasi matematis 100%.

### 2. High-Fidelity Analytics
- **Aggregation Logic:** Menggunakan `YEARWEEK(tanggal, 1)` untuk pengelompokan tren mingguan yang solid (menghindari pemisahan grup saat pergantian tahun).
- **Multi-Dimensional Insight:** Analisis performa berdasarkan Produk (Top 10), Kategori per Bulan, dan Tren Transaksi Real-time.
- **Auto-Insight System:** Memberikan ringkasan naratif otomatis mengenai performa penjualan tertinggi di dashboard.

### 3. Professional Reporting
- **Excel Export:** Menyajikan data mentah hasil cleansing yang siap diolah lebih lanjut.
- **PDF Export:** Laporan dokumen formal dengan layout tabel yang bersih.

---

## ⚙️ Langkah Instalasi

1. **Clone & Install Dependensi**
   ```bash
   composer install
   ```

2. **Setup Environment**
   - Copy `.env.example` menjadi `.env`.
   - Konfigurasi `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.
   - Jalankan `php artisan key:generate`.

3. **Migrasi Database**
   ```bash
   php artisan migrate
   ```

4. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```
   Akses melalui browser di: `http://127.0.0.1:8000`

---

## 📂 Struktur Data (Cleansing Logic)

| Kolom | Tipe | Aturan Cleansing |
| :--- | :--- | :--- |
| **tanggal** | Date | Parse otomatis, skip jika kosong/future date |
| **produk** | String | Trim & Normalisasi case, skip jika kosong |
| **kategori** | String | Default "Tidak Diketahui" jika kosong |
| **jumlah** | Decimal | Harus > 0, mendukung nilai desimal (timbangan) |
| **harga** | Decimal | Harus > 0 |
| **total** | Decimal | Recalculated: `jumlah * harga` |

---
*Project ini dikembangkan sebagai bukti kompetensi dalam pengolahan, pembersihan, dan visualisasi data menggunakan framework Laravel.*

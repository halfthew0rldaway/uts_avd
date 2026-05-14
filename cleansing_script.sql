-- ============================================================
--  FILE   : cleansing_script.sql
--  NAMA   : Wisnu Widya Pradana
--  NIM    : 411231088
--  MATKUL : Analitik dan Visualisasi Data
--  DESKRIPSI:
--    Script SQL untuk proses Data Cleaning pada tabel penjualan.
--    Logika ini merepresentasikan proses yang diimplementasikan
--    di app/Imports/PenjualanImport.php saat proses import data dari Excel.
--
--  CATATAN PENTING:
--    - Script ini adalah REPRESENTASI SQL dari logika PHP.
--    - Proses cleaning asli berjalan otomatis di PHP saat user
--      mengupload file Excel melalui fitur Import di dashboard.
--    - Kategori 'ATK' tersimpan sebagai 'Atk' di DB karena
--      PHP menggunakan ucwords() → sesuaikan jika perlu.
--    - Data hasil cleaning: 289 baris valid dari 300 baris raw.
--      (7 baris di-skip: 4 tanggal invalid, 3 produk NULL)
-- ============================================================

-- ------------------------------------------------------------
-- LANGKAH 1: IDENTIFIKASI DATA KOTOR (DIAGNOSIS)
-- ------------------------------------------------------------

-- 1.1 Cek baris dengan tanggal NULL atau kosong
-- Sumber: PenjualanImport.php baris 25 → empty($row['tanggal']) || strtolower(trim()) === 'null'
SELECT id, tanggal, produk, kategori, jumlah, harga, total
FROM penjualan
WHERE tanggal IS NULL;

-- 1.2 Cek baris dengan produk NULL atau kosong
-- Sumber: PenjualanImport.php baris 42–47 → $produk === '' || strtolower($produk) === 'null' → skip
SELECT id, tanggal, produk, kategori, jumlah, harga, total
FROM penjualan
WHERE produk IS NULL OR TRIM(produk) = '' OR LOWER(TRIM(produk)) = 'null';

-- 1.3 Cek baris dengan kategori NULL atau kosong
-- Sumber: PenjualanImport.php baris 61–66 → jika kosong, diisi 'Tidak Diketahui' (tidak di-skip)
SELECT id, tanggal, produk, kategori, jumlah, harga, total
FROM penjualan
WHERE kategori IS NULL OR TRIM(kategori) = '' OR LOWER(TRIM(kategori)) = 'null';

-- 1.4 Cek baris dengan jumlah atau harga tidak valid (<= 0)
-- Sumber: PenjualanImport.php baris 54–58 → $jumlah <= 0 || $harga <= 0 → skip
SELECT id, tanggal, produk, kategori, jumlah, harga, total
FROM penjualan
WHERE jumlah <= 0 OR harga <= 0;

-- 1.5 Cek baris dengan nilai total yang tidak sesuai (jumlah x harga)
-- Sumber: PenjualanImport.php baris 69 → $total = $jumlah * $harga (rekalkulasi)
--         + safety net baris 99–101 → UPDATE WHERE total != jumlah * harga
SELECT id, tanggal, produk, jumlah, harga, total,
       (jumlah * harga) AS total_seharusnya,
       ABS(total - (jumlah * harga)) AS selisih
FROM penjualan
WHERE ABS(total - (jumlah * harga)) > 0.01;

-- 1.6 Cek duplikasi data (kombinasi 5 kolom)
-- Sumber: PenjualanImport.php baris 72–83 → Penjualan::where([5 kolom])->exists() → skip jika ada
--         + Migration → UNIQUE KEY uq_transaksi (tanggal, produk, kategori, jumlah, harga)
SELECT tanggal, produk, kategori, jumlah, harga,
       COUNT(*) AS jumlah_duplikat
FROM penjualan
GROUP BY tanggal, produk, kategori, jumlah, harga
HAVING COUNT(*) > 1;

-- 1.7 Ringkasan statistik data sebelum cleaning
SELECT
    COUNT(*)                                        AS total_baris,
    SUM(CASE WHEN produk IS NULL
              OR TRIM(produk) = '' THEN 1 ELSE 0 END) AS produk_null,
    SUM(CASE WHEN kategori IS NULL
              OR TRIM(kategori) = '' THEN 1 ELSE 0 END) AS kategori_null,
    SUM(CASE WHEN jumlah <= 0 OR harga <= 0 THEN 1 ELSE 0 END) AS numerik_invalid,
    SUM(CASE WHEN ABS(total - (jumlah * harga)) > 0.01 THEN 1 ELSE 0 END) AS total_salah
FROM penjualan;


-- ------------------------------------------------------------
-- LANGKAH 2: PROSES CLEANING (TRANSFORMASI)
-- ------------------------------------------------------------

-- 2.1 Hapus baris dengan produk NULL/kosong
--     Sumber: PenjualanImport.php baris 42–47
--     Di PHP: baris di-skip saat import → tidak pernah masuk DB
--     Query ini untuk membersihkan jika ada data lama yang lolos
DELETE FROM penjualan
WHERE produk IS NULL
   OR TRIM(produk) = ''
   OR LOWER(TRIM(produk)) = 'null';

-- 2.2 Isi default kategori yang NULL/kosong → 'Tidak Diketahui'
--     Sumber: PenjualanImport.php baris 61–66
--     Di PHP: $kategori = "Tidak Diketahui" jika kosong/null
UPDATE penjualan
SET kategori = 'Tidak Diketahui',
    updated_at = NOW()
WHERE kategori IS NULL
   OR TRIM(kategori) = ''
   OR LOWER(TRIM(kategori)) = 'null';

-- 2.3 Normalisasi kapitalisasi teks produk (Title Case simulasi di SQL)
--     (Di PHP: ucwords(strtolower(trim($produk))))
--     Catatan: MySQL tidak punya fungsi title case native,
--     normalisasi ini dilakukan di level aplikasi (PHP).
--     Query berikut hanya untuk dokumentasi referensi:
-- UPDATE penjualan
-- SET produk = CONCAT(UPPER(SUBSTRING(LOWER(TRIM(produk)), 1, 1)),
--                     LOWER(SUBSTRING(TRIM(produk), 2))),
--     updated_at = NOW();

-- 2.4 Rekalkulasi total yang salah (Safety Net)
--     Sumber: PenjualanImport.php baris 69 → $total = $jumlah * $harga
--             + baris 99–101 → DB::table('penjualan')->whereRaw('total != jumlah * harga')->update(...)
UPDATE penjualan
SET total = jumlah * harga,
    updated_at = NOW()
WHERE ABS(total - (jumlah * harga)) > 0.01;

-- 2.5 Hapus duplikasi — simpan hanya id terkecil per kombinasi unik
--     Sumber: PenjualanImport.php baris 72–83 → Penjualan::where([...])->exists() → skip
--             + Migration → UNIQUE KEY uq_transaksi mencegah duplikat di level DB
DELETE p1
FROM penjualan p1
INNER JOIN penjualan p2
    ON  p1.tanggal  = p2.tanggal
    AND p1.produk   = p2.produk
    AND p1.kategori = p2.kategori
    AND p1.jumlah   = p2.jumlah
    AND p1.harga    = p2.harga
    AND p1.id > p2.id;


-- ------------------------------------------------------------
-- LANGKAH 3: VERIFIKASI HASIL CLEANING
-- ------------------------------------------------------------

-- 3.1 Total baris setelah cleaning
SELECT COUNT(*) AS total_baris_bersih FROM penjualan;

-- 3.2 Pastikan tidak ada total yang salah
SELECT COUNT(*) AS sisa_total_salah
FROM penjualan
WHERE ABS(total - (jumlah * harga)) > 0.01;

-- 3.3 Pastikan tidak ada kategori NULL
SELECT COUNT(*) AS sisa_kategori_null
FROM penjualan
WHERE kategori IS NULL OR TRIM(kategori) = '';

-- 3.4 Pastikan tidak ada duplikasi
SELECT COUNT(*) AS sisa_duplikat
FROM (
    SELECT tanggal, produk, kategori, jumlah, harga, COUNT(*) AS cnt
    FROM penjualan
    GROUP BY tanggal, produk, kategori, jumlah, harga
    HAVING COUNT(*) > 1
) AS dup;

-- 3.5 Distribusi data per kategori setelah cleaning
-- Sumber: DashboardController.php baris 49–52 → distribusiKategori (Pie Chart)
SELECT
    kategori,
    COUNT(*)       AS jumlah_transaksi,
    SUM(total)     AS total_penjualan,
    AVG(total)     AS rata_rata_transaksi,
    MIN(tanggal)   AS tanggal_awal,
    MAX(tanggal)   AS tanggal_akhir
FROM penjualan
GROUP BY kategori
ORDER BY total_penjualan DESC;

-- 3.6 Tren mingguan setelah cleaning
-- Sumber: DashboardController.php baris 23–39 → trenMingguan (Line Chart)
SELECT
    YEARWEEK(tanggal, 1)    AS minggu,
    COUNT(*)                AS jumlah_transaksi,
    SUM(total)              AS total_penjualan
FROM penjualan
GROUP BY minggu
ORDER BY minggu;

-- ============================================================
-- END OF CLEANSING SCRIPT
-- ============================================================

# ATK POS (Sistem Kasir dan Manajemen Stok Alat Tulis Kantor)

Aplikasi POS (Point of Sale) dan Manajemen Stok berbasis web untuk operasional toko alat tulis kantor. Dibangun menggunakan **Laravel**, **MySQL**, **Bootstrap 5**, **Font Awesome**, dan **Chart.js**.

---

## 🚀 Fitur Utama Aplikasi

### 1. Dashboard Interaktif & Statistik
- **Total Produk ATK Aktif**
- **Total Fisik Stok Barang di Toko/Gudang**
- **Jumlah Transaksi & Omset Penjualan Hari Ini**
- **Estimasi Keuntungan Kotor (Laba Kotor = Penjualan - HPP Modal)**
- **Peringatan Barang Stok Menipis & Stok Habis**
- **Grafik Penjualan Harian (7 Hari Terakhir) & Bulanan (Tahun Berjalan) dengan Chart.js**
- **Peringkat Produk Terlaris & 5 Transaksi Terakhir**

### 2. Halaman Kasir / Penjualan (POS Modern)
- Pencarian produk cepat berdasarkan nama, kode barang (`BRG-0001`), atau scan barcode
- Filter kategori produk dengan tombol pill cepat
- Keranjang kasir dengan penambahan, pengurangan, dan input jumlah barang secara fleksibel
- Perhitungan subtotal, diskon transaksi (Rp), dan total pembayaran otomatis
- Tombol nominal cepat (*Quick Cash*): Uang Pas, Rp 10.000, 20.000, 50.000, 100.000, 200.000
- Perhitungan uang kembalian otomatis
- Metode pembayaran: **Tunai**, **QRIS**, **Transfer Bank**, dan **Debit**
- Validasi ketat ketersediaan stok (mencegah penjualan jika stok tidak mencukupi)
- Pengurangan stok otomatis & pencatatan ke kartu mutasi stok melalui **Database Transaction**
- Modal cetak struk kasir (*thermal receipt 58mm/80mm*) dengan tombol cetak langsung

### 3. Riwayat Transaksi Penjualan
- Pencarian nomor struk / transaksi (`TRX-YYYYMMDD-XXXX`)
- Filter berdasarkan rentang tanggal, kasir yang bertugas, dan metode pembayaran
- Detail lengkap item yang dibeli, harga satuan, diskon, dan kembalian
- Fitur cetak ulang struk belanja

### 4. Manajemen Data Barang (Master Produk)
- Kode barang otomatis (`BRG-XXXX`) dan barcode opsional
- Satuan barang fleksibel: Pcs, Rim, Box, Lusin, Pak, Botol, Buah, Set, Roll, dll.
- Harga beli (modal) & harga jual dengan *live preview* kalkulasi laba dan persentase margin
- Batas stok minimum dengan badge indikator:
  - **Stok Aman** (Hijau): Stok > Minimum
  - **Stok Menipis** (Kuning): Stok <= Minimum & > 0
  - **Stok Habis** (Merah): Stok = 0
- Filter kategori, filter status stok, dan pencarian cepat

### 5. Manajemen Kategori & Supplier
- Kategori barang (Alat Tulis, Kertas, Perlengkapan Kantor, Gambar, Printer, Arsip)
- Supplier/Distributor lengkap dengan nomor telepon, email, alamat, dan riwayat PO

### 6. Barang Masuk / Pembelian dari Supplier
- Nomor PO otomatis (`PO-YYYYMMDD-XXXX`)
- Pilih supplier dan tambahkan multi-barang secara dinamis (repeater table)
- Metode pembayaran: Tunai, Transfer Bank, Tempo (Kredit)
- Status pembayaran: Lunas / Belum Lunas
- Stok barang bertambah otomatis secara real-time dan harga beli diperbarui
- Cetak bukti surat penerimaan barang / PO

### 7. Barang Keluar (Non-Penjualan)
- Pencatatan barang keluar selain penjualan:
  - Barang Rusak (*Damage*)
  - Barang Hilang (*Lost*)
  - Keperluan Toko / Kantor
  - Penyesuaian Stok (*Stock Opname*)
  - Cacat Pabrik / Expired
- Stok berkurang secara otomatis dan dicatat permanen dalam riwayat mutasi

### 8. Manajemen Stok & Kartu Stok (Stock Card)
- Monitoring persediaan seluruh barang di toko
- **Fitur Kartu Stok Lengkap**:
  - Riwayat kronologis mutasi: Tanggal, Tipe (Pembelian, Penjualan, Barang Keluar, Penyesuaian)
  - Nomor referensi struk/PO
  - Jumlah masuk (+), Jumlah keluar (-), dan Sisa saldo stok fisik
  - Alasan & keterangan mutasi serta nama operator/petugas

### 9. Laporan Komprehensif & Ekspor Data
- Filter laporan berdasarkan rentang tanggal (*Date Range*)
- Enam jenis laporan utama:
  1. **Laporan Penjualan**: Rekap omset, kasir, diskon, dan metode bayar
  2. **Laporan Keuntungan Kotor**: Analisa detail laba kotor (Omset - HPP Pokok) dan margin %
  3. **Laporan Pembelian**: Rekap belanja dari supplier
  4. **Laporan Barang Keluar**: Rekap barang rusak, hilang, atau keperluan toko
  5. **Laporan Valuasi Persediaan**: Total aset modal beli vs potensi nilai omset toko
  6. **Laporan Produk Terlaris**: Ranking produk dengan penjualan terbanyak
- **Ekspor ke Excel** (Format CSV ber-BOM UTF-8, rapi langsung dibuka di Microsoft Excel)
- **Cetak / PDF** (Format siap cetak dengan layout profesional dan tanda tangan)

### 10. Hak Akses & Keamanan Pengguna
- **Role Administrator**: Akses penuh ke seluruh menu, data master, pembelian, stok keluar, laporan keuangan, dan manajemen pengguna.
- **Role Kasir**: Akses fokus ke halaman Kasir POS, Riwayat Penjualan, dan Cetak Struk (dilindungi Middleware, kasir tidak dapat mengakses laporan keuntungan, data user, atau stok modal).

---

## 🔑 Akun Login Default

| Role | Email Login | Password | Hak Akses |
|---|---|---|---|
| **Administrator** | `admin@atkpos.com` | `admin123` | Akses Penuh (Dashboard, Master, Beli, Keluar, Laporan, User) |
| **Kasir** | `kasir@atkpos.com` | `kasir123` | Kasir POS, Riwayat Penjualan, Cetak Struk |

> *Tersedia tombol satu-klik "Akun Uji Coba Cepat" pada halaman login untuk pengujian instan.*

---

## ⚙️ Petunjuk Menjalankan Project Secara Lokal

### Prasyarat
- PHP >= 8.2 (dengan ekstensi pdo_mysql, mbstring, openssl)
- MySQL Server (Port 3306)

### Langkah-langkah:
1. Pastikan database MySQL aktif dan database `kasir_atk` tersedia:
   ```sql
   CREATE DATABASE IF NOT EXISTS kasir_atk;
   ```
2. Konfigurasi file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=kasir_atk
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Jalankan migration dan seeder data awal:
   ```bash
   php artisan migrate:fresh --seed
   ```
4. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
5. Buka browser di alamat:
   **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

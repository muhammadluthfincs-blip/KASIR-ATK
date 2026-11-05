<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Users (Admin & Kasir)
        $admin = User::updateOrCreate(
            ['email' => 'admin@atkpos.com'],
            [
                'name' => 'Administrator Toko',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        $kasir = User::updateOrCreate(
            ['email' => 'kasir@atkpos.com'],
            [
                'name' => 'Kasir Utama',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'phone' => '089876543210',
                'is_active' => true,
            ]
        );

        // 2. Create Categories
        $categoriesData = [
            ['name' => 'Alat Tulis', 'code' => 'ATK', 'description' => 'Berbagai perlengkapan alat tulis harian seperti pena, pensil, penghapus'],
            ['name' => 'Kertas', 'code' => 'KTS', 'description' => 'Kertas HVS, buku tulis, nota, dan kertas keperluan kantor/sekolah'],
            ['name' => 'Perlengkapan Kantor', 'code' => 'PK', 'description' => 'Stapler, gunting, cutter, lakban, penggaris, dan aksesoris kantor'],
            ['name' => 'Peralatan Menggambar', 'code' => 'PM', 'description' => 'Pensil warna, krayon, cat air, buku gambar, kuas'],
            ['name' => 'Perlengkapan Printer', 'code' => 'PP', 'description' => 'Tinta printer, cartridge, pita ribbon, dan kertas foto'],
            ['name' => 'Perlengkapan Arsip', 'code' => 'PA', 'description' => 'Map kancing, stopmap, ordner, binder, box file'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['code']] = Category::updateOrCreate(['code' => $cat['code']], $cat);
        }

        // 3. Create Suppliers
        $suppliersData = [
            [
                'name' => 'PT. Sinar Dunia Sentosa',
                'phone' => '021-5551234',
                'email' => 'order@sidu.co.id',
                'address' => 'Jl. Industri Rungkut No. 45, Surabaya',
                'description' => 'Distributor resmi buku tulis dan kertas SiDu',
            ],
            [
                'name' => 'CV. Joyko Stationery Mandiri',
                'phone' => '021-8889999',
                'email' => 'sales@joykopusat.com',
                'address' => 'Kawasan Industri Pulogadung Blok B No. 12, Jakarta Timur',
                'description' => 'Penyedia produk peralatan kantor dan alat tulis merek Joyko',
            ],
            [
                'name' => 'PT. Faber-Castell International',
                'phone' => '021-7776543',
                'email' => 'sales.id@faber-castell.com',
                'address' => 'Jl. Raya Narogong Km 14, Cileungsi, Bogor',
                'description' => 'Distributor pensil, alat gambar, dan perlengkapan artistik',
            ],
            [
                'name' => 'CV. PaperOne Distribusi Prima',
                'phone' => '022-4217890',
                'email' => 'marketing@paperoneprima.com',
                'address' => 'Jl. Soekarno Hatta No. 280, Bandung',
                'description' => 'Supplier kertas HVS berbagai gramatur dan ukuran',
            ],
            [
                'name' => 'PT. Sentra Tinta Nusantara (Epson & Canon Partner)',
                'phone' => '021-3456789',
                'email' => 'cs@sentratinta.co.id',
                'address' => 'Mangga Dua Mall Lt. 3 No. 42, Jakarta Pusat',
                'description' => 'Penyedia tinta original botolan untuk printer Epson dan Canon',
            ],
        ];

        foreach ($suppliersData as $sup) {
            Supplier::updateOrCreate(['name' => $sup['name']], $sup);
        }

        // 4. Create Sample ATK Products
        $productsData = [
            [
                'kode_barang' => 'BRG-0001',
                'barcode' => '8992345000012',
                'nama_barang' => 'Buku Tulis Sidu 38 Lembar',
                'category_code' => 'KTS',
                'satuan' => 'Pak',
                'harga_beli' => 28000,
                'harga_jual' => 35000,
                'stok' => 25,
                'stok_minimum' => 5,
                'deskripsi' => '1 Pak isi 10 buku tulis ukuran kwarto 38 lembar.',
            ],
            [
                'kode_barang' => 'BRG-0002',
                'barcode' => '8992345000029',
                'nama_barang' => 'Pulpen Standard AE7 0.5 Black',
                'category_code' => 'ATK',
                'satuan' => 'Lusin',
                'harga_beli' => 22000,
                'harga_jual' => 28000,
                'stok' => 30,
                'stok_minimum' => 5,
                'deskripsi' => 'Pulpen tinta hitam ballpoint 0.5mm tip lancar dan pekat.',
            ],
            [
                'kode_barang' => 'BRG-0003',
                'barcode' => '8992345000036',
                'nama_barang' => 'Pensil 2B Faber-Castell',
                'category_code' => 'PM',
                'satuan' => 'Pcs',
                'harga_beli' => 3500,
                'harga_jual' => 5000,
                'stok' => 50,
                'stok_minimum' => 10,
                'deskripsi' => 'Pensil standar ujian komputer 2B original Faber-Castell.',
            ],
            [
                'kode_barang' => 'BRG-0004',
                'barcode' => '8992345000043',
                'nama_barang' => 'Penghapus Joyko Hitam 526-B40',
                'category_code' => 'ATK',
                'satuan' => 'Pcs',
                'harga_beli' => 2000,
                'harga_jual' => 3500,
                'stok' => 40,
                'stok_minimum' => 10,
                'deskripsi' => 'Penghapus pensil debu minimal tidak merusak kertas.',
            ],
            [
                'kode_barang' => 'BRG-0005',
                'barcode' => '8992345000050',
                'nama_barang' => 'Penggaris Besi 30cm Joyko',
                'category_code' => 'PK',
                'satuan' => 'Buah',
                'harga_beli' => 6000,
                'harga_jual' => 9000,
                'stok' => 20,
                'stok_minimum' => 5,
                'deskripsi' => 'Mistar stainless steel 30cm presisi dan tahan lama.',
            ],
            [
                'kode_barang' => 'BRG-0006',
                'barcode' => '8992345000067',
                'nama_barang' => 'Spidol Snowman Boardmarker Hitam BG-12',
                'category_code' => 'ATK',
                'satuan' => 'Pcs',
                'harga_beli' => 7500,
                'harga_jual' => 10000,
                'stok' => 35,
                'stok_minimum' => 5,
                'deskripsi' => 'Spidol papan tulis putih mudah dihapus dan tidak berbau tajam.',
            ],
            [
                'kode_barang' => 'BRG-0007',
                'barcode' => '8992345000074',
                'nama_barang' => 'Kertas HVS PaperOne A4 75gr',
                'category_code' => 'KTS',
                'satuan' => 'Rim',
                'harga_beli' => 45000,
                'harga_jual' => 52000,
                'stok' => 40,
                'stok_minimum' => 10,
                'deskripsi' => 'Kertas fotokopi & print putih cerah 500 lembar A4.',
            ],
            [
                'kode_barang' => 'BRG-0008',
                'barcode' => '8992345000081',
                'nama_barang' => 'Kertas HVS Sinar Dunia F4 70gr',
                'category_code' => 'KTS',
                'satuan' => 'Rim',
                'harga_beli' => 48000,
                'harga_jual' => 55000,
                'stok' => 30,
                'stok_minimum' => 10,
                'deskripsi' => 'Kertas F4 / Folio 70 gram 500 lembar kualitas SiDu.',
            ],
            [
                'kode_barang' => 'BRG-0009',
                'barcode' => '8992345000098',
                'nama_barang' => 'Map Folio Kancing Plastik',
                'category_code' => 'PA',
                'satuan' => 'Lusin',
                'harga_beli' => 25000,
                'harga_jual' => 35000,
                'stok' => 15,
                'stok_minimum' => 5,
                'deskripsi' => 'Map plastik tebal kancing satu untuk dokumen ukuran folio.',
            ],
            [
                'kode_barang' => 'BRG-0010',
                'barcode' => '8992345000104',
                'nama_barang' => 'Map Ordner Bantex F4 7cm',
                'category_code' => 'PA',
                'satuan' => 'Buah',
                'harga_beli' => 22000,
                'harga_jual' => 30000,
                'stok' => 18,
                'stok_minimum' => 5,
                'deskripsi' => 'Ordner lever arch file tebal 7cm bahan karton lapis plastik.',
            ],
            [
                'kode_barang' => 'BRG-0011',
                'barcode' => '8992345000111',
                'nama_barang' => 'Stapler Max HD-10 Original',
                'category_code' => 'PK',
                'satuan' => 'Buah',
                'harga_beli' => 18000,
                'harga_jual' => 25000,
                'stok' => 12,
                'stok_minimum' => 4,
                'deskripsi' => 'Stapler jepretan Max Japan kuat untuk isi staples No.10.',
            ],
            [
                'kode_barang' => 'BRG-0012',
                'barcode' => '8992345000128',
                'nama_barang' => 'Isi Staples Max No. 10-1M',
                'category_code' => 'PK',
                'satuan' => 'Box',
                'harga_beli' => 2500,
                'harga_jual' => 4500,
                'stok' => 50,
                'stok_minimum' => 10,
                'deskripsi' => 'Isi staples Max original 1000 staples per box kecil.',
            ],
            [
                'kode_barang' => 'BRG-0013',
                'barcode' => '8992345000135',
                'nama_barang' => 'Tinta Printer Epson 003 Black 65ml',
                'category_code' => 'PP',
                'satuan' => 'Botol',
                'harga_beli' => 75000,
                'harga_jual' => 95000,
                'stok' => 15,
                'stok_minimum' => 3,
                'deskripsi' => 'Tinta botol original Epson L3110 / L3210 Black 65ml.',
            ],
            [
                'kode_barang' => 'BRG-0014',
                'barcode' => '8992345000142',
                'nama_barang' => 'Tinta Printer Canon GI-790 Cyan',
                'category_code' => 'PP',
                'satuan' => 'Botol',
                'harga_beli' => 85000,
                'harga_jual' => 105000,
                'stok' => 10,
                'stok_minimum' => 3,
                'deskripsi' => 'Tinta original Canon G1010 / G2010 seri GI-790 Cyan.',
            ],
            [
                'kode_barang' => 'BRG-0015',
                'barcode' => '8992345000159',
                'nama_barang' => 'Cutter Joyko L-500 Besar',
                'category_code' => 'PK',
                'satuan' => 'Buah',
                'harga_beli' => 12000,
                'harga_jual' => 18000,
                'stok' => 4, // sengaja menipis untuk pengujian stok menipis
                'stok_minimum' => 5,
                'deskripsi' => 'Pisau cutter besar mata pisau lebar dan pengunci putar.',
            ],
            [
                'kode_barang' => 'BRG-0016',
                'barcode' => '8992345000166',
                'nama_barang' => 'Lakban Bening Nachi Tape 2 Inch',
                'category_code' => 'PK',
                'satuan' => 'Roll',
                'harga_beli' => 9000,
                'harga_jual' => 13000,
                'stok' => 0, // sengaja habis untuk pengujian stok habis
                'stok_minimum' => 5,
                'deskripsi' => 'Lakban packing bening kuat dan daya rekat tinggi Nachi 48mm.',
            ],
        ];

        foreach ($productsData as $prod) {
            $catCode = $prod['category_code'];
            unset($prod['category_code']);
            $prod['category_id'] = $categories[$catCode]->id ?? null;

            $product = Product::updateOrCreate(
                ['kode_barang' => $prod['kode_barang']],
                $prod
            );

            // Create initial stock movement if stock > 0
            if ($product->stok > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $admin->id,
                    'tanggal' => now(),
                    'tipe' => 'penyesuaian',
                    'referensi' => 'STOK-AWAL',
                    'jumlah_masuk' => $product->stok,
                    'jumlah_keluar' => 0,
                    'sisa_stok' => $product->stok,
                    'alasan' => 'Penyesuaian Stok',
                    'keterangan' => 'Saldo stok awal aplikasi ATK POS',
                ]);
            }
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('tanggal');
            $table->enum('tipe', ['masuk', 'keluar', 'penjualan', 'pembelian', 'penyesuaian']);
            $table->string('referensi')->nullable(); // nomor_transaksi, nomor_pembelian, etc.
            $table->integer('jumlah_masuk')->default(0);
            $table->integer('jumlah_keluar')->default(0);
            $table->integer('sisa_stok')->default(0);
            $table->string('alasan')->nullable(); // Barang Rusak, Barang Hilang, Keperluan Toko, Penjualan, Pembelian, dll.
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};


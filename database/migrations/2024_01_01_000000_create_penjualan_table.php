<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('produk');
            $table->string('kategori')->default('Tidak Diketahui');
            $table->integer('jumlah')->default(0);
            $table->decimal('harga', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();

            // Unique constraint untuk mencegah duplikasi data transaksi
            $table->unique(['tanggal', 'produk', 'kategori', 'jumlah', 'harga'], 'uq_transaksi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};

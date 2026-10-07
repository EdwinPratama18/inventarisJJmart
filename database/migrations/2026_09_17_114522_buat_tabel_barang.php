<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 50)->unique();
            $table->string('nama', 150);
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->nullOnDelete();
            $table->foreignId('pemasok_id')->nullable()->constrained('pemasok')->nullOnDelete();
            $table->string('satuan', 30)->default('pcs');
            $table->integer('stok_saat_ini')->default(0);
            $table->integer('stok_minimum')->default(0)->comment('Safety stock');
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->integer('lead_time_hari')->default(7)->comment('Hari pengiriman dari pemasok');
            $table->decimal('rata_pemakaian_harian', 10, 4)->default(0);
            $table->integer('rop')->default(0)->comment('Reorder Point');
            $table->string('lokasi_rak', 50)->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};

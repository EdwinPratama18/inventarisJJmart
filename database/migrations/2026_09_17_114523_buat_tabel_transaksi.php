<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnDelete();
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->integer('jumlah');
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->string('referensi', 100)->nullable()->comment('No. PO, faktur, dll.');
            $table->foreignId('pengguna_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal')->useCurrent();
            $table->timestamps();

            $table->index('barang_id');
            $table->index(['barang_id', 'jenis', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};

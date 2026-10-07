<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnDelete();
            $table->string('jenis_notifikasi', 50)->default('stok_rendah');
            $table->string('status', 20)->default('terkirim')->comment('terkirim, gagal');
            $table->text('pesan');
            $table->string('id_chat_telegram', 100)->nullable();
            $table->text('respon_api')->nullable();
            $table->timestamp('dikirim_pada')->useCurrent();
            $table->timestamps();

            $table->index(['barang_id', 'dikirim_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_notifikasi');
    }
};

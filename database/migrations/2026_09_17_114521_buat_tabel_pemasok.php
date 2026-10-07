<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasok', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('kontak', 100)->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasok');
    }
};

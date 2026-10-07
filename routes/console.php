<?php

use App\Pekerjaan\HitungUlangROPHarian;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Register custom commands
Artisan::command('rop:hitung-ulang', function () {
    app(HitungUlangROPHarian::class)->handle(app(\App\Layanan\LayananHitungROP::class));
})->purpose('Hitung ulang ROP semua barang aktif');

// Schedule: hitung ulang ROP setiap hari tengah malam
Schedule::command('rop:hitung-ulang')->dailyAt('00:00');


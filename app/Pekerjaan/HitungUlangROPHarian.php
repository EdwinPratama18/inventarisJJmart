<?php

namespace App\Pekerjaan;

use App\Layanan\LayananHitungROP;
use App\Models\Barang;
use Illuminate\Console\Command;

class HitungUlangROPHarian extends Command
{
    protected $signature = 'rop:hitung-ulang {--lookback=30 : Jumlah hari data historis}';
    protected $description = 'Hitung ulang ROP untuk semua barang aktif';

    public function handle(LayananHitungROP $layananROP): int
    {
        $lookback = (int) $this->option('lookback');
        $this->info("Menghitung ulang ROP dengan data {$lookback} hari terakhir...");

        $barangAktif = Barang::where('aktif', true)->get();
        $bar = $this->output->createProgressBar($barangAktif->count());
        $bar->start();

        foreach ($barangAktif as $barang) {
            $layananROP->hitungDanSimpan($barang, $lookback);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Selesai. {$barangAktif->count()} barang telah diperbarui.");

        return Command::SUCCESS;
    }
}

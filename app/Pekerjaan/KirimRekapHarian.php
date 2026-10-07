<?php

namespace App\Pekerjaan;

use App\Layanan\LayananNotifikasiTelegram;
use Illuminate\Console\Command;

class KirimRekapHarian extends Command
{
    protected $signature = 'telegram:rekap-harian';
    protected $description = 'Kirim rekap stok harian via Telegram ke semua admin';

    public function handle(LayananNotifikasiTelegram $layananTelegram): int
    {
        $this->info('Mengirim rekap harian via Telegram...');
        $layananTelegram->kirimRekapHarian();
        $this->info('✓ Rekap harian berhasil dikirim.');

        return Command::SUCCESS;
    }
}

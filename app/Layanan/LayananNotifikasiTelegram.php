<?php

namespace App\Layanan;

use App\Models\Barang;
use App\Models\LogNotifikasi;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class LayananNotifikasiTelegram
{
    private Client $http;
    private string $botToken;
    private string $apiUrl;

    public function __construct()
    {
        $this->http = new Client(['timeout' => 10]);
        $this->botToken = config('telegram.bot_token', '');
        $this->apiUrl = config('telegram.api_url', 'https://api.telegram.org');
    }

    /**
     * Kirim peringatan stok rendah ke semua admin yang punya Telegram.
     */
    public function kirimPeringatan(Barang $barang): void
    {
        if (empty($this->botToken)) {
            Log::warning('Telegram bot token tidak dikonfigurasi.');
            return;
        }

        $admins = User::where('peran', 'admin')
            ->whereNotNull('id_chat_telegram')
            ->get();

        if ($admins->isEmpty()) {
            Log::info("Tidak ada admin dengan Telegram untuk notifikasi barang ID {$barang->id}");
            return;
        }

        $pesan = $this->buatPesanPeringatan($barang);

        foreach ($admins as $admin) {
            $this->kirimPesan($admin->id_chat_telegram, $pesan, $barang);
        }
    }

    /**
     * Kirim pesan uji coba ke chat ID tertentu.
     */
    public function kirimPesanUji(string $chatId): array
    {
        if (empty($this->botToken)) {
            return [
                'berhasil' => false,
                'pesan' => 'Token Bot Telegram belum dikonfigurasi di file .env server (TELEGRAM_BOT_TOKEN masih kosong). Silakan buat bot di @BotFather dan masukkan token ke .env.',
            ];
        }

        $waktu = now()->format('d/m/Y H:i:s');
        $pesan = <<<MSG
🔔 *UJI COBA NOTIFIKASI - JJ Mart*

Halo! Akun Telegram Anda telah berhasil terhubung dengan sistem *JJ Mart Inventaris*.

Notifikasi otomatis akan dikirimkan ke sini ketika:
• Stok barang mencapai atau di bawah batas *Reorder Point (ROP)*
• Rekap harian stok kritis dikirimkan

🕒 *Waktu Uji:* {$waktu}
_JJ Mart Inventory System_
MSG;

        try {
            $url = "{$this->apiUrl}/bot{$this->botToken}/sendMessage";
            $response = $this->http->post($url, [
                'json' => [
                    'chat_id' => $chatId,
                    'text' => $pesan,
                    'parse_mode' => 'Markdown',
                ],
            ]);

            return [
                'berhasil' => true,
                'pesan' => 'Pesan uji coba berhasil dikirim ke Telegram Anda!',
            ];
        } catch (GuzzleException $e) {
            $err = $e->getMessage();
            Log::error("Gagal uji kirim Telegram ke {$chatId}: {$err}");

            if (str_contains($err, 'chat not found') || str_contains($err, 'Unauthorized') || str_contains($err, 'Forbidden')) {
                return [
                    'berhasil' => false,
                    'pesan' => 'Telegram menolak pengiriman: Pastikan Anda sudah membuka bot Anda di Telegram dan menekan tombol "Start" (/start). Bot Telegram tidak diizinkan mengirim pesan sebelum pengguna memulainya.',
                ];
            }

            return [
                'berhasil' => false,
                'pesan' => 'Gagal menghubungi Telegram API: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim rekap harian stok.
     */
    public function kirimRekapHarian(): void
    {
        if (empty($this->botToken)) {
            return;
        }

        $barangKritis = Barang::where('aktif', true)
            ->where('stok_saat_ini', '<=', \DB::raw('rop'))
            ->with('kategori')
            ->get();

        if ($barangKritis->isEmpty()) {
            return;
        }

        $admins = User::where('peran', 'admin')
            ->whereNotNull('id_chat_telegram')
            ->get();

        $pesan = $this->buatPesanRekap($barangKritis);

        foreach ($admins as $admin) {
            $this->kirimPesan($admin->id_chat_telegram, $pesan);
        }
    }

    private function buatPesanPeringatan(Barang $barang): string
    {
        $status = $barang->status_stok;
        $emoji = match($status) {
            'HABIS' => '🔴',
            'PERLU_PEMESANAN' => '🟠',
            'PERINGATAN' => '🟡',
            default => '🟢',
        };

        return <<<MSG
{$emoji} *PERINGATAN STOK - JJ Mart*

📦 *Barang:* {$barang->nama}
🏷️ *Kode:* `{$barang->kode_barang}`
📊 *Status:* {$status}

📉 *Stok Saat Ini:* {$barang->stok_saat_ini} {$barang->satuan}
⚠️ *ROP (Titik Pemesanan):* {$barang->rop} {$barang->satuan}
🛡️ *Stok Minimum:* {$barang->stok_minimum} {$barang->satuan}

⏰ Segera lakukan pemesanan ke pemasok!

_JJ Mart Inventory System_
MSG;
    }

    private function buatPesanRekap($barangKritis): string
    {
        $tanggal = now()->format('d/m/Y H:i');
        $daftar = $barangKritis->map(function ($b) {
            return "• {$b->nama} ({$b->kode_barang}): *{$b->stok_saat_ini}* {$b->satuan} (ROP: {$b->rop})";
        })->implode("\n");

        return <<<MSG
📋 *REKAP HARIAN STOK KRITIS - JJ Mart*
📅 {$tanggal}

Barang yang perlu dipesan ({$barangKritis->count()} item):

{$daftar}

_JJ Mart Inventory System_
MSG;
    }

    private function kirimPesan(string $chatId, string $pesan, ?Barang $barang = null): void
    {
        $status = 'terkirim';
        $responApi = null;

        try {
            $url = "{$this->apiUrl}/bot{$this->botToken}/sendMessage";
            $response = $this->http->post($url, [
                'json' => [
                    'chat_id' => $chatId,
                    'text' => $pesan,
                    'parse_mode' => 'Markdown',
                ],
            ]);
            $responApi = $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            // Retry sekali
            try {
                $url = "{$this->apiUrl}/bot{$this->botToken}/sendMessage";
                $response = $this->http->post($url, [
                    'json' => [
                        'chat_id' => $chatId,
                        'text' => $pesan,
                        'parse_mode' => 'Markdown',
                    ],
                ]);
                $responApi = $response->getBody()->getContents();
            } catch (GuzzleException $e2) {
                $status = 'gagal';
                $responApi = $e2->getMessage();
                Log::error("Gagal kirim notifikasi Telegram ke {$chatId}: {$e2->getMessage()}");
            }
        }

        if ($barang) {
            LogNotifikasi::create([
                'barang_id' => $barang->id,
                'jenis_notifikasi' => 'stok_rendah',
                'status' => $status,
                'pesan' => $pesan,
                'id_chat_telegram' => $chatId,
                'respon_api' => $responApi,
                'dikirim_pada' => now(),
            ]);
        }
    }
}

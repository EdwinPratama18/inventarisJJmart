<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\Pengguna::where('email', 'admin@jjmart.id')->first();

if ($user) {
    echo "Email: " . $user->email . "\n";
    echo "ID Chat Telegram: " . ($user->id_chat_telegram ?? 'KOSONG') . "\n";
    echo "Nama: " . $user->nama . "\n";
} else {
    echo "User admin@jjmart.id tidak ditemukan!\n";
    
    // Tampilkan semua user
    $users = App\Models\Pengguna::all(['id', 'nama', 'email', 'id_chat_telegram', 'peran']);
    echo "\nSemua user:\n";
    foreach ($users as $u) {
        echo "- [{$u->peran}] {$u->email} | Telegram: " . ($u->id_chat_telegram ?? 'KOSONG') . "\n";
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Manager (peran: admin)
        User::updateOrCreate(
            ['email' => 'manager@jjmart.id'],
            [
                'name'     => 'Manager JJ Mart',
                'email'    => 'manager@jjmart.id',
                'password' => Hash::make('manager123'),
                'peran'    => 'admin',
                'id_chat_telegram' => null,
            ]
        );

        // Akun lama (kompatibilitas mundur)
        User::updateOrCreate(
            ['email' => 'admin@jjmart.id'],
            [
                'name'     => 'Admin JJ Mart',
                'email'    => 'admin@jjmart.id',
                'password' => Hash::make('admin123'),
                'peran'    => 'admin',
                'id_chat_telegram' => null,
            ]
        );

        // Akun Staff Gudang (peran: petugas)
        User::updateOrCreate(
            ['email' => 'staff@jjmart.id'],
            [
                'name'     => 'Staff Gudang JJ Mart',
                'email'    => 'staff@jjmart.id',
                'password' => Hash::make('staff123'),
                'peran'    => 'petugas',
                'id_chat_telegram' => null,
            ]
        );

        // Akun lama (kompatibilitas mundur)
        User::updateOrCreate(
            ['email' => 'petugas@jjmart.id'],
            [
                'name'     => 'Petugas Gudang',
                'email'    => 'petugas@jjmart.id',
                'password' => Hash::make('petugas123'),
                'peran'    => 'petugas',
                'id_chat_telegram' => null,
            ]
        );
    }
}
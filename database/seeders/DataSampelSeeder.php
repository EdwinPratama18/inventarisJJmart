<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pemasok;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DataSampelSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kategori
        $katMakanan = Kategori::firstOrCreate(['nama' => 'Makanan Ringan'], ['deskripsi' => 'Snack, biskuit, keripik']);
        $katMinuman = Kategori::firstOrCreate(['nama' => 'Minuman'], ['deskripsi' => 'Air mineral, jus, teh kemasan']);
        $katSembako = Kategori::firstOrCreate(['nama' => 'Sembako'], ['deskripsi' => 'Beras, minyak goreng, gula, tepung']);
        $katToiletries = Kategori::firstOrCreate(['nama' => 'Kebutuhan Pribadi'], ['deskripsi' => 'Sabun, sampo, pasta gigi']);
        $katRumah = Kategori::firstOrCreate(['nama' => 'Perlengkapan Rumah'], ['deskripsi' => 'Detergen, pembersih lantai, tisu']);

        // 2. Pemasok
        $pemasokWings = Pemasok::firstOrCreate(
            ['nama' => 'PT Wings Surya'],
            [
                'kontak' => 'Budi Santoso',
                'telepon' => '081234567890',
                'email' => 'sales@wingssurya.co.id',
                'alamat' => 'Jl. Embong Malang No. 10, Surabaya',
            ]
        );

        $pemasokIndofood = Pemasok::firstOrCreate(
            ['nama' => 'PT Indofood Sukses Makmur'],
            [
                'kontak' => 'Hendra Wijaya',
                'telepon' => '082198765432',
                'email' => 'hendra@indofood.co.id',
                'alamat' => 'Kawasan Industri Rungkut, Surabaya',
            ]
        );

        $pemasokMayora = Pemasok::firstOrCreate(
            ['nama' => 'PT Mayora Indah Tbk'],
            [
                'kontak' => 'Dewi Lestari',
                'telepon' => '085611223344',
                'email' => 'dewi@mayora.co.id',
                'alamat' => 'Daan Mogot KM 18, Jakarta Barat',
            ]
        );

        $admin = User::where('peran', 'admin')->first();

        // 3. Barang
        $items = [
            [
                'kode_barang' => 'BRG-001',
                'nama' => 'Indomie Goreng Original 85g',
                'kategori_id' => $katMakanan->id,
                'pemasok_id' => $pemasokIndofood->id,
                'satuan' => 'dus',
                'stok_saat_ini' => 12,
                'stok_minimum' => 15,
                'harga_beli' => 105000,
                'harga_jual' => 120000,
                'lead_time_hari' => 4,
                'rata_pemakaian_harian' => 5,
                'rop' => 35, // 5 * 4 + 15 = 35 -> KRITIS (12 <= 15)
                'lokasi_rak' => 'Rak A-01',
            ],
            [
                'kode_barang' => 'BRG-002',
                'nama' => 'Minyak Goreng Sania 2L',
                'kategori_id' => $katSembako->id,
                'pemasok_id' => $pemasokWings->id,
                'satuan' => 'karton',
                'stok_saat_ini' => 18,
                'stok_minimum' => 10,
                'harga_beli' => 195000,
                'harga_jual' => 220000,
                'lead_time_hari' => 3,
                'rata_pemakaian_harian' => 4,
                'rop' => 22, // 4 * 3 + 10 = 22 -> MENDEKATI_ROP (18 <= 22)
                'lokasi_rak' => 'Rak B-03',
            ],
            [
                'kode_barang' => 'BRG-003',
                'nama' => 'Aqua Air Mineral 600ml',
                'kategori_id' => $katMinuman->id,
                'pemasok_id' => $pemasokMayora->id,
                'satuan' => 'karton',
                'stok_saat_ini' => 85,
                'stok_minimum' => 20,
                'harga_beli' => 45000,
                'harga_jual' => 55000,
                'lead_time_hari' => 2,
                'rata_pemakaian_harian' => 10,
                'rop' => 40, // AMAN (85 > 40)
                'lokasi_rak' => 'Rak C-02',
            ],
            [
                'kode_barang' => 'BRG-004',
                'nama' => 'Beras Pandan Wangi 5kg',
                'kategori_id' => $katSembako->id,
                'pemasok_id' => $pemasokIndofood->id,
                'satuan' => 'sak',
                'stok_saat_ini' => 5,
                'stok_minimum' => 8,
                'harga_beli' => 68000,
                'harga_jual' => 78000,
                'lead_time_hari' => 5,
                'rata_pemakaian_harian' => 3,
                'rop' => 23, // KRITIS (5 <= 8)
                'lokasi_rak' => 'Gudang-Bawah',
            ],
            [
                'kode_barang' => 'BRG-005',
                'nama' => 'Detergen Daia Putih 850g',
                'kategori_id' => $katRumah->id,
                'pemasok_id' => $pemasokWings->id,
                'satuan' => 'dus',
                'stok_saat_ini' => 42,
                'stok_minimum' => 10,
                'harga_beli' => 145000,
                'harga_jual' => 165000,
                'lead_time_hari' => 3,
                'rata_pemakaian_harian' => 4,
                'rop' => 22, // AMAN (42 > 22)
                'lokasi_rak' => 'Rak D-05',
            ],
            [
                'kode_barang' => 'BRG-006',
                'nama' => 'Teh Botol Sosro 330ml',
                'kategori_id' => $katMinuman->id,
                'pemasok_id' => $pemasokMayora->id,
                'satuan' => 'karton',
                'stok_saat_ini' => 25,
                'stok_minimum' => 12,
                'harga_beli' => 60000,
                'harga_jual' => 72000,
                'lead_time_hari' => 3,
                'rata_pemakaian_harian' => 6,
                'rop' => 30, // MENDEKATI_ROP (25 <= 30)
                'lokasi_rak' => 'Rak C-01',
            ],
        ];

        foreach ($items as $itemData) {
            $barang = Barang::updateOrCreate(
                ['kode_barang' => $itemData['kode_barang']],
                $itemData
            );

            // Buat transaksi sampel untuk tren pemakaian 7 hari terakhir
            for ($hari = 6; $hari >= 0; $hari--) {
                $tgl = Carbon::now()->subDays($hari);
                $qtyKeluar = rand(2, 8);
                $ref = 'TRX-' . $barang->kode_barang . '-' . $tgl->format('Ymd');

                Transaksi::firstOrCreate(
                    [
                        'referensi' => $ref,
                    ],
                    [
                        'barang_id' => $barang->id,
                        'pengguna_id' => $admin ? $admin->id : null,
                        'jenis' => 'keluar',
                        'jumlah' => $qtyKeluar,
                        'stok_sebelum' => $barang->stok_saat_ini + $qtyKeluar,
                        'stok_sesudah' => $barang->stok_saat_ini,
                        'harga_satuan' => $barang->harga_jual,
                        'catatan' => 'Penjualan harian mini market',
                        'tanggal' => $tgl,
                    ]
                );
            }
        }
    }
}

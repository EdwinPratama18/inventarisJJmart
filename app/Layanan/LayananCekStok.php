<?php

namespace App\Layanan;

use App\Models\Barang;

class LayananCekStok
{
    public function __construct(
        private LayananHitungROP $layananROP,
    ) {}

    /**
     * Hitung ulang ROP dan kembalikan status stok terkini.
     */
    public function cekDanNotifikasi(Barang $barang): string
    {
        // Hitung ulang ROP berdasarkan data transaksi terbaru
        $this->layananROP->hitungDanSimpan($barang);
        $barang->refresh();

        return $barang->status_stok;
    }
}

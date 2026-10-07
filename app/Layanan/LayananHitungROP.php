<?php

namespace App\Layanan;

use App\Models\Barang;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LayananHitungROP
{
    /**
     * Hitung rata-rata pemakaian harian berdasarkan N hari terakhir.
     */
    public function hitungRataRataPemakaianHarian(Barang $barang, int $hariLookback = 30): float
    {
        $tanggalMulai = Carbon::now()->subDays($hariLookback);

        $totalKeluar = Transaksi::where('barang_id', $barang->id)
            ->where('jenis', 'keluar')
            ->where('tanggal', '>=', $tanggalMulai)
            ->sum('jumlah');

        if ($totalKeluar == 0) {
            return (float) $barang->rata_pemakaian_harian;
        }

        return round($totalKeluar / $hariLookback, 4);
    }

    /**
     * Hitung stok pengaman (safety stock).
     * Formula: (pemakaian_maksimum - pemakaian_rata2) * lead_time
     */
    public function hitungStokPengaman(Barang $barang, float $rataRataHarian, int $hariLookback = 30): int
    {
        $tanggalMulai = Carbon::now()->subDays($hariLookback);

        $pemakaianMaksimum = Transaksi::where('barang_id', $barang->id)
            ->where('jenis', 'keluar')
            ->where('tanggal', '>=', $tanggalMulai)
            ->select(DB::raw('DATE(tanggal) as tgl'), DB::raw('SUM(jumlah) as total'))
            ->groupBy('tgl')
            ->orderByDesc('total')
            ->value('total');

        $pemakaianMaksimumHarian = $pemakaianMaksimum ?? ($rataRataHarian * 1.5);

        return (int) round(
            ($pemakaianMaksimumHarian - $rataRataHarian) * $barang->lead_time_hari
        );
    }

    /**
     * Hitung ROP: (avg_daily * lead_time) + safety_stock
     */
    public function hitungTitikPemesananUlang(Barang $barang, int $hariLookback = 30): int
    {
        $rataRataHarian = $this->hitungRataRataPemakaianHarian($barang, $hariLookback);
        $stokPengaman = max($this->hitungStokPengaman($barang, $rataRataHarian, $hariLookback), $barang->stok_minimum);

        $rop = (int) round(($rataRataHarian * $barang->lead_time_hari) + $stokPengaman);

        return max($rop, $barang->stok_minimum);
    }

    /**
     * Hitung dan simpan ROP ke database.
     */
    public function hitungDanSimpan(Barang $barang, int $hariLookback = 30): Barang
    {
        $rataRataHarian = $this->hitungRataRataPemakaianHarian($barang, $hariLookback);
        $rop = $this->hitungTitikPemesananUlang($barang, $hariLookback);

        $barang->update([
            'rata_pemakaian_harian' => $rataRataHarian,
            'rop' => $rop,
        ]);

        return $barang->fresh();
    }
}

<?php

namespace App\Http\Pengendali;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Pemasok;
use App\Models\User;

class PengendaliDasbor extends Controller
{
    public function ringkasan(): JsonResponse
    {
        $totalBarang = Barang::where('aktif', true)->count();
        $totalPemasok = Pemasok::count();
        $totalPengguna = User::count();
        $totalNilaiStok = Barang::where('aktif', true)
            ->selectRaw('SUM(stok_saat_ini * harga_beli) as total')
            ->value('total') ?? 0;

        $barangKritis = Barang::where('aktif', true)
            ->whereRaw('stok_saat_ini <= rop')
            ->count();

        $barangHabis = Barang::where('aktif', true)
            ->where('stok_saat_ini', '<=', 0)
            ->count();

        $barangPeringatan = Barang::where('aktif', true)
            ->whereRaw('stok_saat_ini > rop AND stok_saat_ini <= rop * 1.2')
            ->count();

        $transaksiHariIni = Transaksi::whereDate('tanggal', today())->count();

        $totalBarangMasuk = (int) Transaksi::where('jenis', 'masuk')->sum('jumlah');
        $totalBarangKeluar = (int) Transaksi::where('jenis', 'keluar')->sum('jumlah');
        $totalTrxMasuk = Transaksi::where('jenis', 'masuk')->count();
        $totalTrxKeluar = Transaksi::where('jenis', 'keluar')->count();

        return response()->json([
            'berhasil' => true,
            'data' => [
                'total_barang' => $totalBarang,
                'total_pemasok' => $totalPemasok,
                'total_pengguna' => $totalPengguna,
                'total_nilai_stok' => (float) $totalNilaiStok,
                'barang_kritis' => $barangKritis,
                'barang_habis' => $barangHabis,
                'barang_peringatan' => $barangPeringatan,
                'transaksi_hari_ini' => $transaksiHariIni,
                'total_barang_masuk' => $totalBarangMasuk,
                'total_barang_keluar' => $totalBarangKeluar,
                'total_trx_masuk' => $totalTrxMasuk,
                'total_trx_keluar' => $totalTrxKeluar,
            ],
        ]);
    }

    public function trendPemakaian(Request $request): JsonResponse
    {
        $hari = (int) $request->input('hari', 30);
        $barangId = $request->input('barang_id');

        $query = Transaksi::select(
            DB::raw('DATE(tanggal) as tanggal'),
            DB::raw("SUM(CASE WHEN jenis = 'masuk' THEN jumlah ELSE 0 END) as total_masuk"),
            DB::raw("SUM(CASE WHEN jenis = 'keluar' THEN jumlah ELSE 0 END) as total_keluar")
        )
        ->where('tanggal', '>=', Carbon::now()->subDays($hari))
        ->groupBy(DB::raw('DATE(tanggal)'))
        ->orderBy('tanggal');

        if ($barangId) {
            $query->where('barang_id', $barangId);
        }

        return response()->json([
            'berhasil' => true,
            'data' => $query->get(),
        ]);
    }

    public function barangKritis(): JsonResponse
    {
        $barang = Barang::with(['kategori', 'pemasok'])
            ->where('aktif', true)
            ->whereRaw('stok_saat_ini <= rop')
            ->orderBy('stok_saat_ini')
            ->limit(10)
            ->get()
            ->map(function ($b) {
                return array_merge($b->toArray(), ['status_stok' => $b->status_stok]);
            });

        return response()->json(['berhasil' => true, 'data' => $barang]);
    }
}

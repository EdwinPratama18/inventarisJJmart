<?php

namespace App\Http\Pengendali;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengendaliLaporan extends Controller
{
    public function stokSaatIni(Request $request): JsonResponse
    {
        $query = Barang::with(['kategori', 'pemasok'])->where('aktif', true);

        if ($request->has('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $barang = $query->orderBy('nama')->get()->map(function ($b) {
            return array_merge($b->toArray(), ['status_stok' => $b->status_stok]);
        });

        return response()->json([
            'berhasil' => true,
            'data' => $barang,
            'meta' => [
                'total_barang' => $barang->count(),
                'total_nilai_stok' => $barang->sum(fn($b) => $b['stok_saat_ini'] * $b['harga_beli']),
                'dibuat_pada' => now()->toISOString(),
            ],
        ]);
    }

    public function riwayatTransaksi(Request $request): JsonResponse
    {
        $dari = $request->input('dari', Carbon::now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', Carbon::now()->toDateString());

        $transaksi = Transaksi::with(['barang', 'pengguna'])
            ->whereBetween('tanggal', [$dari, "{$sampai} 23:59:59"])
            ->orderByDesc('tanggal')
            ->paginate(50);

        return response()->json([
            'berhasil' => true,
            'data' => $transaksi->items(),
            'meta' => [
                'dari' => $dari,
                'sampai' => $sampai,
                'total' => $transaksi->total(),
                'halaman_saat_ini' => $transaksi->currentPage(),
                'total_halaman' => $transaksi->lastPage(),
            ],
        ]);
    }

    public function riwayatReorder(Request $request): JsonResponse
    {
        $barang = Barang::with(['pemasok'])
            ->where('aktif', true)
            ->whereRaw('stok_saat_ini <= rop')
            ->orderBy('stok_saat_ini')
            ->get()
            ->map(function ($b) {
                $kekurangan = $b->rop - $b->stok_saat_ini;
                return [
                    'id' => $b->id,
                    'kode_barang' => $b->kode_barang,
                    'nama' => $b->nama,
                    'stok_saat_ini' => $b->stok_saat_ini,
                    'rop' => $b->rop,
                    'kekurangan' => max($kekurangan, 0),
                    'pemasok' => $b->pemasok?->nama,
                    'lead_time_hari' => $b->lead_time_hari,
                    'harga_beli' => $b->harga_beli,
                    'status_stok' => $b->status_stok,
                ];
            });

        return response()->json(['berhasil' => true, 'data' => $barang]);
    }
}

<?php

namespace App\Http\Pengendali;

use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanTransaksiRequest;
use App\Layanan\LayananCekStok;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengendaliTransaksi extends Controller
{
    public function __construct(private LayananCekStok $layananCekStok) {}

    public function index(Request $request): JsonResponse
    {
        $query = Transaksi::with(['barang', 'pengguna'])
            ->orderByDesc('tanggal');

        if ($request->filled('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }

        if ($request->filled('jenis') && in_array($request->jenis, ['masuk', 'keluar'])) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('dari')) {
            $query->where('tanggal', '>=', "{$request->dari} 00:00:00");
        }

        if ($request->filled('sampai')) {
            $query->where('tanggal', '<=', "{$request->sampai} 23:59:59");
        }

        if ($request->filled('cari')) {
            $cari = trim($request->cari);
            $query->where(function ($q) use ($cari) {
                $q->where('referensi', 'like', "%{$cari}%")
                  ->orWhere('catatan', 'like', "%{$cari}%")
                  ->orWhereHas('barang', function ($bq) use ($cari) {
                      $bq->where('nama', 'like', "%{$cari}%")
                         ->orWhere('kode_barang', 'like', "%{$cari}%");
                  });
            });
        }

        $perHalaman = min($request->input('per_halaman', 20), 500);
        // Support custom 'halaman' param (frontend uses this instead of Laravel's 'page')
        $halaman = $request->input('halaman', $request->input('page', 1));
        $transaksi = $query->paginate($perHalaman, ['*'], 'page', $halaman);

        return response()->json([
            'berhasil' => true,
            'data' => $transaksi->items(),
            'meta' => [
                'halaman_saat_ini' => $transaksi->currentPage(),
                'total' => $transaksi->total(),
                'per_halaman' => $transaksi->perPage(),
                'total_halaman' => $transaksi->lastPage(),
            ],
        ]);
    }

    public function simpan(SimpanTransaksiRequest $request): JsonResponse
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data, $request) {
            $barang = Barang::lockForUpdate()->findOrFail($data['barang_id']);

            if ($data['jenis'] === 'keluar' && $barang->stok_saat_ini < $data['jumlah']) {
                return response()->json([
                    'berhasil' => false,
                    'pesan' => "Stok tidak mencukupi. Stok saat ini: {$barang->stok_saat_ini} {$barang->satuan}",
                ], 422);
            }

            $stokSebelum = $barang->stok_saat_ini;
            $stokSesudah = $data['jenis'] === 'masuk'
                ? $stokSebelum + $data['jumlah']
                : $stokSebelum - $data['jumlah'];

            $hargaSatuan = !empty($data['harga_satuan']) && (float) $data['harga_satuan'] > 0
                ? (float) $data['harga_satuan']
                : (float) ($data['jenis'] === 'keluar' ? ($barang->harga_jual ?: $barang->harga_beli ?: 0) : ($barang->harga_beli ?: 0));

            $transaksi = Transaksi::create([
                ...$data,
                'harga_satuan' => $hargaSatuan,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'pengguna_id' => $request->user()->id,
                'tanggal' => $data['tanggal'] ?? now(),
            ]);

            $barang->update(['stok_saat_ini' => $stokSesudah]);

            // Cek ROP dan kirim notifikasi jika perlu
            if ($data['jenis'] === 'keluar') {
                $this->layananCekStok->cekDanNotifikasi($barang);
            }

            return response()->json([
                'berhasil' => true,
                'data' => $transaksi->load(['barang', 'pengguna']),
                'pesan' => 'Transaksi berhasil dicatat.',
            ], 201);
        });
    }

    public function tampil(Transaksi $transaksi): JsonResponse
    {
        return response()->json([
            'berhasil' => true,
            'data' => $transaksi->load(['barang', 'pengguna']),
        ]);
    }
}

<?php

namespace App\Http\Pengendali;

use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanBarangRequest;
use App\Layanan\LayananCekStok;
use App\Layanan\LayananHitungROP;
use App\Models\Barang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengendaliBarang extends Controller
{
    public function __construct(
        private LayananHitungROP $layananROP,
        private LayananCekStok $layananCekStok,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Barang::with(['kategori', 'pemasok'])->where('aktif', true);

        if ($request->has('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                  ->orWhere('kode_barang', 'like', "%{$cari}%");
            });
        }

        if ($request->has('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->has('status')) {
            $status = strtoupper($request->status);
            switch ($status) {
                case 'PERLU_PEMESANAN':
                case 'KRITIS':
                    $query->whereRaw('stok_saat_ini <= rop');
                    break;
                case 'PERINGATAN':
                    $query->whereRaw('stok_saat_ini > rop AND stok_saat_ini <= rop * 1.2');
                    break;
                case 'HABIS':
                    $query->where('stok_saat_ini', '<=', 0);
                    break;
                case 'AMAN':
                    $query->whereRaw('stok_saat_ini > rop * 1.2');
                    break;
            }
        }

        $perHalaman = min($request->input('per_halaman', 15), 100);
        $barang = $query->orderBy('nama')->paginate($perHalaman);

        return response()->json([
            'berhasil' => true,
            'data' => $barang->items(),
            'meta' => [
                'halaman_saat_ini' => $barang->currentPage(),
                'total' => $barang->total(),
                'per_halaman' => $barang->perPage(),
                'total_halaman' => $barang->lastPage(),
            ],
        ]);
    }

    public function simpan(SimpanBarangRequest $request): JsonResponse
    {
        $barang = Barang::create($request->validated());

        return response()->json([
            'berhasil' => true,
            'data' => $barang->load(['kategori', 'pemasok']),
            'pesan' => 'Barang berhasil ditambahkan.',
        ], 201);
    }

    public function tampil(Barang $barang): JsonResponse
    {
        return response()->json([
            'berhasil' => true,
            'data' => $barang->load(['kategori', 'pemasok']),
        ]);
    }

    public function perbarui(SimpanBarangRequest $request, Barang $barang): JsonResponse
    {
        $barang->update($request->validated());
        $this->layananCekStok->cekDanNotifikasi($barang);

        return response()->json([
            'berhasil' => true,
            'data' => $barang->fresh()->load(['kategori', 'pemasok']),
            'pesan' => 'Barang berhasil diperbarui.',
        ]);
    }

    public function hapus(Barang $barang): JsonResponse
    {
        $barang->update(['aktif' => false]);

        return response()->json([
            'berhasil' => true,
            'pesan' => 'Barang berhasil dinonaktifkan.',
        ]);
    }

    public function hitungUlangROP(Barang $barang): JsonResponse
    {
        $barang = $this->layananROP->hitungDanSimpan($barang);

        return response()->json([
            'berhasil' => true,
            'data' => [
                'rop' => $barang->rop,
                'rata_pemakaian_harian' => $barang->rata_pemakaian_harian,
                'status_stok' => $barang->status_stok,
            ],
            'pesan' => 'ROP berhasil dihitung ulang.',
        ]);
    }
}

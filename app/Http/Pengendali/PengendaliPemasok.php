<?php

namespace App\Http\Pengendali;

use App\Http\Controllers\Controller;
use App\Models\Pemasok;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengendaliPemasok extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Pemasok::query();
        if ($request->has('cari')) {
            $query->where('nama', 'like', "%{$request->cari}%");
        }
        return response()->json(['berhasil' => true, 'data' => $query->orderBy('nama')->get()]);
    }

    public function simpan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'kontak' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string',
        ]);

        $pemasok = Pemasok::create($data);
        return response()->json(['berhasil' => true, 'data' => $pemasok, 'pesan' => 'Pemasok berhasil ditambahkan.'], 201);
    }

    public function tampil(Pemasok $pemasok): JsonResponse
    {
        return response()->json(['berhasil' => true, 'data' => $pemasok->load('barang')]);
    }

    public function perbarui(Request $request, Pemasok $pemasok): JsonResponse
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'kontak' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string',
        ]);

        $pemasok->update($data);
        return response()->json(['berhasil' => true, 'data' => $pemasok, 'pesan' => 'Pemasok berhasil diperbarui.']);
    }

    public function hapus(Pemasok $pemasok): JsonResponse
    {
        try {
            $nama = $pemasok->nama;
            $pemasok->delete();
            return response()->json(['berhasil' => true, 'pesan' => "Pemasok \"{$nama}\" berhasil dihapus."]);
        } catch (\Throwable $e) {
            return response()->json(['berhasil' => false, 'pesan' => 'Gagal menghapus pemasok: ' . $e->getMessage()], 422);
        }
    }
}

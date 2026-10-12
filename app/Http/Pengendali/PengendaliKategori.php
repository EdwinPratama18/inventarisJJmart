<?php

namespace App\Http\Pengendali;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengendaliKategori extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['berhasil' => true, 'data' => Kategori::orderBy('nama')->get()]);
    }

    public function simpan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100|unique:kategori,nama',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori = Kategori::create($data);
        return response()->json(['berhasil' => true, 'data' => $kategori, 'pesan' => 'Kategori berhasil ditambahkan.'], 201);
    }

    public function perbarui(Request $request, Kategori $kategori): JsonResponse
    {
        $data = $request->validate([
            'nama' => "required|string|max:100|unique:kategori,nama,{$kategori->id}",
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update($data);
        return response()->json(['berhasil' => true, 'data' => $kategori, 'pesan' => 'Kategori berhasil diperbarui.']);
    }

    public function hapus(Kategori $kategori): JsonResponse
    {
        try {
            $nama = $kategori->nama;
            $kategori->delete();
            return response()->json(['berhasil' => true, 'pesan' => "Kategori \"{$nama}\" berhasil dihapus."]);
        } catch (\Throwable $e) {
            return response()->json(['berhasil' => false, 'pesan' => 'Gagal menghapus kategori: ' . $e->getMessage()], 422);
        }
    }
}

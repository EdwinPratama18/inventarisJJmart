<?php

namespace App\Http\Pengendali;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengendaliTelegram extends Controller
{
    public function hubungkan(Request $request): JsonResponse
    {
        $request->validate([
            'id_chat_telegram' => 'required|string|max:100',
        ]);

        $pengguna = $request->user();
        $pengguna->update(['id_chat_telegram' => $request->id_chat_telegram]);

        return response()->json([
            'berhasil' => true,
            'pesan' => 'Akun Telegram berhasil dihubungkan.',
            'data' => ['id_chat_telegram' => $pengguna->id_chat_telegram],
        ]);
    }

    public function putuskan(Request $request): JsonResponse
    {
        $request->user()->update(['id_chat_telegram' => null]);

        return response()->json([
            'berhasil' => true,
            'pesan' => 'Akun Telegram berhasil diputuskan.',
        ]);
    }

    public function uji(Request $request, \App\Layanan\LayananNotifikasiTelegram $layananTelegram): JsonResponse
    {
        $pengguna = $request->user();
        if (empty($pengguna->id_chat_telegram)) {
            return response()->json([
                'berhasil' => false,
                'pesan' => 'ID Chat Telegram belum disimpan. Masukkan ID dan simpan terlebih dahulu.',
            ], 422);
        }

        $hasil = $layananTelegram->kirimPesanUji($pengguna->id_chat_telegram);

        return response()->json($hasil, $hasil['berhasil'] ? 200 : 400);
    }
}

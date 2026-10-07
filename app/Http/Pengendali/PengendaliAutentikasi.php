<?php

namespace App\Http\Pengendali;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PengendaliAutentikasi extends Controller
{
    public function masuk(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak valid.'],
            ]);
        }

        $pengguna = Auth::user();
        $token = $pengguna->createToken('api-token', ['*'], now()->addDay())->plainTextToken;

        return response()->json([
            'berhasil' => true,
            'data' => [
                'pengguna' => [
                    'id' => $pengguna->id,
                    'name' => $pengguna->name,
                    'email' => $pengguna->email,
                    'peran' => $pengguna->peran,
                ],
                'token' => $token,
            ],
            'pesan' => 'Login berhasil.',
        ]);
    }

    public function keluar(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $token = $user->currentAccessToken();
            if ($token && method_exists($token, 'delete')) {
                $token->delete();
            }
        }

        Auth::guard('web')->logout();

        return response()->json([
            'berhasil' => true,
            'pesan' => 'Logout berhasil.',
        ]);
    }

    public function profil(Request $request): JsonResponse
    {
        $pengguna = $request->user();

        return response()->json([
            'berhasil' => true,
            'data' => [
                'id' => $pengguna->id,
                'name' => $pengguna->name,
                'email' => $pengguna->email,
                'peran' => $pengguna->peran,
                'id_chat_telegram' => $pengguna->id_chat_telegram,
            ],
        ]);
    }
}

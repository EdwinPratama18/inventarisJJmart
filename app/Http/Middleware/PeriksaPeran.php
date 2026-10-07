<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PeriksaPeran
{
    public function handle(Request $request, Closure $next, string ...$peran): Response
    {
        $pengguna = $request->user();

        if (!$pengguna) {
            return response()->json([
                'berhasil' => false,
                'pesan' => 'Tidak terautentikasi.',
            ], 401);
        }

        if (!in_array($pengguna->peran, $peran)) {
            return response()->json([
                'berhasil' => false,
                'pesan' => 'Anda tidak memiliki akses untuk tindakan ini.',
            ], 403);
        }

        return $next($request);
    }
}

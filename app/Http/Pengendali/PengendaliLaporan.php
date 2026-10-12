<?php

namespace App\Http\Pengendali;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengendaliLaporan extends Controller
{
    public function stokSaatIni(Request $request): JsonResponse
    {
        $query = Barang::with(['kategori', 'pemasok'])->where('aktif', true);

        if ($request->filled('kategori_id')) {
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

        $query = Transaksi::with(['barang', 'pengguna'])
            ->whereBetween('tanggal', ["{$dari} 00:00:00", "{$sampai} 23:59:59"])
            ->orderByDesc('tanggal');

        if ($request->filled('jenis') && in_array($request->jenis, ['masuk', 'keluar'])) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }

        $perHalaman = min((int) $request->input('per_halaman', 50), 500);
        $halaman = (int) $request->input('halaman', $request->input('page', 1));
        $transaksi = $query->paginate($perHalaman, ['*'], 'page', $halaman);

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

    public function exportPdf(Request $request)
    {
        $user = $request->user('sanctum') ?: $request->user();
        if (!$user && $request->filled('token')) {
            $personalToken = \Laravel\Sanctum\PersonalAccessToken::findToken($request->query('token'));
            if ($personalToken) {
                $user = $personalToken->tokenable;
            }
        }

        if (!$user) {
            return response()->json(['berhasil' => false, 'pesan' => 'Akses tidak diizinkan. Silakan login terlebih dahulu.'], 401);
        }

        Carbon::setLocale('id');
        $tipe = $request->input('tipe', 'stok');
        $mode = $request->input('mode', 'download'); // 'download' or 'stream'
        $petugasCetak = $user ? $user->name : 'Manager JJ Mart Padang Bulan';

        if ($tipe === 'stok') {
            $query = Barang::with(['kategori', 'pemasok'])->where('aktif', true);
            $filterKategori = 'Semua Kategori';

            if ($request->filled('kategori_id')) {
                $query->where('kategori_id', $request->kategori_id);
                $kategori = \App\Models\Kategori::find($request->kategori_id);
                if ($kategori) {
                    $filterKategori = 'Kategori: ' . $kategori->nama;
                }
            }

            $barang = $query->orderBy('nama')->get();
            $totalNilai = $barang->sum(fn($b) => $b->stok_saat_ini * $b->harga_beli);
            $noDokumen = 'JJ/STK/' . date('Ymd') . '/' . strtoupper(substr(uniqid(), -4));

            $pdf = Pdf::loadView('laporan.pdf_stok', [
                'barang' => $barang,
                'total_nilai_stok' => $totalNilai,
                'no_dokumen' => $noDokumen,
                'tanggal_cetak' => Carbon::now()->translatedFormat('d F Y, H:i'),
                'dicetak_oleh' => $petugasCetak,
                'filter_kategori' => $filterKategori,
                'jumlah_aman' => $barang->where('status_stok', 'AMAN')->count(),
                'jumlah_kritis' => $barang->whereIn('status_stok', ['PERINGATAN', 'PERLU_PEMESANAN'])->count(),
                'jumlah_habis' => $barang->where('status_stok', 'HABIS')->count(),
                'petugas_gudang' => $user && $user->peran === 'petugas' ? $user->name : 'Staff Toko JJ Mart',
                'kepala_toko' => $user && $user->peran === 'admin' ? $user->name : 'Manager JJ Mart Padang Bulan',
                'tanggal_pengesahan' => Carbon::now()->translatedFormat('d F Y'),
            ])->setPaper('a4', 'landscape');

            $filename = 'Laporan_Posisi_Stok_JJMart_PadangBulan_' . date('Ymd_His') . '.pdf';
            return $mode === 'stream' ? $pdf->stream($filename) : $pdf->download($filename);
        }

        if ($tipe === 'transaksi') {
            $dari = $request->input('dari', Carbon::now()->startOfMonth()->toDateString());
            $sampai = $request->input('sampai', Carbon::now()->toDateString());

            $query = Transaksi::with(['barang.kategori', 'pengguna'])
                ->whereBetween('tanggal', [$dari, "{$sampai} 23:59:59"])
                ->orderBy('tanggal', 'desc');

            if ($request->filled('jenis') && in_array($request->jenis, ['masuk', 'keluar'])) {
                $query->where('jenis', $request->jenis);
            }

            $transaksi = $query->get();
            $totalMasuk = $transaksi->where('jenis', 'masuk')->sum('jumlah');
            $totalKeluar = $transaksi->where('jenis', 'keluar')->sum('jumlah');
            $noDokumen = 'JJ/TRX/' . date('Ymd') . '/' . strtoupper(substr(uniqid(), -4));

            $periodeTeks = Carbon::parse($dari)->translatedFormat('d F Y') . ' s/d ' . Carbon::parse($sampai)->translatedFormat('d F Y');

            $pdf = Pdf::loadView('laporan.pdf_transaksi', [
                'transaksi' => $transaksi,
                'total_masuk' => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'periode_teks' => $periodeTeks,
                'no_dokumen' => $noDokumen,
                'tanggal_cetak' => Carbon::now()->translatedFormat('d F Y, H:i'),
                'dicetak_oleh' => $petugasCetak,
                'petugas_gudang' => $user && $user->peran === 'petugas' ? $user->name : 'Staff Toko JJ Mart',
                'kepala_toko' => $user && $user->peran === 'admin' ? $user->name : 'Manager JJ Mart Padang Bulan',
                'tanggal_pengesahan' => Carbon::now()->translatedFormat('d F Y'),
            ])->setPaper('a4', 'portrait');

            $filename = 'Laporan_Mutasi_Transaksi_JJMart_PadangBulan_' . date('Ymd_His') . '.pdf';
            return $mode === 'stream' ? $pdf->stream($filename) : $pdf->download($filename);
        }

        if ($tipe === 'reorder') {
            $barang = Barang::with(['pemasok', 'kategori'])
                ->where('aktif', true)
                ->whereRaw('stok_saat_ini <= rop')
                ->orderBy('stok_saat_ini')
                ->get()
                ->map(function ($b) {
                    $kekurangan = max(0, $b->rop - $b->stok_saat_ini);
                    return [
                        'id' => $b->id,
                        'kode_barang' => $b->kode_barang,
                        'nama' => $b->nama,
                        'stok_saat_ini' => $b->stok_saat_ini,
                        'rop' => $b->rop,
                        'kekurangan' => $kekurangan,
                        'pemasok' => $b->pemasok?->nama ?? '—',
                        'lead_time_hari' => $b->lead_time_hari,
                        'harga_beli' => $b->harga_beli,
                        'status_stok' => $b->status_stok,
                    ];
                });

            $totalKebutuhan = $barang->sum('kekurangan');
            $totalAnggaran = $barang->sum(fn($b) => $b['kekurangan'] * $b['harga_beli']);
            $noDokumen = 'JJ/ROP/' . date('Ymd') . '/' . strtoupper(substr(uniqid(), -4));

            $pdf = Pdf::loadView('laporan.pdf_reorder', [
                'barang' => $barang,
                'total_kebutuhan' => $totalKebutuhan,
                'total_anggaran' => $totalAnggaran,
                'total_pemasok' => $barang->pluck('pemasok')->filter(fn($p) => $p !== '—')->unique()->count(),
                'no_dokumen' => $noDokumen,
                'tanggal_cetak' => Carbon::now()->translatedFormat('d F Y, H:i'),
                'dicetak_oleh' => $petugasCetak,
                'petugas_gudang' => $user && $user->peran === 'petugas' ? $user->name : 'Staff Toko JJ Mart',
                'kepala_toko' => $user && $user->peran === 'admin' ? $user->name : 'Manager JJ Mart Padang Bulan',
                'tanggal_pengesahan' => Carbon::now()->translatedFormat('d F Y'),
            ])->setPaper('a4', 'landscape');

            $filename = 'Surat_Rekomendasi_Reorder_JJMart_PadangBulan_' . date('Ymd_His') . '.pdf';
            return $mode === 'stream' ? $pdf->stream($filename) : $pdf->download($filename);
        }

        return response()->json(['berhasil' => false, 'pesan' => 'Tipe laporan tidak valid.'], 400);
    }
}

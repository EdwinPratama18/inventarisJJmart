<?php

use App\Http\Pengendali\PengendaliAutentikasi;
use App\Http\Pengendali\PengendaliBarang;
use App\Http\Pengendali\PengendaliDasbor;
use App\Http\Pengendali\PengendaliKategori;
use App\Http\Pengendali\PengendaliLaporan;
use App\Http\Pengendali\PengendaliPemasok;
use App\Http\Pengendali\PengendaliTransaksi;
use Illuminate\Support\Facades\Route;

// ===== Autentikasi =====
Route::post('/masuk', [PengendaliAutentikasi::class, 'masuk']);
Route::get('/laporan/export-pdf-direct', [PengendaliLaporan::class, 'exportPdf']);

Route::middleware('auth:sanctum')->group(function () {

    // Profil & Logout
    Route::get('/profil', [PengendaliAutentikasi::class, 'profil']);
    Route::post('/keluar', [PengendaliAutentikasi::class, 'keluar']);

    // ===== Dasbor =====
    Route::prefix('dasbor')->group(function () {
        Route::get('/ringkasan', [PengendaliDasbor::class, 'ringkasan']);
        Route::get('/trend-pemakaian', [PengendaliDasbor::class, 'trendPemakaian']);
        Route::get('/barang-kritis', [PengendaliDasbor::class, 'barangKritis']);
    });

    // ===== Barang =====
    Route::prefix('barang')->group(function () {
        Route::get('/', [PengendaliBarang::class, 'index']);
        Route::post('/', [PengendaliBarang::class, 'simpan'])->middleware('periksa.peran:admin');
        Route::get('/{barang}', [PengendaliBarang::class, 'tampil']);
        Route::put('/{barang}', [PengendaliBarang::class, 'perbarui'])->middleware('periksa.peran:admin');
        Route::delete('/{barang}', [PengendaliBarang::class, 'hapus'])->middleware('periksa.peran:admin');
        Route::post('/{barang}/hitung-ulang-rop', [PengendaliBarang::class, 'hitungUlangROP'])->middleware('periksa.peran:admin');
    });

    // ===== Transaksi =====
    Route::prefix('transaksi')->group(function () {
        Route::get('/', [PengendaliTransaksi::class, 'index']);
        Route::post('/', [PengendaliTransaksi::class, 'simpan']);
        Route::get('/{transaksi}', [PengendaliTransaksi::class, 'tampil']);
    });

    // ===== Pemasok =====
    Route::prefix('pemasok')->group(function () {
        Route::get('/', [PengendaliPemasok::class, 'index']);
        Route::post('/', [PengendaliPemasok::class, 'simpan'])->middleware('periksa.peran:admin');
        Route::get('/{pemasok}', [PengendaliPemasok::class, 'tampil']);
        Route::put('/{pemasok}', [PengendaliPemasok::class, 'perbarui'])->middleware('periksa.peran:admin');
        Route::delete('/{pemasok}', [PengendaliPemasok::class, 'hapus'])->middleware('periksa.peran:admin');
    });

    // ===== Kategori =====
    Route::prefix('kategori')->group(function () {
        Route::get('/', [PengendaliKategori::class, 'index']);
        Route::post('/', [PengendaliKategori::class, 'simpan'])->middleware('periksa.peran:admin');
        Route::put('/{kategori}', [PengendaliKategori::class, 'perbarui'])->middleware('periksa.peran:admin');
        Route::delete('/{kategori}', [PengendaliKategori::class, 'hapus'])->middleware('periksa.peran:admin');
    });

    // ===== Laporan =====
    Route::prefix('laporan')->group(function () {
        Route::get('/stok-saat-ini', [PengendaliLaporan::class, 'stokSaatIni']);
        Route::get('/riwayat-transaksi', [PengendaliLaporan::class, 'riwayatTransaksi']);
        Route::get('/riwayat-reorder', [PengendaliLaporan::class, 'riwayatReorder']);
        Route::get('/export-pdf', [PengendaliLaporan::class, 'exportPdf']);
    });

});

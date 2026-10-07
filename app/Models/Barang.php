<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';

    protected $fillable = [
        'kode_barang',
        'nama',
        'kategori_id',
        'pemasok_id',
        'satuan',
        'stok_saat_ini',
        'stok_minimum',
        'harga_beli',
        'harga_jual',
        'lead_time_hari',
        'rata_pemakaian_harian',
        'rop',
        'lokasi_rak',
        'deskripsi',
        'aktif',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'rata_pemakaian_harian' => 'decimal:4',
        'aktif' => 'boolean',
    ];

    protected $appends = ['status_stok'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'pemasok_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'barang_id');
    }

    public function logNotifikasi()
    {
        return $this->hasMany(LogNotifikasi::class, 'barang_id');
    }

    public function getStatusStokAttribute(): string
    {
        if ($this->stok_saat_ini <= 0) {
            return 'HABIS';
        } elseif ($this->stok_saat_ini <= $this->rop) {
            return 'PERLU_PEMESANAN';
        } elseif ($this->stok_saat_ini <= ($this->rop * 1.2)) {
            return 'PERINGATAN';
        }
        return 'AMAN';
    }
}

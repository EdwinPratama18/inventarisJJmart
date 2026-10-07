<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'barang_id',
        'jenis',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'harga_satuan',
        'referensi',
        'pengguna_id',
        'catatan',
        'tanggal',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'tanggal' => 'datetime',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }
}

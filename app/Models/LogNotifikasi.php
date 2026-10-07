<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogNotifikasi extends Model
{
    protected $table = 'log_notifikasi';

    protected $fillable = [
        'barang_id',
        'jenis_notifikasi',
        'status',
        'pesan',
        'id_chat_telegram',
        'respon_api',
        'dikirim_pada',
    ];

    protected $casts = [
        'dikirim_pada' => 'datetime',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}

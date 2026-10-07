<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanTransaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barang_id' => 'required|exists:barang,id',
            'jenis' => 'required|in:masuk,keluar',
            'jumlah' => 'required|integer|min:1',
            'harga_satuan' => 'nullable|numeric|min:0',
            'referensi' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
            'tanggal' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'barang_id.exists' => 'Barang tidak ditemukan.',
            'jenis.in' => 'Jenis transaksi harus masuk atau keluar.',
            'jumlah.min' => 'Jumlah minimal 1.',
        ];
    }
}

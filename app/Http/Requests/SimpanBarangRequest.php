<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $barangId = $this->route('barang');

        return [
            'kode_barang' => "required|string|max:50|unique:barang,kode_barang,{$barangId}",
            'nama' => 'required|string|max:150',
            'kategori_id' => 'nullable|exists:kategori,id',
            'pemasok_id' => 'nullable|exists:pemasok,id',
            'satuan' => 'required|string|max:30',
            'stok_saat_ini' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_jual' => 'nullable|numeric|min:0',
            'lead_time_hari' => 'required|integer|min:1',
            'lokasi_rak' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama.required' => 'Nama barang wajib diisi.',
            'lead_time_hari.min' => 'Lead time minimal 1 hari.',
        ];
    }
}

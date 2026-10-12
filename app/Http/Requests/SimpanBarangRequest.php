<?php

namespace App\Http\Requests;

use App\Models\Barang;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'kategori_id' => $this->kategori_id ?: null,
            'pemasok_id' => $this->pemasok_id ?: null,
            'harga_beli' => $this->harga_beli !== '' && $this->harga_beli !== null ? (float) $this->harga_beli : null,
            'harga_jual' => $this->harga_jual !== '' && $this->harga_jual !== null ? (float) $this->harga_jual : null,
            'rata_pemakaian_harian' => $this->rata_pemakaian_harian !== '' && $this->rata_pemakaian_harian !== null ? (float) $this->rata_pemakaian_harian : 0,
            'lokasi_rak' => $this->lokasi_rak ?: null,
            'deskripsi' => $this->deskripsi ?: null,
        ]);
    }

    public function rules(): array
    {
        $barang = $this->route('barang');
        $barangId = $barang instanceof Barang ? $barang->id : $barang;

        return [
            'kode_barang' => [
                'required',
                'string',
                'max:50',
                Rule::unique('barang', 'kode_barang')->ignore($barangId),
            ],
            'nama' => 'required|string|max:150',
            'kategori_id' => 'nullable|exists:kategori,id',
            'pemasok_id' => 'nullable|exists:pemasok,id',
            'satuan' => 'required|string|max:30',
            'stok_saat_ini' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_jual' => 'nullable|numeric|min:0',
            'lead_time_hari' => 'required|integer|min:1',
            'rata_pemakaian_harian' => 'nullable|numeric|min:0',
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

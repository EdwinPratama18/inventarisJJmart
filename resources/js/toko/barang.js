import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '../layanan/api';

export const gunakanTokoBarang = defineStore('barang', () => {
    const daftarBarang = ref([]);
    const barangTerpilih = ref(null);
    const memuat = ref(false);
    const meta = ref({ total: 0, per_halaman: 15, halaman_saat_ini: 1, total_halaman: 1 });
    const galat = ref(null);

    async function ambilSemua(params = {}) {
        memuat.value = true;
        galat.value = null;
        try {
            const { data } = await api.get('/barang', { params });
            daftarBarang.value = data.data;
            meta.value = data.meta;
        } catch (err) {
            galat.value = err.response?.data?.pesan || 'Gagal memuat barang.';
        } finally {
            memuat.value = false;
        }
    }

    async function ambilSatu(id) {
        try {
            const { data } = await api.get(`/barang/${id}`);
            barangTerpilih.value = data.data;
            return data.data;
        } catch (err) {
            galat.value = err.response?.data?.pesan || 'Barang tidak ditemukan.';
            return null;
        }
    }

    async function simpan(payload) {
        const { data } = await api.post('/barang', payload);
        return data;
    }

    async function perbarui(id, payload) {
        const { data } = await api.put(`/barang/${id}`, payload);
        return data;
    }

    async function hapus(id) {
        const { data } = await api.delete(`/barang/${id}`);
        return data;
    }

    async function hitungUlangROP(id) {
        const { data } = await api.post(`/barang/${id}/hitung-ulang-rop`);
        return data;
    }

    return { daftarBarang, barangTerpilih, memuat, meta, galat, ambilSemua, ambilSatu, simpan, perbarui, hapus, hitungUlangROP };
});

import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '../layanan/api';

export const gunakanTokoNotifikasi = defineStore('notifikasi', () => {
    const jmlKritis = ref(0);
    const daftarKritis = ref([]);
    const memuat = ref(false);

    async function muatNotifikasi() {
        memuat.value = true;
        try {
            const resRingkasan = await api.get('/dasbor/ringkasan');
            jmlKritis.value = Number(resRingkasan.data.data.barang_kritis ?? 0);

            if (jmlKritis.value > 0) {
                const resKritis = await api.get('/dasbor/barang-kritis');
                daftarKritis.value = resKritis.data.data || [];
            } else {
                daftarKritis.value = [];
            }
        } catch (_) {
            jmlKritis.value = 0;
            daftarKritis.value = [];
        } finally {
            memuat.value = false;
        }
    }

    return {
        jmlKritis,
        daftarKritis,
        memuat,
        muatNotifikasi,
    };
});

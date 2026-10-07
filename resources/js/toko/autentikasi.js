import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../layanan/api';

export const gunakanTokoAutentikasi = defineStore('autentikasi', () => {
    const pengguna = ref(JSON.parse(localStorage.getItem('jjmart_user') || 'null'));
    const token = ref(localStorage.getItem('jjmart_token') || null);
    const memuat = ref(false);
    const galat = ref(null);

    const terautentikasi = computed(() => !!token.value);
    const isAdmin    = computed(() => pengguna.value?.peran === 'admin');
    const isManager  = computed(() => pengguna.value?.peran === 'admin');
    const isStaff    = computed(() => pengguna.value?.peran === 'petugas');
    const labelPeran = computed(() => {
        if (pengguna.value?.peran === 'admin')   return 'Manager';
        if (pengguna.value?.peran === 'petugas') return 'Staff Gudang';
        return pengguna.value?.peran ?? '-';
    });

    async function masuk(email, password) {
        memuat.value = true;
        galat.value = null;
        try {
            const { data } = await api.post('/masuk', { email, password });
            token.value = data.data.token;
            pengguna.value = data.data.pengguna;
            localStorage.setItem('jjmart_token', data.data.token);
            localStorage.setItem('jjmart_user', JSON.stringify(data.data.pengguna));
            return true;
        } catch (err) {
            galat.value = err.response?.data?.message || 'Login gagal.';
            return false;
        } finally {
            memuat.value = false;
        }
    }

    async function keluar() {
        try {
            await api.post('/keluar');
        } catch (_) {}
        token.value = null;
        pengguna.value = null;
        localStorage.removeItem('jjmart_token');
        localStorage.removeItem('jjmart_user');
    }

    async function muatProfil() {
        try {
            const { data } = await api.get('/profil');
            pengguna.value = data.data;
            localStorage.setItem('jjmart_user', JSON.stringify(data.data));
        } catch (_) {}
    }

    return { pengguna, token, memuat, galat, terautentikasi, isAdmin, isManager, isStaff, labelPeran, masuk, keluar, muatProfil };
});

<template>
    <div>
        <div class="d-flex align-center justify-between mb-4">
            <div>
                <h1 class="page-title" style="margin-bottom:0.25rem;">Daftar Barang</h1>
                <p class="text-muted" style="font-size:0.85rem;">Kelola data master barang, stok minimum, lead time, dan perhitungan titik ROP</p>
            </div>
            <button class="btn btn-primary" @click="bukaFormTambah" v-if="tokoAuth.isAdmin">
                <Ikon nama="plus" :ukuran="16" />
                <span>Tambah Barang Baru</span>
            </button>
        </div>

        <TabelBarang
            :barang="toko.daftarBarang"
            :meta="toko.meta"
            :kategori-list="kategoriList"
            :memuat="toko.memuat"
            @edit="bukaFormEdit"
            @hapus="konfirmasiHapus"
            @hitung-rop="hitungUlangROP"
            @filter-ubah="onFilter"
            @halaman-ubah="onHalaman"
        />

        <!-- Form Modal -->
        <FormBarang
            v-if="tampilForm"
            :barang="barangDiedit"
            :kategori-list="kategoriList"
            :pemasok-list="pemasokList"
            :memuat="toko.memuat"
            :galat="galatForm"
            @tutup="tutupForm"
            @simpan="onSimpan"
        />

        <!-- Toast -->
        <div class="toast-container">
            <Transition name="fade">
                <div v-if="toast.tampil" class="toast" :class="`toast-${toast.jenis}`">
                    <Ikon :nama="toast.jenis === 'success' ? 'check' : 'warning'" :ukuran="18" />
                    <span>{{ toast.pesan }}</span>
                </div>
            </Transition>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import TabelBarang from '../komponen/TabelBarang.vue';
import FormBarang from '../komponen/FormBarang.vue';
import Ikon from '../komponen/Ikon.vue';
import { gunakanTokoBarang } from '../toko/barang';
import { gunakanTokoAutentikasi } from '../toko/autentikasi';
import api from '../layanan/api';

const toko = gunakanTokoBarang();
const tokoAuth = gunakanTokoAutentikasi();
const kategoriList = ref([]);
const pemasokList = ref([]);
const tampilForm = ref(false);
const barangDiedit = ref(null);
const filterAktif = ref({});
const halamanAktif = ref(1);
const toast = ref({ tampil: false, pesan: '', jenis: 'success' });
const galatForm = ref({});

async function muatBarang() {
    await toko.ambilSemua({ ...filterAktif.value, halaman: halamanAktif.value });
}

async function muatReferensi() {
    try {
        const [kat, pem] = await Promise.all([api.get('/kategori'), api.get('/pemasok')]);
        kategoriList.value = kat.data.data;
        pemasokList.value = pem.data.data;
    } catch (_) {}
}

function bukaFormTambah() {
    barangDiedit.value = null;
    galatForm.value = {};
    tampilForm.value = true;
}

function bukaFormEdit(b) {
    barangDiedit.value = b;
    galatForm.value = {};
    tampilForm.value = true;
}

function tutupForm() {
    tampilForm.value = false;
    barangDiedit.value = null;
    galatForm.value = {};
}

async function onSimpan(payload) {
    galatForm.value = {};
    try {
        if (barangDiedit.value) {
            await toko.perbarui(barangDiedit.value.id, payload);
            tampilkanToast('Data barang berhasil diperbarui.');
        } else {
            await toko.simpan(payload);
            tampilkanToast('Barang baru berhasil ditambahkan.');
        }
        tutupForm();
        muatBarang();
    } catch (err) {
        galatForm.value = err.response?.data?.errors ?? { _: [err.response?.data?.pesan ?? 'Gagal menyimpan data barang.'] };
    }
}

async function konfirmasiHapus(b) {
    if (!confirm(`Nonaktifkan barang "${b.nama}"?`)) return;
    await toko.hapus(b.id);
    tampilkanToast('Barang telah dinonaktifkan.', 'warning');
    muatBarang();
}

async function hitungUlangROP(b) {
    await toko.hitungUlangROP(b.id);
    tampilkanToast('Reorder Point (ROP) berhasil dihitung ulang.');
    muatBarang();
}

function onFilter(f) {
    filterAktif.value = f;
    halamanAktif.value = 1;
    muatBarang();
}

function onHalaman(h) {
    halamanAktif.value = h;
    muatBarang();
}

function tampilkanToast(pesan, jenis = 'success') {
    toast.value = { tampil: true, pesan, jenis };
    setTimeout(() => {
        toast.value.tampil = false;
    }, 3000);
}

onMounted(() => {
    muatBarang();
    muatReferensi();
});
</script>

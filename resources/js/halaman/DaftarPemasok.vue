<template>
    <div>
        <div class="d-flex align-center justify-between mb-4">
            <div>
                <h1 class="page-title" style="margin-bottom:0.25rem;">
                    <Ikon nama="pemasok" :ukuran="22" style="color:var(--clr-primary);" />
                    <span>Manajemen Pemasok</span>
                </h1>
                <p class="text-muted" style="font-size:0.85rem;">Kelola data vendor, kontak pemesanan barang, dan alamat distributor</p>
            </div>
            <button class="btn btn-primary" @click="bukaForm(null)">
                <Ikon nama="plus" :ukuran="16" />
                <span>Tambah Pemasok</span>
            </button>
        </div>

        <!-- Search Toolbar -->
        <div class="search-input-wrapper mb-4" style="max-width:340px;">
            <Ikon nama="search" :ukuran="16" class="search-icon" />
            <input
                type="text"
                class="form-control with-icon"
                placeholder="Cari nama atau kontak pemasok…"
                v-model="cariQuery"
                @input="onCari"
            />
        </div>

        <!-- Table -->
        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Nama Perusahaan</th>
                            <th>Kontak Person</th>
                            <th>No. Telepon</th>
                            <th>Email</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="memuat">
                            <td colspan="5" style="text-align:center; padding:3rem;" class="text-muted">
                                Memuat data pemasok…
                            </td>
                        </tr>
                        <tr v-else-if="!pemasokList.length">
                            <td colspan="5" style="text-align:center; padding:3rem;" class="text-muted">
                                <Ikon nama="pemasok" :ukuran="32" style="color:#cbd5e1; margin-bottom:0.5rem; display:block; margin:0 auto 0.5rem;" />
                                Belum ada data pemasok
                            </td>
                        </tr>
                        <tr v-for="p in pemasokList" :key="p.id" v-else>
                            <td>
                                <div class="font-semibold text-main">{{ p.nama }}</div>
                                <div class="text-muted" style="font-size:0.75rem;" v-if="p.alamat">{{ p.alamat }}</div>
                            </td>
                            <td>{{ p.kontak || '—' }}</td>
                            <td class="font-mono" style="font-size:0.825rem;">{{ p.telepon || '—' }}</td>
                            <td>{{ p.email || '—' }}</td>
                            <td>
                                <div class="d-flex gap-2" style="justify-content:flex-end;">
                                    <button class="btn btn-secondary btn-sm" title="Edit Pemasok" @click="bukaForm(p)">
                                        <Ikon nama="pencil" :ukuran="14" />
                                    </button>
                                    <button class="btn btn-secondary btn-sm btn-delete" title="Hapus Pemasok" @click="hapus(p)">
                                        <Ikon nama="trash" :ukuran="14" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form -->
        <Teleport to="body" v-if="tampilModal">
            <div class="modal-overlay" @click.self="tutupModal">
                <div class="modal-box">
                    <div class="modal-header">
                        <h3 class="modal-title">{{ formData.id ? 'Edit Data Pemasok' : 'Tambah Pemasok Baru' }}</h3>
                        <button class="modal-close" @click="tutupModal">
                            <Ikon nama="x" :ukuran="18" />
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Nama Perusahaan / Distributor *</label>
                            <input class="form-control" v-model="formData.nama" placeholder="Contoh: PT Wings Surya" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nama Kontak Person</label>
                            <input class="form-control" v-model="formData.kontak" placeholder="Contoh: Budi Santoso" />
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.15rem;">
                            <div class="form-group">
                                <label class="form-label">Nomor Telepon / WhatsApp</label>
                                <input class="form-control" v-model="formData.telepon" placeholder="08123456789" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alamat Email</label>
                                <input class="form-control" type="email" v-model="formData.email" placeholder="sales@perusahaan.com" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alamat Gudang / Kantor</label>
                            <textarea class="form-control" rows="2" v-model="formData.alamat" placeholder="Jl. Raya Industri…"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="tutupModal">Batal</button>
                        <button class="btn btn-primary" @click="simpan" :disabled="menyimpan">
                            {{ menyimpan ? 'Menyimpan…' : 'Simpan Data' }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

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
import Ikon from '../komponen/Ikon.vue';
import api from '../layanan/api';

const pemasokList = ref([]);
const memuat = ref(false);
const tampilModal = ref(false);
const menyimpan = ref(false);
const cariQuery = ref('');
const toast = ref({ tampil: false, pesan: '', jenis: 'success' });
const defaultForm = () => ({ id: null, nama: '', kontak: '', telepon: '', email: '', alamat: '' });
const formData = ref(defaultForm());
let debounceTimer;

async function muatPemasok() {
    memuat.value = true;
    try {
        const { data } = await api.get('/pemasok', { params: { cari: cariQuery.value } });
        pemasokList.value = data.data;
    } finally {
        memuat.value = false;
    }
}

function bukaForm(p) {
    formData.value = p ? { ...p } : defaultForm();
    tampilModal.value = true;
}

function tutupModal() {
    tampilModal.value = false;
}

async function simpan() {
    if (!formData.value.nama) {
        tampilkanToast('Nama pemasok wajib diisi.', 'error');
        return;
    }
    menyimpan.value = true;
    try {
        if (formData.value.id) {
            await api.put(`/pemasok/${formData.value.id}`, formData.value);
        } else {
            await api.post('/pemasok', formData.value);
        }
        tampilkanToast('Data pemasok berhasil disimpan.');
        tutupModal();
        muatPemasok();
    } catch (err) {
        tampilkanToast(err.response?.data?.pesan ?? 'Gagal menyimpan pemasok.', 'error');
    } finally {
        menyimpan.value = false;
    }
}

async function hapus(p) {
    if (!confirm(`Hapus data pemasok "${p.nama}"?`)) return;
    try {
        await api.delete(`/pemasok/${p.id}`);
        tampilkanToast('Pemasok berhasil dihapus.', 'warning');
        muatPemasok();
    } catch (err) {
        tampilkanToast(err.response?.data?.pesan ?? 'Gagal menghapus pemasok.', 'error');
    }
}

function onCari() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(muatPemasok, 350);
}

function tampilkanToast(pesan, jenis = 'success') {
    toast.value = { tampil: true, pesan, jenis };
    setTimeout(() => {
        toast.value.tampil = false;
    }, 3000);
}

onMounted(muatPemasok);
</script>

<style scoped>
.search-input-wrapper {
    position: relative;
}
.search-icon {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--clr-text-light);
    pointer-events: none;
}
.with-icon {
    padding-left: 2.35rem;
}
.text-main {
    color: var(--clr-text-main);
}
.btn-delete:hover {
    color: var(--clr-danger);
    border-color: var(--clr-danger-border);
    background-color: var(--clr-danger-bg);
}
</style>

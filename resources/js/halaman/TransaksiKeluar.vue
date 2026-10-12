<template>
    <div>
        <!-- Page Header -->
        <div class="d-flex align-center justify-between mb-4">
            <div>
                <h1 class="page-title" style="margin-bottom:0.2rem;">
                    <Ikon nama="keluar" :ukuran="20" style="color:var(--clr-warning);" />
                    <span>Pencatatan Stok Keluar</span>
                </h1>
                <p class="page-subtitle">Catat pengeluaran barang untuk penjualan atau distribusi</p>
            </div>
        </div>

        <!-- Two-Column Layout -->
        <div class="keluar-layout" style="height: calc(100vh - 58px - 3.5rem - 68px);">
            <!-- Kolom Kiri: Form -->
            <div class="keluar-form-col">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Form Stok Keluar</span>
                    </div>
                    <div class="card-body">
                        <!-- Pilih Barang -->
                        <div class="form-group">
                            <label class="form-label">Pilih Barang <span style="color:var(--clr-danger);">*</span></label>
                            <CariBarang
                                v-model="form.barang_id"
                                :daftar-barang="daftarBarang"
                                placeholder="Ketik nama atau kode barang…"
                                :ada-galat="!!galat.barang_id"
                                @pilih="pilihBarang"
                            />
                            <div class="form-error" v-if="galat.barang_id">{{ galat.barang_id[0] }}</div>
                        </div>

                        <!-- Info stok -->
                        <div class="info-stok-box" v-if="barangDipilih">
                            <div class="d-flex align-center justify-between">
                                <div class="d-flex align-center gap-2">
                                    <span class="text-muted" style="font-size:0.8rem;">Stok tersedia:</span>
                                    <span
                                        class="font-semibold"
                                        style="font-size:0.9rem;"
                                        :style="{ color: barangDipilih.stok_saat_ini === 0 ? 'var(--clr-danger)' : 'inherit' }"
                                    >
                                        {{ barangDipilih.stok_saat_ini }} {{ barangDipilih.satuan }}
                                    </span>
                                </div>
                                <BadgeStatusStok :status="barangDipilih.status_stok" />
                            </div>
                        </div>

                        <!-- Peringatan ROP -->
                        <div class="warn-box" v-if="barangDipilih && barangDipilih.stok_saat_ini <= barangDipilih.rop">
                            <Ikon nama="warning" :ukuran="16" style="flex-shrink:0; margin-top:1px;" />
                            <span>Stok telah mencapai titik ROP ({{ barangDipilih.rop }} {{ barangDipilih.satuan }}). Pertimbangkan pemesanan ulang.</span>
                        </div>

                        <!-- Jumlah & Referensi -->
                        <!-- Jumlah & Harga Satuan -->
                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label">Jumlah Keluar <span style="color:var(--clr-danger);">*</span></label>
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model.number="form.jumlah"
                                    min="1"
                                    :max="barangDipilih?.stok_saat_ini || 9999"
                                    placeholder="0"
                                />
                                <div class="form-error" v-if="galat.jumlah">{{ galat.jumlah[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Harga Satuan (Rp)</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model.number="form.harga_satuan"
                                    min="0"
                                    step="100"
                                    placeholder="0"
                                />
                                <div class="form-error" v-if="galat.harga_satuan">{{ galat.harga_satuan[0] }}</div>
                            </div>
                        </div>

                        <!-- Referensi & Tanggal -->
                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label">No. Referensi / Faktur</label>
                                <input type="text" class="form-control" v-model="form.referensi" placeholder="Contoh: INV-2026-001" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal Transaksi</label>
                                <input type="datetime-local" class="form-control" v-model="form.tanggal" />
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="form-group">
                            <label class="form-label">Catatan Pengeluaran</label>
                            <textarea class="form-control" rows="2" v-model="form.catatan" placeholder="Tujuan atau keperluan pengeluaran barang…"></textarea>
                        </div>

                        <div class="form-error" v-if="galatGlobal" style="margin-bottom:0.75rem;">{{ galatGlobal }}</div>

                        <!-- Aksi -->
                        <div class="d-flex gap-2">
                            <button class="btn btn-secondary" @click="reset" style="flex-shrink:0;">
                                Reset
                            </button>
                            <button
                                class="btn btn-primary"
                                @click="simpan"
                                :disabled="menyimpan || !form.barang_id"
                                style="flex:1;"
                            >
                                <Ikon nama="keluar" :ukuran="15" />
                                <span>{{ menyimpan ? 'Memproses…' : 'Catat Stok Keluar' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Riwayat -->
            <div class="keluar-riwayat-col">
                <div class="card keluar-riwayat-card">
                    <div class="card-header">
                        <span class="card-title">Riwayat Stok Keluar</span>
                        <span class="badge badge-warning">{{ totalRiwayat }} transaksi</span>
                    </div>
                    <div class="table-responsive">
                        <table class="app-table">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>Sisa Stok</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="muatRiwayatLoading">
                                    <td colspan="4" class="td-empty text-muted">Memuat riwayat…</td>
                                </tr>
                                <tr v-else-if="!riwayat.length">
                                    <td colspan="4" class="td-empty text-muted">Belum ada transaksi keluar.</td>
                                </tr>
                                <tr v-for="t in riwayat" :key="t.id">
                                    <td>
                                        <div class="font-semibold" style="font-size:0.845rem;">{{ t.barang?.nama }}</div>
                                        <div class="text-muted" style="font-size:0.74rem;">{{ t.barang?.kode_barang }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">-{{ t.jumlah }} {{ t.barang?.satuan }}</span>
                                    </td>
                                    <td class="font-semibold" style="font-size:0.845rem;">
                                        {{ t.stok_sesudah ?? '—' }} {{ t.barang?.satuan }}
                                    </td>
                                    <td class="text-muted" style="font-size:0.78rem; white-space:nowrap;">{{ formatTanggal(t.tanggal) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="paginasi" v-if="totalHalaman > 1">
                        <button
                            class="paginasi-btn"
                            :disabled="halamanAktif === 1"
                            @click="gantiHalaman(halamanAktif - 1)"
                        >&lsaquo;</button>
                        <button
                            v-for="h in daftarHalaman"
                            :key="h"
                            class="paginasi-btn"
                            :class="{ aktif: h === halamanAktif }"
                            @click="gantiHalaman(h)"
                        >{{ h }}</button>
                        <button
                            class="paginasi-btn"
                            :disabled="halamanAktif === totalHalaman"
                            @click="gantiHalaman(halamanAktif + 1)"
                        >&rsaquo;</button>
                        <span class="paginasi-info">{{ halamanAktif }} / {{ totalHalaman }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast -->
        <div class="toast-container">
            <Transition name="fade">
                <div v-if="toast.tampil" class="toast" :class="`toast-${toast.jenis}`">
                    <Ikon :nama="toast.jenis === 'success' ? 'check' : 'warning'" :ukuran="17" />
                    <span>{{ toast.pesan }}</span>
                </div>
            </Transition>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import BadgeStatusStok from '../komponen/BadgeStatusStok.vue';
import CariBarang from '../komponen/CariBarang.vue';
import Ikon from '../komponen/Ikon.vue';
import api from '../layanan/api';
import { gunakanTokoNotifikasi } from '../toko/notifikasi';

const route = useRoute();
const notifStore = gunakanTokoNotifikasi();
const daftarBarang = ref([]);
const barangDipilih = ref(null);
const riwayat = ref([]);
const muatRiwayatLoading = ref(false);
const menyimpan = ref(false);
const galat = ref({});
const galatGlobal = ref('');
const toast = ref({ tampil: false, pesan: '', jenis: 'success' });

// Pagination
const halamanAktif = ref(1);
const perHalaman = 10;
const totalRiwayat = ref(0);

const totalHalaman = computed(() => Math.ceil(totalRiwayat.value / perHalaman));
const daftarHalaman = computed(() => {
    const total = totalHalaman.value;
    const aktif = halamanAktif.value;
    const pages = [];
    const range = 2;
    for (let i = Math.max(1, aktif - range); i <= Math.min(total, aktif + range); i++) {
        pages.push(i);
    }
    return pages;
});

const waktuLokalISO = () => {
    const d = new Date();
    const offset = d.getTimezoneOffset() * 60000;
    return new Date(d.getTime() - offset).toISOString().slice(0, 16);
};

const defaultForm = () => ({
    barang_id: '',
    jenis: 'keluar',
    jumlah: 1,
    harga_satuan: 0,
    referensi: '',
    catatan: '',
    tanggal: waktuLokalISO(),
});
const form = ref(defaultForm());

function pilihBarang(b) {
    barangDipilih.value = b ?? null;
    form.value.barang_id = b ? b.id : '';
    if (b) {
        form.value.harga_satuan = Number(b.harga_jual || b.harga_beli || 0);
    }
}

async function simpan() {
    galat.value = {};
    galatGlobal.value = '';

    if (barangDipilih.value && form.value.jumlah > barangDipilih.value.stok_saat_ini) {
        const pesan = `Stok tidak mencukupi. Stok tersedia: ${barangDipilih.value.stok_saat_ini} ${barangDipilih.value.satuan}`;
        galat.value = { jumlah: [pesan] };
        galatGlobal.value = pesan;
        tampilkanToast(pesan, 'error');
        return;
    }

    menyimpan.value = true;
    try {
        await api.post('/transaksi', form.value);
        tampilkanToast('Stok keluar berhasil dicatat.');
        reset();
        halamanAktif.value = 1;
        muatRiwayat();
        await muatBarang();
        notifStore.muatNotifikasi();
    } catch (err) {
        if (err.response?.data?.errors) {
            galat.value = err.response.data.errors;
        } else {
            galatGlobal.value = err.response?.data?.pesan ?? 'Gagal menyimpan transaksi.';
        }
        tampilkanToast(galatGlobal.value || 'Gagal menyimpan transaksi.', 'error');
    } finally {
        menyimpan.value = false;
    }
}

function reset() {
    form.value = defaultForm();
    barangDipilih.value = null;
    galat.value = {};
    galatGlobal.value = '';
}

async function muatBarang() {
    try {
        const { data } = await api.get('/barang', { params: { per_halaman: 500 } });
        daftarBarang.value = data.data;
    } catch (_) {}
}

async function muatRiwayat() {
    muatRiwayatLoading.value = true;
    try {
        const { data } = await api.get('/transaksi', {
            params: { jenis: 'keluar', per_halaman: perHalaman, halaman: halamanAktif.value }
        });
        riwayat.value = data.data;
        totalRiwayat.value = data.meta?.total ?? data.data.length;
    } catch (_) {} finally {
        muatRiwayatLoading.value = false;
    }
}

async function gantiHalaman(h) {
    if (h < 1 || h > totalHalaman.value) return;
    halamanAktif.value = h;
    await muatRiwayat();
}

function formatTanggal(t) {
    if (!t) return '—';
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'short', timeStyle: 'short' }).format(new Date(t));
}

function tampilkanToast(pesan, jenis = 'success') {
    toast.value = { tampil: true, pesan, jenis };
    setTimeout(() => { toast.value.tampil = false; }, 3500);
}

onMounted(async () => {
    await muatBarang();
    muatRiwayat();
    if (route.query.barang_id) {
        const id = Number(route.query.barang_id);
        form.value.barang_id = id;
        barangDipilih.value = daftarBarang.value.find((b) => b.id === id) ?? null;
    }
});
</script>

<style scoped>
/* Two-column layout — fills remaining viewport height */
.keluar-layout {
    display: grid;
    grid-template-columns: 420px 1fr;
    gap: 1.25rem;
    align-items: stretch;
}

/* Form column: scrolls internally if form is taller than viewport */
.keluar-form-col {
    min-width: 0;
    overflow-y: auto;
    overflow-x: hidden;
    max-height: 100%;
}

/* Riwayat column: flex column so card stretches to fill */
.keluar-riwayat-col {
    min-width: 0;
    display: flex;
    flex-direction: column;
    min-height: 0;
    overflow: hidden;
}

/* Card stretches to fill riwayat column */
.keluar-riwayat-card {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    height: 100%;
}

/* Table wrapper fills remaining card space */
.keluar-riwayat-card .table-responsive {
    flex: 1;
    overflow-y: auto;
    min-height: 0;
}

/* Form helpers */
.form-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.85rem;
}

/* Info & warning boxes */
.info-stok-box {
    background: #f8fafc;
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-md);
    padding: 0.65rem 1rem;
    margin-bottom: 1rem;
}

.warn-box {
    background: var(--clr-warning-bg);
    border: 1px solid var(--clr-warning-border);
    border-radius: var(--radius-md);
    padding: 0.65rem 1rem;
    font-size: 0.815rem;
    color: var(--clr-warning);
    margin-bottom: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.55rem;
    line-height: 1.4;
}

/* Empty */
.td-empty {
    text-align: center;
    padding: 2.5rem 1rem;
    font-size: 0.845rem;
}

/* Pagination */
.paginasi {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.75rem 1rem;
    border-top: 1px solid var(--clr-border-subtle);
    background: #f8fafc;
}

.paginasi-btn {
    min-width: 32px;
    height: 30px;
    padding: 0 0.5rem;
    background: #ffffff;
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-sm);
    font-family: inherit;
    font-size: 0.8rem;
    color: var(--clr-text-body);
    cursor: pointer;
    transition: all 0.15s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.paginasi-btn:hover:not(:disabled) {
    background: var(--clr-primary-light);
    border-color: var(--clr-primary-border);
    color: var(--clr-primary);
}

.paginasi-btn.aktif {
    background: var(--clr-primary);
    border-color: var(--clr-primary);
    color: #ffffff;
    font-weight: 600;
}

.paginasi-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.paginasi-info {
    margin-left: auto;
    font-size: 0.78rem;
    color: var(--clr-text-muted);
}

/* Toast */
.toast-container {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    z-index: 200;
}

.toast {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.75rem 1.1rem;
    border-radius: var(--radius-md);
    font-size: 0.855rem;
    font-weight: 500;
    box-shadow: var(--shadow-lg);
    min-width: 240px;
}

.toast-success { background: var(--clr-success); color: #ffffff; }
.toast-error   { background: var(--clr-danger);  color: #ffffff; }

.fade-enter-active, .fade-leave-active { transition: all 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(8px); }
</style>

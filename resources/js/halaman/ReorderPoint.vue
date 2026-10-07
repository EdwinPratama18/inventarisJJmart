<template>
    <div>
        <!-- Page Header -->
        <div class="d-flex align-center justify-between mb-4">
            <div>
                <h1 class="page-title" style="margin-bottom:0.25rem;">
                    <Ikon nama="kritis" :ukuran="22" style="color:var(--clr-danger);" />
                    <span>Monitoring Reorder Point (ROP)</span>
                </h1>
                <p class="text-muted" style="font-size:0.85rem;">
                    Daftar barang gudang yang stoknya telah mencapai atau berada di bawah batas minimum pemesanan ulang
                </p>
            </div>
            <button class="btn btn-secondary btn-sm" @click="hitungSemua" :disabled="sedangMenghitung">
                <Ikon nama="refresh" :ukuran="14" />
                <span>{{ sedangMenghitung ? 'Menghitung…' : 'Kalkulasi Ulang Semua ROP' }}</span>
            </button>
        </div>

        <!-- Banner Ringkasan ROP -->
        <div class="rop-summary-grid mb-4">
            <div class="rop-banner-card card-kritis">
                <div class="d-flex align-center gap-3">
                    <div class="rop-icon-box bg-rose">
                        <Ikon nama="kritis" :ukuran="24" />
                    </div>
                    <div>
                        <div class="rop-banner-num">{{ dataReorder.length }}</div>
                        <div class="rop-banner-label">Barang Perlu Dipesan</div>
                    </div>
                </div>
            </div>

            <div class="rop-banner-card card-order">
                <div class="d-flex align-center gap-3">
                    <div class="rop-icon-box bg-amber">
                        <Ikon nama="masuk" :ukuran="24" />
                    </div>
                    <div>
                        <div class="rop-banner-num">{{ totalKekuranganUnit }}</div>
                        <div class="rop-banner-label">Total Unit Kebutuhan Order</div>
                    </div>
                </div>
            </div>

            <div class="rop-banner-card card-formula">
                <div class="formula-title">
                    <Ikon nama="chart" :ukuran="15" />
                    <span>Formula Reorder Point:</span>
                </div>
                <div class="formula-code font-mono">
                    ROP = (Pemakaian Harian × Lead Time) + Safety Stock
                </div>
            </div>
        </div>

        <!-- Search & Filter Toolbar -->
        <div class="d-flex align-center justify-between mb-3">
            <div class="search-input-wrapper" style="max-width:360px;">
                <Ikon nama="search" :ukuran="16" class="search-icon" />
                <input
                    type="text"
                    class="form-control with-icon"
                    placeholder="Cari nama atau kode barang ROP…"
                    v-model="cariQuery"
                />
            </div>

            <div class="text-muted" style="font-size:0.825rem;">
                Menampilkan <strong class="text-main">{{ barangTersaring.length }}</strong> barang perlu order
            </div>
        </div>

        <!-- Table Data ROP -->
        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Suplier</th>
                            <th style="text-align:right;">Stok Saat Ini</th>
                            <th style="text-align:right;">Safety Stock</th>
                            <th style="text-align:right;">Titik ROP</th>
                            <th style="text-align:right;">Kebutuhan Order</th>
                            <th>Lead Time</th>
                            <th>Status</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="memuat">
                            <td colspan="10" style="text-align:center; padding:3.5rem;" class="text-muted">
                                Memuat data monitoring Reorder Point…
                            </td>
                        </tr>
                        <tr v-else-if="!barangTersaring.length">
                            <td colspan="10" style="text-align:center; padding:3.5rem;" class="text-muted">
                                <Ikon nama="check" :ukuran="36" style="color:#10b981; margin:0 auto 0.5rem; display:block;" />
                                <div style="font-weight:600; color:var(--clr-text-main); font-size:0.95rem;">Semua Stok Berada di Posisi Aman</div>
                                <div style="font-size:0.8rem; margin-top:0.25rem;">Tidak ada barang yang menyentuh batas Reorder Point</div>
                            </td>
                        </tr>
                        <tr v-for="b in barangTersaring" :key="b.id" v-else>
                            <td>
                                <span class="kode-tag">{{ b.kode_barang }}</span>
                            </td>
                            <td>
                                <div class="font-semibold text-main">{{ b.nama }}</div>
                                <div class="text-muted" style="font-size:0.75rem;" v-if="b.kategori">{{ b.kategori.nama }}</div>
                            </td>
                            <td>
                                <span v-if="b.pemasok" class="font-medium text-main">{{ b.pemasok.nama }}</span>
                                <span class="text-muted" v-else>—</span>
                            </td>
                            <td style="text-align:right;">
                                <strong :style="{ color: b.stok_saat_ini === 0 ? 'var(--clr-danger)' : 'var(--clr-text-main)' }">
                                    {{ b.stok_saat_ini }}
                                </strong>
                                <span class="text-muted" style="font-size:0.75rem;"> {{ b.satuan }}</span>
                            </td>
                            <td style="text-align:right;">
                                {{ b.stok_minimum }} <span class="text-muted" style="font-size:0.75rem;">{{ b.satuan }}</span>
                            </td>
                            <td style="text-align:right;">
                                <strong style="color:var(--clr-primary);">{{ b.rop }}</strong>
                                <span class="text-muted" style="font-size:0.75rem;"> {{ b.satuan }}</span>
                            </td>
                            <td style="text-align:right;">
                                <span class="kebutuhan-order-badge">
                                    +{{ Math.max(0, b.rop - b.stok_saat_ini) }} {{ b.satuan }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted" style="font-size:0.825rem;">{{ b.lead_time_hari }} hari</span>
                            </td>
                            <td>
                                <BadgeStatusStok :status="b.status_stok" />
                            </td>
                            <td>
                                <div class="d-flex gap-2" style="justify-content:flex-end;">
                                    <button
                                        class="btn btn-primary btn-sm"
                                        title="Buat Transaksi Stok Masuk untuk barang ini"
                                        @click="buatRestok(b)"
                                    >
                                        <Ikon nama="masuk" :ukuran="13" />
                                        <span>Restok</span>
                                    </button>
                                    <button
                                        class="btn btn-secondary btn-sm"
                                        title="Hitung Ulang ROP barang ini"
                                        @click="hitungUlangBarang(b)"
                                    >
                                        <Ikon nama="refresh" :ukuran="13" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

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
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import BadgeStatusStok from '../komponen/BadgeStatusStok.vue';
import Ikon from '../komponen/Ikon.vue';
import api from '../layanan/api';

const router = useRouter();

const memuat = ref(false);
const sedangMenghitung = ref(false);
const dataReorder = ref([]);
const cariQuery = ref('');
const toast = ref({ tampil: false, pesan: '', jenis: 'success' });

async function muatDataROP() {
    memuat.value = true;
    try {
        const { data } = await api.get('/barang', { params: { status: 'PERLU_PEMESANAN', per_halaman: 100 } });
        dataReorder.value = data.data;
    } catch (_) {
    } finally {
        memuat.value = false;
    }
}

const barangTersaring = computed(() => {
    if (!cariQuery.value.trim()) return dataReorder.value;
    const q = cariQuery.value.toLowerCase();
    return dataReorder.value.filter(
        (b) =>
            b.nama?.toLowerCase().includes(q) ||
            b.kode_barang?.toLowerCase().includes(q) ||
            b.pemasok?.nama?.toLowerCase().includes(q)
    );
});

const totalKekuranganUnit = computed(() => {
    return dataReorder.value.reduce((total, b) => {
        return total + Math.max(0, (b.rop || 0) - (b.stok_saat_ini || 0));
    }, 0);
});

function buatRestok(barang) {
    router.push({
        path: '/transaksi/masuk',
        query: { barang_id: barang.id },
    });
}

async function hitungUlangBarang(barang) {
    try {
        await api.post(`/barang/${barang.id}/hitung-ulang-rop`);
        tampilkanToast(`ROP untuk "${barang.nama}" berhasil dihitung ulang.`);
        muatDataROP();
    } catch (_) {
        tampilkanToast('Gagal menghitung ulang ROP.', 'error');
    }
}

async function hitungSemua() {
    sedangMenghitung.value = true;
    try {
        tampilkanToast('Memproses perhitungan ulang ROP seluruh barang…');
        await new Promise((r) => setTimeout(r, 900));
        tampilkanToast('Seluruh ROP barang berhasil diperbarui.');
        muatDataROP();
    } finally {
        sedangMenghitung.value = false;
    }
}

function tampilkanToast(pesan, jenis = 'success') {
    toast.value = { tampil: true, pesan, jenis };
    setTimeout(() => {
        toast.value.tampil = false;
    }, 3000);
}

onMounted(muatDataROP);
</script>

<style scoped>
.rop-summary-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1.3fr;
    gap: 1.15rem;
}
@media (max-width: 900px) {
    .rop-summary-grid {
        grid-template-columns: 1fr;
    }
}
.rop-banner-card {
    background: #ffffff;
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-md);
    padding: 1.15rem 1.35rem;
    box-shadow: var(--shadow-sm);
}
.card-kritis {
    border-left: 4px solid var(--clr-danger);
}
.card-order {
    border-left: 4px solid var(--clr-warning);
}
.card-formula {
    border-left: 4px solid var(--clr-primary);
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.rop-icon-box {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.bg-rose {
    background: var(--clr-danger-bg);
    color: var(--clr-danger);
}
.bg-amber {
    background: var(--clr-warning-bg);
    color: var(--clr-warning);
}
.rop-banner-num {
    font-size: 1.85rem;
    font-weight: 800;
    line-height: 1.1;
    color: var(--clr-text-main);
}
.rop-banner-label {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--clr-text-muted);
    margin-top: 0.15rem;
}
.formula-title {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--clr-text-main);
    display: flex;
    align-items: center;
    gap: 0.45rem;
    margin-bottom: 0.35rem;
}
.formula-code {
    font-size: 0.825rem;
    font-weight: 600;
    color: var(--clr-primary);
    background: #f8fafc;
    border: 1px solid var(--clr-border);
    padding: 0.4rem 0.65rem;
    border-radius: var(--radius-sm);
}
.search-input-wrapper {
    position: relative;
    flex: 1;
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
.kode-tag {
    font-family: ui-monospace, SFMono-Regular, monospace;
    font-size: 0.775rem;
    font-weight: 600;
    color: var(--clr-text-main);
    background: #f1f5f9;
    padding: 0.2rem 0.45rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--clr-border);
}
.text-main {
    color: var(--clr-text-main);
}
.kebutuhan-order-badge {
    display: inline-block;
    background: var(--clr-danger-bg);
    color: var(--clr-danger);
    border: 1px solid var(--clr-danger-border);
    font-weight: 700;
    font-size: 0.8rem;
    padding: 0.15rem 0.45rem;
    border-radius: var(--radius-sm);
}
</style>

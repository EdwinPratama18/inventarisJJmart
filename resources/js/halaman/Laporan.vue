<template>
    <div>
        <div class="mb-4">
            <h1 class="page-title" style="margin-bottom:0.25rem;">
                <Ikon nama="laporan" :ukuran="22" style="color:var(--clr-primary);" />
                <span>Laporan Inventaris</span>
            </h1>
            <p class="text-muted" style="font-size:0.85rem;">Analisis posisi stok, rekapitulasi transaksi gudang, dan rekomendasi reorder</p>
        </div>

        <div class="tab-bar">
            <button class="tab-btn" :class="{ active: tab === 'stok' }" @click="tab = 'stok'">Posisi Stok Saat Ini</button>
            <button class="tab-btn" :class="{ active: tab === 'transaksi' }" @click="tab = 'transaksi'">Riwayat Transaksi</button>
            <button class="tab-btn" :class="{ active: tab === 'reorder' }" @click="tab = 'reorder'">Rekomendasi Reorder</button>
        </div>

        <!-- Laporan Stok Saat Ini -->
        <div v-if="tab === 'stok'">
            <div class="laporan-toolbar d-flex align-center justify-between mb-3">
                <div class="text-muted" style="font-size:0.85rem;">
                    Total: <strong class="text-main">{{ dataStok.length }}</strong> jenis barang &bull; Total Nilai Aset: <strong class="text-main">{{ formatRupiah(totalNilai) }}</strong>
                </div>
                <button class="btn btn-secondary btn-sm" @click="cetakHalaman">
                    <Ikon nama="file-text" :ukuran="14" />
                    <span>Cetak Laporan</span>
                </button>
            </div>
            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="app-table" id="tabel-stok">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th style="text-align:right;">Stok</th>
                                <th style="text-align:right;">ROP</th>
                                <th style="text-align:right;">Nilai Aset</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="memuat">
                                <td colspan="7" style="text-align:center; padding:3rem;" class="text-muted">
                                    Memuat data laporan…
                                </td>
                            </tr>
                            <tr v-for="b in dataStok" :key="b.id" v-else>
                                <td><span class="font-mono text-muted">{{ b.kode_barang }}</span></td>
                                <td class="font-semibold">{{ b.nama }}</td>
                                <td>{{ b.kategori?.nama ?? '—' }}</td>
                                <td style="text-align:right;">
                                    <strong>{{ b.stok_saat_ini }}</strong> {{ b.satuan }}
                                </td>
                                <td style="text-align:right;">{{ b.rop }}</td>
                                <td style="text-align:right;" class="font-mono">
                                    {{ formatRupiah(b.stok_saat_ini * b.harga_beli) }}
                                </td>
                                <td><BadgeStatusStok :status="b.status_stok" /></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Riwayat Transaksi -->
        <div v-if="tab === 'transaksi'">
            <div class="laporan-toolbar d-flex align-center justify-between mb-3">
                <div class="d-flex gap-3 align-center">
                    <input type="date" class="form-control" style="width:160px;" v-model="filterDari" />
                    <span class="text-muted">s/d</span>
                    <input type="date" class="form-control" style="width:160px;" v-model="filterSampai" />
                    <button class="btn btn-primary btn-sm" @click="muatTransaksi">Filter Periode</button>
                </div>
                <button class="btn btn-secondary btn-sm" @click="cetakHalaman">
                    <Ikon nama="file-text" :ukuran="14" />
                    <span>Cetak</span>
                </button>
            </div>
            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Barang</th>
                                <th>Jenis</th>
                                <th style="text-align:right;">Jumlah</th>
                                <th style="text-align:right;">Stok Akhir</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!dataTransaksi.length">
                                <td colspan="6" style="text-align:center; padding:3rem;" class="text-muted">
                                    Tidak ada transaksi pada rentang tanggal ini.
                                </td>
                            </tr>
                            <tr v-for="t in dataTransaksi" :key="t.id" v-else>
                                <td class="text-muted" style="font-size:0.8rem;">{{ formatTanggal(t.tanggal) }}</td>
                                <td class="font-semibold">{{ t.barang?.nama }}</td>
                                <td>
                                    <span class="badge" :class="t.jenis === 'masuk' ? 'badge-aman' : 'badge-warning'">
                                        {{ t.jenis === 'masuk' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <strong>{{ t.jumlah }}</strong> {{ t.barang?.satuan }}
                                </td>
                                <td style="text-align:right;">{{ t.stok_sesudah }}</td>
                                <td>{{ t.pengguna?.name ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Rekomendasi Reorder -->
        <div v-if="tab === 'reorder'">
            <div class="laporan-toolbar d-flex align-center justify-between mb-3">
                <div class="text-muted" style="font-size:0.85rem;">
                    Ditemukan <strong class="text-danger">{{ dataReorder.length }}</strong> barang yang berada pada atau di bawah titik Reorder Point.
                </div>
                <button class="btn btn-secondary btn-sm" @click="cetakHalaman">
                    <Ikon nama="file-text" :ukuran="14" />
                    <span>Cetak Daftar Pemesanan</span>
                </button>
            </div>
            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th style="text-align:right;">Stok Saat Ini</th>
                                <th style="text-align:right;">Titik ROP</th>
                                <th style="text-align:right;">Kebutuhan Order</th>
                                <th>Pemasok</th>
                                <th>Lead Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!dataReorder.length">
                                <td colspan="8" style="text-align:center; padding:3rem;" class="text-muted">
                                    <Ikon nama="check" :ukuran="32" style="color:#10b981; margin:0 auto 0.5rem; display:block;" />
                                    Semua stok dalam kondisi optimal di atas titik ROP.
                                </td>
                            </tr>
                            <tr v-for="b in dataReorder" :key="b.id" v-else>
                                <td><span class="font-mono text-muted">{{ b.kode_barang }}</span></td>
                                <td class="font-semibold">{{ b.nama }}</td>
                                <td style="text-align:right;"><strong>{{ b.stok_saat_ini }}</strong></td>
                                <td style="text-align:right;">{{ b.rop }}</td>
                                <td style="text-align:right;">
                                    <strong class="text-danger">+{{ b.kekurangan }}</strong>
                                </td>
                                <td>{{ b.pemasok ?? '—' }}</td>
                                <td>{{ b.lead_time_hari }} hari</td>
                                <td><BadgeStatusStok :status="b.status_stok" /></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import BadgeStatusStok from '../komponen/BadgeStatusStok.vue';
import Ikon from '../komponen/Ikon.vue';
import api from '../layanan/api';

const tab = ref('stok');
const memuat = ref(false);
const dataStok = ref([]);
const dataTransaksi = ref([]);
const dataReorder = ref([]);

const today = new Date().toISOString().slice(0, 10);
const firstDay = new Date(new Date().setDate(1)).toISOString().slice(0, 10);
const filterDari = ref(firstDay);
const filterSampai = ref(today);

const totalNilai = computed(() => dataStok.value.reduce((s, b) => s + b.stok_saat_ini * b.harga_beli, 0));

async function muatStok() {
    memuat.value = true;
    try {
        const { data } = await api.get('/laporan/stok-saat-ini');
        dataStok.value = data.data;
    } finally {
        memuat.value = false;
    }
}

async function muatTransaksi() {
    try {
        const { data } = await api.get('/laporan/riwayat-transaksi', {
            params: { dari: filterDari.value, sampai: filterSampai.value },
        });
        dataTransaksi.value = data.data;
    } catch (_) {}
}

async function muatReorder() {
    try {
        const { data } = await api.get('/laporan/riwayat-reorder');
        dataReorder.value = data.data;
    } catch (_) {}
}

watch(tab, (t) => {
    if (t === 'stok') muatStok();
    else if (t === 'transaksi') muatTransaksi();
    else muatReorder();
});

function formatRupiah(n) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
}

function formatTanggal(t) {
    if (!t) return '—';
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'short', timeStyle: 'short' }).format(new Date(t));
}

function cetakHalaman() {
    window.print();
}

onMounted(muatStok);
</script>

<style scoped>
.text-main {
    color: var(--clr-text-main);
}
</style>

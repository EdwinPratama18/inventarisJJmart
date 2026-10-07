<template>
    <div>
        <!-- Page Header -->
        <div class="page-header-row">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Monitoring stok gudang dan Reorder Point JJ Mart</p>
            </div>
            <div class="d-flex align-center gap-2" style="font-size:0.8rem; color:var(--clr-text-muted);">
                <Ikon nama="dashboard" :ukuran="14" />
                <span>Home</span>
                <span style="color:var(--clr-border-hover);">/</span>
                <span style="color:var(--clr-text-main); font-weight:500;">Dashboard</span>
            </div>
        </div>

        <!-- Stat Cards Grid -->
        <div class="info-boxes-grid">
            <!-- Data Barang -->
            <div class="info-box box-blue">
                <div class="info-box-body">
                    <div>
                        <div class="info-box-number">{{ ringkasan.total_barang }}</div>
                        <div class="info-box-title">Total Data Barang</div>
                    </div>
                    <div class="info-box-icon">
                        <Ikon nama="package" :ukuran="44" />
                    </div>
                </div>
                <RouterLink to="/barang" class="info-box-footer">
                    <span>Lihat Data Barang</span>
                    <Ikon nama="chevron-right" :ukuran="13" />
                </RouterLink>
            </div>

            <!-- Data Suplier -->
            <div class="info-box box-teal">
                <div class="info-box-body">
                    <div>
                        <div class="info-box-number">{{ ringkasan.total_pemasok || 0 }}</div>
                        <div class="info-box-title">Data Suplier</div>
                    </div>
                    <div class="info-box-icon">
                        <Ikon nama="truck" :ukuran="44" />
                    </div>
                </div>
                <RouterLink to="/pemasok" class="info-box-footer">
                    <span>Lihat Data Suplier</span>
                    <Ikon nama="chevron-right" :ukuran="13" />
                </RouterLink>
            </div>

            <!-- Barang Masuk -->
            <div class="info-box box-emerald">
                <div class="info-box-body">
                    <div>
                        <div class="info-box-number">{{ ringkasan.total_barang_masuk || 0 }}</div>
                        <div class="info-box-title">Total Barang Masuk</div>
                    </div>
                    <div class="info-box-icon">
                        <Ikon nama="masuk" :ukuran="44" />
                    </div>
                </div>
                <RouterLink to="/transaksi/masuk" class="info-box-footer">
                    <span>Lihat Barang Masuk</span>
                    <Ikon nama="chevron-right" :ukuran="13" />
                </RouterLink>
            </div>

            <!-- Barang Keluar -->
            <div class="info-box box-purple">
                <div class="info-box-body">
                    <div>
                        <div class="info-box-number">{{ ringkasan.total_barang_keluar || 0 }}</div>
                        <div class="info-box-title">Total Barang Keluar</div>
                    </div>
                    <div class="info-box-icon">
                        <Ikon nama="keluar" :ukuran="44" />
                    </div>
                </div>
                <RouterLink to="/transaksi/keluar" class="info-box-footer">
                    <span>Lihat Barang Keluar</span>
                    <Ikon nama="chevron-right" :ukuran="13" />
                </RouterLink>
            </div>

            <!-- ROP / Stok Kritis -->
            <div class="info-box box-rose">
                <div class="info-box-body">
                    <div>
                        <div class="info-box-number">{{ ringkasan.barang_kritis }}</div>
                        <div class="info-box-title">Perlu Reorder (ROP)</div>
                    </div>
                    <div class="info-box-icon">
                        <Ikon nama="kritis" :ukuran="44" />
                    </div>
                </div>
                <RouterLink to="/rop" class="info-box-footer">
                    <span>Lihat Monitoring ROP</span>
                    <Ikon nama="chevron-right" :ukuran="13" />
                </RouterLink>
            </div>

            <!-- Transaksi Hari Ini -->
            <div class="info-box box-amber">
                <div class="info-box-body">
                    <div>
                        <div class="info-box-number">{{ ringkasan.transaksi_hari_ini }}</div>
                        <div class="info-box-title">Transaksi Hari Ini</div>
                    </div>
                    <div class="info-box-icon">
                        <Ikon nama="chart" :ukuran="44" />
                    </div>
                </div>
                <RouterLink to="/laporan" class="info-box-footer">
                    <span>Lihat Laporan</span>
                    <Ikon nama="chevron-right" :ukuran="13" />
                </RouterLink>
            </div>

            <!-- Estimasi Nilai Stok -->
            <div class="info-box box-indigo">
                <div class="info-box-body">
                    <div>
                        <div class="info-box-number" style="font-size:1.35rem; padding-top:0.45rem; letter-spacing:-0.02em;">
                            {{ formatRupiah(ringkasan.total_nilai_stok) }}
                        </div>
                        <div class="info-box-title">Estimasi Nilai Stok</div>
                    </div>
                    <div class="info-box-icon">
                        <Ikon nama="dollar" :ukuran="44" />
                    </div>
                </div>
                <RouterLink to="/laporan" class="info-box-footer">
                    <span>Lihat Laporan</span>
                    <Ikon nama="chevron-right" :ukuran="13" />
                </RouterLink>
            </div>

            <!-- Pengguna -->
            <div class="info-box box-slate">
                <div class="info-box-body">
                    <div>
                        <div class="info-box-number">{{ ringkasan.total_pengguna || 2 }}</div>
                        <div class="info-box-title">Pengguna Aktif</div>
                    </div>
                    <div class="info-box-icon">
                        <Ikon nama="user" :ukuran="44" />
                    </div>
                </div>
                <div class="info-box-footer" style="cursor:default; pointer-events:none;">
                    <span>Terdaftar di Sistem</span>
                </div>
            </div>
        </div>

        <!-- Row 2: Session Info + ROP Warning -->
        <div class="dash-row-2">
            <!-- Informasi Sesi Login -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-center gap-2">
                        <Ikon nama="user" :ukuran="16" style="color:var(--clr-primary);" />
                        <span class="card-title">Informasi Sesi</span>
                    </div>
                    <span class="badge badge-aman">
                        <span class="dot"></span>
                        Online
                    </span>
                </div>
                <div class="card-body" style="padding:0;">
                    <table class="detail-table">
                        <tbody>
                            <tr>
                                <td class="detail-label">Nama Pengguna</td>
                                <td class="detail-val font-semibold">{{ tokoAuth.pengguna?.name || 'Administrator JJ Mart' }}</td>
                            </tr>
                            <tr>
                                <td class="detail-label">Email</td>
                                <td class="detail-val">{{ tokoAuth.pengguna?.email || 'admin@jjmart.id' }}</td>
                            </tr>
                            <tr>
                                <td class="detail-label">Hak Akses</td>
                                <td class="detail-val">
                                    <span class="badge badge-info" style="text-transform:uppercase; letter-spacing:0.04em;">
                                        {{ tokoAuth.pengguna?.peran || 'MANAJEMEN' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="detail-label">Status Server</td>
                                <td class="detail-val text-muted" style="font-size:0.8rem;">
                                    Laravel REST API &bull; Vue 3 SPA
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Peringatan ROP -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-center gap-2">
                        <Ikon nama="kritis" :ukuran="16" style="color:var(--clr-danger);" />
                        <span class="card-title">Peringatan Reorder Point</span>
                    </div>
                    <RouterLink to="/rop" class="btn btn-secondary btn-sm">
                        Lihat Semua
                    </RouterLink>
                </div>
                <div class="card-body" style="padding:0;">
                    <div v-if="muatKritis" class="empty-state-sm text-muted">
                        Memuat data ROP…
                    </div>
                    <div v-else-if="!barangKritis.length" class="empty-state-sm">
                        <Ikon nama="check" :ukuran="26" style="color:#10b981; margin:0 auto 0.4rem; display:block;" />
                        <p class="text-muted" style="font-size:0.835rem;">Semua stok aman di atas titik ROP</p>
                    </div>
                    <div v-else class="kritis-list">
                        <div class="kritis-item" v-for="b in barangKritis.slice(0, 5)" :key="b.id">
                            <div class="kritis-left">
                                <div class="kritis-nama">{{ b.nama }}</div>
                                <div class="kritis-sub">{{ b.kode_barang }} &bull; {{ b.pemasok?.nama || '—' }}</div>
                            </div>
                            <div class="kritis-right">
                                <BadgeStatusStok :status="b.status_stok" />
                                <div class="kritis-stok-info">Stok: <strong>{{ b.stok_saat_ini }}</strong> / ROP: {{ b.rop }}</div>
                            </div>
                        </div>
                        <div v-if="barangKritis.length > 5" class="kritis-more">
                            +{{ barangKritis.length - 5 }} barang lainnya —
                            <RouterLink to="/rop" style="color:var(--clr-primary); font-weight:500;">lihat semua</RouterLink>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Grafik Tren -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-center gap-2">
                    <Ikon nama="chart" :ukuran="16" style="color:var(--clr-primary);" />
                    <span class="card-title">Tren Pergerakan Stok</span>
                    <span class="text-muted" style="font-size:0.8rem; font-weight:400;">(Inbound vs Outbound)</span>
                </div>
                <select
                    class="form-select"
                    style="width:155px; font-size:0.8rem; padding:0.38rem 0.7rem;"
                    v-model="periodeChart"
                    @change="muatChart"
                >
                    <option value="7">7 Hari Terakhir</option>
                    <option value="30">30 Hari Terakhir</option>
                    <option value="90">90 Hari Terakhir</option>
                </select>
            </div>
            <div class="card-body">
                <GrafikTren :data="dataChart" v-if="dataChart.length" />
                <div v-else class="empty-state-chart text-muted">
                    <Ikon nama="chart" :ukuran="32" style="color:#cbd5e1; display:block; margin:0 auto 0.5rem;" />
                    <p>Belum ada data riwayat transaksi</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import GrafikTren from '../komponen/GrafikTren.vue';
import BadgeStatusStok from '../komponen/BadgeStatusStok.vue';
import Ikon from '../komponen/Ikon.vue';
import { gunakanTokoAutentikasi } from '../toko/autentikasi';
import api from '../layanan/api';

const tokoAuth = gunakanTokoAutentikasi();

const ringkasan = ref({
    total_barang: 0,
    total_pemasok: 0,
    total_pengguna: 2,
    total_nilai_stok: 0,
    barang_kritis: 0,
    barang_habis: 0,
    barang_peringatan: 0,
    transaksi_hari_ini: 0,
    total_barang_masuk: 0,
    total_barang_keluar: 0,
});
const barangKritis = ref([]);
const dataChart = ref([]);
const periodeChart = ref(30);
const muatKritis = ref(false);

async function muatRingkasan() {
    try {
        const { data } = await api.get('/dasbor/ringkasan');
        ringkasan.value = data.data;
    } catch (_) {}
}

async function muatBarangKritis() {
    muatKritis.value = true;
    try {
        const { data } = await api.get('/dasbor/barang-kritis');
        barangKritis.value = data.data;
    } catch (_) {} finally {
        muatKritis.value = false;
    }
}

async function muatChart() {
    try {
        const { data } = await api.get('/dasbor/trend-pemakaian', { params: { hari: periodeChart.value } });
        dataChart.value = data.data;
    } catch (_) {}
}

function formatRupiah(n) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(n || 0);
}

onMounted(async () => {
    await Promise.all([muatRingkasan(), muatBarangKritis(), muatChart()]);
});
</script>

<style scoped>
.page-header-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1.35rem;
    gap: 1rem;
}

.dash-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

/* ROP list */
.kritis-list {
    max-height: 290px;
    overflow-y: auto;
}

.kritis-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem 1.15rem;
    border-bottom: 1px solid var(--clr-border-subtle);
    gap: 1rem;
    transition: background 0.15s;
}

.kritis-item:hover { background: #f8fafc; }
.kritis-item:last-child { border-bottom: none; }

.kritis-left { flex: 1; min-width: 0; }
.kritis-nama {
    font-size: 0.845rem;
    font-weight: 600;
    color: var(--clr-text-main);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kritis-sub {
    font-size: 0.745rem;
    color: var(--clr-text-muted);
    margin-top: 0.1rem;
}
.kritis-right { text-align: right; flex-shrink: 0; }
.kritis-stok-info {
    font-size: 0.735rem;
    color: var(--clr-text-muted);
    margin-top: 0.2rem;
}
.kritis-more {
    padding: 0.65rem 1.15rem;
    font-size: 0.8rem;
    color: var(--clr-text-muted);
    background: #f8fafc;
}

/* Empty state */
.empty-state-sm {
    text-align: center;
    padding: 2rem 1rem;
}
.empty-state-chart {
    text-align: center;
    padding: 3rem 1rem;
    font-size: 0.875rem;
}
</style>

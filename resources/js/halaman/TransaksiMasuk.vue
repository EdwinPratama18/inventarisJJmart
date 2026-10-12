<template>
    <div class="stok-masuk-page">
        <!-- Page Header -->
        <div class="d-flex align-center justify-between mb-4 page-header">
            <div>
                <h1 class="page-title" style="margin-bottom:0.2rem;">
                    <Ikon
                        nama="masuk"
                        :ukuran="20"
                        style="color:var(--clr-primary);"
                    />
                    <span>Pencatatan Stok Masuk</span>
                </h1>

                <p class="page-subtitle">
                    Catat penerimaan barang dari pemasok atau restok gudang
                </p>
            </div>
        </div>

        <!-- Two-Column Layout -->
        <div class="masuk-layout">

            <!-- =========================================
                 KOLOM KIRI: FORM INPUT
                 ========================================= -->
            <div class="masuk-form-col">

                <div class="card form-card">

                    <div class="card-header">
                        <span class="card-title">
                            Form Stok Masuk
                        </span>
                    </div>

                    <div class="card-body">

                        <!-- Pilih Barang -->
                        <div class="form-group">
                            <label class="form-label">
                                Pilih Barang
                                <span style="color:var(--clr-danger);">*</span>
                            </label>

                            <CariBarang
                                v-model="form.barang_id"
                                :daftar-barang="daftarBarang"
                                placeholder="Ketik nama atau kode barang…"
                                :ada-galat="!!galat.barang_id"
                                @pilih="pilihBarang"
                            />

                            <div
                                class="form-error"
                                v-if="galat.barang_id"
                            >
                                {{ galat.barang_id[0] }}
                            </div>
                        </div>

                        <!-- Info stok barang terpilih -->
                        <div
                            class="info-stok-box"
                            v-if="barangDipilih"
                        >
                            <div class="d-flex align-center justify-between">

                                <div class="d-flex align-center gap-2">

                                    <span
                                        class="text-muted"
                                        style="font-size:0.8rem;"
                                    >
                                        Stok saat ini:
                                    </span>

                                    <span
                                        class="font-semibold"
                                        style="font-size:0.9rem;"
                                    >
                                        {{ barangDipilih.stok_saat_ini }}
                                        {{ barangDipilih.satuan }}
                                    </span>

                                </div>

                                <BadgeStatusStok
                                    :status="barangDipilih.status_stok"
                                />

                            </div>
                        </div>

                        <!-- Jumlah & Harga -->
                        <div class="form-row-2">

                            <div class="form-group">

                                <label class="form-label">
                                    Jumlah Masuk
                                    <span style="color:var(--clr-danger);">*</span>
                                </label>

                                <input
                                    type="number"
                                    class="form-control"
                                    v-model.number="form.jumlah"
                                    min="1"
                                    placeholder="0"
                                />

                                <div
                                    class="form-error"
                                    v-if="galat.jumlah"
                                >
                                    {{ galat.jumlah[0] }}
                                </div>

                            </div>

                            <div class="form-group">

                                <label class="form-label">
                                    Harga Satuan (Rp)
                                </label>

                                <input
                                    type="number"
                                    class="form-control"
                                    v-model.number="form.harga_satuan"
                                    min="0"
                                    step="100"
                                    placeholder="0"
                                />

                            </div>

                        </div>

                        <!-- Referensi & Tanggal -->
                        <div class="form-row-2">

                            <div class="form-group">

                                <label class="form-label">
                                    No. Referensi / PO
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="form.referensi"
                                    placeholder="Contoh: PO-2026-001"
                                />

                            </div>

                            <div class="form-group">

                                <label class="form-label">
                                    Tanggal Transaksi
                                </label>

                                <input
                                    type="datetime-local"
                                    class="form-control"
                                    v-model="form.tanggal"
                                />

                            </div>

                        </div>

                        <!-- Catatan -->
                        <div class="form-group">

                            <label class="form-label">
                                Catatan
                            </label>

                            <textarea
                                class="form-control"
                                rows="2"
                                v-model="form.catatan"
                                placeholder="Keterangan tambahan…"
                            ></textarea>

                        </div>

                        <!-- Error Global -->
                        <div
                            class="form-error"
                            v-if="galatGlobal"
                            style="margin-bottom:0.75rem;"
                        >
                            {{ galatGlobal }}
                        </div>

                        <!-- Aksi -->
                        <div class="d-flex gap-2">

                            <button
                                class="btn btn-secondary"
                                @click="reset"
                                style="flex-shrink:0;"
                            >
                                Reset
                            </button>

                            <button
                                class="btn btn-primary"
                                @click="simpan"
                                :disabled="menyimpan"
                                style="flex:1;"
                            >

                                <Ikon
                                    nama="masuk"
                                    :ukuran="15"
                                />

                                <span>
                                    {{
                                        menyimpan
                                            ? 'Menyimpan…'
                                            : 'Catat Stok Masuk'
                                    }}
                                </span>

                            </button>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =========================================
                 KOLOM KANAN: RIWAYAT
                 ========================================= -->
            <div class="masuk-riwayat-col">

                <div class="card masuk-riwayat-card">

                    <!-- Header -->
                    <div class="card-header riwayat-header">

                        <span class="card-title">
                            Riwayat Stok Masuk
                        </span>

                        <span class="badge badge-info">
                            {{ totalRiwayat }} transaksi
                        </span>

                    </div>


                    <!-- Table -->
                    <div class="table-container">

                        <table class="app-table">

                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>No. Ref</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>

                            <tbody>

                                <!-- Loading -->
                                <tr v-if="muatRiwayatLoading">

                                    <td
                                        colspan="4"
                                        class="td-empty text-muted"
                                    >
                                        Memuat riwayat…
                                    </td>

                                </tr>


                                <!-- Empty -->
                                <tr v-else-if="!riwayat.length">

                                    <td
                                        colspan="4"
                                        class="td-empty text-muted"
                                    >
                                        Belum ada transaksi masuk.
                                    </td>

                                </tr>


                                <!-- Data -->
                                <tr
                                    v-for="t in riwayat"
                                    :key="t.id"
                                >

                                    <td>

                                        <div
                                            class="font-semibold"
                                            style="font-size:0.845rem;"
                                        >
                                            {{ t.barang?.nama }}
                                        </div>

                                        <div
                                            class="text-muted"
                                            style="font-size:0.74rem;"
                                        >
                                            {{ t.barang?.kode_barang }}
                                        </div>

                                    </td>


                                    <td>

                                        <span class="badge badge-aman">
                                            +{{ t.jumlah }}
                                            {{ t.barang?.satuan }}
                                        </span>

                                    </td>


                                    <td>

                                        <span
                                            class="font-mono text-muted"
                                            style="font-size:0.78rem;"
                                        >
                                            {{ t.referensi || '—' }}
                                        </span>

                                    </td>


                                    <td
                                        class="text-muted"
                                        style="
                                            font-size:0.78rem;
                                            white-space:nowrap;
                                        "
                                    >
                                        {{ formatTanggal(t.tanggal) }}
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>


                    <!-- Pagination -->
                    <div
                        class="paginasi"
                        v-if="totalHalaman > 1"
                    >

                        <button
                            class="paginasi-btn"
                            :disabled="halamanAktif === 1"
                            @click="gantiHalaman(halamanAktif - 1)"
                        >
                            &lsaquo;
                        </button>


                        <button
                            v-for="h in daftarHalaman"
                            :key="h"
                            class="paginasi-btn"
                            :class="{
                                aktif: h === halamanAktif
                            }"
                            @click="gantiHalaman(h)"
                        >
                            {{ h }}
                        </button>


                        <button
                            class="paginasi-btn"
                            :disabled="halamanAktif === totalHalaman"
                            @click="gantiHalaman(halamanAktif + 1)"
                        >
                            &rsaquo;
                        </button>


                        <span class="paginasi-info">
                            {{ halamanAktif }} / {{ totalHalaman }}
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- Toast -->
        <div class="toast-container">

            <Transition name="fade">

                <div
                    v-if="toast.tampil"
                    class="toast"
                    :class="`toast-${toast.jenis}`"
                >

                    <Ikon
                        :nama="
                            toast.jenis === 'success'
                                ? 'check'
                                : 'warning'
                        "
                        :ukuran="17"
                    />

                    <span>
                        {{ toast.pesan }}
                    </span>

                </div>

            </Transition>

        </div>

    </div>
</template>


<script setup>
import {
    ref,
    computed,
    onMounted
} from 'vue';

import {
    useRoute
} from 'vue-router';

import BadgeStatusStok
    from '../komponen/BadgeStatusStok.vue';

import CariBarang
    from '../komponen/CariBarang.vue';

import Ikon
    from '../komponen/Ikon.vue';

import api
    from '../layanan/api';

import { gunakanTokoNotifikasi }
    from '../toko/notifikasi';


/* =========================================
   ROUTE & STORE
   ========================================= */

const route = useRoute();
const notifStore = gunakanTokoNotifikasi();


/* =========================================
   STATE
   ========================================= */

const daftarBarang = ref([]);

const barangDipilih = ref(null);

const riwayat = ref([]);

const muatRiwayatLoading = ref(false);

const menyimpan = ref(false);

const galat = ref({});

const galatGlobal = ref('');

const toast = ref({
    tampil: false,
    pesan: '',
    jenis: 'success'
});


/* =========================================
   PAGINATION
   ========================================= */

const halamanAktif = ref(1);

const perHalaman = 10;

const totalRiwayat = ref(0);


const totalHalaman = computed(() => {
    return Math.ceil(
        totalRiwayat.value / perHalaman
    );
});


const daftarHalaman = computed(() => {

    const total = totalHalaman.value;

    const aktif = halamanAktif.value;

    const pages = [];

    const range = 2;

    for (
        let i = Math.max(1, aktif - range);
        i <= Math.min(total, aktif + range);
        i++
    ) {
        pages.push(i);
    }

    return pages;
});


/* =========================================
   DEFAULT FORM
   ========================================= */

const defaultForm = () => ({
    barang_id: '',
    jenis: 'masuk',
    jumlah: 1,
    harga_satuan: 0,
    referensi: '',
    catatan: '',
    tanggal: new Date()
        .toISOString()
        .slice(0, 16),
});


const form = ref(defaultForm());


/* =========================================
   PILIH BARANG
   ========================================= */

function pilihBarang(b) {

    barangDipilih.value = b ?? null;

    form.value.barang_id =
        b ? b.id : '';

}


/* =========================================
   SIMPAN TRANSAKSI
   ========================================= */

async function simpan() {

    galat.value = {};

    galatGlobal.value = '';

    menyimpan.value = true;

    try {

        await api.post(
            '/transaksi',
            form.value
        );

        tampilkanToast(
            'Stok masuk berhasil dicatat.'
        );

        reset();

        halamanAktif.value = 1;

        muatRiwayat();

        await muatBarang();
        notifStore.muatNotifikasi();

    } catch (err) {

        if (
            err.response?.data?.errors
        ) {

            galat.value =
                err.response.data.errors;

        } else {

            galatGlobal.value =
                err.response?.data?.pesan ??
                'Gagal menyimpan transaksi.';

        }

    } finally {

        menyimpan.value = false;

    }

}


/* =========================================
   RESET
   ========================================= */

function reset() {

    form.value =
        defaultForm();

    barangDipilih.value =
        null;

    galat.value = {};

    galatGlobal.value = '';

}


/* =========================================
   MUAT BARANG
   ========================================= */

async function muatBarang() {

    try {

        const {
            data
        } = await api.get(
            '/barang',
            {
                params: {
                    per_halaman: 500
                }
            }
        );

        daftarBarang.value =
            data.data;

    } catch (_) {

        // Abaikan error loading barang

    }

}


/* =========================================
   MUAT RIWAYAT
   ========================================= */

async function muatRiwayat() {

    muatRiwayatLoading.value =
        true;

    try {

        const {
            data
        } = await api.get(
            '/transaksi',
            {
                params: {
                    jenis: 'masuk',
                    per_halaman:
                        perHalaman,
                    halaman:
                        halamanAktif.value
                }
            }
        );

        riwayat.value =
            data.data;

        totalRiwayat.value =
            data.meta?.total ??
            data.data.length;

    } catch (_) {

        // Abaikan error loading riwayat

    } finally {

        muatRiwayatLoading.value =
            false;

    }

}


/* =========================================
   GANTI HALAMAN
   ========================================= */

async function gantiHalaman(h) {

    if (
        h < 1 ||
        h > totalHalaman.value
    ) {
        return;
    }

    halamanAktif.value = h;

    await muatRiwayat();

}


/* =========================================
   FORMAT TANGGAL
   ========================================= */

function formatTanggal(t) {

    if (!t) {
        return '—';
    }

    return new Intl.DateTimeFormat(
        'id-ID',
        {
            dateStyle: 'short',
            timeStyle: 'short'
        }
    ).format(
        new Date(t)
    );

}


/* =========================================
   TOAST
   ========================================= */

function tampilkanToast(
    pesan,
    jenis = 'success'
) {

    toast.value = {
        tampil: true,
        pesan,
        jenis
    };

    setTimeout(() => {

        toast.value.tampil =
            false;

    }, 3500);

}


/* =========================================
   ON MOUNTED
   ========================================= */

onMounted(async () => {

    await muatBarang();

    muatRiwayat();


    if (route.query.barang_id) {

        const id =
            Number(
                route.query.barang_id
            );

        form.value.barang_id =
            id;

        barangDipilih.value =
            daftarBarang.value.find(
                b => b.id === id
            ) ?? null;

    }

});

</script>


<style scoped>

/* =====================================================
   PAGE
   ===================================================== */

.stok-masuk-page {
    width: 100%;
    height: 100%;
    min-height: 0;

    display: flex;
    flex-direction: column;

    overflow: hidden;
}


/* =====================================================
   HEADER
   ===================================================== */

.page-header {
    flex: 0 0 auto;
}


/* =====================================================
   TWO COLUMN LAYOUT
   ===================================================== */

.masuk-layout {

    /*
     * Grid dibuat menjadi satu baris penuh.
     * Kedua kolom otomatis memiliki tinggi yang sama.
     */
    display: grid;

    grid-template-columns:
        420px
        minmax(0, 1fr);

    grid-template-rows:
        minmax(0, 1fr);

    gap: 1.25rem;

    /*
     * Area layout mengambil sisa tinggi layar.
     */
    height:
        calc(
            100vh
            - 58px
            - 3.5rem
            - 68px
        );

    min-height: 0;

    /*
     * Sangat penting:
     * membuat kedua grid item stretch
     * dengan tinggi yang sama.
     */
    align-items: stretch;

    overflow: hidden;
}


/* =====================================================
   KOLOM FORM
   ===================================================== */

.masuk-form-col {

    min-width: 0;
    min-height: 0;

    /*
     * Paksa kolom kiri mengikuti tinggi grid.
     */
    height: 100%;

    display: flex;
    flex-direction: column;

    overflow: hidden;
}


/* =====================================================
   FORM CARD
   ===================================================== */

.form-card {

    width: 100%;

    /*
     * Paksa card kiri sama tinggi dengan
     * card riwayat kanan.
     */
    height: 100%;
    min-height: 0;

    flex: 1 1 auto;

    display: flex;
    flex-direction: column;

    overflow: hidden;
}


/*
 * Card header tidak ikut mengecil.
 */
.form-card .card-header {

    flex: 0 0 auto;

}


/*
 * Body form tetap berada di dalam card.
 */
.form-card .card-body {

    min-height: 0;

    flex: 1 1 auto;

    overflow: hidden;

}


/* =====================================================
   FORM ROW
   ===================================================== */

.form-row-2 {

    display: grid;

    grid-template-columns:
        1fr
        1fr;

    gap: 0.85rem;

}


/* =====================================================
   INFO STOK
   ===================================================== */

.info-stok-box {

    background:
        #f8fafc;

    border:
        1px solid
        var(--clr-border);

    border-radius:
        var(--radius-md);

    padding:
        0.7rem 1rem;

    margin-bottom:
        1rem;

}


/* =====================================================
   KOLOM RIWAYAT
   ===================================================== */

.masuk-riwayat-col {

    min-width: 0;
    min-height: 0;

    /*
     * Tinggi kolom kanan sama dengan kiri.
     */
    height: 100%;

    display: flex;
    flex-direction: column;

    overflow: hidden;
}


/* =====================================================
   CARD RIWAYAT
   ===================================================== */

.masuk-riwayat-card {

    width: 100%;

    /*
     * Tinggi penuh kolom kanan.
     */
    height: 100%;
    min-height: 0;

    flex: 1 1 auto;

    display: flex;
    flex-direction: column;

    overflow: hidden;
}


/* =====================================================
   HEADER RIWAYAT
   ===================================================== */

.riwayat-header {

    flex: 0 0 auto;

}


/* =====================================================
   TABLE CONTAINER
   ===================================================== */

.table-container {

    /*
     * Mengambil seluruh ruang di antara
     * header dan pagination.
     */
    flex: 1 1 auto;

    min-height: 0;

    width: 100%;

    overflow: hidden;

}


/* =====================================================
   TABLE
   ===================================================== */

.table-container .app-table {

    width: 100%;

    table-layout: fixed;

}


/* =====================================================
   LEBAR KOLOM
   ===================================================== */

.table-container .app-table th:nth-child(1),
.table-container .app-table td:nth-child(1) {

    width: 35%;

}


.table-container .app-table th:nth-child(2),
.table-container .app-table td:nth-child(2) {

    width: 20%;

}


.table-container .app-table th:nth-child(3),
.table-container .app-table td:nth-child(3) {

    width: 20%;

}


.table-container .app-table th:nth-child(4),
.table-container .app-table td:nth-child(4) {

    width: 25%;

}


/* =====================================================
   EMPTY STATE
   ===================================================== */

.td-empty {

    text-align: center;

    padding:
        2.5rem 1rem;

    font-size:
        0.845rem;

}


/* =====================================================
   PAGINATION
   ===================================================== */

.paginasi {

    display: flex;

    align-items: center;

    gap: 0.25rem;

    padding:
        0.75rem 1rem;

    border-top:
        1px solid
        var(--clr-border-subtle);

    background:
        #f8fafc;

    /*
     * Pagination tetap di bagian bawah.
     */
    flex: 0 0 auto;

}


.paginasi-btn {

    min-width: 32px;

    height: 30px;

    padding:
        0 0.5rem;

    background:
        #ffffff;

    border:
        1px solid
        var(--clr-border);

    border-radius:
        var(--radius-sm);

    font-family:
        inherit;

    font-size:
        0.8rem;

    color:
        var(--clr-text-body);

    cursor: pointer;

    transition:
        all 0.15s;

    display: flex;

    align-items: center;

    justify-content: center;

}


.paginasi-btn:hover:not(:disabled) {

    background:
        var(--clr-primary-light);

    border-color:
        var(--clr-primary-border);

    color:
        var(--clr-primary);

}


.paginasi-btn.aktif {

    background:
        var(--clr-primary);

    border-color:
        var(--clr-primary);

    color:
        #ffffff;

    font-weight:
        600;

}


.paginasi-btn:disabled {

    opacity:
        0.4;

    cursor:
        not-allowed;

}


.paginasi-info {

    margin-left:
        auto;

    font-size:
        0.78rem;

    color:
        var(--clr-text-muted);

}


/* =====================================================
   TOAST
   ===================================================== */

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

    padding:
        0.75rem 1.1rem;

    border-radius:
        var(--radius-md);

    font-size:
        0.855rem;

    font-weight:
        500;

    box-shadow:
        var(--shadow-lg);

    min-width:
        240px;

}


.toast-success {

    background:
        var(--clr-success);

    color:
        #ffffff;

}


.toast-error {

    background:
        var(--clr-danger);

    color:
        #ffffff;

}


/* =====================================================
   TRANSITION TOAST
   ===================================================== */

.fade-enter-active,
.fade-leave-active {

    transition:
        all 0.25s ease;

}


.fade-enter-from,
.fade-leave-to {

    opacity: 0;

    transform:
        translateY(8px);

}


/* =====================================================
   RESPONSIVE
   ===================================================== */

@media (max-width: 1000px) {

    .masuk-layout {

        grid-template-columns:
            360px
            minmax(0, 1fr);

    }

}


@media (max-width: 800px) {

    .stok-masuk-page {

        height: auto;

        min-height: 100%;

        overflow: visible;

    }


    .masuk-layout {

        grid-template-columns:
            1fr;

        grid-template-rows:
            auto;

        height: auto;

        overflow: visible;

    }


    .masuk-form-col {

        height: auto;

        overflow: visible;

    }


    .form-card {

        height: auto;

        min-height: 0;

    }


    .form-card .card-body {

        overflow: visible;

    }


    .masuk-riwayat-col {

        height: auto;

        min-height: 500px;

        overflow: visible;

    }


    .masuk-riwayat-card {

        min-height: 500px;

        height: 500px;

    }

}

</style>
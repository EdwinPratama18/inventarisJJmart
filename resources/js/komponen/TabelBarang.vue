<template>
    <div>
        <!-- Search & Filter Toolbar -->
        <div class="toolbar d-flex align-center gap-3 mb-4">
            <div class="search-input-wrapper">
                <Ikon nama="search" :ukuran="16" class="search-icon" />
                <input
                    type="text"
                    class="form-control with-icon"
                    placeholder="Cari kode atau nama barang…"
                    v-model="cariQuery"
                    @input="onCari"
                />
            </div>
            <select class="form-select" style="width:180px;" v-model="filterKategori" @change="onFilterKategori">
                <option value="">Semua Kategori</option>
                <option v-for="k in kategoriList" :key="k.id" :value="k.id">{{ k.nama }}</option>
            </select>
            <select class="form-select" style="width:190px;" v-model="filterStatus" @change="onFilterStatus">
                <option value="">Semua Status Stok</option>
                <option value="PERLU_PEMESANAN">Perlu Reorder</option>
                <option value="PERINGATAN">Mendekati ROP</option>
                <option value="AMAN">Stok Aman</option>
            </select>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th style="text-align:right;">Stok</th>
                            <th style="text-align:right;">ROP</th>
                            <th>Status</th>
                            <th>Pemasok</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="memuat">
                            <td colspan="8" style="text-align:center; padding:3rem;">
                                <div class="text-muted">Memuat data barang…</div>
                            </td>
                        </tr>
                        <tr v-else-if="!barang.length">
                            <td colspan="8" style="text-align:center; padding:3rem;">
                                <div class="text-muted" style="margin-bottom:0.5rem;">
                                    <Ikon nama="package" :ukuran="32" style="color:#94a3b8; margin:0 auto 0.5rem; display:block;" />
                                    Tidak ada data barang ditemukan
                                </div>
                            </td>
                        </tr>
                        <tr v-for="b in barang" :key="b.id" v-else>
                            <td><span class="kode-tag">{{ b.kode_barang }}</span></td>
                            <td>
                                <div class="nama-barang">{{ b.nama }}</div>
                                <div class="lokasi-rak" v-if="b.lokasi_rak">Rak: {{ b.lokasi_rak }}</div>
                            </td>
                            <td>
                                <span class="badge badge-info" v-if="b.kategori">{{ b.kategori.nama }}</span>
                                <span class="text-muted" v-else>—</span>
                            </td>
                            <td style="text-align:right;">
                                <span class="stok-angka">{{ b.stok_saat_ini }}</span>
                                <span class="stok-satuan"> {{ b.satuan }}</span>
                            </td>
                            <td style="text-align:right;">
                                <span class="font-medium">{{ b.rop }}</span>
                                <span class="stok-satuan"> {{ b.satuan }}</span>
                            </td>
                            <td>
                                <BadgeStatusStok :status="b.status_stok" />
                            </td>
                            <td>
                                <span v-if="b.pemasok" class="text-secondary">{{ b.pemasok.nama }}</span>
                                <span class="text-muted" v-else>—</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2" style="justify-content:flex-end;">
                                    <button
                                        class="btn btn-secondary btn-sm"
                                        title="Hitung Ulang ROP"
                                        @click="$emit('hitung-rop', b)"
                                    >
                                        <Ikon nama="refresh" :ukuran="14" />
                                    </button>
                                    <button
                                        class="btn btn-secondary btn-sm"
                                        title="Edit Barang"
                                        @click="$emit('edit', b)"
                                    >
                                        <Ikon nama="pencil" :ukuran="14" />
                                    </button>
                                    <button
                                        class="btn btn-secondary btn-sm btn-delete"
                                        title="Hapus Barang"
                                        @click="$emit('hapus', b)"
                                    >
                                        <Ikon nama="trash" :ukuran="14" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination & Metadata -->
        <div class="d-flex align-center justify-between" style="margin-top:1rem; padding:0 0.25rem;">
            <div class="text-muted" style="font-size:0.825rem;">
                Menampilkan {{ barang.length }} dari {{ meta.total }} barang
            </div>

            <div class="pagination" v-if="meta.total_halaman > 1">
                <button
                    class="btn btn-secondary btn-sm"
                    :disabled="meta.halaman_saat_ini === 1"
                    @click="gantiHalaman(meta.halaman_saat_ini - 1)"
                >
                    Sebelumnya
                </button>
                <div class="page-numbers d-flex gap-1">
                    <button
                        v-for="h in halamanVisible"
                        :key="h"
                        class="btn btn-sm"
                        :class="h === meta.halaman_saat_ini ? 'btn-primary' : 'btn-secondary'"
                        @click="gantiHalaman(h)"
                    >
                        {{ h }}
                    </button>
                </div>
                <button
                    class="btn btn-secondary btn-sm"
                    :disabled="meta.halaman_saat_ini === meta.total_halaman"
                    @click="gantiHalaman(meta.halaman_saat_ini + 1)"
                >
                    Selanjutnya
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import BadgeStatusStok from './BadgeStatusStok.vue';
import Ikon from './Ikon.vue';

const props = defineProps({
    barang: { type: Array, default: () => [] },
    meta: { type: Object, default: () => ({ total: 0, per_halaman: 15, halaman_saat_ini: 1, total_halaman: 1 }) },
    kategoriList: { type: Array, default: () => [] },
    memuat: { type: Boolean, default: false },
});

const emit = defineEmits(['edit', 'hapus', 'hitung-rop', 'filter-ubah', 'halaman-ubah']);

const cariQuery = ref('');
const filterKategori = ref('');
const filterStatus = ref('');
let debounceTimer;

function onCari() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => emitFilter(), 350);
}
function onFilterKategori() { emitFilter(); }
function onFilterStatus() { emitFilter(); }

function emitFilter() {
    emit('filter-ubah', {
        cari: cariQuery.value,
        kategori_id: filterKategori.value,
        status: filterStatus.value,
    });
}

function gantiHalaman(h) {
    emit('halaman-ubah', h);
}

const halamanVisible = computed(() => {
    const total = props.meta.total_halaman;
    const cur = props.meta.halaman_saat_ini;
    const pages = [];
    for (let i = Math.max(1, cur - 2); i <= Math.min(total, cur + 2); i++) {
        pages.push(i);
    }
    return pages;
});
</script>

<style scoped>
.search-input-wrapper {
    position: relative;
    flex: 1;
    max-width: 360px;
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
.nama-barang {
    font-weight: 600;
    color: var(--clr-text-main);
}
.lokasi-rak {
    font-size: 0.725rem;
    color: var(--clr-text-muted);
    margin-top: 0.15rem;
}
.stok-angka {
    font-weight: 600;
    color: var(--clr-text-main);
}
.stok-satuan {
    font-size: 0.75rem;
    color: var(--clr-text-muted);
}
.btn-delete:hover {
    color: var(--clr-danger);
    border-color: var(--clr-danger-border);
    background-color: var(--clr-danger-bg);
}
.pagination {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
</style>

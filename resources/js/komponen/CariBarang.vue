<template>
    <div class="cari-barang-wrapper" ref="wrapperRef">
        <!-- Input pencarian -->
        <div class="cari-input-box" :class="{ 'is-open': terbuka, 'has-value': barangDipilihInternal, 'is-error': adaGalat }">
            <!-- Jika sudah dipilih: tampil chip barang terpilih -->
            <div v-if="barangDipilihInternal" class="chip-terpilih">
                <div class="chip-info">
                    <span class="chip-kode">{{ barangDipilihInternal.kode_barang }}</span>
                    <span class="chip-nama">{{ barangDipilihInternal.nama }}</span>
                </div>
                <button type="button" class="chip-hapus" @click="hapusPilihan" title="Hapus pilihan">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <!-- Input teks pencarian -->
            <template v-else>
                <svg class="cari-ikon-search" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input
                    ref="inputRef"
                    type="text"
                    class="cari-input"
                    :placeholder="placeholder"
                    v-model="query"
                    @input="onInput"
                    @focus="onFocus"
                    @keydown.down.prevent="navigasiTurun"
                    @keydown.up.prevent="navigasiNaik"
                    @keydown.enter.prevent="pilihFokus"
                    @keydown.escape="tutup"
                    autocomplete="off"
                />
                <span v-if="memuat" class="cari-loading">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="spin">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    </svg>
                </span>
            </template>
        </div>

        <!-- Dropdown hasil -->
        <Transition name="dropdown-cari">
            <div v-if="terbuka && !barangDipilihInternal" class="cari-dropdown">
                <!-- Tidak ada hasil -->
                <div v-if="!memuat && hasilFilter.length === 0" class="cari-empty">
                    <span v-if="query.length >= 1">Tidak ada barang cocok untuk "<strong>{{ query }}</strong>"</span>
                    <span v-else>Ketik nama atau kode barang untuk mencari…</span>
                </div>

                <!-- Loading skeleton -->
                <div v-else-if="memuat" class="cari-empty text-muted">Mencari barang…</div>

                <!-- Daftar hasil -->
                <template v-else>
                    <div
                        v-for="(b, idx) in hasilFilter"
                        :key="b.id"
                        class="cari-item"
                        :class="{ 'is-fokus': idx === idxFokus }"
                        @mousedown.prevent="pilihBarang(b)"
                        @mouseover="idxFokus = idx"
                    >
                        <div class="cari-item-kiri">
                            <span class="cari-item-kode">{{ b.kode_barang }}</span>
                            <span class="cari-item-nama" v-html="sorot(b.nama)"></span>
                        </div>
                        <div class="cari-item-kanan">
                            <span class="cari-item-stok" :class="stokClass(b)">
                                {{ b.stok_saat_ini }} {{ b.satuan }}
                            </span>
                            <span class="cari-item-label-stok">stok</span>
                        </div>
                    </div>

                    <div v-if="hasilFilter.length === maxTampil && daftarBarang.length > maxTampil" class="cari-info-lebih">
                        Menampilkan {{ maxTampil }} dari {{ daftarBarang.length }} — ketik lebih spesifik
                    </div>
                </template>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: { type: [Number, String], default: '' },
    daftarBarang: { type: Array, default: () => [] },
    placeholder: { type: String, default: 'Ketik nama atau kode barang…' },
    adaGalat: { type: Boolean, default: false },
    maxTampil: { type: Number, default: 8 },
});

const emit = defineEmits(['update:modelValue', 'pilih']);

const query = ref('');
const terbuka = ref(false);
const memuat = ref(false);
const idxFokus = ref(-1);
const wrapperRef = ref(null);
const inputRef = ref(null);
const barangDipilihInternal = ref(null);

// Sinkronisasi jika modelValue berubah dari luar (misalnya dari query URL)
watch(() => props.modelValue, (val) => {
    if (!val) {
        barangDipilihInternal.value = null;
        query.value = '';
        return;
    }
    if (barangDipilihInternal.value?.id !== val) {
        const found = props.daftarBarang.find(b => b.id === val);
        if (found) barangDipilihInternal.value = found;
    }
}, { immediate: true });

watch(() => props.daftarBarang, (list) => {
    if (props.modelValue && !barangDipilihInternal.value) {
        const found = list.find(b => b.id === props.modelValue);
        if (found) barangDipilihInternal.value = found;
    }
});

// Filter hasil berdasarkan query
const hasilFilter = computed(() => {
    const q = query.value.toLowerCase().trim();
    if (!q) return props.daftarBarang.slice(0, props.maxTampil);
    return props.daftarBarang
        .filter(b =>
            b.nama.toLowerCase().includes(q) ||
            b.kode_barang.toLowerCase().includes(q)
        )
        .slice(0, props.maxTampil);
});

function onInput() {
    terbuka.value = true;
    idxFokus.value = -1;
}

function onFocus() {
    terbuka.value = true;
}

function tutup() {
    terbuka.value = false;
    idxFokus.value = -1;
}

function pilihBarang(b) {
    barangDipilihInternal.value = b;
    query.value = '';
    terbuka.value = false;
    idxFokus.value = -1;
    emit('update:modelValue', b.id);
    emit('pilih', b);
}

function hapusPilihan() {
    barangDipilihInternal.value = null;
    query.value = '';
    emit('update:modelValue', '');
    emit('pilih', null);
    // Fokus kembali ke input setelah hapus
    setTimeout(() => inputRef.value?.focus(), 50);
}

function navigasiTurun() {
    if (!terbuka.value) terbuka.value = true;
    if (idxFokus.value < hasilFilter.value.length - 1) idxFokus.value++;
}

function navigasiNaik() {
    if (idxFokus.value > 0) idxFokus.value--;
}

function pilihFokus() {
    if (idxFokus.value >= 0 && hasilFilter.value[idxFokus.value]) {
        pilihBarang(hasilFilter.value[idxFokus.value]);
    }
}

// Sorot teks yang cocok dengan query
function sorot(teks) {
    const q = query.value.trim();
    if (!q) return teks;
    const regex = new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return teks.replace(regex, '<mark>$1</mark>');
}

function stokClass(b) {
    if (b.stok_saat_ini === 0) return 'stok-habis';
    if (b.stok_saat_ini <= (b.rop || 0)) return 'stok-kritis';
    return 'stok-aman';
}

// Tutup dropdown ketika klik di luar
function handleClickLuar(e) {
    if (wrapperRef.value && !wrapperRef.value.contains(e.target)) {
        tutup();
    }
}

onMounted(() => document.addEventListener('mousedown', handleClickLuar));
onUnmounted(() => document.removeEventListener('mousedown', handleClickLuar));
</script>

<style scoped>
.cari-barang-wrapper {
    position: relative;
    width: 100%;
}

/* Input box utama */
.cari-input-box {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    width: 100%;
    background: #ffffff;
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-md);
    padding: 0 0.85rem;
    min-height: 40px;
    box-shadow: var(--shadow-xs);
    transition: border-color 0.15s, box-shadow 0.15s;
    cursor: text;
}

.cari-input-box.is-open {
    border-color: var(--clr-primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
}

.cari-input-box.is-error {
    border-color: var(--clr-danger);
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.10);
}

.cari-ikon-search {
    color: var(--clr-text-light);
    flex-shrink: 0;
}

.cari-input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-family: inherit;
    font-size: 0.875rem;
    color: var(--clr-text-main);
    padding: 0.55rem 0;
}

.cari-input::placeholder {
    color: var(--clr-text-light);
}

.cari-loading {
    color: var(--clr-text-light);
    display: flex;
    align-items: center;
}

.spin {
    animation: spin 0.9s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Chip barang terpilih */
.chip-terpilih {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 0.4rem 0;
    gap: 0.5rem;
}

.chip-info {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    min-width: 0;
}

.chip-kode {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--clr-primary);
    background: var(--clr-primary-light);
    border: 1px solid var(--clr-primary-border);
    padding: 0.1rem 0.45rem;
    border-radius: 4px;
    white-space: nowrap;
    flex-shrink: 0;
}

.chip-nama {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--clr-text-main);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chip-hapus {
    background: transparent;
    border: none;
    cursor: pointer;
    color: var(--clr-text-muted);
    display: flex;
    align-items: center;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.15s;
    flex-shrink: 0;
}

.chip-hapus:hover {
    background: var(--clr-danger-bg);
    color: var(--clr-danger);
}

/* Dropdown */
.cari-dropdown {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    z-index: 50;
    overflow: hidden;
    max-height: 320px;
    overflow-y: auto;
}

.cari-empty {
    padding: 1rem 1rem;
    font-size: 0.835rem;
    color: var(--clr-text-muted);
    text-align: center;
}

.cari-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.65rem 1rem;
    cursor: pointer;
    transition: background 0.1s;
    gap: 0.75rem;
    border-bottom: 1px solid var(--clr-border-subtle);
}

.cari-item:last-child { border-bottom: none; }

.cari-item:hover,
.cari-item.is-fokus {
    background-color: var(--clr-primary-light);
}

.cari-item-kiri {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    min-width: 0;
    flex: 1;
}

.cari-item-kode {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--clr-text-muted);
    background: #f1f5f9;
    padding: 0.1rem 0.4rem;
    border-radius: 4px;
    white-space: nowrap;
    flex-shrink: 0;
}

.cari-item.is-fokus .cari-item-kode {
    background: var(--clr-primary-border);
    color: var(--clr-primary-dark);
}

.cari-item-nama {
    font-size: 0.855rem;
    font-weight: 500;
    color: var(--clr-text-body);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cari-item-nama :deep(mark) {
    background: #fef08a;
    color: #713f12;
    border-radius: 2px;
    padding: 0 1px;
}

.cari-item-kanan {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    flex-shrink: 0;
}

.cari-item-stok {
    font-size: 0.815rem;
    font-weight: 700;
}

.cari-item-label-stok {
    font-size: 0.68rem;
    color: var(--clr-text-light);
}

.stok-aman  { color: var(--clr-success); }
.stok-kritis { color: var(--clr-warning); }
.stok-habis  { color: var(--clr-danger); }

.cari-info-lebih {
    padding: 0.55rem 1rem;
    font-size: 0.78rem;
    color: var(--clr-text-muted);
    background: #f8fafc;
    border-top: 1px solid var(--clr-border-subtle);
    text-align: center;
}

/* Animasi dropdown */
.dropdown-cari-enter-active,
.dropdown-cari-leave-active {
    transition: opacity 0.15s ease, transform 0.15s ease;
}
.dropdown-cari-enter-from,
.dropdown-cari-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}
</style>

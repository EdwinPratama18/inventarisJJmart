<template>
    <Teleport to="body">
        <div class="modal-overlay" @click.self="$emit('tutup')">
            <div class="modal-box">
                <div class="modal-header">
                    <h3 class="modal-title">{{ barang ? 'Edit Barang' : 'Tambah Barang Baru' }}</h3>
                    <button class="modal-close" @click="$emit('tutup')">
                        <Ikon nama="x" :ukuran="18" />
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-error mb-3" v-if="galat._ || galat.pesan" style="background:#fef2f2; border:1px solid #fecaca; padding:0.6rem 0.85rem; border-radius:var(--radius-md);">
                        {{ (galat._ && (Array.isArray(galat._) ? galat._[0] : galat._)) || galat.pesan }}
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.15rem;">
                        <div class="form-group">
                            <label class="form-label">Kode Barang *</label>
                            <input class="form-control" v-model="form.kode_barang" placeholder="Contoh: BRG-001" />
                            <div class="form-error" v-if="galat.kode_barang">{{ galat.kode_barang[0] }}</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nama Barang *</label>
                            <input class="form-control" v-model="form.nama" placeholder="Nama lengkap produk" />
                            <div class="form-error" v-if="galat.nama">{{ galat.nama[0] }}</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kategori</label>
                            <select class="form-select" v-model="form.kategori_id">
                                <option value="">— Pilih Kategori —</option>
                                <option v-for="k in kategoriList" :key="k.id" :value="k.id">{{ k.nama }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pemasok</label>
                            <select class="form-select" v-model="form.pemasok_id">
                                <option value="">— Pilih Pemasok —</option>
                                <option v-for="p in pemasokList" :key="p.id" :value="p.id">{{ p.nama }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Satuan *</label>
                            <input class="form-control" v-model="form.satuan" placeholder="pcs, dus, karton…" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lokasi Rak</label>
                            <input class="form-control" v-model="form.lokasi_rak" placeholder="Contoh: Rak A-01" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stok Saat Ini *</label>
                            <input class="form-control" type="number" min="0" v-model.number="form.stok_saat_ini" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stok Minimum (Safety Stock) *</label>
                            <input class="form-control" type="number" min="0" v-model.number="form.stok_minimum" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga Beli (Rp)</label>
                            <input class="form-control" type="number" min="0" step="100" v-model.number="form.harga_beli" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga Jual (Rp)</label>
                            <input class="form-control" type="number" min="0" step="100" v-model.number="form.harga_jual" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lead Time (Hari) *</label>
                            <input class="form-control" type="number" min="1" v-model.number="form.lead_time_hari" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">Rata-rata Pemakaian / Hari</label>
                            <input class="form-control" type="number" min="0" step="0.1" v-model.number="form.rata_pemakaian_harian" />
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:0.25rem;">
                        <label class="form-label">Deskripsi / Catatan Tambahan</label>
                        <textarea class="form-control" rows="2" v-model="form.deskripsi" placeholder="Keterangan barang jika ada…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" @click="$emit('tutup')">Batal</button>
                    <button class="btn btn-primary" :disabled="memuat" @click="simpan">
                        {{ memuat ? 'Menyimpan…' : (barang ? 'Perbarui Data' : 'Simpan Barang') }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';
import Ikon from './Ikon.vue';

const props = defineProps({
    barang: { type: Object, default: null },
    kategoriList: { type: Array, default: () => [] },
    pemasokList: { type: Array, default: () => [] },
    memuat: { type: Boolean, default: false },
    galat: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['tutup', 'simpan']);

const form = ref({
    kode_barang: '',
    nama: '',
    kategori_id: '',
    pemasok_id: '',
    satuan: 'pcs',
    stok_saat_ini: 0,
    stok_minimum: 0,
    harga_beli: 0,
    harga_jual: 0,
    lead_time_hari: 7,
    rata_pemakaian_harian: 0,
    lokasi_rak: '',
    deskripsi: '',
});

watch(
    () => props.barang,
    (val) => {
        if (val) {
            form.value = { ...val };
        } else {
            form.value = {
                kode_barang: '',
                nama: '',
                kategori_id: '',
                pemasok_id: '',
                satuan: 'pcs',
                stok_saat_ini: 0,
                stok_minimum: 0,
                harga_beli: 0,
                harga_jual: 0,
                lead_time_hari: 7,
                rata_pemakaian_harian: 0,
                lokasi_rak: '',
                deskripsi: '',
            };
        }
    },
    { immediate: true }
);

function simpan() {
    emit('simpan', form.value);
}
</script>

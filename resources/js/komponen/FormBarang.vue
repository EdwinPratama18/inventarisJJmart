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
                            <div class="form-error" v-if="galat.satuan">{{ galat.satuan[0] }}</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lokasi Rak</label>
                            <input class="form-control" v-model="form.lokasi_rak" placeholder="Contoh: Rak A-01" />
                            <div class="form-error" v-if="galat.lokasi_rak">{{ galat.lokasi_rak[0] }}</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stok Saat Ini *</label>
                            <input class="form-control" type="number" min="0" v-model.number="form.stok_saat_ini" />
                            <div class="form-error" v-if="galat.stok_saat_ini">{{ galat.stok_saat_ini[0] }}</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stok Minimum (Safety Stock) *</label>
                            <input class="form-control" type="number" min="0" v-model.number="form.stok_minimum" />
                            <div class="form-error" v-if="galat.stok_minimum">{{ galat.stok_minimum[0] }}</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga Beli (Rp)</label>
                            <input class="form-control" type="number" min="0" step="100" v-model.number="form.harga_beli" />
                            <div class="form-error" v-if="galat.harga_beli">{{ galat.harga_beli[0] }}</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga Jual (Rp)</label>
                            <input class="form-control" type="number" min="0" step="100" v-model.number="form.harga_jual" />
                            <div class="form-error" v-if="galat.harga_jual">{{ galat.harga_jual[0] }}</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lead Time (Hari) *</label>
                            <input class="form-control" type="number" min="1" v-model.number="form.lead_time_hari" />
                            <div class="form-error" v-if="galat.lead_time_hari">{{ galat.lead_time_hari[0] }}</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Rata-rata Pemakaian / Hari</label>
                            <input class="form-control" type="number" min="0" step="0.1" v-model.number="form.rata_pemakaian_harian" />
                            <div class="form-error" v-if="galat.rata_pemakaian_harian">{{ galat.rata_pemakaian_harian[0] }}</div>
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

const defaultFormData = () => ({
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
    aktif: true,
});

const form = ref(defaultFormData());

watch(
    () => props.barang,
    (val) => {
        if (val) {
            form.value = {
                kode_barang: val.kode_barang ?? '',
                nama: val.nama ?? '',
                kategori_id: val.kategori_id ?? '',
                pemasok_id: val.pemasok_id ?? '',
                satuan: val.satuan ?? 'pcs',
                stok_saat_ini: val.stok_saat_ini ?? 0,
                stok_minimum: val.stok_minimum ?? 0,
                harga_beli: val.harga_beli ?? 0,
                harga_jual: val.harga_jual ?? 0,
                lead_time_hari: val.lead_time_hari ?? 7,
                rata_pemakaian_harian: val.rata_pemakaian_harian ?? 0,
                lokasi_rak: val.lokasi_rak ?? '',
                deskripsi: val.deskripsi ?? '',
                aktif: val.aktif ?? true,
            };
        } else {
            form.value = defaultFormData();
        }
    },
    { immediate: true }
);

function simpan() {
    const payload = {
        kode_barang: (form.value.kode_barang || '').trim(),
        nama: (form.value.nama || '').trim(),
        kategori_id: form.value.kategori_id || null,
        pemasok_id: form.value.pemasok_id || null,
        satuan: (form.value.satuan || '').trim() || 'pcs',
        stok_saat_ini: Number(form.value.stok_saat_ini) || 0,
        stok_minimum: Number(form.value.stok_minimum) || 0,
        harga_beli: form.value.harga_beli !== '' && form.value.harga_beli !== null ? Number(form.value.harga_beli) : null,
        harga_jual: form.value.harga_jual !== '' && form.value.harga_jual !== null ? Number(form.value.harga_jual) : null,
        lead_time_hari: Math.max(1, Number(form.value.lead_time_hari) || 1),
        rata_pemakaian_harian: Number(form.value.rata_pemakaian_harian) || 0,
        lokasi_rak: (form.value.lokasi_rak || '').trim() || null,
        deskripsi: (form.value.deskripsi || '').trim() || null,
        aktif: form.value.aktif !== false,
    };
    emit('simpan', payload);
}
</script>

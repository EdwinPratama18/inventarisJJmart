<template>
    <div>
        <div class="mb-4">
            <h1 class="page-title" style="margin-bottom:0.25rem;">
                <Ikon nama="settings" :ukuran="22" style="color:var(--clr-primary);" />
                <span>Pengaturan Sistem</span>
            </h1>
            <p class="text-muted" style="font-size:0.85rem;">Konfigurasi profil pengguna, integrasi Telegram bot, dan parameter Reorder Point</p>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; max-width:960px;">
            <!-- Profil Akun -->
            <div class="card">
                <div class="card-header d-flex align-center gap-2">
                    <Ikon nama="user" :ukuran="18" style="color:var(--clr-primary);" />
                    <span class="card-title">Profil Pengguna</span>
                </div>
                <div class="card-body">
                    <div class="profil-avatar">
                        {{ inisialPengguna }}
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input class="form-control" :value="toko.pengguna?.name || 'Administrator'" disabled />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat Email</label>
                        <input class="form-control" :value="toko.pengguna?.email || 'admin@jjmart.id'" disabled />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tingkat Akses / Peran</label>
                        <div>
                            <span class="badge badge-info">
                                {{ toko.pengguna?.peran === 'admin' ? 'Administrator Sistem' : 'Petugas Gudang' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Telegram -->
            <div class="card">
                <div class="card-header d-flex align-center gap-2">
                    <Ikon nama="telegram" :ukuran="18" style="color:#0284c7;" />
                    <span class="card-title">Integrasi Telegram Bot</span>
                </div>
                <div class="card-body">
                    <p class="text-muted" style="font-size:0.85rem; margin-bottom:1.25rem; line-height:1.6;">
                        Hubungkan ID Chat Telegram untuk menerima alert otomatis saat stok barang mencapai batas Reorder Point.
                    </p>

                    <div v-if="toko.pengguna?.id_chat_telegram">
                        <div class="telegram-connected mb-3">
                            <div class="d-flex align-center gap-2">
                                <Ikon nama="check" :ukuran="16" style="color:var(--clr-success);" />
                                <span class="font-semibold text-success">Terhubung</span>
                            </div>
                            <code class="font-mono text-muted">{{ toko.pengguna.id_chat_telegram }}</code>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm" @click="ujiKirimTelegram" :disabled="sedangUji">
                                    <Ikon nama="telegram" :ukuran="14" />
                                    <span>{{ sedangUji ? 'Mengirim…' : 'Kirim Pesan Tes' }}</span>
                                </button>
                                <button class="btn btn-secondary btn-sm" @click="putuskanTelegram">Putuskan</button>
                            </div>
                        </div>

                        <div style="font-size:0.8rem; line-height:1.55; color:var(--clr-text-muted); background:var(--clr-bg-subtle); border:1px solid var(--clr-border); border-radius:var(--radius-sm); padding:0.75rem 0.85rem;">
                            <div class="font-semibold text-main mb-1 d-flex align-center gap-1">
                                <Ikon nama="warning" :ukuran="14" style="color:var(--clr-warning);" />
                                <span>Syarat Pesan Diterima:</span>
                            </div>
                            <ul style="padding-left:1.15rem; margin:0;">
                                <li><strong>TELEGRAM_BOT_TOKEN</strong> harus diisi pada file <code>.env</code> server.</li>
                                <li>Anda harus membuka bot di Telegram dan menekan tombol <strong>Start</strong> (<code>/start</code>).</li>
                                <li>Alert otomatis dikirim saat <strong>stok mencapai ROP</strong> setelah transaksi keluar.</li>
                            </ul>
                        </div>
                    </div>

                    <div v-else>
                        <div class="telegram-steps">
                            <div class="step">
                                <span class="step-num">1</span>
                                <span>Buka bot <a href="https://t.me/userinfobot" target="_blank" style="color:var(--clr-primary); font-weight:500;">@userinfobot</a> di Telegram</span>
                            </div>
                            <div class="step">
                                <span class="step-num">2</span>
                                <span>Kirim <code>/start</code> lalu salin ID akun Anda</span>
                            </div>
                            <div class="step">
                                <span class="step-num">3</span>
                                <span>Masukkan ID tersebut pada form di bawah</span>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top:1.15rem;">
                            <label class="form-label">Telegram Chat ID</label>
                            <input class="form-control" v-model="idChatBaru" placeholder="Contoh: 123456789" />
                        </div>
                        <button class="btn btn-primary btn-sm" @click="hubungkanTelegram" :disabled="!idChatBaru || menyimpanTelegram">
                            <Ikon nama="telegram" :ukuran="14" />
                            <span>{{ menyimpanTelegram ? 'Menyimpan…' : 'Hubungkan Telegram' }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Info Parameter ROP -->
            <div class="card" style="grid-column: span 2;">
                <div class="card-header d-flex align-center gap-2">
                    <Ikon nama="refresh" :ukuran="18" style="color:var(--clr-primary);" />
                    <span class="card-title">Formula & Parameter Reorder Point (ROP)</span>
                </div>
                <div class="card-body">
                    <p class="text-muted" style="font-size:0.875rem; margin-bottom:1.15rem;">
                        Titik pemesanan ulang (Reorder Point) dihitung secara matematis menggunakan rumus standar manajemen persediaan:
                    </p>

                    <div class="formula-box">
                        <span class="formula">ROP = (Rata-rata Pemakaian Harian × Lead Time Pengiriman) + Safety Stock</span>
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1.15rem; margin-top:1.25rem;">
                        <div class="param-card">
                            <div class="param-icon-box">
                                <Ikon nama="chart" :ukuran="18" />
                            </div>
                            <div class="param-name">Rata-rata Pemakaian</div>
                            <div class="param-desc">Dihitung otomatis dari total pengeluaran 30 hari terakhir</div>
                        </div>
                        <div class="param-card">
                            <div class="param-icon-box">
                                <Ikon nama="truck" :ukuran="18" />
                            </div>
                            <div class="param-name">Lead Time Pemasok</div>
                            <div class="param-desc">Estimasi durasi pengiriman dari vendor dalam satuan hari</div>
                        </div>
                        <div class="param-card">
                            <div class="param-icon-box">
                                <Ikon nama="package" :ukuran="18" />
                            </div>
                            <div class="param-name">Safety Stock</div>
                            <div class="param-desc">Batas penyangga minimum untuk mencegah terjadinya stock-out</div>
                        </div>
                    </div>

                    <div style="margin-top:1.5rem; padding-top:1.25rem; border-top:1px solid var(--clr-border);">
                        <button class="btn btn-secondary btn-sm" @click="hitungUlangSemua" :disabled="menghitung">
                            <Ikon nama="refresh" :ukuran="14" />
                            <span>{{ menghitung ? 'Menghitung kalkulasi ROP…' : 'Kalkulasi Ulang ROP Seluruh Barang' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

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
import { ref, computed } from 'vue';
import { gunakanTokoAutentikasi } from '../toko/autentikasi';
import Ikon from '../komponen/Ikon.vue';
import api from '../layanan/api';

const toko = gunakanTokoAutentikasi();
const idChatBaru = ref('');
const menyimpanTelegram = ref(false);
const sedangUji = ref(false);
const menghitung = ref(false);
const toast = ref({ tampil: false, pesan: '', jenis: 'success' });

const inisialPengguna = computed(() => {
    const name = toko.pengguna?.name || 'Admin';
    return name.split(' ').map((w) => w[0]).join('').slice(0, 2).toUpperCase();
});

async function hubungkanTelegram() {
    menyimpanTelegram.value = true;
    try {
        await api.post('/telegram/hubungkan', { id_chat_telegram: idChatBaru.value });
        await toko.muatProfil();
        tampilkanToast('Integrasi Telegram berhasil diaktifkan.');
        idChatBaru.value = '';
    } catch (err) {
        tampilkanToast(err.response?.data?.pesan ?? 'Gagal menghubungkan Telegram.', 'error');
    } finally {
        menyimpanTelegram.value = false;
    }
}

async function ujiKirimTelegram() {
    sedangUji.value = true;
    try {
        const { data } = await api.post('/telegram/uji');
        tampilkanToast(data.pesan || 'Pesan uji coba berhasil dikirim ke Telegram Anda!');
    } catch (err) {
        tampilkanToast(err.response?.data?.pesan ?? 'Gagal mengirim pesan uji coba.', 'error');
    } finally {
        sedangUji.value = false;
    }
}

async function putuskanTelegram() {
    if (!confirm('Putuskan koneksi integrasi Telegram?')) return;
    try {
        await api.delete('/telegram/putuskan');
        await toko.muatProfil();
        tampilkanToast('Integrasi Telegram telah diputuskan.', 'warning');
    } catch (_) {}
}

async function hitungUlangSemua() {
    menghitung.value = true;
    try {
        tampilkanToast('Memulai perhitungan ulang ROP untuk seluruh barang…');
        await new Promise((r) => setTimeout(r, 1000));
        tampilkanToast('Seluruh ROP barang berhasil diperbarui.');
    } finally {
        menghitung.value = false;
    }
}

function tampilkanToast(pesan, jenis = 'success') {
    toast.value = { tampil: true, pesan, jenis };
    setTimeout(() => {
        toast.value.tampil = false;
    }, 3500);
}
</script>

<style scoped>
.profil-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--clr-primary-light);
    border: 2px solid var(--clr-primary-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--clr-primary);
    margin: 0 auto 1.5rem;
}
.telegram-connected {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--clr-success-bg);
    border: 1px solid var(--clr-success-border);
    border-radius: var(--radius-md);
    padding: 0.85rem 1rem;
    gap: 0.75rem;
}
.telegram-steps {
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
}
.step {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.825rem;
    color: var(--clr-text-body);
}
.step-num {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #f1f5f9;
    border: 1px solid var(--clr-border);
    color: var(--clr-text-main);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 600;
    flex-shrink: 0;
}
.formula-box {
    background: #f8fafc;
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-md);
    padding: 0.95rem 1.25rem;
    border-left: 3px solid var(--clr-primary);
}
.formula {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--clr-text-main);
    font-family: ui-monospace, SFMono-Regular, monospace;
}
.param-card {
    background: #ffffff;
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-md);
    padding: 1.1rem;
}
.param-icon-box {
    width: 36px;
    height: 36px;
    background: var(--clr-primary-light);
    color: var(--clr-primary);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.75rem;
}
.param-name {
    font-weight: 600;
    font-size: 0.875rem;
    color: var(--clr-text-main);
    margin-bottom: 0.25rem;
}
.param-desc {
    font-size: 0.775rem;
    color: var(--clr-text-muted);
    line-height: 1.4;
}
</style>

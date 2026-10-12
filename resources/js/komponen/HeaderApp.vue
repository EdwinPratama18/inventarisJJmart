<template>
    <header class="app-header">
        <!-- Breadcrumb / Header Title -->
        <div class="header-breadcrumb">
            <span class="crumb-root">JJ Mart</span>
            <span class="crumb-separator">/</span>
            <span class="crumb-active">{{ judul }}</span>
        </div>

        <!-- Right Controls -->
        <div class="header-right-controls">
            <!-- Notifikasi Stok Kritis & Reorder Point -->
            <div class="notification-dropdown-container">
                <button
                    class="header-icon-btn"
                    :class="{ active: notifBuka, 'has-alert': jmlKritis > 0 }"
                    @click="toggleNotifikasi"
                    :title="jmlKritis > 0 ? `${jmlKritis} barang perlu di-reorder` : 'Semua stok dalam kondisi aman'"
                    aria-label="Pemberitahuan Reorder"
                >
                    <Ikon
                        nama="bell"
                        :ukuran="19"
                        :class="{ 'bell-alert-anim': jmlKritis > 0 }"
                    />
                    <!-- Tanda Notif Dot Merah: HANYA MUNCUL JIKA ADA BARANG YANG PERLU DI-REORDER -->
                    <span v-if="jmlKritis > 0" class="header-notif-dot">
                        {{ jmlKritis > 99 ? '99+' : jmlKritis }}
                    </span>
                </button>

                <!-- Dropdown Popup Notifikasi -->
                <Transition name="dropdown">
                    <div v-if="notifBuka" class="notif-popup">
                        <div class="notif-header d-flex align-center justify-between">
                            <div class="d-flex align-center gap-2">
                                <Ikon nama="bell" :ukuran="17" style="color:var(--clr-primary);" />
                                <span class="notif-title">Notifikasi Reorder</span>
                            </div>
                            <span v-if="jmlKritis > 0" class="badge-notif-danger">
                                {{ jmlKritis }} Perlu Dipesan
                            </span>
                            <span v-else class="badge-notif-safe">
                                Stok Optimal
                            </span>
                        </div>

                        <!-- KONTEN: JIKA ADA BARANG PERLU REORDER (> 0) -->
                        <div v-if="jmlKritis > 0" class="notif-content-scrollable">
                            <div class="notif-banner-alert">
                                <strong>Peringatan Titik Kritis:</strong> Stok barang berikut berada pada atau di bawah batas minimum pemesanan ulang (ROP):
                            </div>
                            <div class="notif-item-list">
                                <div
                                    v-for="b in daftarKritis"
                                    :key="b.id"
                                    class="notif-item-card"
                                    @click="keHalamanRop"
                                >
                                    <div
                                        class="notif-item-badge-icon"
                                        :class="b.stok_saat_ini <= 0 ? 'bg-habis' : 'bg-kritis'"
                                    >
                                        <Ikon :nama="b.stok_saat_ini <= 0 ? 'kritis' : 'warning'" :ukuran="16" />
                                    </div>
                                    <div class="notif-item-info">
                                        <div class="notif-item-title">{{ b.nama }}</div>
                                        <div class="notif-item-meta">
                                            <span class="font-mono text-muted">{{ b.kode_barang }}</span>
                                            <span class="sep">&bull;</span>
                                            <span>Stok: <strong :class="b.stok_saat_ini <= 0 ? 'text-danger' : 'text-main'">{{ b.stok_saat_ini }}</strong> / ROP: {{ b.rop }}</span>
                                        </div>
                                        <div class="notif-item-deficit">
                                            Defisit Order: <strong class="text-danger">+{{ hitungDefisit(b) }} {{ b.satuan || 'unit' }}</strong>
                                        </div>
                                    </div>
                                    <Ikon nama="chevron-right" :ukuran="14" class="notif-item-arrow" />
                                </div>
                            </div>
                        </div>

                        <!-- KONTEN: JIKA TIDAK ADA BARANG PERLU REORDER (=== 0) -->
                        <div v-else class="notif-empty-state">
                            <div class="notif-empty-icon-circle">
                                <Ikon nama="check" :ukuran="26" />
                            </div>
                            <h4 class="notif-empty-title">Semua Posisi Stok Aman</h4>
                            <p class="notif-empty-desc">
                                Tidak ada barang yang memerlukan pemesanan ulang saat ini. Seluruh stok gudang berada di atas ambang batas Reorder Point (ROP).
                            </p>
                        </div>

                        <!-- Footer Dropdown -->
                        <div class="notif-footer">
                            <button class="notif-footer-btn" @click="keHalamanRop">
                                <span>Buka Halaman Reorder Point (ROP)</span>
                                <Ikon nama="chevron-right" :ukuran="14" />
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>

            <!-- Divider -->
            <div class="header-divider"></div>

            <!-- Profile Dropdown Trigger -->
            <div class="profile-dropdown-container">
                <button
                    class="profile-trigger-btn"
                    :class="{ active: menuBuka }"
                    @click="toggleMenu"
                >
                    <div class="profile-avatar">{{ inisialPengguna }}</div>
                    <div class="profile-meta">
                        <span class="profile-name">{{ toko.pengguna?.name || 'Administrator' }}</span>
                        <span class="profile-badge">{{ toko.pengguna?.peran === 'admin' ? 'Admin' : 'Petugas' }}</span>
                    </div>
                    <Ikon
                        nama="chevron-down"
                        :ukuran="14"
                        class="profile-chevron"
                        :class="{ 'rotate-180': menuBuka }"
                    />
                </button>

                <!-- Profile Dropdown Popup -->
                <Transition name="dropdown">
                    <div v-if="menuBuka" class="profile-popup">
                        <div class="popup-user-section">
                            <div class="popup-avatar">{{ inisialPengguna }}</div>
                            <div class="popup-user-details">
                                <div class="popup-name">{{ toko.pengguna?.name || 'Administrator' }}</div>
                                <div class="popup-email">{{ toko.pengguna?.email || 'admin@jjmart.id' }}</div>
                                <span class="popup-role-tag">
                                    {{ toko.pengguna?.peran === 'admin' ? 'Administrator' : 'Petugas Gudang' }}
                                </span>
                            </div>
                        </div>

                        <div class="popup-sep"></div>

                        <div class="popup-menu">
                            <RouterLink to="/pengaturan" class="popup-item" @click="menuBuka = false">
                                <Ikon nama="settings" :ukuran="16" />
                                <span>Pengaturan Sistem</span>
                            </RouterLink>
                            <button class="popup-item-logout" @click="bukaModalKonfirmasi">
                                <Ikon nama="logout" :ukuran="16" />
                                <span>Keluar dari Sistem</span>
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>

        <!-- Backdrops untuk menutup dropdown ketika klik di luar -->
        <div v-if="menuBuka || notifBuka" class="dropdown-backdrop" @click="tutupSemuaDropdown"></div>

        <!-- Modal Konfirmasi Logout -->
        <Teleport to="body" v-if="tampilModalLogout">
            <div class="modal-overlay" @click.self="tampilModalLogout = false">
                <div class="modal-box logout-confirm-box">
                    <div class="logout-icon-circle">
                        <Ikon nama="logout" :ukuran="26" />
                    </div>
                    <h3 class="logout-title">Konfirmasi Keluar</h3>
                    <p class="logout-desc">
                        Apakah Anda yakin ingin keluar dari sistem inventaris JJ Mart? Sesi kerja Anda akan diakhiri.
                    </p>
                    <div class="logout-actions">
                        <button class="btn btn-secondary" @click="tampilModalLogout = false">Batal</button>
                        <button class="btn btn-danger" @click="eksekusiKeluar">Ya, Keluar</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </header>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { gunakanTokoAutentikasi } from '../toko/autentikasi';
import { gunakanTokoNotifikasi } from '../toko/notifikasi';
import Ikon from './Ikon.vue';

const toko = gunakanTokoAutentikasi();
const notifStore = gunakanTokoNotifikasi();
const route = useRoute();
const router = useRouter();

const menuBuka = ref(false);
const notifBuka = ref(false);
const tampilModalLogout = ref(false);

const jmlKritis = computed(() => notifStore.jmlKritis);
const daftarKritis = computed(() => notifStore.daftarKritis);

const judulMap = {
    dasbor: 'Dashboard',
    'daftar-barang': 'Data Barang',
    'transaksi-masuk': 'Barang Masuk',
    'transaksi-keluar': 'Barang Keluar',
    'reorder-point': 'Reorder Point (ROP)',
    'daftar-pemasok': 'Data Suplier',
    laporan: 'Laporan Inventaris',
    pengaturan: 'Pengaturan Sistem',
};

const judul = computed(() => judulMap[route.name] ?? 'Inventaris');
const inisialPengguna = computed(() => {
    const name = toko.pengguna?.name ?? 'Admin';
    return name.split(' ').map((w) => w[0]).join('').slice(0, 2).toUpperCase();
});

function toggleNotifikasi() {
    menuBuka.value = false;
    notifBuka.value = !notifBuka.value;
    if (notifBuka.value) {
        notifStore.muatNotifikasi();
    }
}

function toggleMenu() {
    notifBuka.value = false;
    menuBuka.value = !menuBuka.value;
}

function tutupSemuaDropdown() {
    menuBuka.value = false;
    notifBuka.value = false;
}

function keHalamanRop() {
    tutupSemuaDropdown();
    if (toko.isAdmin) {
        router.push('/rop');
    } else {
        router.push('/barang');
    }
}

function hitungDefisit(b) {
    return Math.max(0, (b.rop || 0) - (b.stok_saat_ini || 0));
}

function bukaModalKonfirmasi() {
    tutupSemuaDropdown();
    tampilModalLogout.value = true;
}

async function eksekusiKeluar() {
    tampilModalLogout.value = false;
    await toko.keluar();
    router.push('/login');
}

onMounted(() => {
    notifStore.muatNotifikasi();
});
</script>

<style scoped>
.app-header {
    height: 58px;
    background: #ffffff;
    border-bottom: 1px solid var(--clr-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.75rem;
    position: sticky;
    top: 0;
    z-index: 20;
}

/* Breadcrumb */
.header-breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}
.crumb-root {
    color: var(--clr-text-muted);
    font-weight: 500;
}
.crumb-separator {
    color: var(--clr-text-light);
    font-size: 0.8rem;
}
.crumb-active {
    color: var(--clr-text-main);
    font-weight: 600;
}

/* Right Controls */
.header-right-controls {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

/* Notification Container & Trigger */
.notification-dropdown-container {
    position: relative;
    z-index: 30;
}

.header-icon-btn {
    width: 38px;
    height: 38px;
    border-radius: var(--radius-md);
    background: transparent;
    border: none;
    cursor: pointer;
    color: var(--clr-text-body);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    transition: all var(--transition);
}
.header-icon-btn:hover,
.header-icon-btn.active {
    background-color: #f1f5f9;
    color: var(--clr-primary);
}
.header-icon-btn.has-alert {
    color: #e11d48;
}

.header-notif-dot {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #e11d48;
    color: #ffffff;
    font-size: 0.625rem;
    font-weight: 800;
    min-width: 17px;
    height: 17px;
    border-radius: 99px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #ffffff;
    padding: 0 0.25rem;
    line-height: 1;
    box-shadow: 0 2px 4px rgba(225, 29, 72, 0.35);
}

.bell-alert-anim {
    animation: bell-tilt 2.5s infinite ease-in-out;
}

@keyframes bell-tilt {
    0%, 100% { transform: rotate(0); }
    5% { transform: rotate(14deg); }
    10% { transform: rotate(-14deg); }
    15% { transform: rotate(10deg); }
    20% { transform: rotate(-10deg); }
    25% { transform: rotate(0); }
}

/* Notification Popup */
.notif-popup {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 340px;
    max-width: 90vw;
    background: #ffffff;
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-modal);
    z-index: 40;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.notif-header {
    padding: 0.85rem 1rem;
    border-bottom: 1px solid var(--clr-border);
    background: #fafbfc;
}
.notif-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--clr-text-main);
}
.badge-notif-danger {
    font-size: 0.675rem;
    font-weight: 700;
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
    padding: 0.15rem 0.5rem;
    border-radius: 99px;
}
.badge-notif-safe {
    font-size: 0.675rem;
    font-weight: 700;
    background: #dcfce7;
    color: #166534;
    border: 1px solid #86efac;
    padding: 0.15rem 0.5rem;
    border-radius: 99px;
}

.notif-content-scrollable {
    max-height: 310px;
    overflow-y: auto;
}

.notif-banner-alert {
    padding: 0.6rem 0.95rem;
    background: #fff1f2;
    border-bottom: 1px solid #ffe4e6;
    font-size: 0.735rem;
    color: #9f1239;
    line-height: 1.35;
}

.notif-item-list {
    display: flex;
    flex-direction: column;
}

.notif-item-card {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: background 0.15s ease;
}
.notif-item-card:hover {
    background-color: #f8fafc;
}
.notif-item-card:last-child {
    border-bottom: none;
}

.notif-item-badge-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.bg-habis {
    background: #fee2e2;
    color: #dc2626;
}
.bg-kritis {
    background: #fef3c7;
    color: #d97706;
}

.notif-item-info {
    flex: 1;
    min-width: 0;
}
.notif-item-title {
    font-size: 0.815rem;
    font-weight: 600;
    color: var(--clr-text-main);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.notif-item-meta {
    font-size: 0.725rem;
    color: var(--clr-text-muted);
    margin: 1px 0;
}
.notif-item-meta .sep {
    color: #cbd5e1;
    margin: 0 3px;
}
.notif-item-deficit {
    font-size: 0.7rem;
    font-weight: 600;
}

.notif-item-arrow {
    color: #cbd5e1;
    transition: transform 0.15s;
}
.notif-item-card:hover .notif-item-arrow {
    color: var(--clr-primary);
    transform: translateX(2px);
}

/* Empty State Notifikasi */
.notif-empty-state {
    padding: 2rem 1.25rem;
    text-align: center;
}
.notif-empty-icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #dcfce7;
    color: #16a34a;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
}
.notif-empty-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.25rem;
}
.notif-empty-desc {
    font-size: 0.75rem;
    color: #64748b;
    line-height: 1.4;
    margin: 0 auto;
    max-width: 250px;
}

.notif-footer {
    padding: 0.65rem 0.85rem;
    border-top: 1px solid var(--clr-border);
    background: #fafbfc;
}
.notif-footer-btn {
    width: 100%;
    padding: 0.45rem 0.65rem;
    background: transparent;
    border: none;
    border-radius: var(--radius-sm);
    color: var(--clr-primary);
    font-size: 0.785rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    transition: background 0.15s;
}
.notif-footer-btn:hover {
    background-color: var(--clr-primary-light);
}

.header-divider {
    width: 1px;
    height: 22px;
    background-color: var(--clr-border);
}

/* Profile Trigger */
.profile-dropdown-container {
    position: relative;
    z-index: 30;
}

.profile-trigger-btn {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    background: transparent;
    border: none;
    border-radius: var(--radius-md);
    padding: 0.3rem 0.5rem;
    cursor: pointer;
    transition: background var(--transition);
}
.profile-trigger-btn:hover,
.profile-trigger-btn.active {
    background-color: #f1f5f9;
}

.profile-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--clr-primary-light);
    color: var(--clr-primary);
    border: 1px solid var(--clr-primary-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.775rem;
    font-weight: 700;
    flex-shrink: 0;
}

.profile-meta {
    display: flex;
    align-items: center;
    gap: 0.45rem;
}

.profile-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--clr-text-main);
}

.profile-badge {
    font-size: 0.675rem;
    font-weight: 600;
    color: var(--clr-text-muted);
    background: #f1f5f9;
    padding: 0.15rem 0.45rem;
    border-radius: 4px;
    text-transform: uppercase;
}

.profile-chevron {
    color: var(--clr-text-light);
    transition: transform var(--transition);
}
.rotate-180 {
    transform: rotate(180deg);
}

/* Dropdown Popup */
.profile-popup {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 250px;
    background: #ffffff;
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-modal);
    padding: 0.85rem;
    z-index: 40;
}

.popup-user-section {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.25rem;
}

.popup-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--clr-primary-light);
    color: var(--clr-primary);
    border: 1px solid var(--clr-primary-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 700;
    flex-shrink: 0;
}

.popup-user-details {
    min-width: 0;
}

.popup-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--clr-text-main);
    line-height: 1.25;
}

.popup-email {
    font-size: 0.75rem;
    color: var(--clr-text-muted);
    margin-top: 0.15rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.popup-role-tag {
    display: inline-block;
    margin-top: 0.4rem;
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--clr-success);
    background: var(--clr-success-bg);
    border: 1px solid var(--clr-success-border);
    padding: 0.15rem 0.5rem;
    border-radius: 99px;
}

.popup-sep {
    height: 1px;
    background: var(--clr-border);
    margin: 0.75rem -0.85rem;
}

.popup-menu {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.popup-item {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    width: 100%;
    padding: 0.55rem 0.65rem;
    background: transparent;
    border: none;
    border-radius: var(--radius-md);
    color: var(--clr-text-main);
    font-size: 0.825rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all var(--transition);
}
.popup-item:hover {
    background-color: var(--clr-surface-hover);
    color: var(--clr-primary);
}

.popup-item-logout {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    width: 100%;
    padding: 0.55rem 0.65rem;
    background: transparent;
    border: none;
    border-radius: var(--radius-md);
    color: var(--clr-danger);
    font-size: 0.825rem;
    font-weight: 600;
    cursor: pointer;
    transition: background var(--transition);
}
.popup-item-logout:hover {
    background-color: var(--clr-danger-bg);
}

.dropdown-backdrop {
    position: fixed;
    inset: 0;
    z-index: 25;
    background: transparent;
}

/* Transitions */
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.15s ease-out;
}
.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}

/* Logout Modal */
.logout-confirm-box {
    max-width: 380px;
    text-align: center;
    padding: 1.75rem;
}
.logout-icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--clr-danger-bg);
    color: var(--clr-danger);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.85rem;
}
.logout-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--clr-text-main);
    margin-bottom: 0.35rem;
}
.logout-desc {
    font-size: 0.825rem;
    color: var(--clr-text-muted);
    line-height: 1.5;
    margin-bottom: 1.35rem;
}
.logout-actions {
    display: flex;
    justify-content: center;
    gap: 0.75rem;
}
</style>

<template>
    <aside class="sidebar">
        <!-- Brand -->
        <div class="sidebar-brand">
            <div class="brand-logo">JJ</div>
            <div>
                <div class="brand-name">JJ Mart</div>
                <div class="brand-sub">Sistem Inventaris</div>
            </div>
        </div>

        <!-- Role Badge -->
        <div class="role-badge" :class="isAdmin ? 'role-manager' : 'role-staff'">
            <span class="role-dot"></span>
            {{ toko.labelPeran }}
        </div>

        <!-- Navigation -->
        <nav class="sidebar-menu">
            <!-- UTAMA (Manager only) -->
            <template v-if="isAdmin">
                <div class="menu-heading">Utama</div>
                <RouterLink to="/dasbor" class="menu-item" :class="{ active: $route.name === 'dasbor' }">
                    <Ikon nama="dashboard" :ukuran="18" />
                    <span>Dashboard</span>
                </RouterLink>
            </template>

            <!-- INVENTARIS -->
            <div class="menu-heading" style="margin-top:1rem;">Inventaris</div>
            <RouterLink to="/barang" class="menu-item" :class="{ active: $route.name === 'daftar-barang' }">
                <Ikon nama="package" :ukuran="18" />
                <span>Data Barang</span>
            </RouterLink>
            <!-- Pemasok: Manager only -->
            <RouterLink v-if="isAdmin" to="/pemasok" class="menu-item" :class="{ active: $route.name === 'daftar-pemasok' }">
                <Ikon nama="pemasok" :ukuran="18" />
                <span>Data Suplier</span>
            </RouterLink>
            <RouterLink to="/transaksi/masuk" class="menu-item" :class="{ active: $route.name === 'transaksi-masuk' }">
                <Ikon nama="masuk" :ukuran="18" />
                <span>Barang Masuk</span>
            </RouterLink>
            <RouterLink to="/transaksi/keluar" class="menu-item" :class="{ active: $route.name === 'transaksi-keluar' }">
                <Ikon nama="keluar" :ukuran="18" />
                <span>Barang Keluar</span>
            </RouterLink>
            <!-- Reorder Point: Manager only -->
            <RouterLink v-if="isAdmin" to="/rop" class="menu-item" :class="{ active: $route.name === 'reorder-point' }">
                <Ikon nama="kritis" :ukuran="18" />
                <span>Reorder Point</span>
                <span class="menu-badge-rop" v-if="jmlKritis > 0">{{ jmlKritis }}</span>
            </RouterLink>
            <!-- Laporan: Manager only -->
            <RouterLink v-if="isAdmin" to="/laporan" class="menu-item" :class="{ active: $route.name === 'laporan' }">
                <Ikon nama="laporan" :ukuran="18" />
                <span>Laporan</span>
            </RouterLink>

            <!-- LAINNYA -->
            <div class="menu-heading" style="margin-top:1rem;">Lainnya</div>
            <RouterLink to="/pengaturan" class="menu-item" :class="{ active: $route.name === 'pengaturan' }">
                <Ikon nama="settings" :ukuran="18" />
                <span>Pengaturan</span>
            </RouterLink>
        </nav>
    </aside>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import Ikon from './Ikon.vue';
import api from '../layanan/api';
import { gunakanTokoAutentikasi } from '../toko/autentikasi';

const toko    = gunakanTokoAutentikasi();
const isAdmin = computed(() => toko.isAdmin);

const jmlKritis = ref(0);

async function muatKritis() {
    if (!isAdmin.value) return; // Staff tidak perlu muat kritis
    try {
        const { data } = await api.get('/dasbor/ringkasan');
        jmlKritis.value = data.data.barang_kritis ?? 0;
    } catch (_) {}
}

onMounted(muatKritis);
</script>

<style scoped>
.sidebar {
    width: 234px;
    background: #ffffff;
    border-right: 1px solid var(--clr-border);
    display: flex;
    flex-direction: column;
    height: 100vh;
    flex-shrink: 0;
    position: sticky;
    top: 0;
    overflow-y: auto;
}

.sidebar-brand {
    padding: 1.2rem 1.1rem 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border-bottom: 1px solid var(--clr-border);
    flex-shrink: 0;
}

.sidebar-menu {
    flex: 1;
    padding: 0.85rem 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 1px;
}

.menu-heading {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--clr-text-light);
    letter-spacing: 0.07em;
    padding: 0.6rem 0.7rem 0.3rem;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    padding: 0.57rem 0.8rem;
    color: var(--clr-text-muted);
    font-size: 0.855rem;
    font-weight: 500;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.15s ease;
    position: relative;
    letter-spacing: -0.01em;
}

.menu-item:hover {
    background-color: #f1f5f9;
    color: var(--clr-text-main);
}

.menu-item.active {
    background-color: var(--clr-primary-light);
    color: var(--clr-primary);
    font-weight: 600;
}

.menu-item.active::before {
    content: '';
    position: absolute;
    left: -0.75rem;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 16px;
    background: var(--clr-primary);
    border-radius: 0 3px 3px 0;
}

.menu-item.active :deep(.ikon-svg) {
    stroke: var(--clr-primary);
}

.menu-badge-rop {
    background: var(--clr-danger);
    color: #ffffff;
    font-size: 0.67rem;
    font-weight: 700;
    padding: 0.12rem 0.42rem;
    border-radius: 99px;
    line-height: 1.4;
    margin-left: auto;
}

.role-badge {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    margin: 0.6rem 0.9rem;
    padding: 0.38rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
}

.role-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
}

.role-manager {
    background: #eff6ff;
    color: #1d4ed8;
}
.role-manager .role-dot { background: #2563eb; }

.role-staff {
    background: #f0fdf4;
    color: #15803d;
}
.role-staff .role-dot { background: #16a34a; }
</style>

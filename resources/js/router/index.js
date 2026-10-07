import { createRouter, createWebHistory } from 'vue-router';
import { gunakanTokoAutentikasi } from '../toko/autentikasi';

const routes = [
    {
        path: '/login',
        name: 'masuk',
        component: () => import('../halaman/Masuk.vue'),
        meta: { guest: true },
    },
    {
        path: '/',
        component: () => import('../halaman/TataLetakUtama.vue'),
        meta: { requiresAuth: true },
        children: [
            // Redirect default berdasarkan role
            {
                path: '',
                redirect: () => {
                    const toko = gunakanTokoAutentikasi();
                    return toko.isAdmin ? '/dasbor' : '/barang';
                },
            },
            // ===== MANAGER (admin) ONLY =====
            { path: 'dasbor',   name: 'dasbor',         component: () => import('../halaman/Dasbor.vue'),       meta: { requiresAdmin: true } },
            { path: 'rop',      name: 'reorder-point',  component: () => import('../halaman/ReorderPoint.vue'), meta: { requiresAdmin: true } },
            { path: 'pemasok',  name: 'daftar-pemasok', component: () => import('../halaman/DaftarPemasok.vue'),meta: { requiresAdmin: true } },
            { path: 'laporan',  name: 'laporan',        component: () => import('../halaman/Laporan.vue'),      meta: { requiresAdmin: true } },
            // ===== SEMUA ROLE =====
            { path: 'barang',           name: 'daftar-barang',   component: () => import('../halaman/DaftarBarang.vue') },
            { path: 'transaksi/masuk',  name: 'transaksi-masuk', component: () => import('../halaman/TransaksiMasuk.vue') },
            { path: 'transaksi/keluar', name: 'transaksi-keluar',component: () => import('../halaman/TransaksiKeluar.vue') },
            { path: 'pengaturan',       name: 'pengaturan',      component: () => import('../halaman/Pengaturan.vue') },
        ],
    },
    { path: '/:pathMatch(.*)*', redirect: '/' },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { top: 0 };
    },
});

router.beforeEach((to, from, next) => {
    const toko = gunakanTokoAutentikasi();

    // Belum login → ke halaman login
    if (to.meta.requiresAuth && !toko.terautentikasi) {
        return next('/login');
    }

    // Sudah login → arahkan ke halaman utama sesuai role
    if (to.meta.guest && toko.terautentikasi) {
        return next(toko.isAdmin ? '/dasbor' : '/barang');
    }

    // Staff Gudang mencoba akses halaman khusus Manager → redirect ke /barang
    if (to.meta.requiresAdmin && toko.terautentikasi && !toko.isAdmin) {
        return next('/barang');
    }

    next();
});

export default router;

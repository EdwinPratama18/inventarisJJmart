<template>
    <div class="layout-wrapper">
        <NavSidebar />
        <div class="main-content">
            <HeaderApp :jml-kritis="jmlKritis" @lihat-kritis="router.push('/barang?status=PERLU_PEMESANAN')" />
            <main class="page-body">
                <RouterView v-slot="{ Component }">
                    <component :is="Component" />
                </RouterView>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import NavSidebar from '../komponen/NavSidebar.vue';
import HeaderApp from '../komponen/HeaderApp.vue';
import api from '../layanan/api';

const router = useRouter();
const jmlKritis = ref(0);

async function muatKritis() {
    try {
        const { data } = await api.get('/dasbor/ringkasan');
        jmlKritis.value = data.data.barang_kritis ?? 0;
    } catch (_) {}
}

onMounted(muatKritis);
</script>

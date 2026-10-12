<template>
    <div class="layout-wrapper">
        <NavSidebar />
        <div class="main-content">
            <HeaderApp />
            <main class="page-body">
                <RouterView v-slot="{ Component }">
                    <component :is="Component" />
                </RouterView>
            </main>
        </div>
    </div>
</template>

<script setup>
import { onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import NavSidebar from '../komponen/NavSidebar.vue';
import HeaderApp from '../komponen/HeaderApp.vue';
import { gunakanTokoNotifikasi } from '../toko/notifikasi';

const notifStore = gunakanTokoNotifikasi();
const route = useRoute();

onMounted(() => {
    notifStore.muatNotifikasi();
});

// Otomatis sinkronkan jumlah barang kritis setiap kali berpindah halaman
watch(
    () => route.path,
    () => {
        notifStore.muatNotifikasi();
    }
);
</script>

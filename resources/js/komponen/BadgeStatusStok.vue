<template>
    <span class="badge" :class="kelasStatus">
        <span class="dot"></span>
        {{ labelStatus }}
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: { type: String, default: 'AMAN' },
});

const kelasStatus = computed(() => {
    switch (props.status) {
        case 'AMAN':
            return 'badge-aman';
        case 'PERINGATAN':
            return 'badge-warning';
        case 'PERLU_PEMESANAN':
            return 'badge-kritis';
        case 'HABIS':
            return 'badge-habis';
        default:
            return 'badge-info';
    }
});

const labelStatus = computed(() => {
    const map = {
        AMAN: 'Stok Aman',
        PERINGATAN: 'Mendekati ROP',
        PERLU_PEMESANAN: 'Perlu Reorder',
        HABIS: 'Stok Habis',
    };
    return map[props.status] ?? props.status;
});
</script>

<style scoped>
.dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}
</style>

<template>
    <div class="stat-card">
        <div class="stat-icon" :style="{ backgroundColor: iconBg, color: iconColor }">
            <Ikon :nama="ikon" :ukuran="22" />
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ label }}</div>
            <div class="stat-val">{{ nilaiTerformat }}</div>
            <div v-if="sublabel" class="stat-sub">{{ sublabel }}</div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import Ikon from './Ikon.vue';

const props = defineProps({
    ikon: { type: String, default: 'box' },
    nilai: { type: [Number, String], default: 0 },
    label: { type: String, required: true },
    sublabel: { type: String, default: '' },
    format: { type: String, default: 'angka' }, // angka | rupiah
    iconBg: { type: String, default: '#eff6ff' },
    iconColor: { type: String, default: '#2563eb' },
});

const nilaiTerformat = computed(() => {
    if (props.format === 'rupiah') {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(props.nilai || 0);
    }
    return new Intl.NumberFormat('id-ID').format(props.nilai || 0);
});
</script>

<style scoped>
.stat-sub {
    font-size: 0.75rem;
    color: var(--clr-text-muted);
    margin-top: 0.2rem;
}
</style>

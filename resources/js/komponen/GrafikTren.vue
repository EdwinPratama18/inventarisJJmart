<template>
    <div class="grafik-wrapper">
        <apexchart
            type="area"
            height="260"
            :options="chartOptions"
            :series="series"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: { type: Array, default: () => [] }, // [{ tanggal, total_masuk, total_keluar }]
});

const series = computed(() => [
    { name: 'Stok Masuk', data: props.data.map(d => Number(d.total_masuk ?? 0)) },
    { name: 'Stok Keluar', data: props.data.map(d => Number(d.total_keluar ?? 0)) },
]);

const chartOptions = computed(() => ({
    chart: {
        background: 'transparent',
        toolbar: { show: false },
        fontFamily: 'Inter, sans-serif',
        animations: { enabled: true, easing: 'easeinout', speed: 500 },
    },
    theme: { mode: 'light' },
    colors: ['#2563eb', '#d97706'],
    fill: {
        type: 'gradient',
        gradient: { shadeIntensity: 0.9, opacityFrom: 0.25, opacityTo: 0.02 },
    },
    stroke: { curve: 'smooth', width: 2 },
    dataLabels: { enabled: false },
    xaxis: {
        categories: props.data.map(d => {
            if (!d.tanggal) return '';
            const dt = new Date(d.tanggal);
            return isNaN(dt.getTime()) ? d.tanggal : dt.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        }),
        labels: { style: { colors: '#64748b', fontSize: '11px', fontWeight: 500 } },
        axisBorder: { color: '#e2e8f0' },
        axisTicks: { color: '#e2e8f0' },
    },
    yaxis: {
        labels: { style: { colors: '#64748b', fontSize: '11px', fontWeight: 500 } },
    },
    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
    legend: {
        position: 'top',
        horizontalAlign: 'right',
        labels: { colors: '#334155' },
    },
    tooltip: {
        theme: 'light',
        x: { format: 'dd MMM yyyy' },
    },
}));
</script>

<style scoped>
.grafik-wrapper {
    width: 100%;
}
</style>

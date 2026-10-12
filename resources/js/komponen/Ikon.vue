<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        :width="ukuran || 18"
        :height="ukuran || 18"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        :stroke-width="tebal || 2"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="ikon-svg"
        :class="kelasTambahan"
    >
        <!-- Dashboard -->
        <g v-if="nama === 'dashboard' || nama === 'dasbor'">
            <rect width="7" height="9" x="3" y="3" rx="1" />
            <rect width="7" height="5" x="14" y="3" rx="1" />
            <rect width="7" height="9" x="14" y="12" rx="1" />
            <rect width="7" height="5" x="3" y="16" rx="1" />
        </g>

        <!-- Package / Box / Barang -->
        <g v-else-if="nama === 'box' || nama === 'barang' || nama === 'package'">
            <path d="m7.5 4.27 9 5.15" />
            <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
            <path d="m3.3 7 8.7 5 8.7-5" />
            <path d="M12 22V12" />
        </g>

        <!-- Stok Masuk (Arrow Down Left / Inbound) -->
        <g v-else-if="nama === 'masuk' || nama === 'inbound' || nama === 'arrow-down-left'">
            <path d="M17 7 7 17" />
            <path d="M17 17H7V7" />
        </g>

        <!-- Stok Keluar (Arrow Up Right / Outbound) -->
        <g v-else-if="nama === 'keluar' || nama === 'outbound' || nama === 'arrow-up-right'">
            <path d="M7 17 17 7" />
            <path d="M7 7h10v10" />
        </g>

        <!-- Pemasok / Truck / Supplier -->
        <g v-else-if="nama === 'pemasok' || nama === 'truck'">
            <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2" />
            <path d="M15 18H9" />
            <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14" />
            <circle cx="17" cy="18" r="2" />
            <circle cx="7" cy="18" r="2" />
        </g>

        <!-- Laporan / File-Text -->
        <g v-else-if="nama === 'laporan' || nama === 'file-text'">
            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
            <path d="M14 2v4a2 2 0 0 0 2 2h4" />
            <path d="M10 9H8" />
            <path d="M16 13H8" />
            <path d="M16 17H8" />
        </g>

        <!-- Pengaturan / Settings -->
        <g v-else-if="nama === 'pengaturan' || nama === 'settings'">
            <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
            <circle cx="12" cy="12" r="3" />
        </g>

        <!-- Search -->
        <g v-else-if="nama === 'search' || nama === 'cari'">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
        </g>

        <!-- Plus / Tambah -->
        <g v-else-if="nama === 'plus' || nama === 'tambah'">
            <path d="M5 12h14" />
            <path d="M12 5v14" />
        </g>

        <!-- Edit / Pencil -->
        <g v-else-if="nama === 'edit' || nama === 'ubah' || nama === 'pencil'">
            <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
            <path d="m15 5 4 4" />
        </g>

        <!-- Trash / Hapus -->
        <g v-else-if="nama === 'trash' || nama === 'hapus'">
            <path d="M3 6h18" />
            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
            <line x1="10" x2="10" y1="11" y2="17" />
            <line x1="14" x2="14" y1="11" y2="17" />
        </g>

        <!-- Refresh / Hitung ROP -->
        <g v-else-if="nama === 'refresh' || nama === 'sync' || nama === 'hitung'">
            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
            <path d="M21 3v5h-5" />
            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
            <path d="M8 16H3v5" />
        </g>

        <!-- Filter -->
        <g v-else-if="nama === 'filter'">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
        </g>

        <!-- Bell / Notifikasi -->
        <g v-else-if="nama === 'bell' || nama === 'notifikasi'">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
        </g>

        <!-- Warning / Peringatan (Triangle) -->
        <g v-else-if="nama === 'warning' || nama === 'peringatan'">
            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
            <line x1="12" x2="12" y1="9" y2="13" />
            <line x1="12" x2="12.01" y1="17" y2="17" />
        </g>

        <!-- Alert Octagon / Kritis -->
        <g v-else-if="nama === 'kritis' || nama === 'danger' || nama === 'alert-octagon'">
            <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2" />
            <line x1="12" x2="12" y1="8" y2="12" />
            <line x1="12" x2="12.01" y1="16" y2="16" />
        </g>

        <!-- Check Circle / Aman / Sukses -->
        <g v-else-if="nama === 'check' || nama === 'sukses' || nama === 'aman'">
            <circle cx="12" cy="12" r="10" />
            <path d="m9 12 2 2 4-4" />
        </g>

        <!-- Dollar / Nilai Stok / Currency -->
        <g v-else-if="nama === 'dollar' || nama === 'uang' || nama === 'rupiah'">
            <line x1="12" x2="12" y1="2" y2="22" />
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
        </g>

        <!-- User / Pengguna -->
        <g v-else-if="nama === 'user' || nama === 'pengguna'">
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
        </g>

        <!-- Log Out / Keluar -->
        <g v-else-if="nama === 'logout' || nama === 'keluar-pintu'">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" x2="9" y1="12" y2="12" />
        </g>

        <!-- Eye (Show Password) -->
        <g v-else-if="nama === 'eye'">
            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
            <circle cx="12" cy="12" r="3" />
        </g>

        <!-- Eye Off (Hide Password) -->
        <g v-else-if="nama === 'eye-off'">
            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
            <line x1="2" x2="22" y1="2" y2="22" />
        </g>

        <!-- Lock / Gembok -->
        <g v-else-if="nama === 'lock' || nama === 'kunci'">
            <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
        </g>

        <!-- Mail / Email -->
        <g v-else-if="nama === 'mail' || nama === 'email'">
            <rect width="20" height="16" x="2" y="4" rx="2" />
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
        </g>

        <!-- Chevron Down -->
        <g v-else-if="nama === 'chevron-down'">
            <path d="m6 9 6 6 6-6" />
        </g>

        <!-- Chevron Right -->
        <g v-else-if="nama === 'chevron-right'">
            <path d="m9 18 6-6-6-6" />
        </g>

        <!-- X / Close -->
        <g v-else-if="nama === 'x' || nama === 'close' || nama === 'tutup'">
            <path d="M18 6 6 18" />
            <path d="m6 6 12 12" />
        </g>

        <!-- Telegram / Send -->
        <g v-else-if="nama === 'telegram' || nama === 'send'">
            <path d="M22 2 11 13" />
            <path d="m22 2-7 20-4-9-9-4 20-7z" />
        </g>

        <!-- Activity / Trend / Chart -->
        <g v-else-if="nama === 'chart' || nama === 'tren' || nama === 'activity'">
            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
        </g>

        <!-- Download / Unduh -->
        <g v-else-if="nama === 'download' || nama === 'unduh'">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            <polyline points="7 10 12 15 17 10" />
            <line x1="12" x2="12" y1="15" y2="3" />
        </g>

        <!-- Printer / Cetak -->
        <g v-else-if="nama === 'printer' || nama === 'cetak'">
            <polyline points="6 9 6 2 18 2 18 9" />
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
            <rect width="12" height="8" x="6" y="14" />
        </g>

        <!-- External Link / Buka Tab Baru -->
        <g v-else-if="nama === 'external-link' || nama === 'buka' || nama === 'tab'">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
            <polyline points="15 3 21 3 21 9" />
            <line x1="10" x2="21" y1="14" y2="3" />
        </g>

        <!-- Info -->
        <g v-else>
            <circle cx="12" cy="12" r="10" />
            <path d="M12 16v-4" />
            <path d="M12 8h.01" />
        </g>
    </svg>
</template>

<script setup>
defineProps({
    nama: { type: String, required: true },
    ukuran: { type: [Number, String], default: 18 },
    tebal: { type: [Number, String], default: 2 },
    kelasTambahan: { type: String, default: '' },
});
</script>

<style scoped>
.ikon-svg {
    display: inline-block;
    vertical-align: middle;
    flex-shrink: 0;
}
</style>

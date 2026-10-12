<template>
    <div class="laporan-page">
        <!-- Header Halaman -->
        <div class="page-header-row mb-4">
            <div>
                <h1 class="page-title d-flex align-center gap-2" style="margin-bottom:0.25rem;">
                    <Ikon nama="laporan" :ukuran="24" style="color:var(--clr-primary);" />
                    <span>Laporan Inventaris Toko</span>
                </h1>
                <p class="text-muted" style="font-size:0.875rem;">
                    Pusat rekapitulasi data fisik inventaris, mutasi gudang, analisis Reorder Point, dan export dokumen resmi PDF JJ Mart
                </p>
            </div>
            <!-- Quick Actions Header -->
            <div class="d-flex gap-2 align-center no-print">
                <button class="btn btn-secondary btn-sm" @click="bukaModalPratinjau" :disabled="memuat">
                    <Ikon nama="eye" :ukuran="15" />
                    <span>Pratinjau Dokumen PDF</span>
                </button>
                <button class="btn btn-primary btn-sm" @click="unduhPdf(tab)" :disabled="sedangEkspor || memuat">
                    <Ikon nama="download" :ukuran="15" />
                    <span>{{ sedangEkspor ? 'Mengekspor PDF…' : 'Unduh PDF Resmi' }}</span>
                </button>
            </div>
        </div>

        <!-- Tab Bar Navigasi Laporan -->
        <div class="tab-bar no-print">
            <button class="tab-btn" :class="{ active: tab === 'stok' }" @click="tab = 'stok'">
                <Ikon nama="barang" :ukuran="16" class="mr-1" />
                <span>Posisi Stok & Valuasi</span>
            </button>
            <button class="tab-btn" :class="{ active: tab === 'transaksi' }" @click="tab = 'transaksi'">
                <Ikon nama="activity" :ukuran="16" class="mr-1" />
                <span>Riwayat Mutasi & Transaksi</span>
            </button>
            <button class="tab-btn" :class="{ active: tab === 'reorder' }" @click="tab = 'reorder'">
                <Ikon nama="warning" :ukuran="16" class="mr-1" />
                <span>Rekomendasi Pemesanan (ROP)</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: POSISI STOK & VALUASI               -->
        <!-- ========================================== -->
        <div v-if="tab === 'stok'">
            <div class="laporan-toolbar card-filter d-flex align-center justify-between flex-wrap gap-3 mb-3 p-3 no-print">
                <div class="d-flex align-center gap-3 flex-wrap">
                    <div class="filter-item">
                        <label class="form-label-xs">Kategori Barang:</label>
                        <select class="form-control form-control-sm" style="min-width:180px;" v-model="filterKategoriId" @change="muatStok">
                            <option value="">Semua Kategori</option>
                            <option v-for="kat in daftarKategori" :key="kat.id" :value="kat.id">{{ kat.nama }}</option>
                        </select>
                    </div>
                    <div class="summary-pill d-flex align-center gap-2">
                        <span>Total: <strong class="text-main">{{ dataStok.length }}</strong> SKU</span>
                        <span class="dot-separator">&bull;</span>
                        <span>Total Unit: <strong class="text-main">{{ formatAngka(totalKuantitasStok) }}</strong></span>
                        <span class="dot-separator">&bull;</span>
                        <span>Valuasi HPP: <strong class="text-emerald">{{ formatRupiah(totalNilai) }}</strong></span>
                    </div>
                </div>

                <div class="d-flex align-center gap-2">
                    <button class="btn btn-secondary btn-sm" @click="bukaModalPratinjau" title="Buka Pratinjau Dokumen Resmi">
                        <Ikon nama="eye" :ukuran="14" />
                        <span>Pratinjau</span>
                    </button>
                    <button class="btn btn-primary btn-sm" @click="unduhPdf('stok')" :disabled="sedangEkspor">
                        <Ikon nama="download" :ukuran="14" />
                        <span>{{ sedangEkspor ? 'Membuat PDF…' : 'Export PDF' }}</span>
                    </button>
                    <button class="btn btn-outline btn-sm" @click="cetakHalaman" title="Cetak Langsung">
                        <Ikon nama="printer" :ukuran="14" />
                        <span>Cetak</span>
                    </button>
                </div>
            </div>

            <!-- Tabel Stok -->
            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="app-table" id="tabel-stok">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align:center;">No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Lokasi Rak</th>
                                <th style="text-align:right;">Stok Fisik</th>
                                <th style="text-align:right;">ROP</th>
                                <th style="text-align:right;">Harga Beli (HPP)</th>
                                <th style="text-align:right;">Total Valuasi</th>
                                <th style="text-align:center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="memuat">
                                <td colspan="10" style="text-align:center; padding:3rem;" class="text-muted">
                                    Memuat data posisi stok inventaris…
                                </td>
                            </tr>
                            <tr v-for="(b, idx) in dataStok" :key="b.id" v-else>
                                <td style="text-align:center;" class="text-muted">{{ idx + 1 }}</td>
                                <td><span class="font-mono text-muted">{{ b.kode_barang }}</span></td>
                                <td class="font-semibold">{{ b.nama }}</td>
                                <td>{{ b.kategori?.nama ?? '—' }}</td>
                                <td><span class="badge-rak">{{ b.lokasi_rak || '—' }}</span></td>
                                <td style="text-align:right;">
                                    <strong>{{ formatAngka(b.stok_saat_ini) }}</strong> 
                                    <span class="text-muted" style="font-size:0.75rem;">{{ b.satuan }}</span>
                                </td>
                                <td style="text-align:right;" class="text-muted">{{ b.rop }}</td>
                                <td style="text-align:right;" class="font-mono text-muted">
                                    {{ formatRupiah(b.harga_beli) }}
                                </td>
                                <td style="text-align:right;" class="font-mono font-semibold text-main">
                                    {{ formatRupiah(b.stok_saat_ini * b.harga_beli) }}
                                </td>
                                <td style="text-align:center;">
                                    <BadgeStatusStok :status="b.status_stok" />
                                </td>
                            </tr>
                        </tbody>
                        <tfoot v-if="dataStok.length">
                            <tr class="tfoot-summary">
                                <td colspan="5" style="text-align:right; font-weight:700;">TOTAL KESELURUHAN ({{ dataStok.length }} SKU):</td>
                                <td style="text-align:right; font-weight:700;">{{ formatAngka(totalKuantitasStok) }} Unit</td>
                                <td colspan="2"></td>
                                <td style="text-align:right; font-weight:700; color:var(--clr-primary);" class="font-mono">
                                    {{ formatRupiah(totalNilai) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: RIWAYAT MUTASI & TRANSAKSI          -->
        <!-- ========================================== -->
        <div v-if="tab === 'transaksi'">
            <div class="laporan-toolbar card-filter d-flex align-center justify-between flex-wrap gap-3 mb-3 p-3 no-print">
                <div class="d-flex gap-3 align-center flex-wrap">
                    <div class="filter-item">
                        <label class="form-label-xs">Dari Tanggal:</label>
                        <input type="date" class="form-control form-control-sm" style="width:145px;" v-model="filterDari" />
                    </div>
                    <div class="filter-item">
                        <label class="form-label-xs">Sampai Tanggal:</label>
                        <input type="date" class="form-control form-control-sm" style="width:145px;" v-model="filterSampai" />
                    </div>
                    <div class="filter-item">
                        <label class="form-label-xs">Jenis Mutasi:</label>
                        <select class="form-control form-control-sm" style="width:130px;" v-model="filterJenis">
                            <option value="">Semua Jenis</option>
                            <option value="masuk">Barang Masuk</option>
                            <option value="keluar">Barang Keluar</option>
                        </select>
                    </div>
                    <button class="btn btn-secondary btn-sm align-self-end" @click="muatTransaksi" style="margin-top:auto;">
                        <Ikon nama="filter" :ukuran="13" />
                        <span>Terapkan</span>
                    </button>
                </div>

                <div class="d-flex align-center gap-2">
                    <button class="btn btn-secondary btn-sm" @click="bukaModalPratinjau" title="Buka Pratinjau Dokumen Resmi">
                        <Ikon nama="eye" :ukuran="14" />
                        <span>Pratinjau</span>
                    </button>
                    <button class="btn btn-primary btn-sm" @click="unduhPdf('transaksi')" :disabled="sedangEkspor">
                        <Ikon nama="download" :ukuran="14" />
                        <span>{{ sedangEkspor ? 'Membuat PDF…' : 'Export PDF' }}</span>
                    </button>
                    <button class="btn btn-outline btn-sm" @click="cetakHalaman">
                        <Ikon nama="printer" :ukuran="14" />
                        <span>Cetak</span>
                    </button>
                </div>
            </div>

            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th style="width:40px; text-align:center;">No</th>
                                <th>Tanggal & Waktu</th>
                                <th>Barang</th>
                                <th>Jenis</th>
                                <th style="text-align:right;">Jumlah</th>
                                <th style="text-align:right;">Stok Akhir</th>
                                <th>No. Referensi</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!dataTransaksi.length">
                                <td colspan="8" style="text-align:center; padding:3rem;" class="text-muted">
                                    Tidak ada catatan transaksi pada rentang tanggal yang dipilih.
                                </td>
                            </tr>
                            <tr v-for="(t, idx) in dataTransaksi" :key="t.id" v-else>
                                <td style="text-align:center;" class="text-muted">{{ idx + 1 }}</td>
                                <td class="text-muted font-mono" style="font-size:0.8rem;">{{ formatTanggal(t.tanggal) }}</td>
                                <td>
                                    <div class="font-semibold">{{ t.barang?.nama }}</div>
                                    <div class="font-mono text-muted" style="font-size:0.75rem;">{{ t.barang?.kode_barang }}</div>
                                </td>
                                <td>
                                    <span class="badge" :class="t.jenis === 'masuk' ? 'badge-aman' : 'badge-warning'">
                                        {{ t.jenis === 'masuk' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <strong :class="t.jenis === 'masuk' ? 'text-success' : 'text-amber'">
                                        {{ t.jenis === 'masuk' ? '+' : '-' }}{{ formatAngka(t.jumlah) }}
                                    </strong>
                                    <span class="text-muted" style="font-size:0.75rem;">{{ t.barang?.satuan }}</span>
                                </td>
                                <td style="text-align:right;" class="font-mono">{{ formatAngka(t.stok_sesudah) }}</td>
                                <td><span class="font-mono text-muted" style="font-size:0.8rem;">{{ t.referensi || '—' }}</span></td>
                                <td>{{ t.pengguna?.name ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: REKOMENDASI REORDER POINT (ROP)     -->
        <!-- ========================================== -->
        <div v-if="tab === 'reorder'">
            <div class="laporan-toolbar card-filter d-flex align-center justify-between flex-wrap gap-3 mb-3 p-3 no-print">
                <div class="text-muted" style="font-size:0.85rem;">
                    Ditemukan <strong class="text-danger">{{ dataReorder.length }}</strong> barang yang berada pada atau di bawah ambang batas Reorder Point.
                    <span v-if="dataReorder.length" class="ml-2">
                        &bull; Estimasi Anggaran Pengadaan: <strong class="text-emerald">{{ formatRupiah(totalAnggaranReorder) }}</strong>
                    </span>
                </div>
                <div class="d-flex align-center gap-2">
                    <button class="btn btn-secondary btn-sm" @click="bukaModalPratinjau" title="Buka Pratinjau Surat Pemesanan">
                        <Ikon nama="eye" :ukuran="14" />
                        <span>Pratinjau PO</span>
                    </button>
                    <button class="btn btn-primary btn-sm" @click="unduhPdf('reorder')" :disabled="sedangEkspor">
                        <Ikon nama="download" :ukuran="14" />
                        <span>{{ sedangEkspor ? 'Membuat PDF…' : 'Export PDF Rekomendasi' }}</span>
                    </button>
                    <button class="btn btn-outline btn-sm" @click="cetakHalaman">
                        <Ikon nama="printer" :ukuran="14" />
                        <span>Cetak</span>
                    </button>
                </div>
            </div>

            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th style="width:40px; text-align:center;">No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th style="text-align:right;">Stok Fisik</th>
                                <th style="text-align:right;">Titik ROP</th>
                                <th style="text-align:right;">Kebutuhan Order</th>
                                <th style="text-align:right;">Estimasi HPP</th>
                                <th style="text-align:right;">Estimasi Anggaran</th>
                                <th>Pemasok / Vendor</th>
                                <th style="text-align:center;">Lead Time</th>
                                <th style="text-align:center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!dataReorder.length">
                                <td colspan="11" style="text-align:center; padding:3.5rem;" class="text-muted">
                                    <Ikon nama="check" :ukuran="36" style="color:#10b981; margin:0 auto 0.5rem; display:block;" />
                                    <div class="font-semibold text-main mb-1">Kondisi Stok Optimal!</div>
                                    Semua stok barang berada dalam jumlah aman di atas titik Reorder Point (ROP).
                                </td>
                            </tr>
                            <tr v-for="(b, idx) in dataReorder" :key="b.id" v-else>
                                <td style="text-align:center;" class="text-muted">{{ idx + 1 }}</td>
                                <td><span class="font-mono text-muted">{{ b.kode_barang }}</span></td>
                                <td class="font-semibold">{{ b.nama }}</td>
                                <td style="text-align:right;">
                                    <strong :class="b.stok_saat_ini <= 0 ? 'text-danger' : 'text-main'">
                                        {{ formatAngka(b.stok_saat_ini) }}
                                    </strong>
                                </td>
                                <td style="text-align:right;" class="text-muted">{{ b.rop }}</td>
                                <td style="text-align:right;">
                                    <strong class="text-danger font-mono">+{{ formatAngka(b.kekurangan) }}</strong>
                                </td>
                                <td style="text-align:right;" class="font-mono text-muted">
                                    {{ formatRupiah(b.harga_beli) }}
                                </td>
                                <td style="text-align:right;" class="font-mono font-semibold text-emerald">
                                    {{ formatRupiah(b.kekurangan * b.harga_beli) }}
                                </td>
                                <td>{{ b.pemasok ?? '—' }}</td>
                                <td style="text-align:center;">{{ b.lead_time_hari }} hari</td>
                                <td style="text-align:center;"><BadgeStatusStok :status="b.status_stok" /></td>
                            </tr>
                        </tbody>
                        <tfoot v-if="dataReorder.length">
                            <tr class="tfoot-summary">
                                <td colspan="5" style="text-align:right; font-weight:700;">TOTAL REKOMENDASI PENGADAAN ({{ dataReorder.length }} Item):</td>
                                <td style="text-align:right; font-weight:700; color:var(--clr-danger);" class="font-mono">
                                    +{{ formatAngka(totalKebutuhanReorder) }} Unit
                                </td>
                                <td></td>
                                <td style="text-align:right; font-weight:700; color:#0f766e;" class="font-mono">
                                    {{ formatRupiah(totalAnggaranReorder) }}
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL PRATINJAU DOKUMEN PDF RESMI TOKO (PROFESIONAL A4)  -->
        <!-- ======================================================== -->
        <div v-if="tampilkanPratinjau" class="modal-overlay no-print" @click.self="tampilkanPratinjau = false">
            <div class="modal-card modal-lg pratinjau-modal">
                <!-- Header Modal -->
                <div class="modal-header d-flex align-center justify-between">
                    <div class="d-flex align-center gap-2">
                        <Ikon nama="file-text" :ukuran="20" style="color:var(--clr-primary);" />
                        <div>
                            <h2 class="modal-title" style="margin-bottom:0.15rem;">Pratinjau Dokumen PDF Resmi Toko</h2>
                            <p class="text-muted" style="font-size:0.75rem; margin:0;">
                                Tata letak lembar cetak standar retail JJ Mart (Format Cetak A4 Resmi)
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-center gap-2">
                        <button class="btn btn-primary btn-sm" @click="unduhPdf(tab)" :disabled="sedangEkspor">
                            <Ikon nama="download" :ukuran="14" />
                            <span>{{ sedangEkspor ? 'Mengunduh…' : 'Unduh PDF (.pdf)' }}</span>
                        </button>
                        <button class="btn btn-secondary btn-sm" @click="bukaTabBaru(tab)" title="Buka PDF murni di tab peramban baru">
                            <Ikon nama="external-link" :ukuran="14" />
                            <span>Buka di Tab Baru</span>
                        </button>
                        <button class="btn btn-outline btn-sm" @click="cetakDariModal">
                            <Ikon nama="printer" :ukuran="14" />
                            <span>Cetak</span>
                        </button>
                        <button class="btn-close" @click="tampilkanPratinjau = false" aria-label="Tutup">
                            <Ikon nama="close" :ukuran="18" />
                        </button>
                    </div>
                </div>

                <!-- Konten Lembar Dokumen Kertas (Paper Sheet Preview) -->
                <div class="modal-body modal-scrollable p-4 bg-muted-surface">
                    <div class="paper-sheet shadow-lg" id="area-cetak-dokumen">
                        
                        <!-- KOP SURAT RESMI JJ MART -->
                        <div class="paper-kop d-flex align-center justify-between pb-3 mb-2">
                            <div class="d-flex align-center gap-3">
                                <div class="paper-logo">JJ</div>
                                <div>
                                    <div class="paper-toko-title">JJ MART PADANG BULAN</div>
                                    <div class="paper-toko-sub">Toko Sembako &amp; Kebutuhan Pokok Sehari-hari</div>
                                    <div class="paper-toko-meta">
                                        9MV3+P47, Jl. Raya Abepura - Sentani, Hinekombe, Kec. Sentani Tim., Kota Jayapura, Papua 99351<br>
                                        Telp/WA: 0812-4800-8899 &bull; Toko Sembako &bull; Jayapura, Papua
                                    </div>
                                </div>
                            </div>
                            <div class="paper-doc-badge text-right">
                                <div class="badge-dokumen-pill">DOKUMEN RESMI TOKO</div>
                                <div class="paper-doc-no font-mono">No: {{ infoNoDokumen }}</div>
                                <div class="paper-doc-status text-muted">Status: Terverifikasi Sistem</div>
                            </div>
                        </div>

                        <!-- Garis Ganda Pembatas Kop -->
                        <div class="paper-divider-thick"></div>
                        <div class="paper-divider-thin mb-3"></div>

                        <!-- Judul Dokumen -->
                        <div class="text-center mb-3">
                            <h3 class="paper-doc-title">{{ judulDokumenAktif }}</h3>
                            <p class="paper-doc-subtitle text-muted">{{ subjudulDokumenAktif }}</p>
                        </div>

                        <!-- Metadata Strip -->
                        <div class="paper-meta-strip mb-3">
                            <div class="meta-col">
                                <span class="meta-label">Tanggal Cetak:</span>
                                <span class="meta-value">{{ tanggalSekarang }} WIT</span>
                            </div>
                            <div class="meta-col">
                                <span class="meta-label">Dicetak Oleh:</span>
                                <span class="meta-value">{{ namaPenggunaAktif }}</span>
                            </div>
                            <div class="meta-col">
                                <span class="meta-label">Filter Dokumen:</span>
                                <span class="meta-value">{{ infoFilterAktif }}</span>
                            </div>
                            <div class="meta-col">
                                <span class="meta-label">Unit Gudang:</span>
                                <span class="meta-value">Gudang JJ Mart Padang Bulan</span>
                            </div>
                        </div>

                        <!-- Ringkasan Eksekutif KPI Cards -->
                        <div class="paper-kpi-grid mb-3">
                            <!-- Card 1 -->
                            <div class="paper-kpi-card accent-teal">
                                <div class="kpi-label">{{ kpiCard1.label }}</div>
                                <div class="kpi-num">{{ kpiCard1.value }}</div>
                                <div class="kpi-desc">{{ kpiCard1.desc }}</div>
                            </div>
                            <!-- Card 2 -->
                            <div class="paper-kpi-card accent-blue">
                                <div class="kpi-label">{{ kpiCard2.label }}</div>
                                <div class="kpi-num">{{ kpiCard2.value }}</div>
                                <div class="kpi-desc">{{ kpiCard2.desc }}</div>
                            </div>
                            <!-- Card 3 -->
                            <div class="paper-kpi-card accent-amber">
                                <div class="kpi-label">{{ kpiCard3.label }}</div>
                                <div class="kpi-num">{{ kpiCard3.value }}</div>
                                <div class="kpi-desc">{{ kpiCard3.desc }}</div>
                            </div>
                            <!-- Card 4 -->
                            <div class="paper-kpi-card accent-rose">
                                <div class="kpi-label">{{ kpiCard4.label }}</div>
                                <div class="kpi-num">{{ kpiCard4.value }}</div>
                                <div class="kpi-desc">{{ kpiCard4.desc }}</div>
                            </div>
                        </div>

                        <!-- Tabel Data Pratinjau (Bergantung pada tab) -->
                        <div class="paper-table-wrap mb-3">
                            <!-- Tabel Stok -->
                            <table class="paper-table" v-if="tab === 'stok'">
                                <thead>
                                    <tr>
                                        <th style="width:30px; text-align:center;">No</th>
                                        <th style="width:85px;">Kode</th>
                                        <th>Nama Barang</th>
                                        <th style="width:110px;">Kategori</th>
                                        <th style="width:65px;">Rak</th>
                                        <th style="text-align:right; width:80px;">Stok</th>
                                        <th style="text-align:right; width:50px;">ROP</th>
                                        <th style="text-align:right; width:95px;">Harga Beli</th>
                                        <th style="text-align:right; width:105px;">Valuasi Aset</th>
                                        <th style="text-align:center; width:90px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(b, idx) in dataStok" :key="b.id">
                                        <td style="text-align:center;" class="text-muted">{{ idx + 1 }}</td>
                                        <td class="font-mono fw-semibold">{{ b.kode_barang }}</td>
                                        <td class="fw-semibold">{{ b.nama }}</td>
                                        <td>{{ b.kategori?.nama ?? '—' }}</td>
                                        <td class="text-muted">{{ b.lokasi_rak || '—' }}</td>
                                        <td style="text-align:right;">
                                            <strong>{{ formatAngka(b.stok_saat_ini) }}</strong>
                                            <span class="text-muted" style="font-size:0.7rem;">{{ b.satuan }}</span>
                                        </td>
                                        <td style="text-align:right;" class="text-muted">{{ b.rop }}</td>
                                        <td style="text-align:right;" class="font-mono text-muted">{{ formatRupiah(b.harga_beli) }}</td>
                                        <td style="text-align:right;" class="font-mono fw-bold">{{ formatRupiah(b.stok_saat_ini * b.harga_beli) }}</td>
                                        <td style="text-align:center;">
                                            <span class="badge" :class="getStatusBadgeClass(b.status_stok)">
                                                {{ getStatusLabel(b.status_stok) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" style="text-align:right; font-weight:700;">TOTAL KESELURUHAN ({{ dataStok.length }} SKU):</td>
                                        <td style="text-align:right; font-weight:700;">{{ formatAngka(totalKuantitasStok) }} Unit</td>
                                        <td colspan="2"></td>
                                        <td style="text-align:right; font-weight:700; color:#0f766e;" class="font-mono">
                                            {{ formatRupiah(totalNilai) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>

                            <!-- Tabel Transaksi -->
                            <table class="paper-table" v-else-if="tab === 'transaksi'">
                                <thead>
                                    <tr>
                                        <th style="width:30px; text-align:center;">No</th>
                                        <th style="width:110px;">Waktu & Tanggal</th>
                                        <th>Nama Barang & Kode</th>
                                        <th style="width:65px; text-align:center;">Jenis</th>
                                        <th style="text-align:right; width:75px;">Jumlah</th>
                                        <th style="text-align:right; width:55px;">Sblm</th>
                                        <th style="text-align:right; width:55px;">Ssdh</th>
                                        <th style="width:95px;">No. Referensi</th>
                                        <th style="width:90px;">Petugas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(t, idx) in dataTransaksi" :key="t.id">
                                        <td style="text-align:center;" class="text-muted">{{ idx + 1 }}</td>
                                        <td class="font-mono text-muted" style="font-size:0.75rem;">{{ formatTanggal(t.tanggal) }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ t.barang?.nama }}</div>
                                            <div class="font-mono text-muted" style="font-size:0.7rem;">{{ t.barang?.kode_barang }}</div>
                                        </td>
                                        <td style="text-align:center;">
                                            <span class="badge" :class="t.jenis === 'masuk' ? 'badge-aman' : 'badge-warning'">
                                                {{ t.jenis === 'masuk' ? 'Masuk' : 'Keluar' }}
                                            </span>
                                        </td>
                                        <td style="text-align:right; font-weight:700;">
                                            <span :class="t.jenis === 'masuk' ? 'text-success' : 'text-amber'">
                                                {{ t.jenis === 'masuk' ? '+' : '-' }}{{ formatAngka(t.jumlah) }}
                                            </span>
                                            <span class="text-muted" style="font-size:0.7rem;">{{ t.barang?.satuan }}</span>
                                        </td>
                                        <td style="text-align:right;" class="font-mono text-muted">{{ t.stok_sebelum }}</td>
                                        <td style="text-align:right;" class="font-mono fw-bold">{{ t.stok_sesudah }}</td>
                                        <td class="font-mono text-muted" style="font-size:0.75rem;">{{ t.referensi || '—' }}</td>
                                        <td>{{ t.pengguna?.name ?? '—' }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Tabel Reorder -->
                            <table class="paper-table" v-else>
                                <thead>
                                    <tr>
                                        <th style="width:30px; text-align:center;">No</th>
                                        <th style="width:80px;">Kode</th>
                                        <th>Nama Barang</th>
                                        <th>Pemasok / Vendor</th>
                                        <th style="text-align:right; width:65px;">Stok</th>
                                        <th style="text-align:right; width:50px;">ROP</th>
                                        <th style="text-align:right; width:75px;">Order</th>
                                        <th style="text-align:right; width:95px;">Estimasi HPP</th>
                                        <th style="text-align:right; width:105px;">Anggaran PO</th>
                                        <th style="text-align:center; width:65px;">Lead Time</th>
                                        <th style="text-align:center; width:80px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(b, idx) in dataReorder" :key="b.id">
                                        <td style="text-align:center;" class="text-muted">{{ idx + 1 }}</td>
                                        <td class="font-mono fw-semibold">{{ b.kode_barang }}</td>
                                        <td class="fw-semibold">{{ b.nama }}</td>
                                        <td>{{ b.pemasok ?? '—' }}</td>
                                        <td style="text-align:right;">
                                            <strong :class="b.stok_saat_ini <= 0 ? 'text-danger' : 'text-main'">
                                                {{ formatAngka(b.stok_saat_ini) }}
                                            </strong>
                                        </td>
                                        <td style="text-align:right;" class="text-muted">{{ b.rop }}</td>
                                        <td style="text-align:right;" class="text-danger font-mono fw-bold">+{{ formatAngka(b.kekurangan) }}</td>
                                        <td style="text-align:right;" class="font-mono text-muted">{{ formatRupiah(b.harga_beli) }}</td>
                                        <td style="text-align:right;" class="font-mono fw-bold text-emerald">{{ formatRupiah(b.kekurangan * b.harga_beli) }}</td>
                                        <td style="text-align:center;" class="text-muted">{{ b.lead_time_hari }} hari</td>
                                        <td style="text-align:center;">
                                            <span class="badge badge-kritis">Mendesak</span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" style="text-align:right; font-weight:700;">TOTAL REKOMENDASI PEMESANAN:</td>
                                        <td style="text-align:right; font-weight:700; color:var(--clr-danger);" class="font-mono">
                                            +{{ formatAngka(totalKebutuhanReorder) }} Unit
                                        </td>
                                        <td></td>
                                        <td style="text-align:right; font-weight:700; color:#0f766e;" class="font-mono">
                                            {{ formatRupiah(totalAnggaranReorder) }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Catatan Operasional & Lembar Tanda Tangan Resmi -->
                        <div class="paper-footer-section d-flex justify-between gap-4 mt-3">
                            <!-- Catatan -->
                            <div class="paper-notes-box">
                                <div class="notes-heading">Catatan Operasional Toko:</div>
                                <ol class="notes-list">
                                    <li>Data ditarik secara terpadu melalui database Sistem Informasi Inventaris JJ Mart Padang Bulan.</li>
                                    <li>Laporan ini memiliki kekuatan sebagai dokumen pengawasan dan audit inventaris resmi Toko Sembako.</li>
                                    <li>Seluruh selisih fisik barang wajib dilaporkan kepada Store Manager maksimal 1x24 jam.</li>
                                </ol>
                            </div>

                            <!-- Otorisasi Tanda Tangan -->
                            <div class="paper-signatures d-flex gap-4">
                                <div class="sig-box text-center">
                                    <div class="sig-title">Dipersiapkan Oleh,</div>
                                    <div class="sig-role">Staff / Petugas Gudang</div>
                                    <div class="sig-line-space"></div>
                                    <div class="sig-name">Staff Toko JJ Mart</div>
                                    <div class="sig-dept text-muted">Bagian Operasional Gudang</div>
                                </div>
                                <div class="sig-box text-center">
                                    <div class="sig-title">Jayapura, {{ tanggalSaja }}</div>
                                    <div class="sig-role">Mengetahui & Menyetujui,</div>
                                    <div class="sig-line-space"></div>
                                    <div class="sig-name">Manager JJ Mart Padang Bulan</div>
                                    <div class="sig-dept text-muted">Kepala Toko / Store Manager</div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Dokumen -->
                        <div class="paper-bottom-bar d-flex justify-between mt-4 pt-2">
                            <span>JJ Mart Padang Bulan &bull; Toko Sembako Jayapura &bull; Dokumen Rahasia Internal &bull; ID: {{ infoNoDokumen }}</span>
                            <span>Dicetak: {{ tanggalSekarang }} WIT</span>
                        </div>

                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="modal-footer d-flex justify-between align-center">
                    <span class="text-muted" style="font-size:0.8rem;">
                        Tips: Gunakan tombol "Unduh PDF" untuk menyimpan berkas PDF resmi beresolusi tinggi.
                    </span>
                    <div class="d-flex gap-2">
                        <button class="btn btn-secondary btn-sm" @click="tampilkanPratinjau = false">Tutup</button>
                        <button class="btn btn-primary btn-sm" @click="unduhPdf(tab)" :disabled="sedangEkspor">
                            <Ikon nama="download" :ukuran="14" />
                            <span>Unduh PDF</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import BadgeStatusStok from '../komponen/BadgeStatusStok.vue';
import Ikon from '../komponen/Ikon.vue';
import api from '../layanan/api';
import { gunakanTokoAutentikasi } from '../toko/autentikasi';

const authStore = gunakanTokoAutentikasi();

const tab = ref('stok');
const memuat = ref(false);
const sedangEkspor = ref(false);
const tampilkanPratinjau = ref(false);

const dataStok = ref([]);
const dataTransaksi = ref([]);
const dataReorder = ref([]);
const daftarKategori = ref([]);

const filterKategoriId = ref('');
const filterJenis = ref('');

const today = new Date().toISOString().slice(0, 10);
const firstDay = new Date(new Date().setDate(1)).toISOString().slice(0, 10);
const filterDari = ref(firstDay);
const filterSampai = ref(today);

// Computed Ringkasan
const totalNilai = computed(() => dataStok.value.reduce((s, b) => s + b.stok_saat_ini * b.harga_beli, 0));
const totalKuantitasStok = computed(() => dataStok.value.reduce((s, b) => s + Number(b.stok_saat_ini || 0), 0));

const totalKebutuhanReorder = computed(() => dataReorder.value.reduce((s, b) => s + Number(b.kekurangan || 0), 0));
const totalAnggaranReorder = computed(() => dataReorder.value.reduce((s, b) => s + (b.kekurangan * b.harga_beli), 0));

const namaPenggunaAktif = computed(() => authStore.pengguna?.name || 'Manager JJ Mart Padang Bulan');

const tanggalSekarang = computed(() => {
    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'long',
        timeStyle: 'short',
    }).format(new Date());
});

const tanggalSaja = computed(() => {
    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'long',
    }).format(new Date());
});

const infoNoDokumen = computed(() => {
    const tgl = new Date().toISOString().slice(0, 10).replace(/-/g, '');
    const prefix = tab.value === 'stok' ? 'STK' : (tab.value === 'transaksi' ? 'TRX' : 'ROP');
    return `JJ/${prefix}/${tgl}/` + Math.floor(100 + Math.random() * 900);
});

const judulDokumenAktif = computed(() => {
    if (tab.value === 'stok') return 'Laporan Posisi Stok & Valuasi Inventaris';
    if (tab.value === 'transaksi') return 'Laporan Rekapitulasi Mutasi & Transaksi Gudang';
    return 'Surat Rekomendasi Pemesanan Barang (Reorder Point)';
});

const subjudulDokumenAktif = computed(() => {
    if (tab.value === 'stok') return 'Rekapitulasi Fisik Barang, Titik Pemesanan (ROP), dan Nilai Aset Gudang Aktif';
    if (tab.value === 'transaksi') return `Periode Mutasi: ${filterDari.value} s/d ${filterSampai.value}`;
    return 'Usulan Pengadaan / Purchase Order Berdasarkan Ambang Batas Stok Kritis';
});

const infoFilterAktif = computed(() => {
    if (tab.value === 'stok') {
        if (!filterKategoriId.value) return 'Semua Kategori Barang';
        const k = daftarKategori.value.find(item => item.id == filterKategoriId.value);
        return k ? `Kategori: ${k.nama}` : 'Semua Kategori';
    }
    if (tab.value === 'transaksi') {
        const j = filterJenis.value ? `Jenis: ${filterJenis.value}` : 'Semua Jenis';
        return `${j} (${filterDari.value} s/d ${filterSampai.value})`;
    }
    return 'Kondisi Stok <= Titik ROP';
});

// KPI Cards untuk Pratinjau
const kpiCard1 = computed(() => {
    if (tab.value === 'stok') {
        return { label: 'Total Macam Barang', value: `${dataStok.value.length} SKU`, desc: 'Barang aktif terdata' };
    }
    if (tab.value === 'transaksi') {
        return { label: 'Total Transaksi', value: `${dataTransaksi.value.length} Record`, desc: 'Catatan mutasi' };
    }
    return { label: 'Item Perlu Reorder', value: `${dataReorder.value.length} SKU`, desc: 'Stok kritis <= ROP' };
});

const kpiCard2 = computed(() => {
    if (tab.value === 'stok') {
        return { label: 'Total Fisik Stok', value: `${formatAngka(totalKuantitasStok.value)} Unit`, desc: 'Seluruh stok gudang' };
    }
    if (tab.value === 'transaksi') {
        const jmlMasuk = dataTransaksi.value.filter(t => t.jenis === 'masuk').reduce((s, t) => s + Number(t.jumlah), 0);
        return { label: 'Barang Masuk', value: `+${formatAngka(jmlMasuk)} Unit`, desc: 'Penerimaan / Restock' };
    }
    return { label: 'Kebutuhan Order', value: `+${formatAngka(totalKebutuhanReorder.value)} Unit`, desc: 'Total defisit stok' };
});

const kpiCard3 = computed(() => {
    if (tab.value === 'stok') {
        return { label: 'Total Valuasi Aset', value: formatRupiah(totalNilai.value), desc: 'Berdasarkan HPP Beli' };
    }
    if (tab.value === 'transaksi') {
        const jmlKeluar = dataTransaksi.value.filter(t => t.jenis === 'keluar').reduce((s, t) => s + Number(t.jumlah), 0);
        return { label: 'Barang Keluar', value: `-${formatAngka(jmlKeluar)} Unit`, desc: 'Penjualan / Pengeluaran' };
    }
    return { label: 'Estimasi Anggaran PO', value: formatRupiah(totalAnggaranReorder.value), desc: 'Kebutuhan x HPP Beli' };
});

const kpiCard4 = computed(() => {
    if (tab.value === 'stok') {
        const aman = dataStok.value.filter(b => b.status_stok === 'AMAN').length;
        const kritis = dataStok.value.filter(b => b.status_stok === 'PERINGATAN' || b.status_stok === 'PERLU_PEMESANAN').length;
        return { label: 'Status Inventaris', value: `${aman} Aman • ${kritis} Kritis`, desc: `${dataStok.value.filter(b => b.status_stok === 'HABIS').length} stok habis (0)` };
    }
    if (tab.value === 'transaksi') {
        const jmlMasuk = dataTransaksi.value.filter(t => t.jenis === 'masuk').reduce((s, t) => s + Number(t.jumlah), 0);
        const jmlKeluar = dataTransaksi.value.filter(t => t.jenis === 'keluar').reduce((s, t) => s + Number(t.jumlah), 0);
        const net = jmlMasuk - jmlKeluar;
        return { label: 'Net Arus Barang', value: `${net >= 0 ? '+' : ''}${formatAngka(net)} Unit`, desc: 'Selisih bersih periode' };
    }
    const pemasokCount = new Set(dataReorder.value.map(b => b.pemasok).filter(p => !!p)).size;
    return { label: 'Mitra Pemasok', value: `${pemasokCount} Vendor`, desc: 'Pemasok terkait order' };
});

// API Calls
async function muatKategori() {
    try {
        const { data } = await api.get('/kategori');
        daftarKategori.value = data.data || [];
    } catch (_) {}
}

async function muatStok() {
    memuat.value = true;
    try {
        const params = {};
        if (filterKategoriId.value) params.kategori_id = filterKategoriId.value;
        const { data } = await api.get('/laporan/stok-saat-ini', { params });
        dataStok.value = data.data;
    } finally {
        memuat.value = false;
    }
}

async function muatTransaksi() {
    memuat.value = true;
    try {
        const params = { dari: filterDari.value, sampai: filterSampai.value };
        if (filterJenis.value) params.jenis = filterJenis.value;
        const { data } = await api.get('/laporan/riwayat-transaksi', { params });
        dataTransaksi.value = data.data;
    } catch (_) {}
    finally {
        memuat.value = false;
    }
}

async function muatReorder() {
    memuat.value = true;
    try {
        const { data } = await api.get('/laporan/riwayat-reorder');
        dataReorder.value = data.data;
    } catch (_) {}
    finally {
        memuat.value = false;
    }
}

watch(tab, (t) => {
    if (t === 'stok') muatStok();
    else if (t === 'transaksi') muatTransaksi();
    else muatReorder();
});

// Helper Format
function formatRupiah(n) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n || 0);
}

function formatAngka(n) {
    return new Intl.NumberFormat('id-ID').format(n || 0);
}

function formatTanggal(t) {
    if (!t) return '—';
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'short', timeStyle: 'short' }).format(new Date(t));
}

function getStatusBadgeClass(status) {
    if (status === 'AMAN') return 'badge-aman';
    if (status === 'PERINGATAN') return 'badge-warning';
    if (status === 'PERLU_PEMESANAN') return 'badge-kritis';
    if (status === 'HABIS') return 'badge-habis';
    return 'badge-info';
}

function getStatusLabel(status) {
    const map = {
        AMAN: 'Stok Aman',
        PERINGATAN: 'Mendekati ROP',
        PERLU_PEMESANAN: 'Perlu Reorder',
        HABIS: 'Stok Habis (0)',
    };
    return map[status] || status;
}

// Export PDF & Print Actions
async function unduhPdf(tipeLaporan) {
    sedangEkspor.value = true;
    try {
        const params = { tipe: tipeLaporan, mode: 'download' };
        if (tipeLaporan === 'stok' && filterKategoriId.value) {
            params.kategori_id = filterKategoriId.value;
        } else if (tipeLaporan === 'transaksi') {
            params.dari = filterDari.value;
            params.sampai = filterSampai.value;
            if (filterJenis.value) params.jenis = filterJenis.value;
        }

        const response = await api.get('/laporan/export-pdf', {
            params,
            responseType: 'blob',
        });

        const blob = new Blob([response.data], { type: 'application/pdf' });
        const downloadUrl = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = downloadUrl;
        
        const timestamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');
        const filename = `Laporan_${tipeLaporan.toUpperCase()}_JJMart_${timestamp}.pdf`;
        link.setAttribute('download', filename);
        
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(downloadUrl);
    } catch (err) {
        alert('Gagal mengunduh berkas PDF. Silakan coba kembali.');
    } finally {
        sedangEkspor.value = false;
    }
}

async function bukaTabBaru(tipeLaporan) {
    try {
        const params = { tipe: tipeLaporan, mode: 'stream' };
        if (tipeLaporan === 'stok' && filterKategoriId.value) {
            params.kategori_id = filterKategoriId.value;
        } else if (tipeLaporan === 'transaksi') {
            params.dari = filterDari.value;
            params.sampai = filterSampai.value;
            if (filterJenis.value) params.jenis = filterJenis.value;
        }

        const response = await api.get('/laporan/export-pdf', {
            params,
            responseType: 'blob',
        });

        const blob = new Blob([response.data], { type: 'application/pdf' });
        const fileUrl = window.URL.createObjectURL(blob);
        window.open(fileUrl, '_blank');
    } catch (err) {
        alert('Gagal membuka dokumen PDF di tab baru.');
    }
}

function bukaModalPratinjau() {
    tampilkanPratinjau.value = true;
}

function cetakHalaman() {
    window.print();
}

function cetakDariModal() {
    window.print();
}

onMounted(() => {
    muatKategori();
    muatStok();
});
</script>

<style scoped>
.text-main {
    color: var(--clr-text-main);
}
.text-emerald {
    color: #059669;
}
.text-amber {
    color: #d97706;
}
.badge-rak {
    display: inline-block;
    padding: 0.15rem 0.5rem;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-size: 0.75rem;
    font-family: monospace;
}
.card-filter {
    background: #ffffff;
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-xs);
}
.form-label-xs {
    display: block;
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--clr-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.2rem;
}
.summary-pill {
    padding: 0.35rem 0.75rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: var(--radius-sm);
    font-size: 0.825rem;
    color: #475569;
}
.dot-separator {
    color: #cbd5e1;
}
.tfoot-summary td {
    background-color: #f8fafc;
    border-top: 2px solid var(--clr-border);
    padding: 0.75rem 1rem;
    font-size: 0.85rem;
}

/* ===== PRATINJAU DOKUMEN MODAL STYLING ===== */
.pratinjau-modal {
    max-width: 1020px;
    width: 95vw;
}
.modal-scrollable {
    max-height: 80vh;
    overflow-y: auto;
}
.bg-muted-surface {
    background: #f1f5f9;
}
.paper-sheet {
    background: #ffffff;
    border-radius: 4px;
    padding: 2.25rem 2.5rem;
    margin: 0 auto;
    max-width: 920px;
    color: #1e293b;
    border: 1px solid #cbd5e1;
    font-size: 0.825rem;
}

/* KOP SURAT */
.paper-logo {
    width: 52px;
    height: 52px;
    background: #0f766e;
    color: #ffffff;
    font-size: 1.35rem;
    font-weight: 800;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    letter-spacing: 0.5px;
}
.paper-toko-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.5px;
    line-height: 1.1;
}
.paper-toko-sub {
    font-size: 0.725rem;
    font-weight: 700;
    color: #0d9488;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 2px;
}
.paper-toko-meta {
    font-size: 0.725rem;
    color: #64748b;
    line-height: 1.3;
}
.badge-dokumen-pill {
    display: inline-block;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 4px;
}
.paper-doc-no {
    font-size: 0.75rem;
    color: #475569;
    font-weight: 600;
}
.paper-doc-status {
    font-size: 0.7rem;
}

/* Pembatas Kop */
.paper-divider-thick {
    height: 2.5px;
    background: #0f172a;
    margin-top: 4px;
    margin-bottom: 2px;
}
.paper-divider-thin {
    height: 1px;
    background: #cbd5e1;
}

/* Judul Dokumen */
.paper-doc-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.15rem;
}
.paper-doc-subtitle {
    font-size: 0.75rem;
}

/* Meta Strip */
.paper-meta-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 0.6rem 0.85rem;
    gap: 0.75rem;
}
.meta-col {
    display: flex;
    flex-direction: column;
}
.meta-label {
    font-size: 0.65rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.meta-value {
    font-size: 0.75rem;
    font-weight: 700;
    color: #0f172a;
}

/* KPI Grid */
.paper-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.6rem;
}
.paper-kpi-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 0.6rem 0.85rem;
}
.paper-kpi-card.accent-teal { border-top: 3px solid #0d9488; }
.paper-kpi-card.accent-blue { border-top: 3px solid #2563eb; }
.paper-kpi-card.accent-amber { border-top: 3px solid #d97706; }
.paper-kpi-card.accent-rose { border-top: 3px solid #e11d48; }

.kpi-label {
    font-size: 0.65rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.kpi-num {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    margin: 2px 0;
}
.kpi-desc {
    font-size: 0.65rem;
    color: #64748b;
}

/* Tabel Kertas Dokumen */
.paper-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.75rem;
}
.paper-table th {
    background: #1e293b;
    color: #ffffff;
    padding: 0.45rem 0.6rem;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    border: 1px solid #1e293b;
    text-align: left;
}
.paper-table td {
    padding: 0.45rem 0.6rem;
    border: 1px solid #cbd5e1;
    vertical-align: middle;
}
.paper-table tbody tr:nth-child(even) {
    background: #f8fafc;
}
.paper-table tfoot td {
    background: #f1f5f9;
    border-top: 2px solid #0f172a;
    font-weight: 700;
    padding: 0.5rem 0.6rem;
}

/* Catatan & Tanda Tangan */
.paper-notes-box {
    flex: 1;
    background: #f8fafc;
    border-left: 3px solid #0d9488;
    padding: 0.6rem 0.85rem;
    border-radius: 0 4px 4px 0;
    font-size: 0.7rem;
    color: #475569;
}
.notes-heading {
    font-weight: 700;
    color: #0f172a;
    text-transform: uppercase;
    font-size: 0.68rem;
    margin-bottom: 0.25rem;
}
.notes-list {
    margin: 0;
    padding-left: 1.15rem;
    line-height: 1.4;
}
.sig-box {
    width: 170px;
}
.sig-title {
    font-size: 0.7rem;
    color: #475569;
}
.sig-role {
    font-size: 0.75rem;
    font-weight: 700;
    color: #0f172a;
}
.sig-line-space {
    height: 44px;
}
.sig-name {
    font-size: 0.75rem;
    font-weight: 700;
    color: #0f172a;
    text-decoration: underline;
}
.sig-dept {
    font-size: 0.68rem;
}
.paper-bottom-bar {
    border-top: 1px solid #e2e8f0;
    font-size: 0.68rem;
    color: #94a3b8;
}

/* Print Only Settings */
@media print {
    .modal-overlay {
        background: transparent !important;
        position: static !important;
        inset: auto !important;
        z-index: auto !important;
        padding: 0 !important;
    }
    .modal-card {
        max-width: 100% !important;
        width: 100% !important;
        box-shadow: none !important;
        border: none !important;
    }
    .modal-header, .modal-footer {
        display: none !important;
    }
    .modal-body {
        padding: 0 !important;
        background: transparent !important;
        max-height: none !important;
        overflow: visible !important;
    }
    .paper-sheet {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        max-width: 100% !important;
    }
}
</style>

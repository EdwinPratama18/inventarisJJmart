<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $judul_dokumen ?? 'Laporan Posisi Stok Inventaris' }} - JJ Mart</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm 12mm 12mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.35;
            background: #ffffff;
        }

        /* ===== KOP SURAT TOKO ===== */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .kop-table td {
            vertical-align: middle;
            border: none;
        }

        .kop-logo-cell {
            width: 70px;
            padding-right: 12px;
        }

        .logo-badge {
            width: 60px;
            height: 60px;
            background: #0f766e;
            color: #ffffff;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            line-height: 60px;
            font-size: 16pt;
            letter-spacing: 1px;
        }

        .kop-text-cell {
            text-align: left;
        }

        .toko-nama {
            font-size: 16pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            line-height: 1.1;
        }

        .toko-tagline {
            font-size: 8pt;
            font-weight: 600;
            color: #0d9488;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 3px;
        }

        .toko-kontak {
            font-size: 7.5pt;
            color: #64748b;
            line-height: 1.3;
        }

        .kop-meta-cell {
            width: 220px;
            text-align: right;
            vertical-align: middle;
        }

        .doc-badge-pill {
            display: inline-block;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: 700;
            color: #334155;
            margin-bottom: 3px;
        }

        .doc-no-text {
            font-size: 7.5pt;
            color: #64748b;
            font-family: 'Courier New', Courier, monospace;
        }

        /* Garis ganda pembatas KOP */
        .kop-divider {
            border: none;
            border-top: 2.5px solid #0f172a;
            margin-top: 2px;
            margin-bottom: 2px;
        }

        .kop-divider-thin {
            border: none;
            border-top: 0.75px solid #94a3b8;
            margin-bottom: 10px;
        }

        /* ===== JUDUL & METADATA BAR ===== */
        .header-title-box {
            text-align: center;
            margin-bottom: 10px;
        }

        .doc-main-title {
            font-size: 13pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .doc-sub-title {
            font-size: 8pt;
            color: #64748b;
            margin-top: 2px;
        }

        .meta-strip {
            width: 100%;
            border-collapse: collapse;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .meta-strip td {
            padding: 5px 8px;
            font-size: 7.8pt;
            border: none;
        }

        .meta-label {
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 7pt;
        }

        .meta-value {
            color: #1e293b;
            font-weight: 700;
        }

        /* ===== RINGKASAN EKSEKUTIF / KPI CARDS ===== */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-bottom: 10px;
        }

        .kpi-card {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 10px;
            text-align: left;
            vertical-align: top;
            width: 25%;
        }

        .kpi-card.accent-teal {
            border-top: 3px solid #0d9488;
        }

        .kpi-card.accent-blue {
            border-top: 3px solid #2563eb;
        }

        .kpi-card.accent-amber {
            border-top: 3px solid #d97706;
        }

        .kpi-card.accent-rose {
            border-top: 3px solid #e11d48;
        }

        .kpi-title {
            font-size: 7pt;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kpi-value {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .kpi-sub {
            font-size: 6.8pt;
            color: #64748b;
            margin-top: 2px;
        }

        /* ===== TABEL DATA UTAMA ===== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8pt;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            padding: 6px 6px;
            font-weight: 700;
            font-size: 7.2pt;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: 1px solid #1e293b;
            text-align: left;
        }

        .data-table th.text-center {
            text-align: center;
        }

        .data-table th.text-right {
            text-align: right;
        }

        .data-table tbody tr {
            page-break-inside: avoid;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .data-table td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
            color: #1e293b;
        }

        .data-table td.text-center {
            text-align: center;
        }

        .data-table td.text-right {
            text-align: right;
        }

        .font-mono {
            font-family: 'Courier New', Courier, monospace;
        }

        .fw-bold {
            font-weight: 700;
        }

        .fw-semibold {
            font-weight: 600;
        }

        .text-muted {
            color: #64748b;
        }

        /* Badge Status */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 6.8pt;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
            letter-spacing: 0.3px;
        }

        .badge-aman {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde047;
        }

        .badge-kritis {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .badge-habis {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        /* Footer Tabel */
        .data-table tfoot td {
            background-color: #f1f5f9;
            font-weight: 700;
            padding: 6px 6px;
            border: 1px solid #94a3b8;
            border-top: 2px solid #0f172a;
            color: #0f172a;
        }

        /* ===== CATATAN & PENGESAHAN ===== */
        .notes-signatures-wrap {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
            margin-top: 8px;
        }

        .notes-signatures-wrap td {
            border: none;
            vertical-align: top;
        }

        .notes-cell {
            width: 50%;
            padding-right: 20px;
        }

        .notes-box {
            background: #f8fafc;
            border-left: 3px solid #0d9488;
            padding: 6px 10px;
            border-radius: 0 4px 4px 0;
            font-size: 7.2pt;
            color: #475569;
        }

        .notes-title {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
            text-transform: uppercase;
            font-size: 7pt;
        }

        /* Tanda Tangan */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signatures-table td {
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
            border: none;
            width: 50%;
        }

        .sig-caption {
            font-size: 7.5pt;
            color: #475569;
            margin-bottom: 2px;
        }

        .sig-role {
            font-size: 8pt;
            font-weight: 700;
            color: #0f172a;
        }

        .sig-space {
            height: 48px;
        }

        .sig-name {
            font-size: 8pt;
            font-weight: 700;
            color: #0f172a;
            text-decoration: underline;
        }

        .sig-id {
            font-size: 7pt;
            color: #64748b;
        }

        /* ===== FOOTER HALAMAN ===== */
        .page-footer {
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px solid #e2e8f0;
            font-size: 6.8pt;
            color: #94a3b8;
            width: 100%;
            display: table;
        }

        .page-footer-left {
            display: table-cell;
            text-align: left;
        }

        .page-footer-right {
            display: table-cell;
            text-align: right;
        }
    </style>
</head>

<body>

    <!-- KOP SURAT TOKO -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo-cell">
                <div class="logo-badge">JJ</div>
            </td>
            <td class="kop-text-cell">
                <div class="toko-nama">JJ MART PADANG BULAN</div>
                <div class="toko-tagline">Toko Sembako &amp; Kebutuhan Pokok Sehari-hari</div>
                <div class="toko-kontak">
                    9MV3+P47, Jl. Raya Abepura - Sentani, Hinekombe, Kec. Sentani Tim., Kota Jayapura, Papua 99351<br>
                    Telp/WA: 0812-4800-8899 &bull; Toko Sembako &bull; Jayapura, Papua
                </div>
            </td>
            <td class="kop-meta-cell">
                <div class="doc-badge-pill">DOKUMEN RESMI INVENTARIS</div>
                <div class="doc-no-text">No: {{ $no_dokumen ?? 'JJ/STK/' . date('Ymd') . '/' . rand(100, 999) }}</div>
                <div class="doc-no-text">Status: Terverifikasi Sistem</div>
            </td>
        </tr>
    </table>

    <hr class="kop-divider">
    <hr class="kop-divider-thin">

    <!-- JUDUL LAPORAN -->
    <div class="header-title-box">
        <h1 class="doc-main-title">Laporan Posisi Stok & Valuasi Inventaris</h1>
        <p class="doc-sub-title">Rekapitulasi Fisik Barang, Titik Pemesanan (ROP), dan Nilai Aset Toko Sembako</p>
    </div>

    <!-- METADATA STRIP -->
    <table class="meta-strip">
        <tr>
            <td>
                <span class="meta-label">Tanggal Cetak:</span><br>
                <span class="meta-value">{{ $tanggal_cetak ?? date('d F Y, H:i') }} WIT</span>
            </td>
            <td>
                <span class="meta-label">Dicetak Oleh:</span><br>
                <span class="meta-value">{{ $dicetak_oleh ?? 'Manager JJ Mart Padang Bulan' }}</span>
            </td>
            <td>
                <span class="meta-label">Filter Kategori:</span><br>
                <span class="meta-value">{{ $filter_kategori ?? 'Semua Kategori' }}</span>
            </td>
            <td>
                <span class="meta-label">Lokasi Gudang:</span><br>
                <span class="meta-value">Gudang JJ Mart Padang Bulan</span>
            </td>
        </tr>
    </table>

    <!-- RINGKASAN EKSEKUTIF / KPI -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-card accent-teal">
                <div class="kpi-title">Total Macam Barang</div>
                <div class="kpi-value">{{ count($barang) }} <span
                        style="font-size:7.5pt; font-weight:normal; color:#64748b;">SKU</span></div>
                <div class="kpi-sub">Barang status aktif terdata</div>
            </td>
            <td class="kpi-card accent-blue">
                <div class="kpi-title">Total Stok Fisik</div>
                <div class="kpi-value">{{ number_format($barang->sum('stok_saat_ini'), 0, ',', '.') }} <span
                        style="font-size:7.5pt; font-weight:normal; color:#64748b;">Unit</span></div>
                <div class="kpi-sub">Akumulasi seluruh kuantitas</div>
            </td>
            <td class="kpi-card accent-amber">
                <div class="kpi-title">Total Nilai Valuasi Aset</div>
                <div class="kpi-value">Rp {{ number_format($total_nilai_stok ?? 0, 0, ',', '.') }}</div>
                <div class="kpi-sub">Berdasarkan HPP Beli</div>
            </td>
            <td class="kpi-card accent-rose">
                <div class="kpi-title">Status Kondisi Stok</div>
                <div class="kpi-value" style="font-size:9pt; font-weight:700;">
                    <span style="color:#166534;">{{ $jumlah_aman ?? 0 }} Aman</span> &bull;
                    <span style="color:#991b1b;">{{ $jumlah_kritis ?? 0 }} Kritis</span>
                </div>
                <div class="kpi-sub">{{ $jumlah_habis ?? 0 }} barang stok kosong (0)</div>
            </td>
        </tr>
    </table>

    <!-- TABEL DATA UTAMA -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 28px;">No</th>
                <th style="width: 75px;">Kode</th>
                <th>Nama Barang</th>
                <th style="width: 100px;">Kategori</th>
                <th style="width: 65px;">Rak</th>
                <th class="text-right" style="width: 75px;">Stok Fisik</th>
                <th class="text-right" style="width: 50px;">ROP</th>
                <th class="text-right" style="width: 85px;">Harga Beli (HPP)</th>
                <th class="text-right" style="width: 95px;">Valuasi Aset</th>
                <th class="text-center" style="width: 85px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barang as $idx => $b)
                @php
                    $nilaiAset = ($b->stok_saat_ini ?? 0) * ($b->harga_beli ?? 0);
                    $status = $b->status_stok ?? 'AMAN';
                @endphp
                <tr>
                    <td class="text-center text-muted">{{ $idx + 1 }}</td>
                    <td class="font-mono fw-semibold">{{ $b->kode_barang }}</td>
                    <td class="fw-semibold">{{ $b->nama }}</td>
                    <td>{{ $b->kategori?->nama ?? '—' }}</td>
                    <td class="text-muted">{{ $b->lokasi_rak ?? '—' }}</td>
                    <td class="text-right">
                        <strong style="color:#0f172a;">{{ number_format($b->stok_saat_ini, 0, ',', '.') }}</strong>
                        <span class="text-muted" style="font-size:7pt;">{{ $b->satuan }}</span>
                    </td>
                    <td class="text-right text-muted">{{ $b->rop }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($b->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-right font-mono fw-bold">Rp {{ number_format($nilaiAset, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($status === 'AMAN')
                            <span class="badge badge-aman">Stok Aman</span>
                        @elseif($status === 'PERINGATAN')
                            <span class="badge badge-warning">Mendekati ROP</span>
                        @elseif($status === 'PERLU_PEMESANAN')
                            <span class="badge badge-kritis">Perlu Reorder</span>
                        @elseif($status === 'HABIS')
                            <span class="badge badge-habis">Habis (0)</span>
                        @else
                            <span class="badge badge-aman">{{ $status }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted" style="padding: 20px;">
                        Tidak ada data barang yang memenuhi kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right fw-bold" style="text-transform: uppercase; font-size:7.5pt;">
                    Total Keseluruhan ({{ count($barang) }} SKU):
                </td>
                <td class="text-right font-mono fw-bold">
                    {{ number_format($barang->sum('stok_saat_ini'), 0, ',', '.') }} Unit
                </td>
                <td class="text-center text-muted">—</td>
                <td class="text-center text-muted">—</td>
                <td class="text-right font-mono fw-bold" style="color:#0f766e;">
                    Rp {{ number_format($total_nilai_stok ?? 0, 0, ',', '.') }}
                </td>
                <td class="text-center text-muted">—</td>
            </tr>
        </tfoot>
    </table>

    <!-- CATATAN & PENGESAHAN DOKUMEN -->
    <table class="notes-signatures-wrap">
        <tr>
            <td class="notes-cell">
                <div class="notes-box">
                    <div class="notes-title">Catatan Operasional Inventaris:</div>
                    1. Data posisi stok ditarik secara terpadu melalui database Sistem Manajemen JJ Mart Padang Bulan.<br>
                    2. Nilai aset dihitung berdasarkan Harga Pokok Pembelian (HPP) pada stok fisik yang tersedia.<br>
                    3. Barang dengan status <em>Perlu Reorder</em> diinstruksikan segera diproses pembuatan Purchase
                    Order (PO).<br>
                    4. Segala perbedaan antara stok fisik dan sistem wajib dituangkan dalam Berita Acara Stock Opname Toko Sembako.
                </div>
            </td>
            <td style="width: 50%;">
                <table class="signatures-table">
                    <tr>
                        <td>
                            <div class="sig-caption">Dipersiapkan Oleh,</div>
                            <div class="sig-role">Staff / Petugas Gudang</div>
                            <div class="sig-space"></div>
                            <div class="sig-name">{{ $petugas_gudang ?? 'Staff Toko JJ Mart' }}</div>
                            <div class="sig-id">Bagian Pengelolaan Inventaris</div>
                        </td>
                        <td>
                            <div class="sig-caption">Jayapura, {{ $tanggal_pengesahan ?? date('d F Y') }}</div>
                            <div class="sig-role">Mengetahui & Menyetujui,</div>
                            <div class="sig-space"></div>
                            <div class="sig-name">{{ $kepala_toko ?? 'Manager JJ Mart Padang Bulan' }}</div>
                            <div class="sig-id">Kepala Toko / Store Manager</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- FOOTER HALAMAN -->
    <div class="page-footer">
        <div class="page-footer-left">
            JJ Mart Padang Bulan &bull; Toko Sembako Jayapura &bull; Dokumen Rahasia Toko (Internal Only) &bull; ID:
            {{ $no_dokumen ?? 'DOC-STK' }}
        </div>
        <div class="page-footer-right">
            Dicetak: {{ date('d/m/Y H:i:s') }} WIT
        </div>
    </div>

</body>

</html>
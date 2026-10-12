<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $judul_dokumen ?? 'Laporan Riwayat Transaksi' }} - JJ Mart</title>
    <style>
        @page {
            size: A4 portrait;
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
            width: 65px;
            padding-right: 12px;
        }

        .logo-badge {
            width: 55px;
            height: 55px;
            background: #0f766e;
            color: #ffffff;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            line-height: 55px;
            font-size: 15pt;
            letter-spacing: 1px;
        }

        .kop-text-cell {
            text-align: left;
        }

        .toko-nama {
            font-size: 15pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            line-height: 1.1;
        }

        .toko-tagline {
            font-size: 7.8pt;
            font-weight: 600;
            color: #0d9488;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 3px;
        }

        .toko-kontak {
            font-size: 7.2pt;
            color: #64748b;
            line-height: 1.3;
        }

        .kop-meta-cell {
            width: 200px;
            text-align: right;
            vertical-align: middle;
        }

        .doc-badge-pill {
            display: inline-block;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 7.2pt;
            font-weight: 700;
            color: #334155;
            margin-bottom: 3px;
        }

        .doc-no-text {
            font-size: 7.2pt;
            color: #64748b;
            font-family: 'Courier New', Courier, monospace;
        }

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
            font-size: 12.5pt;
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
            font-size: 7.6pt;
            border: none;
        }

        .meta-label {
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 6.8pt;
        }

        .meta-value {
            color: #1e293b;
            font-weight: 700;
        }

        /* ===== RINGKASAN EKSEKUTIF / KPI CARDS ===== */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            margin-bottom: 10px;
        }

        .kpi-card {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
            width: 25%;
        }

        .kpi-card.accent-teal {
            border-top: 3px solid #0d9488;
        }

        .kpi-card.accent-emerald {
            border-top: 3px solid #10b981;
        }

        .kpi-card.accent-amber {
            border-top: 3px solid #f59e0b;
        }

        .kpi-card.accent-blue {
            border-top: 3px solid #3b82f6;
        }

        .kpi-title {
            font-size: 6.8pt;
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
            font-size: 6.5pt;
            color: #64748b;
            margin-top: 2px;
        }

        /* ===== TABEL DATA UTAMA ===== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 7.8pt;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            padding: 5px 6px;
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
            padding: 4px 6px;
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

        /* Badge Transaksi */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 6.8pt;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
        }

        .badge-masuk {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .badge-keluar {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde047;
        }

        /* Footer Tabel */
        .data-table tfoot td {
            background-color: #f1f5f9;
            font-weight: 700;
            padding: 5px 6px;
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
            width: 48%;
            padding-right: 15px;
        }

        .notes-box {
            background: #f8fafc;
            border-left: 3px solid #0d9488;
            padding: 6px 8px;
            border-radius: 0 4px 4px 0;
            font-size: 7pt;
            color: #475569;
        }

        .notes-title {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
            text-transform: uppercase;
            font-size: 6.8pt;
        }

        .signatures-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signatures-table td {
            text-align: center;
            vertical-align: top;
            padding: 0 8px;
            border: none;
            width: 50%;
        }

        .sig-caption {
            font-size: 7.2pt;
            color: #475569;
            margin-bottom: 2px;
        }

        .sig-role {
            font-size: 7.8pt;
            font-weight: 700;
            color: #0f172a;
        }

        .sig-space {
            height: 44px;
        }

        .sig-name {
            font-size: 7.8pt;
            font-weight: 700;
            color: #0f172a;
            text-decoration: underline;
        }

        .sig-id {
            font-size: 6.8pt;
            color: #64748b;
        }

        .page-footer {
            margin-top: 15px;
            padding-top: 4px;
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
                <div class="doc-badge-pill">REKAPITULASI RESMI MUTASI</div>
                <div class="doc-no-text">No: {{ $no_dokumen ?? 'JJ/TRX/' . date('Ymd') . '/' . rand(100, 999) }}</div>
                <div class="doc-no-text">Status: Terverifikasi Sistem</div>
            </td>
        </tr>
    </table>

    <hr class="kop-divider">
    <hr class="kop-divider-thin">

    <!-- JUDUL LAPORAN -->
    <div class="header-title-box">
        <h1 class="doc-main-title">Laporan Riwayat Mutasi & Transaksi Toko Sembako</h1>
        <p class="doc-sub-title">Rekapitulasi Aliran Barang Masuk & Keluar Berdasarkan Periode Operasional</p>
    </div>

    <!-- METADATA STRIP -->
    <table class="meta-strip">
        <tr>
            <td>
                <span class="meta-label">Periode Transaksi:</span><br>
                <span class="meta-value">{{ $periode_teks ?? 'Bulan Berjalan' }}</span>
            </td>
            <td>
                <span class="meta-label">Tanggal Cetak:</span><br>
                <span class="meta-value">{{ $tanggal_cetak ?? date('d F Y, H:i') }} WIT</span>
            </td>
            <td>
                <span class="meta-label">Dicetak Oleh:</span><br>
                <span class="meta-value">{{ $dicetak_oleh ?? 'Manager JJ Mart Padang Bulan' }}</span>
            </td>
            <td>
                <span class="meta-label">Gudang / Outlet:</span><br>
                <span class="meta-value">JJ Mart Padang Bulan</span>
            </td>
        </tr>
    </table>

    <!-- RINGKASAN EKSEKUTIF / KPI -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-card accent-teal">
                <div class="kpi-title">Total Mutasi</div>
                <div class="kpi-value">{{ count($transaksi) }} <span
                        style="font-size:7.5pt; font-weight:normal; color:#64748b;">Record</span></div>
                <div class="kpi-sub">Total baris pergerakan</div>
            </td>
            <td class="kpi-card accent-emerald">
                <div class="kpi-title">Total Barang Masuk</div>
                <div class="kpi-value" style="color:#166534;">+{{ number_format($total_masuk ?? 0, 0, ',', '.') }}</div>
                <div class="kpi-sub">Dari restock / penerimaan</div>
            </td>
            <td class="kpi-card accent-amber">
                <div class="kpi-title">Total Barang Keluar</div>
                <div class="kpi-value" style="color:#b45309;">-{{ number_format($total_keluar ?? 0, 0, ',', '.') }}
                </div>
                <div class="kpi-sub">Penjualan / pengeluaran</div>
            </td>
            <td class="kpi-card accent-blue">
                <div class="kpi-title">Net Arus Stok</div>
                <div class="kpi-value">
                    @php $net = ($total_masuk ?? 0) - ($total_keluar ?? 0); @endphp
                    <span style="color: {{ $net >= 0 ? '#166534' : '#b91c1c' }};">
                        {{ $net > 0 ? '+' . $net : $net }}
                    </span>
                </div>
                <div class="kpi-sub">Selisih kuantitas bersih</div>
            </td>
        </tr>
    </table>

    <!-- TABEL DATA UTAMA -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th style="width: 80px;">Waktu & Tanggal</th>
                <th>Nama Barang & Kode</th>
                <th class="text-center" style="width: 55px;">Jenis</th>
                <th class="text-right" style="width: 55px;">Jumlah</th>
                <th class="text-right" style="width: 50px;">Sblm</th>
                <th class="text-right" style="width: 50px;">Ssdh</th>
                <th style="width: 80px;">No. Referensi</th>
                <th style="width: 80px;">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $idx => $t)
                <tr>
                    <td class="text-center text-muted">{{ $idx + 1 }}</td>
                    <td class="text-muted font-mono" style="font-size:7.2pt;">
                        {{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $t->barang?->nama ?? '—' }}</div>
                        <div class="font-mono text-muted" style="font-size:6.8pt;">{{ $t->barang?->kode_barang }}</div>
                    </td>
                    <td class="text-center">
                        @if($t->jenis === 'masuk')
                            <span class="badge badge-masuk">Masuk</span>
                        @else
                            <span class="badge badge-keluar">Keluar</span>
                        @endif
                    </td>
                    <td class="text-right fw-bold">
                        <span style="color: {{ $t->jenis === 'masuk' ? '#166534' : '#b45309' }};">
                            {{ $t->jenis === 'masuk' ? '+' : '-' }}{{ number_format($t->jumlah, 0, ',', '.') }}
                        </span>
                        <span class="text-muted" style="font-size:6.5pt;">{{ $t->barang?->satuan }}</span>
                    </td>
                    <td class="text-right text-muted font-mono">{{ $t->stok_sebelum }}</td>
                    <td class="text-right font-mono fw-bold">{{ $t->stok_sesudah }}</td>
                    <td class="font-mono text-muted" style="font-size:7pt;">{{ $t->referensi ?? '—' }}</td>
                    <td style="font-size:7.2pt;">{{ $t->pengguna?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted" style="padding: 20px;">
                        Tidak ada transaksi yang tercatat pada rentang tanggal ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right fw-bold" style="text-transform: uppercase;">
                    Total Kuantitas Masuk / Keluar:
                </td>
                <td class="text-right font-mono fw-bold">
                    <span style="color:#166534;">+{{ number_format($total_masuk ?? 0, 0, ',', '.') }}</span> /
                    <span style="color:#b45309;">-{{ number_format($total_keluar ?? 0, 0, ',', '.') }}</span>
                </td>
                <td colspan="4" class="text-muted" style="font-size:7pt;">
                    Total Baris: {{ count($transaksi) }} transaksi
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- CATATAN & PENGESAHAN DOKUMEN -->
    <table class="notes-signatures-wrap">
        <tr>
            <td class="notes-cell">
                <div class="notes-box">
                    <div class="notes-title">Catatan Operasional Mutasi:</div>
                    1. Seluruh transaksi terekam otomatis dalam basis data log audit sistem inventaris.<br>
                    2. Perubahan stok langsung merefleksikan kondisi fisik di area penyimpanan/rak.<br>
                    3. No. Referensi merujuk pada Surat Jalan Pemasok atau Nota Pengeluaran Kasir.
                </div>
            </td>
            <td style="width: 52%;">
                <table class="signatures-table">
                    <tr>
                        <td>
                            <div class="sig-caption">Diverifikasi Oleh,</div>
                            <div class="sig-role">Staff / Petugas Gudang</div>
                            <div class="sig-space"></div>
                            <div class="sig-name">{{ $petugas_gudang ?? 'Staff Toko JJ Mart' }}</div>
                            <div class="sig-id">Pelaksana Operasional</div>
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
            JJ Mart Padang Bulan &bull; Toko Sembako Jayapura &bull; Laporan Rekapitulasi Mutasi Resmi &bull; ID:
            {{ $no_dokumen ?? 'DOC-TRX' }}
        </div>
        <div class="page-footer-right">
            Dicetak: {{ date('d/m/Y H:i:s') }} WIT
        </div>
    </div>

</body>

</html>
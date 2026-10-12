<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $judul_dokumen ?? 'Rekomendasi Pemesanan Reorder Point' }} - JJ Mart</title>
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
            width: 230px;
            text-align: right;
            vertical-align: middle;
        }

        .doc-badge-pill {
            display: inline-block;
            background: #fef2f2;
            border: 1px solid #fecaca;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: 700;
            color: #991b1b;
            margin-bottom: 3px;
        }

        .doc-no-text {
            font-size: 7.5pt;
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

        .kpi-card.accent-rose {
            border-top: 3px solid #e11d48;
        }

        .kpi-card.accent-amber {
            border-top: 3px solid #d97706;
        }

        .kpi-card.accent-teal {
            border-top: 3px solid #0d9488;
        }

        .kpi-card.accent-indigo {
            border-top: 3px solid #4f46e5;
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

        /* Badge Urgensi */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 6.8pt;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
        }

        .badge-kritis {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde047;
        }

        .badge-habis {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

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
            border-left: 3px solid #e11d48;
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
                <div class="doc-badge-pill">SURAT REKOMENDASI PENGADAAN</div>
                <div class="doc-no-text">No: {{ $no_dokumen ?? 'JJ/ROP/' . date('Ymd') . '/' . rand(100, 999) }}</div>
                <div class="doc-no-text">Prioritas: Mendesak (Kritis)</div>
            </td>
        </tr>
    </table>

    <hr class="kop-divider">
    <hr class="kop-divider-thin">

    <!-- JUDUL LAPORAN -->
    <div class="header-title-box">
        <h1 class="doc-main-title">Surat Rekomendasi Pemesanan Ulang (Reorder Point)</h1>
        <p class="doc-sub-title">Daftar Sembako Mencapai Titik Kritis yang Direkomendasikan Segera Dipesan Ulang
            (PO)</p>
    </div>

    <!-- METADATA STRIP -->
    <table class="meta-strip">
        <tr>
            <td>
                <span class="meta-label">Tanggal Usulan:</span><br>
                <span class="meta-value">{{ $tanggal_cetak ?? date('d F Y, H:i') }} WIT</span>
            </td>
            <td>
                <span class="meta-label">Disusun Oleh:</span><br>
                <span class="meta-value">{{ $dicetak_oleh ?? 'Staff Toko / Purchasing' }}</span>
            </td>
            <td>
                <span class="meta-label">Status Urgensi:</span><br>
                <span class="meta-value" style="color:#991b1b;">Tinggi (Mendekati / Di Bawah ROP)</span>
            </td>
            <td>
                <span class="meta-label">Unit Pengadaan:</span><br>
                <span class="meta-value">Logistik JJ Mart Padang Bulan</span>
            </td>
        </tr>
    </table>

    <!-- RINGKASAN EKSEKUTIF / KPI -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-card accent-rose">
                <div class="kpi-title">Item Perlu Reorder</div>
                <div class="kpi-value" style="color:#991b1b;">{{ count($barang) }} <span
                        style="font-size:7.5pt; font-weight:normal; color:#64748b;">Barang</span></div>
                <div class="kpi-sub">Stok &le; Reorder Point</div>
            </td>
            <td class="kpi-card accent-amber">
                <div class="kpi-title">Total Kebutuhan Order</div>
                <div class="kpi-value" style="color:#b45309;">+{{ number_format($total_kebutuhan ?? 0, 0, ',', '.') }}
                    <span style="font-size:7.5pt; font-weight:normal; color:#64748b;">Unit</span></div>
                <div class="kpi-sub">Total kuantitas defisit ROP</div>
            </td>
            <td class="kpi-card accent-teal">
                <div class="kpi-title">Estimasi Anggaran PO</div>
                <div class="kpi-value" style="color:#0f766e;">Rp {{ number_format($total_anggaran ?? 0, 0, ',', '.') }}
                </div>
                <div class="kpi-sub">Kebutuhan x Estimasi HPP</div>
            </td>
            <td class="kpi-card accent-indigo">
                <div class="kpi-title">Mitra Pemasok</div>
                <div class="kpi-value">{{ $total_pemasok ?? 0 }} <span
                        style="font-size:7.5pt; font-weight:normal; color:#64748b;">Vendor</span></div>
                <div class="kpi-sub">Pemasok utama terdaftar</div>
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
                <th style="width: 120px;">Pemasok / Distributor</th>
                <th class="text-right" style="width: 60px;">Stok Fisik</th>
                <th class="text-right" style="width: 50px;">ROP</th>
                <th class="text-right" style="width: 70px;">Order (+Unit)</th>
                <th class="text-right" style="width: 85px;">Estimasi HPP</th>
                <th class="text-right" style="width: 95px;">Estimasi Anggaran</th>
                <th class="text-center" style="width: 55px;">Lead Time</th>
                <th class="text-center" style="width: 75px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barang as $idx => $b)
                @php
                    $kekurangan = is_array($b) ? ($b['kekurangan'] ?? 0) : ($b->kekurangan ?? 0);
                    $hargaBeli = is_array($b) ? ($b['harga_beli'] ?? 0) : ($b->harga_beli ?? 0);
                    $subtotal = $kekurangan * $hargaBeli;
                    $status = is_array($b) ? ($b['status_stok'] ?? 'PERLU_PEMESANAN') : ($b->status_stok ?? 'PERLU_PEMESANAN');
                @endphp
                <tr>
                    <td class="text-center text-muted">{{ $idx + 1 }}</td>
                    <td class="font-mono fw-semibold">{{ is_array($b) ? $b['kode_barang'] : $b->kode_barang }}</td>
                    <td class="fw-semibold">{{ is_array($b) ? $b['nama'] : $b->nama }}</td>
                    <td>{{ is_array($b) ? ($b['pemasok'] ?? '—') : ($b->pemasok?->nama ?? '—') }}</td>
                    <td class="text-right fw-bold"
                        style="color: {{ (is_array($b) ? $b['stok_saat_ini'] : $b->stok_saat_ini) <= 0 ? '#991b1b' : '#0f172a' }};">
                        {{ number_format(is_array($b) ? $b['stok_saat_ini'] : $b->stok_saat_ini, 0, ',', '.') }}
                    </td>
                    <td class="text-right text-muted">{{ is_array($b) ? $b['rop'] : $b->rop }}</td>
                    <td class="text-right fw-bold" style="color:#b91c1c;">
                        +{{ number_format($kekurangan, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-mono">Rp {{ number_format($hargaBeli, 0, ',', '.') }}</td>
                    <td class="text-right font-mono fw-bold" style="color:#0f766e;">Rp
                        {{ number_format($subtotal, 0, ',', '.') }}</td>
                    <td class="text-center text-muted">{{ is_array($b) ? $b['lead_time_hari'] : $b->lead_time_hari }} hr</td>
                    <td class="text-center">
                        @if($status === 'HABIS')
                            <span class="badge badge-habis">Habis (0)</span>
                        @elseif($status === 'PERLU_PEMESANAN')
                            <span class="badge badge-kritis">Mendesak</span>
                        @else
                            <span class="badge badge-warning">Waspada</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center text-muted" style="padding: 25px;">
                        Alhamdulillah, semua stok barang dalam kondisi aman di atas titik Reorder Point (ROP).
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right fw-bold" style="text-transform: uppercase;">
                    Total Rekomendasi Pemesanan ({{ count($barang) }} Item):
                </td>
                <td class="text-right font-mono fw-bold" style="color:#b91c1c;">
                    +{{ number_format($total_kebutuhan ?? 0, 0, ',', '.') }} Unit
                </td>
                <td class="text-center text-muted">—</td>
                <td class="text-right font-mono fw-bold" style="color:#0f766e;">
                    Rp {{ number_format($total_anggaran ?? 0, 0, ',', '.') }}
                </td>
                <td colspan="2" class="text-center text-muted">—</td>
            </tr>
        </tfoot>
    </table>

    <!-- CATATAN & PENGESAHAN DOKUMEN -->
    <table class="notes-signatures-wrap">
        <tr>
            <td class="notes-cell">
                <div class="notes-box">
                    <div class="notes-title">Instruksi & Prosedur Pengadaan Toko:</div>
                    1. Reorder Point (ROP) = (Rata-rata Pemakaian Harian &times; Lead Time) + Safety Stock.<br>
                    2. Jumlah rekomendasi order merupakan estimasi minimal untuk mengembalikan stok ke batas
                    optimal.<br>
                    3. Setelah disetujui Kepala Toko, harap segera menerbitkan Purchase Order (PO) ke pemasok
                    terkait.<br>
                    4. Konfirmasi estimasi waktu pengiriman agar stok tidak sampai mengalami kehabisan (stockout).
                </div>
            </td>
            <td style="width: 50%;">
                <table class="signatures-table">
                    <tr>
                        <td>
                            <div class="sig-caption">Diajukan Oleh,</div>
                            <div class="sig-role">Staff Toko / Purchasing</div>
                            <div class="sig-space"></div>
                            <div class="sig-name">{{ $petugas_gudang ?? 'Staff Toko JJ Mart' }}</div>
                            <div class="sig-id">Bagian Pengadaan & Stok</div>
                        </td>
                        <td>
                            <div class="sig-caption">Jayapura, {{ $tanggal_pengesahan ?? date('d F Y') }}</div>
                            <div class="sig-role">Disetujui untuk PO,</div>
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
            JJ Mart Padang Bulan &bull; Toko Sembako Jayapura &bull; Dokumen Usulan Pengadaan Resmi &bull; ID:
            {{ $no_dokumen ?? 'DOC-ROP' }}
        </div>
        <div class="page-footer-right">
            Dicetak: {{ date('d/m/Y H:i:s') }} WIT
        </div>
    </div>

</body>

</html>
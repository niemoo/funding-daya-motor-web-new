<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draft Pembelian — {{ $attendance->store_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #1a202c;
            background: #fff;
            padding: 40px 48px;
        }

        /* ── Header ── */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 32px;
            border-bottom: 2px solid #1D61AF;
            padding-bottom: 20px;
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
            text-align: right;
        }

        .logo {
            height: 52px;
            width: auto;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #1D61AF;
            letter-spacing: -0.5px;
        }

        .invoice-date {
            font-size: 11px;
            color: #718096;
            margin-top: 4px;
        }

        /* ── Info Section ── */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 28px;
        }

        .info-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 20px;
            border-left: 1px solid #E2E8F0;
        }

        .info-header {
            font-size: 11px;
            font-weight: bold;
            color: #1D61AF;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #EBF3FC;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .info-label {
            display: table-cell;
            width: 90px;
            font-size: 11px;
            color: #718096;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            font-size: 11px;
            color: #1a202c;
            font-weight: 600;
            vertical-align: top;
        }

        /* ── Table ── */
        .table-section {
            margin-bottom: 28px;
        }

        .table-title {
            font-size: 11px;
            font-weight: bold;
            color: #1D61AF;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background-color: #1D61AF;
        }

        thead th {
            padding: 9px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.3px;
        }

        thead th.center {
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #EBF3FC;
        }

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        tbody td {
            padding: 8px 12px;
            font-size: 11px;
            color: #2D3748;
            border-bottom: 1px solid #EDF2F7;
        }

        tbody td.center {
            text-align: center;
        }

        tbody td.mono {
            font-family: monospace;
            font-size: 11px;
        }

        .empty-row td {
            padding: 20px 12px;
            text-align: center;
            color: #A0AEC0;
            font-style: italic;
        }

        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1.5px solid #CBD5E0;
            margin: 28px 0;
        }

        /* ── Approval ── */
        .approval-note {
            font-size: 11px;
            color: #4A5568;
            margin-bottom: 32px;
            font-style: italic;
        }

        .signature-section {
            display: table;
            width: 100%;
        }

        .signature-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 12px;
            vertical-align: top;
        }

        .signature-title {
            font-size: 11px;
            font-weight: bold;
            color: #2D3748;
            margin-bottom: 100px;
        }

        .signature-line {
            /* border-top: 1px solid #2D3748; */
            padding-top: 6px;
            font-size: 10px;
            color: #718096;
        }

        /* ── Footer ── */
        .footer {
            margin-top: 32px;
            padding-top: 12px;
            border-top: 1px solid #EDF2F7;
            text-align: center;
            font-size: 10px;
            color: #A0AEC0;
        }
    </style>
</head>

<body>

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="header-left">
            @php
                $path = public_path('assets/optipart_invoice.png');
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            @endphp

            <img src="{{ $base64 }}">
        </div>
        <div class="header-right">
            <div class="invoice-title">Draft Pembelian</div>
            <div class="invoice-date">
                Tanggal: {{ now()->locale('id')->isoFormat('D MMMM Y') }}
            </div>
            <div class="invoice-date" style="margin-top: 2px;">
                No. Kunjungan: #{{ str_pad($attendance->id, 5, '0', STR_PAD_LEFT) }}
            </div>
        </div>
    </div>

    {{-- ── INFO SECTION ── --}}
    <div class="info-section">
        <div class="info-left">
            <div class="info-header">Tujuan Pengiriman</div>
            <div class="info-row">
                <div class="info-label">Nama Toko</div>
                <div class="info-value">: {{ $attendance->store_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">PIC</div>
                <div class="info-value">: {{ $attendance->person_in_charge_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">No. Telepon</div>
                <div class="info-value">: {{ $attendance->person_in_charge_phone }}</div>
            </div>
        </div>
        <div class="info-right">
            <div class="info-header">Nama Sales</div>
            <div class="info-row">
                <div class="info-label">Pengirim</div>
                <div class="info-value">: {{ $attendance->user?->name ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- ── PAYMENT INFO ── --}}
    <div style="margin-bottom: 28px;">
        <div
            style="
        font-size: 11px;
        font-weight: bold;
        color: #1D61AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    ">
            Informasi Pembayaran
        </div>

        <div style="font-size: 11px; color: #2D3748;">
            Jenis Pembayaran:
            <span style="font-weight: 600;">
                {{ $attendance->jenis_pembayaran ?? '-' }}
            </span>
        </div>
    </div>

    {{-- ── TABLE ── --}}
    <div class="table-section">
        <div class="table-title">Daftar Part yang Dipesan</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;" class="center">No</th>
                    <th style="width: 130px;">Kode Part</th>
                    <th>Deskripsi Part</th>
                    <th style="width: 100px;">Group</th>
                    <th style="width: 60px;" class="center">Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendance->items as $i => $item)
                    @php $part = $partsMap[$item->part_number] ?? null; @endphp
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td class="mono">{{ $item->part_number }}</td>
                        <td>{{ $part?->deskripsi_part ?? '—' }}</td>
                        <td>{{ $part?->group?->name ?? '—' }}</td>
                        <td class="center">{{ $item->quantity }}</td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="5">Tidak ada part yang dipesan</td>
                    </tr>
                @endforelse
            </tbody>
            {{-- <tbody>
                @forelse($attendance->items as $i => $item)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>
                            @php
                                $part = $partsMap[$item->part_number] ?? null;
                            @endphp
                        <td class="mono">{{ $item->part_number }}</td>
                        <td>{{ $part?->deskripsi_part ?? '—' }}</td>
                        <td>{{ $part?->group?->name ?? '—' }}</td>
                        <td class="center">{{ $item->quantity }}</td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="5">Tidak ada part yang dipesan</td>
                    </tr>
                @endforelse
            </tbody> --}}
        </table>
    </div>

    {{-- ── DIVIDER ── --}}
    <hr class="divider">

    {{-- ── APPROVAL ── --}}
    <div class="approval-note">
        Draft Pembelian ini telah diperiksa dan divalidasi oleh:
    </div>

    <div class="signature-section">
        <div class="signature-col">
            <div class="signature-title">Admin Depo</div>
            <div class="signature-line">(__________________________________________)</div>
        </div>
        <div class="signature-col">
            <div class="signature-title">Kepala Bengkel</div>
            <div class="signature-line">(__________________________________________)</div>
        </div>
        <div class="signature-col">
            <div class="signature-title">Administration Head / Kepala Cabang</div>
            <div class="signature-line">(__________________________________________)</div>
        </div>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        Dokumen ini digenerate otomatis oleh sistem OptiPart &bull; {{ now()->format('d/m/Y H:i') }} WIB
    </div>

</body>

</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan {{ $jenisList[$jenis] }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
        h4 { text-align: center; margin-bottom: 5px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background-color: #f0f0f0; }
        .text-end { text-align: right; }
        .btn-print { display: inline-block; margin: 10px 0; padding: 6px 16px; background: #007bff; color: #fff; border: none; border-radius: 3px; cursor: pointer; }
        @media print { .btn-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <h4>Syaklinik</h4>
        <p><strong>Laporan: {{ $jenisList[$jenis] }}</strong></p>
        <p>Periode: {{ $dari ? \Carbon\Carbon::parse($dari)->format('d/m/Y') : 'Semua' }} — {{ $sampai ? \Carbon\Carbon::parse($sampai)->format('d/m/Y') : 'Semua' }}</p>
        <p>Dicetak: {{ now()->format('d/m/Y H:i') }} | Total: {{ $ringkasan['total_baris'] }} baris
            @if ($ringkasan['total_biaya'] > 0)
                | Pendapatan: Rp {{ number_format($ringkasan['total_biaya'], 0, ',', '.') }}
            @endif
        </p>
    </div>

    <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>

    <table>
        @if ($jenis === 'pasien')
            <thead>
                <tr><th>No</th><th>No. RM</th><th>NIK</th><th>Nama</th><th>JK</th><th>Tgl Lahir</th><th>Telepon</th></tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $r)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $r->nomor_rekam_medis }}</td><td>{{ $r->nik }}</td><td>{{ $r->nama_pasien }}</td><td>{{ $r->jenis_kelamin }}</td><td>{{ \Carbon\Carbon::parse($r->tanggal_lahir)->format('d/m/Y') }}</td><td>{{ $r->nomor_telepon }}</td></tr>
                @endforeach
            </tbody>

        @elseif ($jenis === 'dokter')
            <thead>
                <tr><th>No</th><th>Nama</th><th>Spesialisasi</th><th>Telepon</th></tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $r)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $r->nama_dokter }}</td><td>{{ $r->spesialisasi }}</td><td>{{ $r->nomor_telepon }}</td></tr>
                @endforeach
            </tbody>

        @elseif ($jenis === 'pendaftaran')
            <thead>
                <tr><th>No</th><th>No. Pendaftaran</th><th>Tanggal</th><th>Pasien</th><th>Dokter</th><th>Keluhan</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $r)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $r->nomor_pendaftaran }}</td><td>{{ \Carbon\Carbon::parse($r->tanggal_kunjungan)->format('d/m/Y') }}</td><td>{{ $r->pasien->nama_pasien ?? '-' }}</td><td>{{ $r->dokter->nama_dokter ?? '-' }}</td><td>{{ $r->keluhan }}</td><td>{{ $r->status }}</td></tr>
                @endforeach
            </tbody>

        @elseif ($jenis === 'antrean')
            <thead>
                <tr><th>No</th><th>No. Antrean</th><th>Tanggal</th><th>Pasien</th><th>Dokter</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $r)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $r->nomor_antrean }}</td><td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td><td>{{ $r->pendaftaran->pasien->nama_pasien ?? '-' }}</td><td>{{ $r->pendaftaran->jadwal->dokter->nama_dokter ?? '-' }}</td><td>{{ $r->status }}</td></tr>
                @endforeach
            </tbody>

        @elseif ($jenis === 'pemeriksaan')
            <thead>
                <tr><th>No</th><th>Tanggal</th><th>Pasien</th><th>Dokter</th><th>Diagnosis</th><th>Tindakan</th></tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $r)
                    <tr><td>{{ $i + 1 }}</td><td>{{ \Carbon\Carbon::parse($r->tanggal_pemeriksaan)->format('d/m/Y') }}</td><td>{{ $r->pasien->nama_pasien ?? '-' }}</td><td>{{ $r->dokter->nama_dokter ?? '-' }}</td><td>{{ $r->diagnosis }}</td><td>{{ $r->tindakans->pluck('nama_tindakan')->implode(', ') }}</td></tr>
                @endforeach
            </tbody>

        @elseif ($jenis === 'rekam_medis')
            <thead>
                <tr><th>No</th><th>Tanggal</th><th>Pasien</th><th>Keluhan</th><th>Diagnosis</th><th>Tindakan</th></tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $r)
                    <tr><td>{{ $i + 1 }}</td><td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td><td>{{ $r->pasien->nama_pasien ?? '-' }}</td><td>{{ $r->keluhan }}</td><td>{{ $r->diagnosis }}</td><td>{{ $r->tindakan }}</td></tr>
                @endforeach
            </tbody>

        @elseif ($jenis === 'obat')
            <thead>
                <tr><th>No</th><th>Kode</th><th>Nama</th><th>Satuan</th><th>Harga</th><th>Stok</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $r)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $r->kode_obat }}</td><td>{{ $r->nama_obat }}</td><td>{{ $r->satuan }}</td><td>Rp {{ number_format($r->harga, 0, ',', '.') }}</td><td>{{ $r->stok }}</td><td>{{ $r->status }}</td></tr>
                @endforeach
            </tbody>

        @elseif ($jenis === 'pembayaran')
            <thead>
                <tr><th>No</th><th>No. Pembayaran</th><th>Tanggal</th><th>Pasien</th><th>Total</th><th>Bayar</th><th>Kembalian</th></tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $r)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $r->nomor_pembayaran }}</td><td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td><td>{{ $r->pasien->nama_pasien ?? '-' }}</td><td>Rp {{ number_format($r->total, 0, ',', '.') }}</td><td>Rp {{ number_format($r->bayar, 0, ',', '.') }}</td><td>Rp {{ number_format($r->kembalian, 0, ',', '.') }}</td></tr>
                @endforeach
            </tbody>
        @endif
    </table>

    <p style="margin-top:15px; font-size:11px; text-align:right;">Dokumen dicetak otomatis {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
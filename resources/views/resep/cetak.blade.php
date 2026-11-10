<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Resep Obat - Syaklinik</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-end { text-align: right; }
        .btn-print { display: inline-block; margin-bottom: 10px; padding: 6px 16px; background: #007bff; color: #fff; border: none; border-radius: 3px; cursor: pointer; }
        @media print { .btn-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <h4>Syaklinik</h4>
        <p><strong>Data Resep Obat</strong></p>
        <p>
            Periode: {{ $dari ? \Carbon\Carbon::parse($dari)->format('d/m/Y') : 'Semua' }} —
            {{ $sampai ? \Carbon\Carbon::parse($sampai)->format('d/m/Y') : 'Semua' }}
            @if ($keyword) | Pencarian: {{ $keyword }} @endif
        </p>
        <p>Total: {{ $resep->count() }} resep | Total Nilai: Rp {{ number_format($totalHarga, 0, ',', '.') }} | Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>

    <table>
        <thead>
            <tr><th>No</th><th>Tanggal</th><th>Pasien</th><th>Dokter</th><th>Obat</th><th>Total</th><th>Status</th></tr>
        </thead>
        <tbody>
            @foreach ($resep as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $r->pasien->nama_pasien ?? '-' }}<br><small>RM: {{ $r->pasien->nomor_rekam_medis ?? '-' }}</small></td>
                    <td>{{ $r->dokter->nama_dokter ?? '-' }}</td>
                    <td>
                        @foreach ($r->detail as $d)
                            {{ $d->obat->nama_obat ?? '-' }} x{{ $d->jumlah }}<br>
                        @endforeach
                    </td>
                    <td class="text-end">Rp {{ number_format($r->detail->sum('subtotal'), 0, ',', '.') }}</td>
                    <td>{{ $r->status === 'ditebus' ? 'Sudah Ditebus' : 'Belum Ditebus' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top:15px; font-size:11px; text-align:right;">Dokumen dicetak otomatis {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Pembayaran - Syaklinik</title>
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
        <p><strong>Data Pembayaran</strong></p>
        <p>
            Periode: {{ $dari ? \Carbon\Carbon::parse($dari)->format('d/m/Y') : 'Semua' }} —
            {{ $sampai ? \Carbon\Carbon::parse($sampai)->format('d/m/Y') : 'Semua' }}
            @if ($keyword) | Pencarian: {{ $keyword }} @endif
        </p>
        <p>Total: {{ $pembayaran->count() }} transaksi | Pendapatan (lunas): Rp {{ number_format($totalPembayaran, 0, ',', '.') }} | Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>

    <table>
        <thead>
            <tr><th>No</th><th>No. Pembayaran</th><th>Tanggal</th><th>Pasien</th><th>Total</th><th>Bayar</th><th>Kembalian</th><th>Status</th></tr>
        </thead>
        <tbody>
            @foreach ($pembayaran as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->nomor_pembayaran }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $p->pasien->nama_pasien ?? '-' }}</td>
                    <td class="text-end">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($p->bayar, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($p->kembalian, 0, ',', '.') }}</td>
                    <td>{{ $p->status === 'lunas' ? 'Lunas' : 'Belum Lunas' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top:15px; font-size:11px; text-align:right;">Dokumen dicetak otomatis {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
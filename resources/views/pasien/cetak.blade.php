<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Data Pasien - Syaklinik</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        .btn-print { display: inline-block; margin-bottom: 10px; padding: 6px 16px; background: #007bff; color: #fff; border: none; border-radius: 3px; cursor: pointer; }
        @media print { .btn-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <h4>Syaklinik</h4>
        <p><strong>Data Pasien</strong></p>
        @if ($keyword)
            <p>Pencarian: {{ $keyword }}</p>
        @endif
        <p>Total: {{ $pasien->count() }} pasien | Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>

    <table>
        <thead>
            <tr><th>No</th><th>No. RM</th><th>NIK</th><th>Nama</th><th>JK</th><th>Tgl Lahir</th><th>Telepon</th><th>Alamat</th></tr>
        </thead>
        <tbody>
            @foreach ($pasien as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->nomor_rekam_medis }}</td>
                    <td>{{ $p->nik }}</td>
                    <td>{{ $p->nama_pasien }}</td>
                    <td>{{ $p->jenis_kelamin }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggal_lahir)->format('d/m/Y') }}</td>
                    <td>{{ $p->nomor_telepon }}</td>
                    <td>{{ $p->alamat }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top:15px; font-size:11px; text-align:right;">Dokumen dicetak otomatis {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
@extends('layouts.app')
@section('title', 'Laporan')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0">Laporan Klinik</h4>
            <span class="text-secondary">Pilih jenis laporan & periode (boleh dikosongkan)</span>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Jenis Laporan</label>
            <select name="jenis" class="form-select" onchange="this.form.submit()">
                @foreach ($jenisList as $k => $v)
                    <option value="{{ $k }}" {{ $jenis === $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Dari</label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Sampai</label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-control">
        </div>
        <div class="col-auto d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-dark text-white mb-0">Tampilkan</button>
            <a href="{{ route('laporan.data') }}" class="btn btn-secondary mb-0">Reset</a>
            <a href="{{ route('laporan.cetak', ['jenis' => $jenis, 'dari' => $dari, 'sampai' => $sampai]) }}" target="_blank" class="btn btn-warning mb-0">Cetak</a>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0 text-primary">{{ $jenisList[$jenis] }}</h5>
        <span class="badge bg-dark">Total: {{ $ringkasan['total_baris'] }} baris
            @if ($ringkasan['total_biaya'] > 0)
                | Pendapatan: Rp {{ number_format($ringkasan['total_biaya'], 0, ',', '.') }}
            @endif
        </span>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-sm mb-0">

                @if ($jenis === 'pasien')
                    <thead class="table-dark">
                        <tr><th>No</th><th>No. RM</th><th>NIK</th><th>Nama</th><th>JK</th><th>Tgl Lahir</th><th>Telepon</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $i => $r)
                            <tr><td>{{ $i + 1 }}</td><td>{{ $r->nomor_rekam_medis }}</td><td>{{ $r->nik }}</td><td>{{ $r->nama_pasien }}</td><td>{{ $r->jenis_kelamin }}</td><td>{{ \Carbon\Carbon::parse($r->tanggal_lahir)->format('d/m/Y') }}</td><td>{{ $r->nomor_telepon }}</td></tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>

                @elseif ($jenis === 'dokter')
                    <thead class="table-dark">
                        <tr><th>No</th><th>Nama</th><th>Spesialisasi</th><th>Telepon</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $i => $r)
                            <tr><td>{{ $i + 1 }}</td><td>{{ $r->nama_dokter }}</td><td>{{ $r->spesialisasi }}</td><td>{{ $r->nomor_telepon }}</td><td><span class="badge bg-{{ $r->status === 'aktif' ? 'success' : 'secondary' }}">{{ $r->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}</span></td></tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>

                @elseif ($jenis === 'pendaftaran')
                    <thead class="table-dark">
                        <tr><th>No</th><th>No. Pendaftaran</th><th>Tanggal</th><th>Pasien</th><th>Dokter</th><th>Keluhan</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $i => $r)
                            <tr><td>{{ $i + 1 }}</td><td>{{ $r->nomor_pendaftaran }}</td><td>{{ \Carbon\Carbon::parse($r->tanggal_kunjungan)->format('d/m/Y') }}</td><td>{{ $r->pasien->nama_pasien ?? '-' }}</td><td>{{ $r->dokter->nama_dokter ?? '-' }}</td><td><small>{{ Str::limit($r->keluhan ?? '-', 35) }}</small></td><td><span class="badge bg-{{ $r->status === 'batal' ? 'danger' : 'success' }}">{{ ucfirst($r->status) }}</span></td></tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>

                @elseif ($jenis === 'antrean')
                    <thead class="table-dark">
                        <tr><th>No</th><th>No. Antrean</th><th>Tanggal</th><th>Pasien</th><th>Dokter</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $i => $r)
                            <tr><td>{{ $i + 1 }}</td><td>{{ $r->nomor_antrean }}</td><td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td><td>{{ $r->pendaftaran->pasien->nama_pasien ?? '-' }}</td><td>{{ $r->pendaftaran->jadwal->dokter->nama_dokter ?? '-' }}</td><td><span class="badge bg-{{ $r->status === 'menunggu' ? 'warning text-dark' : ($r->status === 'dipanggil' ? 'info' : ($r->status === 'diperiksa' ? 'primary' : ($r->status === 'selesai' ? 'success' : 'danger'))) }}">{{ ucfirst($r->status) }}</span></td></tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>

                @elseif ($jenis === 'pemeriksaan')
                    <thead class="table-dark">
                        <tr><th>No</th><th>Tanggal</th><th>Pasien</th><th>Dokter</th><th>Diagnosis</th><th>Tindakan</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $i => $r)
                            <tr><td>{{ $i + 1 }}</td><td>{{ \Carbon\Carbon::parse($r->tanggal_pemeriksaan)->format('d/m/Y') }}</td><td>{{ $r->pasien->nama_pasien ?? '-' }}</td><td>{{ $r->dokter->nama_dokter ?? '-' }}</td><td><small>{{ Str::limit($r->diagnosis ?? '-', 35) }}</small></td><td><small>{{ Str::limit($r->tindakans->pluck('nama_tindakan')->implode(', ') ?: '-', 35) }}</small></td></tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>

                @elseif ($jenis === 'rekam_medis')
                    <thead class="table-dark">
                        <tr><th>No</th><th>Tanggal</th><th>Pasien</th><th>Keluhan</th><th>Diagnosis</th><th>Tindakan</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $i => $r)
                            <tr><td>{{ $i + 1 }}</td><td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td><td>{{ $r->pasien->nama_pasien ?? '-' }}</td><td><small>{{ Str::limit($r->keluhan ?? '-', 30) }}</small></td><td><small>{{ Str::limit($r->diagnosis ?? '-', 30) }}</small></td><td><small>{{ Str::limit($r->tindakan ?? '-', 30) }}</small></td></tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>

                @elseif ($jenis === 'obat')
                    <thead class="table-dark">
                        <tr><th>No</th><th>Kode</th><th>Nama</th><th>Satuan</th><th>Harga</th><th>Stok</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $i => $r)
                            <tr><td>{{ $i + 1 }}</td><td>{{ $r->kode_obat }}</td><td>{{ $r->nama_obat }}</td><td>{{ $r->satuan }}</td><td>Rp {{ number_format($r->harga, 0, ',', '.') }}</td><td>{{ $r->stok }}</td><td><span class="badge bg-{{ $r->status === 'tersedia' ? 'success' : ($r->status === 'habis' ? 'danger' : 'warning text-dark') }}">{{ ucfirst($r->status) }}</span></td></tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>

                @elseif ($jenis === 'pembayaran')
                    <thead class="table-dark">
                        <tr><th>No</th><th>No. Pembayaran</th><th>Tanggal</th><th>Pasien</th><th>Total</th><th>Bayar</th><th>Kembalian</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $i => $r)
                            <tr><td>{{ $i + 1 }}</td><td>{{ $r->nomor_pembayaran }}</td><td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td><td>{{ $r->pasien->nama_pasien ?? '-' }}</td><td>Rp {{ number_format($r->total, 0, ',', '.') }}</td><td>Rp {{ number_format($r->bayar, 0, ',', '.') }}</td><td>Rp {{ number_format($r->kembalian, 0, ',', '.') }}</td></tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                @endif
            </table>
        </div>
    </div>
@endsection
@extends('layouts.app')
@section('title', 'Detail Rekam Medis')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Detail Rekam Medis</h4>
        <a href="{{ route('rekam-medis.data') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><strong>Nama</strong><br>{{ $pasien->nama_pasien }}</div>
                <div class="col-md-3"><strong>No. Rekam Medis</strong><br>{{ $pasien->nomor_rekam_medis }}</div>
                <div class="col-md-3"><strong>NIK</strong><br>{{ $pasien->nik }}</div>
                <div class="col-md-3"><strong>Telepon</strong><br>{{ $pasien->nomor_telepon }}</div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white pt-3">
            <h6 class="mb-0 fw-bold">Riwayat Pelayanan</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>Dokter</th>
                        <th>Keluhan</th>
                        <th>Diagnosis</th>
                        <th>Tindakan</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekamMedis as $rm)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($rm->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $rm->pemeriksaan->dokter->nama_dokter ?? '-' }}</td>
                            <td><small>{{ $rm->keluhan ?? '-' }}</small></td>
                            <td><small>{{ $rm->diagnosis ?? '-' }}</small></td>
                            <td>
                                <small>
                                    @if ($rm->pemeriksaan && $rm->pemeriksaan->tindakans->isNotEmpty())
                                        {{ $rm->pemeriksaan->tindakans->pluck('nama_tindakan')->implode(', ') }}
                                    @else
                                        {{ $rm->tindakan ?? '-' }}
                                    @endif
                                </small>
                            </td>
                            <td><small>{{ $rm->catatan ?? '-' }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">Pasien ini belum memiliki riwayat pelayanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
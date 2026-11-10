@extends('layouts.app')
@section('title', 'Rekam Medis')
@section('content')
    <h4 class="fw-bold mb-3">Rekam Medis</h4>

    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama pasien, NIK, atau no. rekam medis">
        </div>
        <div class="col-md-3">
            <input type="date" name="dari" value="{{ request('dari') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control">
        </div>
        <div class="col-auto d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-dark text-white mb-0">Filter</button>
            <a href="{{ route('rekam-medis.data') }}" class="btn btn-secondary mb-0">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Keluhan</th>
                        <th>Diagnosis</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekamMedis as $i => $rm)
                        <tr>
                            <td>{{ $rekamMedis->firstItem() + $i }}</td>
                            <td>{{ \Carbon\Carbon::parse($rm->tanggal)->format('d/m/Y') }}</td>
                            <td>
                                {{ $rm->pasien->nama_pasien ?? '-' }}<br>
                                <small class="text-secondary">{{ $rm->pasien->nomor_rekam_medis ?? '' }}</small>
                            </td>
                            <td>{{ $rm->pemeriksaan->dokter->nama_dokter ?? '-' }}</td>
                            <td><small>{{ Str::limit($rm->keluhan ?? '-', 30) }}</small></td>
                            <td><small>{{ Str::limit($rm->diagnosis ?? '-', 30) }}</small></td>
                            <td>
                                <a href="{{ route('rekam-medis.detail', $rm->pasien_id) }}" class="btn btn-sm btn-info">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Belum ada rekam medis.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $rekamMedis->links() }}</div>
@endsection
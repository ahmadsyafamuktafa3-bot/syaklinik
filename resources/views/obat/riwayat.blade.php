@extends('layouts.app')
@section('title', 'Riwayat Stok Obat')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Riwayat Stok Obat</h4>
        <a href="{{ route('obat.data') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Data Obat</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari kode/nama obat">
        </div>
        <div class="col-auto">
            <select name="jenis" class="form-select">
                <option value="">Semua Jenis</option>
                <option value="masuk" @selected(request('jenis') === 'masuk')>Stok Masuk</option>
                <option value="keluar" @selected(request('jenis') === 'keluar')>Stok Keluar</option>
            </select>
        </div>
        <div class="col-auto d-flex flex-wrap align-items-center gap-2"><button class="btn btn-secondary mb-0">Cari</button></div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr><th>No</th><th>Tanggal</th><th>Obat</th><th>Jenis</th><th>Jumlah</th><th>Keterangan</th><th>Oleh</th></tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $i => $rh)
                        <tr class="{{ $rh->jenis === 'masuk' ? 'table-success' : 'table-danger' }}">
                            <td>{{ $riwayat->firstItem() + $i }}</td>
                            <td>{{ \Carbon\Carbon::parse($rh->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $rh->obat->nama_obat ?? '-' }} <small class="text-secondary">({{ $rh->obat->kode_obat ?? '-' }})</small></td>
                            <td>
                                @if ($rh->jenis === 'masuk')
                                    <span class="badge bg-gradient-success">Masuk</span>
                                @else
                                    <span class="badge bg-gradient-danger">Keluar</span>
                                @endif
                            </td>
                            <td>{{ $rh->jumlah }} {{ $rh->obat->satuan ?? '' }}</td>
                            <td>{{ $rh->keterangan ?: '-' }}</td>
                            <td>{{ $rh->pengguna->nama_lengkap ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Tidak ada riwayat stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $riwayat->links() }}</div>
@endsection
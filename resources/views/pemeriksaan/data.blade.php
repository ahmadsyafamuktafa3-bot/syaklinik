@extends('layouts.app')
@section('title', 'Pemeriksaan Pasien')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Riwayat Pemeriksaan</h4>
    </div>

    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama pasien">
        </div>
        <div class="col-md-3">
            <input type="date" name="dari" value="{{ request('dari') }}" class="form-control" placeholder="Dari">
        </div>
        <div class="col-md-3">
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control" placeholder="Sampai">
        </div>
        <div class="col-auto d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-dark text-white mb-0">Filter</button>
            <a href="{{ route('pemeriksaan.data') }}" class="btn btn-secondary mb-0">Reset</a>
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
                        <th>Keluhan</th>
                        <th>Diagnosis</th>
                        <th>Tindakan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pemeriksaan as $i => $p)
                        <tr>
                            <td>{{ $pemeriksaan->firstItem() + $i }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_pemeriksaan)->format('d/m/Y') }}</td>
                            <td>{{ $p->pasien->nama_pasien ?? '-' }}</td>
                            <td><small>{{ Str::limit($p->keluhan ?? '-', 30) }}</small></td>
                            <td><small>{{ Str::limit($p->diagnosis ?? '-', 30) }}</small></td>
                            <td><small>{{ $p->tindakans->pluck('nama_tindakan')->implode(', ') ?: '-' }}</small></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning btn-prx-edit"
                                    data-id="{{ $p->id }}"
                                    data-pendaftaran="{{ $p->pendaftaran_id }}"
                                    data-nama="{{ $p->pasien->nama_pasien ?? '-' }}"
                                    data-no_pd="{{ $p->pendaftaran->nomor_pendaftaran ?? '-' }}"
                                    data-no_antrean="{{ $p->pendaftaran?->antrean?->nomor_antrean ?? '-' }}"
                                    data-tanggal="{{ $p->tanggal_pemeriksaan }}"
                                    data-keluhan="{{ $p->keluhan }}"
                                    data-fisik="{{ $p->pemeriksaan_fisik }}"
                                    data-diagnosis="{{ $p->diagnosis }}"
                                    data-catatan="{{ $p->catatan }}"
                                    data-tindakan="{{ $p->tindakans->pluck('id')->implode(',') }}"
                                    data-bs-toggle="modal" data-bs-target="#modalPemeriksaan">Edit</button>
                                @if (!$p->resep)
                                    <button type="button" class="btn btn-sm btn-success btn-resep-tambah"
                                        data-pemeriksaan="{{ $p->id }}"
                                        data-bs-toggle="modal" data-bs-target="#modalResep">Buat Resep</button>
                                @endif
                                <form action="{{ route('pemeriksaan.hapus', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus pemeriksaan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Belum ada pemeriksaan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $pemeriksaan->links() }}</div>

    @include('pemeriksaan._modal', ['tindakanList' => $tindakanList])
    @include('resep._modal', ['pemeriksaanList' => $pemeriksaanList, 'obatList' => $obatList, 'presetPemeriksaanId' => ''])
@endsection
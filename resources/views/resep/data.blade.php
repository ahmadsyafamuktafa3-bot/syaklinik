@extends('layouts.app')
@section('title', 'Resep Obat')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0">Daftar Resep</h4>
            <span class="text-secondary">Total: {{ $resep->total() }} resep</span>
        </div>
        <div>
            <a href="{{ route('resep.cetak', ['q' => request('q'), 'dari' => request('dari'), 'sampai' => request('sampai'), 'status' => request('status')]) }}" target="_blank" class="btn btn-warning mb-0">Cetak</a>
            @if (auth()->user()->role === 'dokter')
                <button type="button" class="btn btn-primary mb-0 btn-resep-tambah" data-pemeriksaan="" data-bs-toggle="modal" data-bs-target="#modalResep">+ Buat Resep</button>
            @endif
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/NIK/no. RM pasien">
        </div>
        <div class="col-md-3">
            <input type="date" name="dari" value="{{ request('dari') }}" class="form-control" placeholder="Dari">
        </div>
        <div class="col-md-3">
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control" placeholder="Sampai">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="belum_ditebus" {{ request('status') === 'belum_ditebus' ? 'selected' : '' }}>Belum Ditebus</option>
                <option value="ditebus" {{ request('status') === 'ditebus' ? 'selected' : '' }}>Sudah Ditebus</option>
            </select>
        </div>
        <div class="col-auto d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-dark text-white mb-0">Filter</button>
            <a href="{{ route('resep.data') }}" class="btn btn-secondary mb-0">Reset</a>
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
                        <th>Jenis Obat</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($resep as $i => $r)
                        <tr>
                            <td>{{ $resep->firstItem() + $i }}</td>
                            <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td>
                            <td>
                                {{ $r->pasien->nama_pasien ?? '-' }}
                                <small class="d-block text-muted">RM: {{ $r->pasien->nomor_rekam_medis ?? '-' }}</small>
                            </td>
                            <td>{{ $r->dokter->nama_dokter ?? '-' }}</td>
                            <td>
                                @forelse ($r->detail as $d)
                                    <span class="badge bg-light text-dark border">{{ $d->obat->nama_obat ?? '-' }} ({{ $d->jumlah }})</span>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td class="fw-bold">Rp {{ number_format($r->detail->sum('subtotal'), 0, ',', '.') }}</td>
                            <td>
                                @if ($r->status === 'ditebus')
                                    <span class="badge bg-success">Sudah Ditebus</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum Ditebus</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                @if (auth()->user()->role === 'dokter')
                                    <button type="button" class="btn btn-sm btn-warning btn-resep-edit"
                                        data-id="{{ $r->id }}"
                                        data-pasien="{{ $r->pasien->nama_pasien ?? '-' }}"
                                        data-tgl_periksa="{{ $r->pemeriksaan ? \Carbon\Carbon::parse($r->pemeriksaan->tanggal_pemeriksaan)->format('d/m/Y') : '-' }}"
                                        data-tanggal="{{ $r->tanggal }}"
                                        data-catatan="{{ $r->catatan_resep }}"
                                        data-detail="{{ $r->detail->map(fn($d) => ['id' => $d->obat_id, 'jumlah' => $d->jumlah, 'aturan' => $d->aturan_pakai])->toJson() }}"
                                        data-bs-toggle="modal" data-bs-target="#modalResep">Edit</button>
                                @endif
                                @if ($r->status === 'belum_ditebus')
                                    <form action="{{ route('resep.ditebus', $r->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Tandai Ditebus</button>
                                    </form>
                                @endif
                                @if (auth()->user()->role === 'dokter')
                                    <form action="{{ route('resep.hapus', $r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus resep? Stok obat akan dikembalikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">Belum ada resep.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $resep->links() }}</div>

    @if (auth()->user()->role === 'dokter')
        @include('resep._modal', ['pemeriksaanList' => $pemeriksaanList, 'obatList' => $obatList, 'presetPemeriksaanId' => ''])
    @endif
@endsection
@extends('layouts.app')
@section('title', 'Pembayaran')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0">Daftar Pembayaran</h4>
            <span class="text-secondary">Total: {{ $pembayaran->total() }} pembayaran</span>
        </div>
        <div>
            <a href="{{ route('pembayaran.cetak', ['q' => request('q'), 'dari' => request('dari'), 'sampai' => request('sampai'), 'status' => request('status')]) }}" target="_blank" class="btn btn-warning mb-0">Cetak</a>
            <button type="button" class="btn btn-primary mb-0" data-bs-toggle="modal" data-bs-target="#modalBuatPembayaran">+ Buat Pembayaran</button>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger border-0">{{ session('error') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nomor pembayaran / pasien">
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
                <option value="belum_lunas" {{ request('status') === 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>
        <div class="col-auto d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-dark text-white mb-0">Filter</button>
            <a href="{{ route('pembayaran.data') }}" class="btn btn-secondary mb-0">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>No. Pembayaran</th>
                        <th>Tanggal</th>
                        <th>Pasien</th>
                        <th>Total</th>
                        <th>Bayar</th>
                        <th>Kembalian</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pembayaran as $i => $p)
                        <tr>
                            <td>{{ $pembayaran->firstItem() + $i }}</td>
                            <td><span class="badge bg-info">{{ $p->nomor_pembayaran }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $p->pasien->nama_pasien ?? '-' }}</td>
                            <td class="fw-bold">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($p->bayar, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($p->kembalian, 0, ',', '.') }}</td>
                            <td>
                                @if ($p->status === 'lunas')
                                    <span class="badge bg-success">Lunas</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum Lunas</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('pembayaran.detail', $p->id) }}" class="btn btn-sm btn-primary">Detail</a>
                                @if ($p->status === 'belum_lunas')
                                    <form action="{{ route('pembayaran.hapus', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pembayaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted">Belum ada pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $pembayaran->links() }}</div>

    <div class="modal fade" id="modalBuatPembayaran" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Buat Pembayaran</h5>
                        <span class="text-secondary small">Total dihitung otomatis dari tindakan pemeriksaan dan obat resep.</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Pasien</th>
                                    <th>Dokter</th>
                                    <th>Tindakan</th>
                                    <th>Obat</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pemeriksaan as $i => $p)
                                    @php
                                        $totalTindakan = $p->tindakans->sum(fn ($t) => $t->pivot->biaya ?? 0);
                                        $totalObat = $p->resep ? $p->resep->detail->sum('subtotal') : 0;
                                        $total = $totalTindakan + $totalObat;
                                    @endphp
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($p->tanggal_pemeriksaan)->format('d/m/Y') }}</td>
                                        <td>
                                            {{ $p->pasien->nama_pasien ?? '-' }}
                                            <small class="d-block text-muted">RM: {{ $p->pasien->nomor_rekam_medis ?? '-' }}</small>
                                        </td>
                                        <td>{{ $p->dokter->nama_dokter ?? '-' }}</td>
                                        <td>
                                            @forelse ($p->tindakans as $t)
                                                <span class="badge bg-light text-dark border">{{ $t->nama_tindakan }} (Rp {{ number_format($t->pivot->biaya, 0, ',', '.') }})</span>
                                            @empty
                                                -
                                            @endforelse
                                        </td>
                                        <td>
                                            @if ($p->resep)
                                                @foreach ($p->resep->detail as $d)
                                                    <span class="badge bg-light text-dark border">{{ $d->obat->nama_obat ?? '-' }} x{{ $d->jumlah }}</span>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="fw-bold">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                        <td>
                                            <form action="{{ route('pembayaran.buat', $p->id) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-sm btn-primary">Buat & Bayar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted">Tidak ada pemeriksaan yang belum dibuatkan pembayaran.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
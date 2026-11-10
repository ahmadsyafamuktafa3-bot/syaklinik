@extends('layouts.app')
@section('title', 'Pembayaran Saya')
@section('content')
    <h4 class="fw-bold mb-3">Pembayaran Saya</h4>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show text-white" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @forelse ($pembayaran as $p)
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <div>
                        <h6 class="mb-1">No. {{ $p->nomor_pembayaran }}</h6>
                        <small class="text-secondary">
                            {{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }} |
                            Dokter: {{ $p->pemeriksaan->dokter->nama_dokter ?? '-' }}
                        </small>
                    </div>
                    <div class="text-end">
                        <h5 class="mb-1">{{ number_format($p->total, 0, ',', '.') }}</h5>
                        @if ($p->status === 'lunas')
                            <span class="badge bg-gradient-success">Lunas</span>
                        @else
                            <span class="badge bg-gradient-warning text-dark">Belum Lunas</span>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-2">
                        <thead class="table-dark">
                            <tr>
                                <th>Jenis</th>
                                <th>Pelayanan</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($p->detail as $d)
                                <tr>
                                    <td>{{ $d->jenis_pelayanan }}</td>
                                    <td>{{ $d->nama_pelayanan }}</td>
                                    <td class="text-center">{{ $d->jumlah }}</td>
                                    <td class="text-end">{{ number_format($d->harga, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($d->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total</th>
                                <th class="text-end">{{ number_format($p->total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if ($p->status === 'belum_lunas')
                    <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#formBayar{{ $p->id }}">
                        Bayar Sekarang
                    </button>
                    <div class="collapse mt-3" id="formBayar{{ $p->id }}">
                        <form action="{{ route('pembayaran.pasien_bayar', $p->id) }}" method="POST" class="row g-2 align-items-end">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label mb-1">Jumlah Pembayaran (Rp)</label>
                                <input type="number" name="bayar" class="form-control @error('bayar') is-invalid @enderror" min="0" required
                                    placeholder="{{ number_format($p->total, 0, ',', '.') }}">
                                @error('bayar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-success">Bayar</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-light border text-secondary">
            Belum ada pembayaran. Tagihan akan otomatis muncul setelah resep ditandai ditebus, atau admin membuat pembayaran dari pemeriksaan Anda.
        </div>
    @endforelse

    @if ($belumAdaTagihan->isNotEmpty())
        <div class="card shadow-sm">
            <div class="card-header pb-0">
                <h6 class="mb-0">Pemeriksaan Belum Ada Tagihan</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <tbody>
                            @foreach ($belumAdaTagihan as $k)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($k->tanggal_pemeriksaan)->format('d/m/Y') }}
                                        <small class="text-secondary d-block">Dokter: {{ $k->dokter->nama_dokter ?? '-' }}</small>
                                    </td>
                                    <td class="text-end text-secondary">Belum dibuat tagihan oleh admin</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection
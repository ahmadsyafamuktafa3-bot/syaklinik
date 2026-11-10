@extends('layouts.app')
@section('title', 'Detail Pembayaran')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0">Detail Pembayaran</h4>
            <span class="badge bg-info">{{ $pembayaran->nomor_pembayaran }}</span>
        </div>
        <a href="{{ route('pembayaran.data') }}" class="btn btn-secondary mb-0">Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Data Transaksi</div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><th class="text-start">Tanggal</th><td>{{ \Carbon\Carbon::parse($pembayaran->tanggal)->format('d/m/Y') }}</td></tr>
                        <tr><th class="text-start">Pasien</th><td>{{ $pembayaran->pasien->nama_pasien ?? '-' }}</td></tr>
                        <tr><th class="text-start">No. RM</th><td>{{ $pembayaran->pasien->nomor_rekam_medis ?? '-' }}</td></tr>
                        <tr><th class="text-start">Dokter</th><td>{{ $pembayaran->pemeriksaan->dokter->nama_dokter ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>

            @if ($pembayaran->status === 'belum_lunas')
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-warning text-dark">Terima Pembayaran</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('pembayaran.bayar', $pembayaran->id) }}">
                            @csrf
                            <label class="form-label">Total Tagihan</label>
                            <input type="text" class="form-control mb-2" value="Rp {{ number_format($pembayaran->total, 0, ',', '.') }}" disabled>
                            <label class="form-label">Uang Diterima</label>
                            <input type="number" name="bayar" class="form-control" min="0" step="any" placeholder="0"
                                value="{{ old('bayar') }}" required>
                            <button class="btn btn-primary mt-3 w-100 mb-0">Proses Lunas</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Rincian Tagihan</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis</th>
                                <th>Pelayanan</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pembayaran->detail as $d)
                                <tr>
                                    <td>
                                        <span class="badge {{ $d->jenis_pelayanan === 'Obat' ? 'bg-info' : 'bg-secondary' }}">{{ $d->jenis_pelayanan }}</span>
                                    </td>
                                    <td>{{ $d->nama_pelayanan }}</td>
                                    <td class="text-center">{{ $d->jumlah }}</td>
                                    <td class="text-end">Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">Tidak ada rincian.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-dark">
                                <th colspan="4" class="text-end">TOTAL</th>
                                <th class="text-end">Rp {{ number_format($pembayaran->total, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Pembayaran</th>
                                <th class="text-end">Rp {{ number_format($pembayaran->bayar, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Kembalian</th>
                                <th class="text-end">Rp {{ number_format($pembayaran->kembalian, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Status</th>
                                <th class="text-end">
                                    @if ($pembayaran->status === 'lunas')
                                        <span class="badge bg-success">LUNAS</span>
                                    @else
                                        <span class="badge bg-danger">BELUM LUNAS</span>
                                    @endif
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
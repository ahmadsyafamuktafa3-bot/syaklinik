@extends('layouts.app')
@section('title', 'Pendaftaran Saya')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h4 class="fw-bold mb-0">Pendaftaran Saya</h4>
        <a href="{{ route('pendaftaran.daftar') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Daftar Periksa
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>No. Pendaftaran</th>
                        <th>Tanggal</th>
                        <th>Dokter</th>
                        <th>Jadwal</th>
                        <th>No. Antrean</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendaftaran as $i => $p)
                        <tr>
                            <td>{{ $pendaftaran->firstItem() + $i }}</td>
                            <td>{{ $p->nomor_pendaftaran }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_kunjungan)->format('d/m/Y') }}</td>
                            <td>{{ $p->dokter->nama_dokter ?? '-' }}</td>
                            <td>
                                @if ($p->jadwal)
                                    {{ $p->jadwal->hari }} {{ substr($p->jadwal->jam_mulai, 0, 5) }}-{{ substr($p->jadwal->jam_selesai, 0, 5) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($p->antrean)
                                    <span class="badge bg-primary">{{ $p->antrean->nomor_antrean }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($p->antrean)
                                    <span class="badge {{ $p->antrean->status === 'menunggu' ? 'bg-warning text-dark' : ($p->antrean->status === 'selesai' ? 'bg-success' : 'bg-info') }}">{{ ucfirst($p->antrean->status) }}</span>
                                @else
                                    <span class="badge bg-danger">Batal</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Anda belum memiliki pendaftaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $pendaftaran->links() }}</div>
@endsection
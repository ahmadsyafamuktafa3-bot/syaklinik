@extends('layouts.app')
@section('title', 'Antrean Pasien')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Antrean Pasien</h4>
        <span class="badge bg-secondary fs-6">{{ $antrean->total() }} data</span>
    </div>

    <form method="GET" class="row g-2 mb-3 align-items-end" id="form-antrean">
        <div class="col-md-3">
            <label class="form-label mb-1">Tanggal</label>
            <select name="tanggal" class="form-select">
                <option value="">Semua Tanggal</option>
                @foreach ($tanggalList as $t)
                    <option value="{{ $t }}" @selected($tanggal === (string) $t)>
                        {{ \Carbon\Carbon::parse($t)->isoFormat('dddd, D MMM Y') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Dokter</label>
            <select name="dokter_id" class="form-select">
                <option value="">Semua Dokter</option>
                @foreach ($dokterList as $d)
                    <option value="{{ $d->id }}" @selected($dokterId == $d->id)>{{ $d->nama_dokter }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label mb-1">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="menunggu" @selected($status === 'menunggu')>Menunggu</option>
                <option value="dipanggil" @selected($status === 'dipanggil')>Dipanggil</option>
                <option value="diperiksa" @selected($status === 'diperiksa')>Diperiksa</option>
                <option value="selesai" @selected($status === 'selesai')>Selesai</option>
                <option value="batal" @selected($status === 'batal')>Batal</option>
            </select>
        </div>
        <div class="col-auto d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-dark text-white mb-0">Filter</button>
            <a href="{{ route('antrean.data') }}" class="btn btn-secondary mb-0">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Antrean</th>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Jam Praktik</th>
                        <th>Keluhan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($antrean as $a)
                        @php $pendaftaran = $a->pendaftaran; @endphp
                        <tr>
                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($a->tanggal)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-primary fs-6" title="Nomor antrean dihitung per dokter">{{ $a->nomor_antrean }}</span>
                            </td>
                            <td>
                                {{ $pendaftaran?->pasien?->nama_pasien ?? '-' }}<br>
                                <small class="text-secondary">{{ $pendaftaran?->nomor_pendaftaran ?? '-' }}</small>
                            </td>
                            <td>{{ $pendaftaran?->dokter?->nama_dokter ?? '-' }}</td>
                            <td>
                                @if ($pendaftaran?->jadwal)
                                    {{ substr($pendaftaran->jadwal->jam_mulai, 0, 5) }}-{{ substr($pendaftaran->jadwal->jam_selesai, 0, 5) }}
                                    <small class="text-secondary">{{ $pendaftaran->jadwal->ruang }}</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td><small>{{ Str::limit($pendaftaran?->keluhan ?? '-', 30) }}</small></td>
                            <td>
                                @php
                                    $warna = [
                                        'menunggu' => 'bg-warning text-dark',
                                        'dipanggil' => 'bg-info',
                                        'diperiksa' => 'bg-primary',
                                        'selesai' => 'bg-success',
                                        'batal' => 'bg-danger',
                                    ];
                                @endphp
                                <span class="badge {{ $warna[$a->status] ?? 'bg-secondary' }}">{{ ucfirst($a->status) }}</span>
                            </td>
                            <td>
                                @if ($a->status === 'menunggu')
                                    <form action="{{ route('antrean.status', $a->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="dipanggil">
                                        <button class="btn btn-sm btn-info">Panggil</button>
                                    </form>
                                @endif
                                @if ($a->status === 'dipanggil')
                                    <form action="{{ route('antrean.status', $a->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="diperiksa">
                                        <button class="btn btn-sm btn-primary">Periksa</button>
                                    </form>
                                @endif
                                @if ($a->status === 'diperiksa')
                                    <form action="{{ route('antrean.status', $a->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="selesai">
                                        <button class="btn btn-sm btn-success">Selesai</button>
                                    </form>
                                @endif
                                @if (!in_array($a->status, ['selesai', 'batal']))
                                    <form action="{{ route('antrean.status', $a->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan antrean ini?')">
                                        @csrf
                                        <input type="hidden" name="status" value="batal">
                                        <button class="btn btn-sm btn-danger">Batal</button>
                                    </form>
                                    <form action="{{ route('antrean.hapus', $a->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus antrean ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada antrean untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $antrean->links() }}</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('#form-antrean select').forEach(function (el) {
        el.addEventListener('change', function () {
            this.form.submit();
        });
    });
</script>
@endpush
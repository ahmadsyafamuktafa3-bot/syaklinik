@extends('layouts.app')
@section('title', 'Antrean Pasien')
@section('content')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <h4 class="fw-bold mb-0" id="antrean-section">
            Antrean untuk Diperiksa
            <span class="fs-6 fw-normal text-secondary">
                ({{ $tanggalAntrean ? \Carbon\Carbon::parse($tanggalAntrean)->isoFormat('dddd, D MMMM Y') : 'Semua Tanggal' }})
            </span>
        </h4>
        <span class="badge bg-secondary fs-6">{{ $antrean->total() }} data</span>
    </div>

    <form method="GET" class="row g-2 mb-3 align-items-end" id="form-antrean">
        <div class="col-md-3">
            <label class="form-label mb-1">Tanggal</label>
            <select name="tanggal_antrean" class="form-select">
                <option value="">Semua Tanggal</option>
                @foreach ($tanggalList as $t)
                    <option value="{{ $t }}" @selected($tanggalAntrean === (string) $t)>
                        {{ \Carbon\Carbon::parse($t)->isoFormat('dddd, D MMMM Y') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Cari Pasien</label>
            <input type="text" name="q_antrean" value="{{ $qAntrean }}" class="form-control" placeholder="Nama pasien...">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">&nbsp;</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="sembunyi_diperiksa" value="1" id="sembunyiDiperiksa"
                    @checked($sembunyiSudahDiperiksa)>
                <label class="form-check-label" for="sembunyiDiperiksa">Sembunyikan yang sudah diperiksa</label>
            </div>
        </div>
        <div class="col-auto d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-dark text-white mb-0">Tampilkan</button>
            <a href="{{ route('pemeriksaan.antrean') }}" class="btn btn-secondary mb-0">Reset</a>
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
                        <th>Jam Praktik</th>
                        <th>Keluhan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($antrean as $a)
                        @php
                            $pendaftaran = $a->pendaftaran;
                            $pemeriksaan = $pendaftaran?->pemeriksaan;
                            $sudahDiperiksa = (bool) $pemeriksaan;
                        @endphp
                        <tr class="{{ $sudahDiperiksa ? 'table-light opacity-75' : '' }}">
                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($a->tanggal)->format('d/m/Y') }}</td>
                            <td><span class="badge bg-primary fs-6">{{ $a->nomor_antrean }}</span></td>
                            <td>
                                {{ $pendaftaran?->pasien?->nama_pasien ?? '-' }}
                                @if ($sudahDiperiksa)
                                    <span class="badge bg-success ms-1">Sudah Diperiksa</span>
                                @endif
                            </td>
                            <td>
                                @if ($pendaftaran?->jadwal)
                                    {{ substr($pendaftaran->jadwal->jam_mulai, 0, 5) }}-{{ substr($pendaftaran->jadwal->jam_selesai, 0, 5) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td><small>{{ Str::limit($pendaftaran?->keluhan ?? '-', 40) }}</small></td>
                            <td>
                                @if ($a->status === 'menunggu')
                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                @elseif ($a->status === 'dipanggil')
                                    <span class="badge bg-info">Dipanggil</span>
                                @elseif ($a->status === 'diperiksa')
                                    <span class="badge bg-primary">Diperiksa</span>
                                @elseif ($a->status === 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($a->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($a->status === 'menunggu')
                                    <form action="{{ route('pemeriksaan.panggil', $a->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-warning">Panggil</button>
                                    </form>
                                @endif
                                @if ($sudahDiperiksa)
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-prx-edit"
                                        data-id="{{ $pemeriksaan->id }}"
                                        data-pendaftaran="{{ $pendaftaran?->id }}"
                                        data-nama="{{ $pendaftaran?->pasien?->nama_pasien ?? '-' }}"
                                        data-no_pd="{{ $pendaftaran?->nomor_pendaftaran ?? '-' }}"
                                        data-no_antrean="{{ $a->nomor_antrean }}"
                                        data-tanggal="{{ $pemeriksaan->tanggal_pemeriksaan }}"
                                        data-keluhan="{{ $pemeriksaan->keluhan }}"
                                        data-fisik="{{ $pemeriksaan->pemeriksaan_fisik }}"
                                        data-diagnosis="{{ $pemeriksaan->diagnosis }}"
                                        data-catatan="{{ $pemeriksaan->catatan }}"
                                        data-tindakan="{{ $pemeriksaan->tindakans->pluck('id')->implode(',') }}"
                                        data-bs-toggle="modal" data-bs-target="#modalPemeriksaan">Edit</button>
                                @else
                                    <button type="button" class="btn btn-sm btn-primary btn-prx-add"
                                        data-pendaftaran="{{ $pendaftaran?->id }}"
                                        data-nama="{{ $pendaftaran?->pasien?->nama_pasien ?? '-' }}"
                                        data-no_pd="{{ $pendaftaran?->nomor_pendaftaran ?? '-' }}"
                                        data-no_antrean="{{ $a->nomor_antrean }}"
                                        data-keluhan="{{ $pendaftaran?->keluhan ?? '' }}"
                                        data-tanggal="{{ $a->tanggal instanceof \DateTimeInterface ? $a->tanggal->format('Y-m-d') : $a->tanggal }}"
                                        data-bs-toggle="modal" data-bs-target="#modalPemeriksaan">Periksa</button>
                                @endif
                                @if ($a->status === 'diperiksa' || $a->status === 'dipanggil')
                                    <form action="{{ route('pemeriksaan.selesai', $a->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai antrean ini selesai?')">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Selesai</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada antrean untuk filter ini. Pastikan pasien sudah didaftarkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $antrean->links() }}</div>

    @include('pemeriksaan._modal', ['tindakanList' => $tindakanList, 'pathResep' => false])
@endsection

@push('scripts')
<script>
    document.querySelectorAll('#form-antrean select, #form-antrean input[type="checkbox"]').forEach(function (el) {
        el.addEventListener('change', function () {
            this.form.submit();
        });
    });
</script>
@endpush
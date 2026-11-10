@extends('layouts.app')
@section('title', 'Dashboard Pasien')
@section('content')
    <div class="row mb-4">
        <div class="col">
            <h4 class="font-weight-bolder mb-0">Selamat datang, {{ Auth::user()->nama_lengkap }}</h4>
            <p class="text-sm text-secondary mb-0">Informasi pendaftaran dan riwayat pelayanan Anda</p>
        </div>
    </div>

    <div class="row gy-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Pendaftaran Saya</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center justify-content-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No. Pendaftaran</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dokter</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendaftaran as $p)
                                    <tr>
                                        <td>{{ $p->nomor_pendaftaran }}</td>
                                        <td>{{ \Carbon\Carbon::parse($p->tanggal_kunjungan)->format('d/m/Y') }}</td>
                                        <td>{{ $p->dokter->nama_dokter ?? '-' }}</td>
                                        <td>
                                            @if ($p->antrean)
                                                <span class="badge bg-gradient-info">{{ $p->antrean->status }}</span>
                                            @else
                                                <span class="badge bg-gradient-secondary">terdaftar</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-secondary">Belum ada pendaftaran.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer pb-2">{{ $pendaftaran->links() }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Riwayat Pelayanan</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center justify-content-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dokter</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Diagnosis</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riwayat as $r)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($r->tanggal_pemeriksaan)->format('d/m/Y') }}</td>
                                        <td>{{ $r->dokter->nama_dokter ?? '-' }}</td>
                                        <td>{{ Str::limit($r->diagnosis ?? '-', 30) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-secondary">Belum ada riwayat pelayanan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer pb-2">{{ $riwayat->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    @if ($rekamMedis->isNotEmpty())
    <div class="row gy-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Rekam Medis Saya</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center justify-content-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Keluhan</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Diagnosis</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekamMedis as $rm)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($rm->tanggal)->format('d/m/Y') }}</td>
                                        <td>{{ Str::limit($rm->keluhan ?? '-', 30) }}</td>
                                        <td>{{ Str::limit($rm->diagnosis ?? '-', 30) }}</td>
                                        <td>{{ Str::limit($rm->tindakan ?? '-', 30) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer pb-2">{{ $rekamMedis->links() }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
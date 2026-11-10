@extends('layouts.app')
@section('title', 'Dashboard Dokter')
@section('content')
    <div class="row mb-4">
        <div class="col">
            <h4 class="font-weight-bolder mb-0">Selamat datang, {{ Auth::user()->nama_lengkap }}</h4>
            <p class="text-sm text-secondary mb-0">Halaman jadwal praktik dan antrean pasien Anda</p>
        </div>
    </div>

    <div class="row gy-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Jadwal Praktik Anda</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center justify-content-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hari</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jam</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ruang</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kuota</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jadwal as $j)
                                    <tr>
                                        <td><span class="badge bg-gradient-primary">{{ $j->hari }}</span></td>
                                        <td>{{ $j->jam_mulai }} - {{ $j->jam_selesai }}</td>
                                        <td>{{ $j->ruang }}</td>
                                        <td>{{ $j->kuota }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-secondary">Belum ada jadwal.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Pasien Hari Ini</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center justify-content-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No Antrean</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pasien</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Keluhan</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($antrean as $a)
                                    <tr>
                                        <td>{{ $a->nomor_antrean }}</td>
                                        <td>{{ $a->pendaftaran->pasien->nama_pasien ?? '-' }}</td>
                                        <td>{{ Str::limit($a->pendaftaran->keluhan ?? '-', 25) }}</td>
                                        <td><span class="badge bg-gradient-info">{{ $a->status }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-secondary">Belum ada pasien yang antre hari ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
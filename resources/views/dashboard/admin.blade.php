@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')
    <div class="row gy-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Dashboard</h6>
                            <h4 class="font-weight-bolder mb-0">Selamat datang, {{ Auth::user()->nama_lengkap }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="fas fa-users text-white opacity-10"></i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Pasien</p>
                        <h4 class="mb-0">{{ $total_pasien }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <a href="{{ route('pasien.data') }}" class="mb-0 text-sm text-primary">Kelola Pasien →</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="fas fa-user-md text-white opacity-10"></i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Dokter</p>
                        <h4 class="mb-0">{{ $total_dokter }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <a href="{{ route('dokter.data') }}" class="mb-0 text-sm text-success">Kelola Dokter →</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="fas fa-clipboard-list text-white opacity-10"></i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Pendaftaran</p>
                        <h4 class="mb-0">{{ $total_pendaftaran }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <a href="{{ route('pendaftaran.data') }}" class="mb-0 text-sm text-warning">Kelola Pendaftaran →</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="fas fa-list-ol text-white opacity-10"></i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Antrean Menunggu</p>
                        <h4 class="mb-0">{{ $total_antrean_menunggu }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <a href="{{ route('antrean.data') }}" class="mb-0 text-sm text-info">Lihat Antrean →</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                        <i class="fas fa-capsules text-white opacity-10"></i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Jenis Obat</p>
                        <h4 class="mb-0">{{ $total_obat }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <a href="{{ route('obat.data') }}" class="mb-0 text-sm text-danger">Kelola Obat →</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-secondary shadow-secondary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="fas fa-cash-register text-white opacity-10"></i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Transaksi Pembayaran</p>
                        <h4 class="mb-0">{{ $total_pembayaran }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <a href="{{ route('pembayaran.data') }}" class="mb-0 text-sm text-secondary">Kelola Pembayaran →</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="fas fa-coins text-white opacity-10"></i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Pendapatan (Lunas)</p>
                        <h4 class="mb-0">Rp {{ number_format($pendapatan, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <span class="mb-0 text-sm text-dark">Total semua pembayaran lunas</span>
                </div>
            </div>
        </div>
    </div>
@endsection
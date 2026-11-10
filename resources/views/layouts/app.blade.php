<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
    <title>@yield('title', 'Sistem Informasi Klinik')</title>

    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link id="pagestyle" href="{{ asset('assets/css/soft-ui-dashboard.min.css?v=1.1.0') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">

    @stack('styles')

    <style>
        .sidenav .navbar-collapse {
            overflow-y: auto;
            overflow-x: hidden;
            max-height: calc(100vh - 120px);
        }
        .sidenav .navbar-collapse::-webkit-scrollbar {
            width: 4px;
        }
        .sidenav .navbar-collapse::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 5px;
        }
        .sidenav .navbar-nav .nav-link:focus,
        .sidenav .navbar-nav .nav-link:active {
            background-color: transparent;
            box-shadow: none;
            color: inherit;
        }
        .sidenav .navbar-nav .nav-link.active {
            background-image: linear-gradient(310deg, #627594 0%, #8fa4be 100%);
            box-shadow: 0 4px 10px rgba(45, 206, 137, 0.12);
        }
        .row + .row.gy-4 {
            margin-top: 0;
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-100">
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3" id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="{{ route('dashboard.' . Auth::user()->role) }}">
                <img src="{{ asset('assets/img/logo-ct-dark.png') }}" class="navbar-brand-img h-100" alt="main_logo">
                <span class="ms-1 font-weight-bold">Syaklinik</span>
            </a>
        </div>
        <hr class="horizontal dark mt-0">
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">

                @if (Auth::user()->role === 'admin')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pasien*') ? 'active' : '' }}" href="{{ route('pasien.data') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-users text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Data Pasien</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dokter*') ? 'active' : '' }}" href="{{ route('dokter.data') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-user-md text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Data Dokter</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('jadwal*') ? 'active' : '' }}" href="{{ route('jadwal.data') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-calendar-week text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Jadwal Dokter</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pendaftaran*') ? 'active' : '' }}" href="{{ route('pendaftaran.data') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-clipboard-list text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Pendaftaran</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('antrean*') ? 'active' : '' }}" href="{{ route('antrean.data') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-list-ol text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Antrean</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('obat*') ? 'active' : '' }}" href="{{ route('obat.data') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-capsules text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Data Obat</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('tindakan*') ? 'active' : '' }}" href="{{ route('tindakan.data') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-briefcase-medical text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Data Tindakan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('rekam-medis*') ? 'active' : '' }}" href="{{ route('rekam-medis.data') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-folder-open text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Rekam Medis</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('resep*') ? 'active' : '' }}" href="{{ route('resep.data') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-file-prescription text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Resep</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pembayaran*') ? 'active' : '' }}" href="{{ route('pembayaran.data') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-cash-register text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Pembayaran</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}" href="{{ route('laporan.data') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-file-alt text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Laporan</span>
                    </a>
                </li>
                @endif

                @if (Auth::user()->role === 'dokter')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard/dokter*') ? 'active' : '' }}" href="{{ route('dashboard.dokter') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-chart-pie text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pemeriksaan/antrean*') ? 'active' : '' }}" href="{{ route('pemeriksaan.antrean') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-list-ol text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Antrean Pasien</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('pemeriksaan*') && !request()->is('pemeriksaan/antrean*')) ? 'active' : '' }}" href="{{ route('pemeriksaan.data') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-stethoscope text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Pemeriksaan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('rekam-medis*') ? 'active' : '' }}" href="{{ route('rekam-medis.data') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-folder-open text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Rekam Medis</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('resep*') ? 'active' : '' }}" href="{{ route('resep.data') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-file-prescription text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Resep</span>
                    </a>
                </li>
                @endif

                @if (Auth::user()->role === 'pasien')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard/pasien*') ? 'active' : '' }}" href="{{ route('dashboard.pasien') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-chart-pie text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pendaftaran/saya*') ? 'active' : '' }}" href="{{ route('pendaftaran.saya') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-clipboard-list text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Pendaftaran Saya</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pendaftaran/daftar*') ? 'active' : '' }}" href="{{ route('pendaftaran.daftar') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-user-plus text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Daftar Periksa</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pembayaran/saya*') ? 'active' : '' }}" href="{{ route('pembayaran.saya') }}">
                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-money-bill-wave text-dark text-sm opacity-8"></i>
                        </div>
                        <span class="nav-link-text ms-1">Pembayaran Saya</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </aside>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">@yield('title', 'Dashboard')</li>
                    </ol>
                    <h6 class="font-weight-bolder mb-0">@yield('title', 'Dashboard')</h6>
                </nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <ul class="navbar-nav justify-content-end ms-auto">
                        <li class="nav-item d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body font-weight-bold px-0">
                                <i class="fa fa-user me-sm-1"></i>
                                <span class="d-sm-inline d-none">{{ Auth::user()->nama_lengkap }}</span>
                                <span class="badge bg-primary ms-2">{{ ucfirst(Auth::user()->role) }}</span>
                            </a>
                        </li>
                        <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                                <div class="sidenav-toggler-inner">
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item px-3 d-flex align-items-center">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm mb-0">
                                    <i class="fas fa-sign-out-alt me-1"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid py-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if ($errors->any() && request()->method() === 'POST')
                <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="{{ asset('assets/js/core/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/soft-ui-dashboard.min.js?v=1.1.0') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/id.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('input[type="date"]').forEach(function (el) {
                flatpickr(el, {
                    altInput: true,
                    altFormat: 'd/m/Y',
                    dateFormat: 'Y-m-d',
                    locale: 'id',
                    minDate: el.getAttribute('min') || undefined,
                    disableMobile: true,
                    allowInput: true,
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
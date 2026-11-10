<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
    <title>Login | Syaklinik</title>

    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link id="pagestyle" href="{{ asset('assets/css/soft-ui-dashboard.min.css?v=1.1.0') }}" rel="stylesheet" />

    <style>
        body {
            font-family: "Inter", sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            padding: 1rem;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
        }

        .login-card .card {
            border-radius: 1rem;
        }

        .input-group-text {
            border: 0;
            border-radius: 0.75rem 0 0 0.75rem;
            padding-right: 0;
            background: transparent;
        }

        .input-group .form-control {
            border-left: 0;
            border-radius: 0 0.75rem 0.75rem 0;
        }

        .form-control {
            border-radius: 0.75rem;
            font-size: 0.875rem;
            padding: 0.6rem 0.9rem;
        }

        .btn-show-password {
            border: 0;
            background: transparent;
            color: #67748e;
            cursor: pointer;
            font-size: 0.9rem;
            padding: 0 0.8rem;
            border-radius: 0 0.75rem 0.75rem 0;
        }

        .alert {
            font-size: 0.82rem;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="card shadow-xl border-0">
            <div class="card-header text-center bg-transparent border-0 pt-4">
                <div class="avatar avatar-xxl rounded-circle bg-gradient-primary shadow-lg mb-3 mx-auto d-flex align-items-center justify-content-center text-white">
                    <i class="fas fa-heart-pulse fa-2x"></i>
                </div>
                <h3 class="fw-bold mb-0">Syaklinik</h3>
                <p class="text-muted mb-0">Sistem Informasi Klinik</p>
            </div>
            <div class="card-body px-4 pb-4 pt-3">
                @if (session('success'))
                    <div class="alert alert-success text-white py-2 d-flex align-items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger text-white py-2 d-flex align-items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.aksi') }}" autocomplete="off">
                    @csrf

                    <label class="form-label fw-normal text-muted">Username</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-user text-secondary"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username"
                            value="{{ old('username') }}" required autofocus autocomplete="off">
                    </div>

                    <label class="form-label fw-normal text-muted">Password</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-lock text-secondary"></i></span>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password"
                            required autocomplete="new-password">
                        <button type="button" class="btn-show-password" id="togglePassword" tabindex="-1">
                            <i class="fas fa-eye" id="iconPassword"></i>
                        </button>
                    </div>

                    <label class="form-label fw-normal text-muted">NIK
                        <span class="text-xs text-secondary fst-italic">(wajib untuk pasien)</span>
                    </label>
                    <div class="input-group mb-4">
                        <span class="input-group-text"><i class="fas fa-id-card text-secondary"></i></span>
                        <input type="text" name="nik" id="nik" class="form-control" placeholder="Nomor Induk Kependudukan"
                            value="{{ old('nik') }}" maxlength="16" autocomplete="off" inputmode="numeric"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-0">
                        <i class="fas fa-sign-in-alt me-2"></i>Masuk
                    </button>
                </form>

                <div class="mt-4">
                    <hr class="horizontal dark my-2">
                    <p class="text-xs text-center text-muted mb-0">© {{ date('Y') }} Syaklinik</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');
        const iconPassword = document.getElementById('iconPassword');

        togglePassword.addEventListener('click', function () {
            const isPassword = passwordField.type === 'password';
            passwordField.type = isPassword ? 'text' : 'password';
            iconPassword.classList.toggle('fa-eye');
            iconPassword.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>
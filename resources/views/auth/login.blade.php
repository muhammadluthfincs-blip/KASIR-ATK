<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ATK POS</title>

    <!-- Favicon / Logo Tab Browser (Base64 Inline + Multi-size PNG + ICO) -->
    <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAB/UlEQVRYhc2X30tUQRTHP3O5f0Avm4tkESq4aWwZvfqyD6ZIIj4sSJjRX2GE0Uv/RfSD6CkJJZL+ACNaf22pPWQEKbHrvuQ/cKcHuTd359x7Z9250hcG5s4c5nPm3HOGGUWC+u83dNK8rb4/zam4OWOi717dCTROe8+6mphNH31ztUzhkRPP8xE36vTO/j4TeKgfL7tV5EDvnYPM4NX9BwAUe56YTry6oDwArYNMWggPHWmdB/AACLTz9rVUMHb85eBhsx2gLpd/Og//9uhQ1NcvpqP+1fxjw9ZDB7hsJ+EA6u7iMbzrkWjv68BdAHbGi+L4UG4hCnmrfILADXziujg++G4TiGf42oEDu7dviONXltcT4QA+ujMHdidvyvClSioccJsDoQpvPwN263aUA0drZZhqgS9+wmbnobzTnnJHa2UAuucvRYsNvPnY9jqq59aWGKtfK3JJAZzLDxpjf2o7qbu9OFY1xtqugtPCASSWr1OqQAK2axM6KLGcHURWEiNgWYaF1xupNt9mhhPnJVbHB1Fbkn6BbRKm7c6O/1/mgE7OAdsSs5HE8mqrI4ogwGhZqIVRWx1RPsj/JgvJOQCxtxXnEjgeQL1SUtKV2bVOrl+vlP49TEKdH/5wJqE43Bg1n2ahctdWMnWisTUW/zg1nCm+d+JMozoey/kLfXLT+o9Xsv4AAAAASUVORK5CYII=">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v={{ time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.76) 0%, rgba(30, 58, 138, 0.70) 100%),
                        url("{{ asset('images/login-bg.jpg') }}") no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
        }

        .login-card {
            background-color: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.25);
            max-width: 450px;
            width: 100%;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: transform 0.2s ease;
        }

        .login-header {
            padding: 34px 32px 20px;
            text-align: center;
            background: transparent;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .btn-login {
            background: linear-gradient(90deg, #2563eb, #1d4ed8);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            background: linear-gradient(90deg, #1d4ed8, #1e40af);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
        }

        .demo-box {
            background-color: rgba(248, 250, 252, 0.95);
            border-top: 1px solid #e2e8f0;
            padding: 18px 32px 22px;
        }

        .btn-demo {
            font-size: 0.8rem;
            padding: 7px 12px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .page-footer-text {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.82rem;
            margin-top: 20px;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="d-inline-block mb-3">
                <img src="{{ asset('favicon.svg') }}" alt="Logo ATK POS" width="68" height="68" class="rounded-4 shadow-sm">
            </div>
            <h4 class="fw-bold mb-1 text-dark">ATK <span class="text-primary">POS</span></h4>
            <p class="text-muted small mb-0">Sistem Kasir & Manajemen Stok Alat Tulis Kantor</p>
        </div>

        <div class="p-4 pt-0">
            @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center gap-2 py-2 small mb-3">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>{{ session('error') }}</div>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 py-2 small mb-3">
                <i class="fa-solid fa-circle-check"></i>
                <div>{{ session('success') }}</div>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Email Pengguna</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="emailInput" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
                    </div>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label class="form-label small fw-semibold text-secondary">Password</label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="passwordInput" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" placeholder="••••••••" required>
                    </div>
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberCheck" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="rememberCheck">
                            Ingat saya di perangkat ini
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Masuk ke Sistem</span>
                </button>
            </form>
        </div>

        <!-- Demo Account Quick Fill -->
        <div class="demo-box">
            <div class="d-flex align-items-center gap-1 text-muted small mb-2 fw-semibold">
                <i class="fa-solid fa-bolt text-warning"></i>
                <span>Akun Uji Coba Cepat:</span>
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <button type="button" class="btn btn-outline-primary btn-demo w-100 text-truncate text-start" onclick="fillLogin('admin@atkpos.com', 'admin123')">
                        <div class="fw-bold"><i class="fa-solid fa-shield-halved me-1"></i> Admin</div>
                        <small class="text-muted" style="font-size: 0.72rem;">admin@atkpos.com</small>
                    </button>
                </div>
                <div class="col-6">
                    <button type="button" class="btn btn-outline-success btn-demo w-100 text-truncate text-start" onclick="fillLogin('kasir@atkpos.com', 'kasir123')">
                        <div class="fw-bold"><i class="fa-solid fa-user-tag me-1"></i> Kasir</div>
                        <small class="text-muted" style="font-size: 0.72rem;">kasir@atkpos.com</small>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-footer-text">
        &copy; {{ date('Y') }} <strong>ATK POS</strong> &bull; Sistem Kasir & Manajemen Stok Alat Tulis Kantor
    </div>

    <script>
        function fillLogin(email, password) {
            document.getElementById('emailInput').value = email;
            document.getElementById('passwordInput').value = password;
        }
    </script>
</body>
</html>


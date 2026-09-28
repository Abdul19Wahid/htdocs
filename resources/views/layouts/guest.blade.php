<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Sync with the same dark-mode preference used across the app, set
         before first paint so there's no flash of the wrong theme. -->
    <script>
        (function () {
            var saved = localStorage.getItem('smartfinance-darkmode');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            var isDark = saved !== null ? saved === 'true' : prefersDark;
            if (isDark) document.documentElement.classList.add('dark-mode');
        })();
    </script>

    <title>{{ config('app.name', 'Smart Finance') }} — Personal Financial Management</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:600,700|inter:400,500,600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sf-primary: #2563eb;
            --sf-dark: #0f172a;
            --sf-muted: #64748b;
            --sf-bg: #f6f8fc;
            --sf-panel: #0f172a;
            --sf-text: #172033;
            --sf-border: #e5e7eb;
            --sf-accent: #10b981;
            --sf-card-shadow: 0 20px 50px rgba(15, 23, 42, 0.10);
        }

        html.dark-mode {
            --sf-bg: #0f172a;
            --sf-text: #f1f5f9;
            --sf-border: #1e293b;
            --sf-muted: #94a3b8;
            --sf-panel: #060b16;
            --sf-card-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }

        html.dark-mode .auth-card { background: #16233b; }
        html.dark-mode .form-control { background: #101c30; border-color: #253552; color: var(--sf-text); }
        html.dark-mode .form-control:focus { background: #101c30; color: var(--sf-text); }
        html.dark-mode .form-control::placeholder { color: #5b6b85; }
        html.dark-mode .input-group-text { background: #101c30; border-color: #253552; color: var(--sf-muted); }

        * { font-family: 'Inter', -apple-system, sans-serif; }

        body {
            background: var(--sf-bg);
            color: var(--sf-text);
            min-height: 100vh;
        }

        .auth-shell {
            min-height: 100vh;
            display: flex;
        }

        /* Brand panel — desktop only. A quiet statement rather than a
           generic gradient hero: the app's real color language (navy +
           blue + the same green used for income throughout the product)
           and a literal cash-flow sparkline motif, echoing the actual
           dashboard chart, instead of decorative abstract shapes. */
        .auth-brand {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 44%;
            padding: 3.5rem 3.25rem;
            background: var(--sf-panel);
            background-image:
                radial-gradient(circle at 15% 15%, rgba(37, 99, 235, 0.35), transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(16, 185, 129, 0.18), transparent 40%);
            color: #f8fafc;
            position: relative;
        }

        @media (min-width: 992px) {
            .auth-brand { display: flex; }
        }

        .auth-brand-logo {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.35rem;
            letter-spacing: -0.01em;
        }

        .auth-brand-logo .icon {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            color: #93c5fd;
        }

        .auth-brand-headline {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 2.15rem;
            line-height: 1.2;
            letter-spacing: -0.01em;
            max-width: 21ch;
        }

        .auth-brand-sub {
            color: #94a3b8;
            font-size: 1rem;
            margin-top: 0.85rem;
            max-width: 34ch;
            line-height: 1.6;
        }

        .auth-brand-features {
            list-style: none;
            padding: 0;
            margin: 2rem 0 0;
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        .auth-brand-features li {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.92rem;
            color: #cbd5e1;
        }

        .auth-brand-features i {
            color: #10b981;
            font-size: 1.05rem;
        }

        .auth-sparkline {
            opacity: 0.9;
        }

        /* Form panel */
        .auth-form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
        }

        .auth-card {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 18px;
            box-shadow: var(--sf-card-shadow);
            padding: 2.25rem 2rem;
        }

        .auth-mobile-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--sf-text);
            margin-bottom: 1.75rem;
        }

        @media (min-width: 992px) {
            .auth-mobile-brand { display: none; }
        }

        .auth-mobile-brand .icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: var(--sf-primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .auth-title {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: -0.01em;
            margin-bottom: 0.25rem;
        }

        .auth-subtitle {
            color: var(--sf-muted);
            font-size: 0.92rem;
            margin-bottom: 1.75rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--sf-text);
            margin-bottom: 0.4rem;
        }

        .form-control {
            padding: 0.65rem 0.9rem;
            border-radius: 10px;
            border-color: var(--sf-border);
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--sf-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .input-group .form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .input-group-text {
            border-radius: 0 10px 10px 0;
            border-color: var(--sf-border);
            background: #f8fafc;
            cursor: pointer;
        }

        .btn-auth-primary {
            background: var(--sf-primary);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.7rem 1rem;
            border-radius: 10px;
            font-size: 0.95rem;
            width: 100%;
            transition: background 0.15s ease, transform 0.1s ease;
        }

        .btn-auth-primary:hover {
            background: #1d4ed8;
            color: #fff;
        }

        .btn-auth-primary:active {
            transform: scale(0.99);
        }

        .auth-switch {
            text-align: center;
            font-size: 0.88rem;
            color: var(--sf-muted);
            margin-top: 1.5rem;
        }

        .auth-switch a {
            color: var(--sf-primary);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-switch a:hover {
            text-decoration: underline;
        }

        .auth-status {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
        }

        .invalid-feedback {
            font-size: 0.8rem;
        }

        .form-check-input:checked {
            background-color: var(--sf-primary);
            border-color: var(--sf-primary);
        }

        .form-check-label {
            font-size: 0.88rem;
            color: var(--sf-muted);
        }

        .auth-forgot-link {
            font-size: 0.85rem;
            color: var(--sf-primary);
            text-decoration: none;
        }

        .auth-forgot-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <div class="auth-brand">
            <div>
                <div class="auth-brand-logo">
                    <span class="icon"><i class="bi bi-wallet2"></i></span>
                    <span>Smart Finance</span>
                </div>
                <div class="auth-brand-headline" style="margin-top: 3rem;">
                    {{ $brandHeadline ?? 'Personal financial management, built for real life.' }}
                </div>
                <div class="auth-brand-sub">
                    {{ $brandSub ?? 'Track every cedi in and out, stay ahead of your budgets, and watch your savings goals actually move.' }}
                </div>
                <ul class="auth-brand-features">
                    <li><i class="bi bi-check-circle-fill"></i> Income &amp; expense tracking</li>
                    <li><i class="bi bi-check-circle-fill"></i> Budgets with real-time alerts</li>
                    <li><i class="bi bi-check-circle-fill"></i> Savings goals you can watch grow</li>
                </ul>
            </div>

            <svg class="auth-sparkline" width="220" height="48" viewBox="0 0 220 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 34 L38 22 L74 30 L110 12 L146 20 L182 6 L218 14" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.55"/>
                <circle cx="2" cy="34" r="3" fill="#60a5fa"/>
                <circle cx="74" cy="30" r="3" fill="#60a5fa"/>
                <circle cx="146" cy="20" r="3" fill="#10b981"/>
                <circle cx="218" cy="14" r="3.5" fill="#10b981"/>
            </svg>
        </div>

        <div class="auth-form-panel">
            <div class="auth-card">
                <div class="auth-mobile-brand">
                    <span class="icon"><i class="bi bi-wallet2"></i></span>
                    <span>Smart Finance</span>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.togglePassword);
                var icon = btn.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        });
    </script>
</body>
</html>

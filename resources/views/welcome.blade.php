<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Smart Finance — a personal finance manager for tracking income, expenses, budgets, and savings goals.">
    <title>Smart Finance — Personal Financial Management</title>

    <script>
        (function () {
            var saved = localStorage.getItem('smartfinance-darkmode');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            var isDark = saved !== null ? saved === 'true' : prefersDark;
            if (isDark) document.documentElement.classList.add('dark-mode');
        })();
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:600,700,800|inter:400,500,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sf-primary: #2563eb;
            --sf-dark: #0f172a;
            --sf-muted: #64748b;
            --sf-bg: #f6f8fc;
            --sf-text: #172033;
            --sf-border: #e5e7eb;
            --sf-accent: #10b981;
        }
        html.dark-mode {
            --sf-bg: #0f172a;
            --sf-text: #f1f5f9;
            --sf-border: #1e293b;
            --sf-muted: #94a3b8;
        }
        html.dark-mode body { background: var(--sf-bg); }
        html.dark-mode .card-surface { background: #16233b; border-color: #253552; }
        html.dark-mode .feature-card { background: #16233b; border-color: #253552; }
        html.dark-mode .navbar-glass { background: rgba(15, 23, 42, 0.85) !important; }
        html.dark-mode .founder-note { background: #16233b; }

        * { font-family: 'Inter', -apple-system, sans-serif; }
        body { background: var(--sf-bg); color: var(--sf-text); }
        h1, h2, h3, .brand-font { font-family: 'Sora', sans-serif; letter-spacing: -0.01em; }

        .navbar-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--sf-border);
        }
        .brand-logo {
            width: 36px; height: 36px; border-radius: 10px;
            background: var(--sf-primary); color: #fff;
            display: flex; align-items: center; justify-content: center;
        }

        .hero {
            padding: 5rem 0 4rem;
        }
        .hero h1 {
            font-size: clamp(2.1rem, 4.5vw, 3.2rem);
            font-weight: 800;
            line-height: 1.15;
        }
        .hero .lead {
            color: var(--sf-muted);
            font-size: 1.1rem;
            max-width: 480px;
        }
        .btn-brand {
            background: var(--sf-primary); color: #fff; border: none;
            padding: 0.75rem 1.5rem; border-radius: 10px; font-weight: 600;
        }
        .btn-brand:hover { background: #1d4ed8; color: #fff; }
        .btn-brand-outline {
            border: 1px solid var(--sf-border); color: var(--sf-text);
            padding: 0.75rem 1.5rem; border-radius: 10px; font-weight: 600;
        }

        /* Product visual — a stylized mockup, not a screenshot, since the
           app doesn't have polished sample data to photograph yet. Built
           from the same tokens as the real dashboard so it's an honest
           representation rather than a generic stock illustration. */
        .mock-dashboard {
            background: var(--sf-dark);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.25);
            transform: rotate(1.2deg);
            color: #f1f5f9;
        }
        .mock-dashboard .mock-row { display: flex; gap: 0.75rem; margin-bottom: 0.75rem; }
        .mock-stat {
            background: rgba(255,255,255,0.06);
            border-radius: 12px;
            padding: 0.9rem;
            flex: 1;
        }
        .mock-stat .label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.04em; color: #94a3b8; }
        .mock-stat .value { font-size: 1.15rem; font-weight: 700; margin-top: 0.2rem; }
        .mock-chart {
            background: rgba(255,255,255,0.06);
            border-radius: 12px;
            padding: 1rem;
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 100px;
        }
        .mock-chart .bar { flex: 1; border-radius: 4px 4px 0 0; background: #3b82f6; }
        .mock-chart .bar.alt { background: #10b981; }

        .feature-card {
            background: #fff;
            border: 1px solid var(--sf-border);
            border-radius: 16px;
            padding: 1.75rem;
            height: 100%;
        }
        .feature-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; margin-bottom: 1rem;
        }

        .founder-note {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem;
        }

        .cta-band {
            background: var(--sf-dark);
            border-radius: 24px;
            padding: 3rem 2rem;
            color: #fff;
            text-align: center;
        }

        footer { color: var(--sf-muted); font-size: 0.85rem; }
        footer a { color: var(--sf-muted); text-decoration: none; }
        footer a:hover { color: var(--sf-text); }
    </style>
</head>
<body>

    <nav class="navbar navbar-glass sticky-top py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <span class="brand-logo"><i class="bi bi-wallet2"></i></span>
                <span class="brand-font fw-bold fs-5" style="color: var(--sf-text)">Smart Finance</span>
            </a>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('login') }}" class="btn btn-link text-decoration-none" style="color: var(--sf-text)">Log in</a>
                <a href="{{ route('register') }}" class="btn-brand">Get Started</a>
            </div>
        </div>
    </nav>

    <div class="container hero">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h1 class="mb-3">Know exactly where your money goes.</h1>
                <p class="lead mb-4">
                    Track income and expenses, set budgets that actually alert you before you overspend,
                    and watch your savings goals move — all in one place built for real, everyday finances.
                </p>
                <div class="d-flex flex-wrap gap-3 mb-3">
                    <a href="{{ route('register') }}" class="btn-brand">
                        Create your free account <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="{{ route('login') }}" class="btn-brand-outline">Log in</a>
                </div>
                <div class="small" style="color: var(--sf-muted)">
                    <i class="bi bi-check-circle-fill text-success me-1"></i>Free to use
                    <i class="bi bi-check-circle-fill text-success ms-3 me-1"></i>No card required
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mock-dashboard">
                    <div class="mock-row">
                        <div class="mock-stat">
                            <div class="label">Total Income</div>
                            <div class="value">GHS 10,000.00</div>
                        </div>
                        <div class="mock-stat">
                            <div class="label">Total Expenses</div>
                            <div class="value">GHS 6,420.00</div>
                        </div>
                    </div>
                    <div class="mock-row">
                        <div class="mock-stat">
                            <div class="label">Current Balance</div>
                            <div class="value" style="color:#60a5fa">GHS 3,580.00</div>
                        </div>
                    </div>
                    <div class="mock-chart">
                        <div class="bar" style="height:40%"></div>
                        <div class="bar alt" style="height:65%"></div>
                        <div class="bar" style="height:30%"></div>
                        <div class="bar alt" style="height:80%"></div>
                        <div class="bar" style="height:55%"></div>
                        <div class="bar alt" style="height:70%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-2">Everything you need, nothing you don't</h2>
            <p style="color: var(--sf-muted)">Built around how people actually manage money day to day.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:rgba(37,99,235,0.1); color:#2563eb"><i class="bi bi-arrow-left-right"></i></div>
                    <h5 class="fw-bold">Income &amp; Expense Tracking</h5>
                    <p class="small mb-0" style="color: var(--sf-muted)">Log transactions in seconds, tag them by category and payment method, and search or filter your full history anytime.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:rgba(217,119,6,0.1); color:#d97706"><i class="bi bi-pie-chart"></i></div>
                    <h5 class="fw-bold">Budgets with Real Alerts</h5>
                    <p class="small mb-0" style="color: var(--sf-muted)">Set a spending limit per category and actually get notified as you approach it — not after the damage is done.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:rgba(16,185,129,0.1); color:#10b981"><i class="bi bi-piggy-bank"></i></div>
                    <h5 class="fw-bold">Savings Goals</h5>
                    <p class="small mb-0" style="color: var(--sf-muted)">Set a target, log contributions as you make them, and watch your progress bar move toward the finish line.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:rgba(99,102,241,0.1); color:#6366f1"><i class="bi bi-lightbulb"></i></div>
                    <h5 class="fw-bold">Reports &amp; Insights</h5>
                    <p class="small mb-0" style="color: var(--sf-muted)">See where your money actually goes with monthly comparisons, category breakdowns, and plain-language observations.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:rgba(236,72,153,0.1); color:#ec4899"><i class="bi bi-arrow-repeat"></i></div>
                    <h5 class="fw-bold">Recurring Transactions</h5>
                    <p class="small mb-0" style="color: var(--sf-muted)">Set up bills and regular income once — subscriptions, rent, salary — and they record themselves going forward.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:rgba(6,182,212,0.1); color:#06b6d4"><i class="bi bi-phone"></i></div>
                    <h5 class="fw-bold">Built for Mobile</h5>
                    <p class="small mb-0" style="color: var(--sf-muted)">A quick-add sheet gets an expense logged in a few taps — no full-page forms when you're on the go.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <div class="founder-note">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="small fw-semibold text-uppercase mb-2" style="color: var(--sf-primary); letter-spacing: 0.04em">Why I built this</div>
                    <p class="mb-0" style="font-size: 1.05rem; color: var(--sf-text)">
                        Smart Finance started as a personal project to actually track my own money —
                        most budgeting apps felt built for someone else's finances. This one is built
                        to be genuinely useful day to day, and to keep growing as I use it myself.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('register') }}" class="btn-brand">Try it out</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="cta-band">
            <h2 class="fw-bold mb-2" style="color:#fff">Start tracking your money today</h2>
            <p class="mb-4" style="color:#94a3b8">Free to use. Takes less than a minute to set up.</p>
            <a href="{{ route('register') }}" class="btn-brand">
                Create your free account <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <footer class="container py-4 text-center border-top">
        <div class="mb-2">
            <span class="brand-logo" style="width:28px;height:28px;font-size:0.85rem"><i class="bi bi-wallet2"></i></span>
        </div>
        &copy; {{ date('Y') }} Smart Finance. Built by Abdul-Wahidu.
    </footer>

</body>
</html>

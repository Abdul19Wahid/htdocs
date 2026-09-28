<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Something went wrong' }} — Smart Finance</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:600,700|inter:400,500,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        {{--
            Deliberately self-contained: an error page can't assume the DB,
            session, or auth state are working (that may be exactly what
            broke), so this doesn't extend the main layout, run any
            queries, or call auth()->user() anywhere.
        --}}
        * { font-family: 'Inter', -apple-system, sans-serif; box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f6f8fc;
            color: #172033;
            padding: 1.5rem;
        }
        @media (prefers-color-scheme: dark) {
            body { background: #0f172a; color: #f1f5f9; }
            .card { background: #16233b !important; }
            .btn-light { background: #1e293b !important; color: #f1f5f9 !important; border-color: #334155 !important; }
        }
        .card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.10);
            padding: 2.5rem 2rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
        }
        .icon-wrap {
            width: 64px; height: 64px;
            border-radius: 16px;
            background: {{ $accentBg ?? 'rgba(37, 99, 235, 0.1)' }};
            color: {{ $accent ?? '#2563eb' }};
            display: flex; align-items: center; justify-content: center;
            font-size: 1.75rem;
            margin: 0 auto 1.25rem;
        }
        h1 { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.35rem; margin: 0 0 0.5rem; }
        p.desc { color: #64748b; font-size: 0.92rem; margin: 0 0 1.75rem; line-height: 1.6; }
        .btn-primary-brand {
            background: #2563eb; color: #fff; border: none;
            padding: 0.65rem 1.25rem; border-radius: 10px;
            font-weight: 600; font-size: 0.9rem; text-decoration: none;
            display: inline-block;
        }
        .btn-primary-brand:hover { background: #1d4ed8; color: #fff; }
        .btn-light {
            background: #f8fafc; color: #172033; border: 1px solid #e5e7eb;
            padding: 0.65rem 1.25rem; border-radius: 10px;
            font-weight: 600; font-size: 0.9rem; text-decoration: none;
            display: inline-block; margin-left: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap"><i class="bi {{ $icon ?? 'bi-exclamation-triangle' }}"></i></div>
        <h1>{{ $title ?? 'Something went wrong' }}</h1>
        <p class="desc">{{ $message ?? 'Please try again in a moment.' }}</p>
        <a href="{{ url('/dashboard') }}" class="btn-primary-brand">Go to dashboard</a>
        <a href="javascript:history.back()" class="btn-light">Go back</a>
    </div>
</body>
</html>

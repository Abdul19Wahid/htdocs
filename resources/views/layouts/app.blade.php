<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Apply dark mode before first paint / before any other scripts run,
         so charts and other scripts reading the class see the right value
         immediately instead of waiting for DOMContentLoaded (which fires
         after page scripts have already executed and rendered in the
         wrong colors). Also avoids a flash of the wrong theme. -->
    <script>
        (function () {
            var saved = localStorage.getItem('smartfinance-darkmode');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            var isDark = saved !== null ? saved === 'true' : prefersDark;
            if (isDark) document.documentElement.classList.add('dark-mode');
        })();
    </script>

    <title>{{ config('app.name', 'Smart Finance') }} — Personal Financial Management</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <style>
        :root {
            --sf-primary: #2563eb;
            --sf-dark: #0f172a;
            --sf-muted: #64748b;
            --sf-bg: #f6f8fc;
            --sf-sidebar: #0f172a;
            --sf-text: #172033;
            --sf-border: #e5e7eb;
            --sf-card-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
            --sf-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --sf-bg: #0f172a;
                --sf-text: #f1f5f9;
                --sf-border: #1e293b;
                --sf-muted: #94a3b8;
                --sf-card-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
            }
        }

        html.dark-mode {
            color-scheme: dark;
        }

        html.dark-mode,
        html.dark-mode body {
            --sf-bg: #0f172a;
            --sf-text: #f1f5f9;
            --sf-border: #1e293b;
            --sf-muted: #94a3b8;
            --sf-card-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
        }

        html.dark-mode body {
            background: var(--sf-bg);
            color: var(--sf-text);
        }

        html.dark-mode .card {
            background: #1e293b;
            border-color: var(--sf-border);
            color: var(--sf-text);
        }

        html.dark-mode .topbar {
            background: #1e293b;
            border-bottom-color: var(--sf-border);
        }

        html.dark-mode .form-control,
        html.dark-mode .form-select {
            background: #0f172a;
            border-color: var(--sf-border);
            color: var(--sf-text);
        }

        html.dark-mode .form-control:focus,
        html.dark-mode .form-select:focus {
            background: #0f172a;
            color: var(--sf-text);
        }

        html.dark-mode .table {
            color: var(--sf-text);
        }

        html.dark-mode .table tbody tr {
            border-bottom-color: var(--sf-border);
        }

        html.dark-mode .table tbody tr:hover {
            background-color: rgba(37, 99, 235, 0.1);
        }

        html.dark-mode .btn-light {
            background: #1e293b;
            border-color: var(--sf-border);
            color: var(--sf-text);
        }

        html.dark-mode .btn-light:hover {
            background: #334155;
        }

        html.dark-mode .dropdown-menu {
            background: #1e293b;
            border-color: var(--sf-border);
        }

        html.dark-mode .dropdown-item {
            color: var(--sf-text);
        }

        html.dark-mode .dropdown-item:hover {
            background-color: #334155;
        }

        html.dark-mode .alert {
            border-color: var(--sf-border);
        }

        html.dark-mode .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #86efac;
        }

        html.dark-mode .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
        }

        html.dark-mode .alert-info {
            background-color: rgba(2, 132, 199, 0.1);
            color: #7dd3fc;
        }

        /* Plain Bootstrap utility classes used throughout the views —
           these don't automatically follow our custom dark-mode class
           (only Bootstrap's own data-bs-theme="dark" would do that, and
           this app doesn't use that mechanism), so without these
           overrides their text renders in Bootstrap's light-mode colors
           and becomes nearly invisible against dark card backgrounds. */
        html.dark-mode .text-muted {
            color: var(--sf-muted) !important;
        }

        html.dark-mode .text-dark {
            color: var(--sf-text) !important;
        }

        html.dark-mode .text-secondary,
        html.dark-mode .text-body-secondary {
            color: var(--sf-muted) !important;
        }

        html.dark-mode .bg-white,
        html.dark-mode .bg-light,
        html.dark-mode .text-bg-light {
            background-color: #1e293b !important;
            color: var(--sf-text) !important;
        }

        html.dark-mode .border-top,
        html.dark-mode .border-bottom,
        html.dark-mode .border {
            border-color: var(--sf-border) !important;
        }

        /* Skip Links */
        .skip-links {
            position: absolute;
            top: -40px;
            left: 0;
            z-index: 10000;
            background: #2563eb;
        }

        .skip-link {
            display: inline-block;
            padding: 8px 16px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .skip-link:focus {
            top: 0;
            outline: 2px solid #fff;
            outline-offset: 2px;
        }

        .skip-link:hover {
            background: #1d4ed8;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            background: var(--sf-bg);
            color: #172033;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #0f172a 0%, #172554 100%);
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 1000;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .brand {
            font-size: 1.3rem;
            font-weight: 800;
            color: #ffffff;
            text-decoration: none;
            transition: opacity 0.2s ease;
        }

        .brand:hover {
            opacity: 0.9;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .brand:hover .brand-icon {
            background: rgba(255, 255, 255, 0.18);
        }

        .sidebar-section {
            color: #94a3b8;
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-top: 25px;
            margin-bottom: 10px;
        }

        .sidebar .nav-link {
            color: #cbd5e1;
            border-radius: 10px;
            padding: .8rem .85rem;
            margin-bottom: 4px;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            transform: translateX(2px);
        }

        .sidebar .nav-link.active {
            background: rgba(37, 99, 235, 0.25);
            color: #fff;
            border-left: 3px solid #2563eb;
            padding-left: calc(0.85rem - 3px);
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }

        .main-wrap {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            height: 72px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            flex-shrink: 0;
        }

        .topbar h5 {
            color: #172033;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .topbar small {
            color: #94a3b8;
            font-weight: 500;
        }

        .page-content {
            padding: 30px;
            flex: 1;
            overflow-y: auto;
        }

        .card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.1);
        }

        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: default;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.12);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .page-title {
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .small-muted {
            font-size: .82rem;
            color: var(--sf-muted);
            font-weight: 500;
        }

        .table {
            margin-bottom: 0;
        }

        .table > :not(caption) > * > * {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .table th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #64748b;
            border: none;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .table tbody tr:hover {
            background-color: rgba(37, 99, 235, 0.02);
        }

        .progress {
            height: 8px;
            border-radius: 20px;
            background-color: rgba(0, 0, 0, 0.05);
        }

        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.6rem 0.875rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-label {
            color: #172033;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
        }

        .btn:not(.btn-sm, .btn-group-sm .btn) {
            padding: 0.7rem 1.25rem;
        }

        .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-danger {
            background: #ef4444;
            border-color: #ef4444;
        }

        .btn-danger:hover {
            background: #dc2626;
            border-color: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-success {
            background: #10b981;
            border-color: #10b981;
        }

        .btn-success:hover {
            background: #059669;
            border-color: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-outline-primary {
            color: #2563eb;
            border-color: #2563eb;
        }

        .btn-outline-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
            transform: translateY(-1px);
        }

        .badge {
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 0.4rem 0.75rem;
        }

        .alert {
            border-radius: 10px;
            border: 1px solid;
            padding: 1rem 1.25rem;
        }

        .alert-success {
            background-color: #f0fdf4;
            border-color: #10b981;
            color: #047857;
        }

        .alert-info {
            background-color: #f0f9ff;
            border-color: #0284c7;
            color: #0369a1;
        }

        .alert-danger {
            background-color: #fef2f2;
            border-color: #ef4444;
            color: #991b1b;
        }

        .modal-content {
            border-radius: 12px;
            border: none;
        }

        .dropdown-menu {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f1f5f9;
        }

        .mobile-menu {
            display: none;
        }

        /* Tablet and smaller */
        @media (max-width: 991px) {
            .sidebar {
                width: 220px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrap {
                margin-left: 0;
            }

            .mobile-menu {
                display: inline-flex;
            }

            .page-content {
                padding: 20px;
            }

            .topbar {
                padding: 0 16px;
                height: 64px;
            }

            .card {
                border-radius: 12px;
            }

            .stat-card {
                margin-bottom: 0.5rem;
            }
        }

        /* Small phones */
        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
                position: fixed;
            }

            .page-content {
                padding: 16px;
            }

            .topbar {
                padding: 0 12px;
                height: 56px;
            }

            .topbar h5 {
                font-size: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .card {
                margin-bottom: 1rem;
            }

            .floating-actions {
                bottom: 20px;
                right: 20px;
            }

            .fab-menu {
                width: 50px;
                height: 50px;
            }

            .fab-main {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .fab-item {
                width: 140px;
                padding: 10px 12px;
                font-size: 13px;
            }

            .btn {
                padding: 0.5rem 0.875rem;
                font-size: 0.875rem;
            }

            .btn:not(.btn-sm, .btn-group-sm .btn) {
                padding: 0.5rem 1rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Accessibility Improvements */
        a:focus,
        button:focus,
        input:focus,
        select:focus,
        textarea:focus {
            outline: 2px solid #2563eb;
            outline-offset: 2px;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        .slide-in-up {
            animation: slideInUp 0.4s ease-out;
        }

        .slide-in-right {
            animation: slideInRight 0.3s ease-out;
        }

        /* Improved Focus States */
        .btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-control:focus,
        .form-select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Floating Action Button */
        .floating-actions {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 900;
        }

        .fab-menu {
            position: relative;
            width: 60px;
            height: 60px;
        }

        .fab-main {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
            z-index: 902;
        }

        .fab-main:hover {
            transform: scale(1.1) translateY(-2px);
            box-shadow: 0 12px 32px rgba(37, 99, 235, 0.4);
        }

        .fab-main.active {
            transform: rotate(45deg);
        }

        .fab-submenu {
            position: absolute;
            bottom: 0;
            right: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
            opacity: 0;
            visibility: hidden;
            transform: scale(0.8);
            transform-origin: bottom right;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fab-submenu.active {
            opacity: 1;
            visibility: visible;
            transform: scale(1);
        }

        .fab-item {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 160px;
            padding: 12px 16px;
            background: white;
            border-radius: 10px;
            border: none;
            color: #172033;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
            font-size: 14px;
            cursor: pointer;
        }

        .fab-item:hover {
            transform: translateX(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .fab-item i {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .fab-item:nth-child(1) {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .fab-item:nth-child(1) i {
            color: #ef4444;
        }

        .fab-item:nth-child(2) {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .fab-item:nth-child(2) i {
            color: #10b981;
        }

        /* Quick Add Bottom Sheet */
        .quick-add-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease;
        }

        .quick-add-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .quick-add-sheet {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1050;
            background: white;
            border-radius: 20px 20px 0 0;
            padding: 12px 20px 24px;
            max-width: 480px;
            margin: 0 auto;
            box-shadow: 0 -8px 30px rgba(0, 0, 0, 0.15);
            transform: translateY(100%);
            transition: transform 0.25s ease;
        }

        .quick-add-sheet.active {
            transform: translateY(0);
        }

        .quick-add-handle {
            width: 40px;
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            margin: 4px auto 14px;
        }

        .quick-type-btn {
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
        }

        .quick-type-btn.active {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        .quick-add-amount-wrap {
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 8px;
        }

        .quick-add-currency {
            font-size: 1.1rem;
            font-weight: 600;
            color: #94a3b8;
        }

        .quick-add-amount {
            border: none;
            outline: none;
            font-size: 2.75rem;
            font-weight: 700;
            width: 220px;
            text-align: center;
            color: #172033;
        }

        .quick-add-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            margin-bottom: 18px;
            max-height: 140px;
            overflow-y: auto;
        }

        .quick-add-chip {
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
        }

        .quick-add-chip.active {
            background: #172033;
            border-color: #172033;
            color: white;
        }

        html.dark-mode .quick-add-sheet {
            background: #1f2937;
            color: #f1f5f9;
        }

        html.dark-mode .quick-add-amount {
            color: #f1f5f9;
        }

        html.dark-mode .quick-add-chip {
            background: #111827;
            border-color: #334155;
            color: #cbd5e1;
        }

        @media (max-width: 991px) {
            .floating-actions {
                bottom: 24px;
                right: 20px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

@auth

<!-- Skip Links -->
<div class="skip-links">
    <a href="#mainContent" class="skip-link">Skip to main content</a>
    <a href="#sidebar" class="skip-link">Skip to sidebar</a>
    <a href="#userMenu" class="skip-link">Skip to user menu</a>
</div>

<div class="sidebar" id="sidebar" role="navigation" aria-label="Main navigation">

    <div class="p-4">

        <a href="{{ url('/dashboard') }}" class="brand d-flex align-items-center gap-2">

            <div class="brand-icon">
                <i class="bi bi-wallet2 fs-4"></i>
            </div>

            <div class="d-flex flex-column lh-sm">
                <span>Smart Finance</span>
                <small class="text-white-50" style="font-size: 0.65rem; font-weight: 400; letter-spacing: 0.02em;">Personal Financial Management</small>
            </div>

        </a>

        <div class="sidebar-section">
            Overview
        </div>

        <nav class="nav flex-column">

            <a href="{{ url('/dashboard') }}" class="nav-link">
                <i class="bi bi-grid-1x2"></i>
                Dashboard
            </a>

            <a href="{{ route('reports.index') }}" class="nav-link">
                <i class="bi bi-bar-chart"></i>
                Reports
            </a>

        </nav>


        <div class="sidebar-section">
            Transactions
        </div>

        <nav class="nav flex-column">

            <a href="{{ route('transactions.index') }}" class="nav-link">
                <i class="bi bi-arrow-left-right"></i>
                Transactions
            </a>

            <a href="{{ route('incomes.index') }}" class="nav-link">
                <i class="bi bi-arrow-down-circle"></i>
                Income
            </a>

            <a href="{{ route('expenses.index') }}" class="nav-link">
                <i class="bi bi-arrow-up-circle"></i>
                Expenses
            </a>

            <a href="{{ url('/recurring-transactions') }}" class="nav-link">
                <i class="bi bi-arrow-repeat"></i>
                Recurring
            </a>

        </nav>


        <div class="sidebar-section">
            Planning
        </div>

        <nav class="nav flex-column">

            <a href="{{ route('budgets.index') }}" class="nav-link">
                <i class="bi bi-pie-chart"></i>
                Budgets
            </a>

            <a href="{{ route('savings-goals.index') }}" class="nav-link">
                <i class="bi bi-bullseye"></i>
                Savings Goals
            </a>

        </nav>


        <div class="sidebar-section">
            Setup
        </div>

        <nav class="nav flex-column">

            <a href="{{ url('/categories') }}" class="nav-link">
                <i class="bi bi-tags"></i>
                Categories
            </a>

            <a href="{{ url('/income-sources') }}" class="nav-link">
                <i class="bi bi-wallet"></i>
                Income Sources
            </a>

            <a href="{{ url('/payment-methods') }}" class="nav-link">
                <i class="bi bi-credit-card"></i>
                Payment Methods
            </a>

            <a href="{{ route('settings.edit') }}" class="nav-link">
                <i class="bi bi-gear"></i>
                Settings
            </a>

        </nav>

    </div>

</div>


<div class="main-wrap">

    <header class="topbar">

        <div class="d-flex align-items-center gap-3">

            <button
                class="btn btn-light mobile-menu"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#mobileSidebar"
            >
                <i class="bi bi-list fs-4"></i>
            </button>

            <div>
                <h5 class="mb-0 fw-bold">
                    {{ $title ?? 'Personal Finance' }}
                </h5>

                <small class="text-muted">
                    Track your money with clarity.
                </small>
            </div>

        </div>


        <div class="dropdown">

            <button
                class="btn btn-light dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                id="userMenu"
                aria-label="User menu"
                aria-expanded="false"
            >
                <i class="bi bi-person-circle me-1"></i>

                {{ auth()->user()->name }}
            </button>

            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">

                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person me-2"></i>
                        Profile
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="{{ route('settings.edit') }}">
                        <i class="bi bi-gear me-2"></i>
                        Settings
                    </a>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="dropdown-item text-danger"
                        >
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Logout
                        </button>

                    </form>

                </li>

            </ul>

        </div>

    </header>


    <main class="page-content" id="mainContent" role="main">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>

                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>
        @endif


        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-2"></i>

                {{ session('error') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>
        @endif


        @if ($errors->any())

            <div class="alert alert-danger">

                <strong>Please fix the following:</strong>

                <ul class="mb-0 mt-2">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif


        {{ $slot }}

    </main>

</div>


<!-- Mobile Sidebar -->

<div
    class="offcanvas offcanvas-start"
    tabindex="-1"
    id="mobileSidebar"
>

    <div class="offcanvas-header">

        <h5 class="offcanvas-title fw-bold">
            Smart Finance
        </h5>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="offcanvas"
        ></button>

    </div>

    <div class="offcanvas-body">

        <nav class="nav flex-column gap-1">

            <a href="{{ url('/dashboard') }}" class="nav-link">
                <i class="bi bi-grid me-2"></i>
                Dashboard
            </a>

            <a href="{{ route('incomes.index') }}" class="nav-link">
                <i class="bi bi-arrow-down-circle me-2"></i>
                Income
            </a>

            <a href="{{ route('expenses.index') }}" class="nav-link">
                <i class="bi bi-arrow-up-circle me-2"></i>
                Expenses
            </a>

            <a href="{{ route('budgets.index') }}" class="nav-link">
                <i class="bi bi-pie-chart me-2"></i>
                Budgets
            </a>

            <a href="{{ route('savings-goals.index') }}" class="nav-link">
                <i class="bi bi-bullseye me-2"></i>
                Savings Goals
            </a>

            <a href="{{ route('reports.index') }}" class="nav-link">
                <i class="bi bi-bar-chart me-2"></i>
                Reports
            </a>

            <a href="{{ route('settings.edit') }}" class="nav-link">
                <i class="bi bi-gear me-2"></i>
                Settings
            </a>

        </nav>

    </div>

</div>
<!-- Floating Action Button -->
<div class="floating-actions">
    <div class="fab-menu">
        <button class="fab-main" id="fabMain" title="Quick add">
            <i class="bi bi-plus-lg"></i>
        </button>
        <div class="fab-submenu" id="fabSubmenu">
            <button type="button" class="fab-item" title="Add Expense" data-quick-add="expense">
                <i class="bi bi-arrow-up-circle"></i>
                <span>Expense</span>
            </button>
            <button type="button" class="fab-item" title="Add Income" data-quick-add="income">
                <i class="bi bi-arrow-down-circle"></i>
                <span>Income</span>
            </button>
        </div>
    </div>
</div>

<!-- Quick Add Bottom Sheet -->
<div class="quick-add-overlay" id="quickAddOverlay"></div>
<div class="quick-add-sheet" id="quickAddSheet" role="dialog" aria-modal="true" aria-labelledby="quickAddTitle">
    <div class="quick-add-handle"></div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="btn-group" role="group" aria-label="Entry type">
            <button type="button" class="btn btn-sm quick-type-btn active" data-type="expense" id="quickAddTitle">Expense</button>
            <button type="button" class="btn btn-sm quick-type-btn" data-type="income">Income</button>
        </div>
        <button type="button" class="btn-close" id="quickAddClose" aria-label="Close"></button>
    </div>

    <div class="text-center mb-3">
        <div class="quick-add-amount-wrap">
            <span class="quick-add-currency">{{ auth()->user()->currency ?? 'GHS' }}</span>
            <input type="text" inputmode="decimal" class="quick-add-amount" id="quickAddAmount" placeholder="0.00" autocomplete="off">
        </div>
    </div>

    <div class="quick-add-chips" id="quickAddChips">
        <!-- populated by JS from data attributes below -->
    </div>

    <div id="quickAddError" class="text-danger small text-center mb-2" style="display:none;"></div>

    <button type="button" class="btn btn-primary w-100 py-2 fw-semibold" id="quickAddSave">
        <span id="quickAddSaveLabel">Save</span>
        <span id="quickAddSaveSpinner" class="spinner-border spinner-border-sm ms-2" style="display:none;"></span>
    </button>

    <div class="text-center mt-3">
        <a href="{{ route('expenses.create') }}" id="quickAddFullFormLink" class="small text-muted">Need more detail (receipt, notes)? Use the full form</a>
    </div>

    <!-- Data source for chips, kept out of JS so it stays server-rendered and per-user -->
    <script type="application/json" id="quickAddData">
        {
            "categories": @json($quickAddCategories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])),
            "incomeSources": @json($quickAddIncomeSources->map(fn($s) => ['id' => $s->id, 'name' => $s->name])),
            "expenseStoreUrl": @json(route('expenses.store')),
            "incomeStoreUrl": @json(route('incomes.store')),
            "expensesCreateUrl": @json(route('expenses.create')),
            "incomesCreateUrl": @json(route('incomes.create'))
        }
    </script>
</div>
@else

    {{ $slot }}

@endauth


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

@stack('scripts')

<script>
// Dark Mode Toggle
class DarkModeManager {
    constructor() {
        this.storageKey = 'smartfinance-darkmode';
        this.init();
    }

    init() {
        // Check for saved preference or system preference
        const savedMode = localStorage.getItem(this.storageKey);
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedMode) {
            this.setMode(savedMode === 'true');
        } else if (prefersDark) {
            this.setMode(true);
        }

        // Listen for system preference changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem(this.storageKey)) {
                this.setMode(e.matches);
            }
        });
    }

    setMode(isDark) {
        if (isDark) {
            document.documentElement.classList.add('dark-mode');
        } else {
            document.documentElement.classList.remove('dark-mode');
        }
        localStorage.setItem(this.storageKey, isDark);
    }

    toggle() {
        const isDark = document.documentElement.classList.contains('dark-mode');
        this.setMode(!isDark);
    }
}

// Floating Action Button
class FloatingActionButton {
    constructor() {
        this.fabMain = document.getElementById('fabMain');
        this.fabSubmenu = document.getElementById('fabSubmenu');
        
        if (this.fabMain) {
            this.init();
        }
    }

    init() {
        this.fabMain.addEventListener('click', () => this.toggle());
        document.addEventListener('click', (e) => {
            if (!this.fabMain.contains(e.target) && !this.fabSubmenu.contains(e.target)) {
                this.close();
            }
        });

        // Keyboard support
        this.fabMain.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.toggle();
            }
        });
    }

    toggle() {
        this.fabMain.classList.toggle('active');
        this.fabSubmenu.classList.toggle('active');
    }

    close() {
        this.fabMain.classList.remove('active');
        this.fabSubmenu.classList.remove('active');
    }
}

// Quick Add Bottom Sheet — Expense/Income in a few taps, no full-page form
class QuickAddSheet {
    constructor() {
        this.overlay = document.getElementById('quickAddOverlay');
        this.sheet = document.getElementById('quickAddSheet');
        if (!this.overlay || !this.sheet) return;

        const dataEl = document.getElementById('quickAddData');
        this.data = dataEl ? JSON.parse(dataEl.textContent) : { categories: [], incomeSources: [] };

        this.amountEl = document.getElementById('quickAddAmount');
        this.chipsEl = document.getElementById('quickAddChips');
        this.errorEl = document.getElementById('quickAddError');
        this.saveBtn = document.getElementById('quickAddSave');
        this.saveLabel = document.getElementById('quickAddSaveLabel');
        this.saveSpinner = document.getElementById('quickAddSaveSpinner');
        this.fullFormLink = document.getElementById('quickAddFullFormLink');

        this.type = 'expense';
        this.selectedId = null;

        this.bindOpenTriggers();
        this.bindTypeToggle();
        this.bindClose();
        this.bindSave();
        this.renderChips();
    }

    bindOpenTriggers() {
        document.querySelectorAll('[data-quick-add]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.open(btn.dataset.quickAdd);
                // Close the FAB submenu it lives inside, if any
                document.getElementById('fabMain')?.classList.remove('active');
                document.getElementById('fabSubmenu')?.classList.remove('active');
            });
        });
    }

    bindTypeToggle() {
        document.querySelectorAll('.quick-type-btn').forEach(btn => {
            btn.addEventListener('click', () => this.setType(btn.dataset.type));
        });
    }

    bindClose() {
        document.getElementById('quickAddClose')?.addEventListener('click', () => this.close());
        this.overlay.addEventListener('click', () => this.close());
    }

    bindSave() {
        this.saveBtn.addEventListener('click', () => this.save());
        // Enter key on the amount field submits too
        this.amountEl.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); this.save(); }
        });
    }

    open(type) {
        this.setType(type);
        this.overlay.classList.add('active');
        this.sheet.classList.add('active');
        this.amountEl.value = '';
        this.selectedId = null;
        this.hideError();
        setTimeout(() => this.amountEl.focus(), 150);
    }

    close() {
        this.overlay.classList.remove('active');
        this.sheet.classList.remove('active');
        if (window.__quickAddDirty) {
            window.__quickAddDirty = false;
            window.location.reload();
        }
    }

    setType(type) {
        this.type = type;
        this.selectedId = null;
        document.querySelectorAll('.quick-type-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.type === type);
        });
        this.fullFormLink.href = type === 'expense' ? this.data.expensesCreateUrl : this.data.incomesCreateUrl;
        this.renderChips();
    }

    renderChips() {
        const items = this.type === 'expense' ? this.data.categories : this.data.incomeSources;
        this.chipsEl.innerHTML = '';
        if (!items || items.length === 0) {
            this.chipsEl.innerHTML = `<span class="small text-muted">No ${this.type === 'expense' ? 'categories' : 'income sources'} yet — you can still save without one.</span>`;
            return;
        }
        items.forEach(item => {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'quick-add-chip';
            chip.textContent = item.name;
            chip.dataset.id = item.id;
            chip.addEventListener('click', () => {
                const alreadyActive = chip.classList.contains('active');
                this.chipsEl.querySelectorAll('.quick-add-chip').forEach(c => c.classList.remove('active'));
                if (!alreadyActive) {
                    chip.classList.add('active');
                    this.selectedId = item.id;
                } else {
                    this.selectedId = null;
                }
            });
            this.chipsEl.appendChild(chip);
        });
    }

    showError(msg) {
        this.errorEl.textContent = msg;
        this.errorEl.style.display = 'block';
    }

    hideError() {
        this.errorEl.style.display = 'none';
    }

    async save() {
        const amount = parseFloat(this.amountEl.value);
        if (!amount || amount <= 0) {
            this.showError('Enter an amount first.');
            this.amountEl.focus();
            return;
        }
        this.hideError();
        this.saveLabel.textContent = 'Saving...';
        this.saveSpinner.style.display = 'inline-block';
        this.saveBtn.disabled = true;

        const isExpense = this.type === 'expense';
        const body = new URLSearchParams({
            amount: amount.toFixed(2),
            date: new Date().toISOString().slice(0, 10),
        });
        if (this.selectedId) {
            body.append(isExpense ? 'category_id' : 'income_source_id', this.selectedId);
        }

        try {
            const res = await fetch(isExpense ? this.data.expenseStoreUrl : this.data.incomeStoreUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body,
            });

            if (!res.ok) {
                const errData = await res.json().catch(() => null);
                throw new Error(errData?.message || 'Could not save. Check your connection and try again.');
            }

            // Quick success flash, then reset for the next entry — so
            // recording several expenses in a row stays a few-second habit.
            this.saveLabel.textContent = 'Saved ✓';
            setTimeout(() => {
                this.saveLabel.textContent = 'Save';
                this.amountEl.value = '';
                this.chipsEl.querySelectorAll('.quick-add-chip').forEach(c => c.classList.remove('active'));
                this.selectedId = null;
                this.amountEl.focus();
                // If the current page shows totals that just changed, refresh
                // once the user is done rather than mid-entry.
                window.__quickAddDirty = true;
            }, 600);
        } catch (err) {
            this.showError(err.message || 'Something went wrong.');
        } finally {
            this.saveSpinner.style.display = 'none';
            this.saveBtn.disabled = false;
        }
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    new DarkModeManager();
    new FloatingActionButton();
    new QuickAddSheet();
    
    // Add fade-in animation to content
    const pageContent = document.querySelector('.page-content');
    if (pageContent) {
        pageContent.classList.add('fade-in');
    }

    // Smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && document.querySelector(href)) {
                e.preventDefault();
                document.querySelector(href).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Enhanced form validation feedback
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    });

    // Add loading state to buttons
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.form && this.form.checkValidity()) {
                const originalText = this.innerHTML;

                // Let the browser's default submit action fire FIRST.
                // Disabling the button synchronously inside its own click
                // handler can cancel the form submission entirely.
                setTimeout(() => {
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                }, 0);

                // Safety net: if the page hasn't navigated away after a few
                // seconds (e.g. validation failed server-side and reloaded
                // the same page), re-enable the button so it's not stuck.
                setTimeout(() => {
                    this.disabled = false;
                    this.innerHTML = originalText;
                }, 8000);
            }
        });
    });
});
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dashboard Analitik Penjualan – visualisasi dan analisis data penjualan berbasis Laravel 11">
    <title>@yield('title', 'Dashboard') – Analitik Penjualan</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bs-primary: #696cff;
            --bs-body-bg: #f5f5f9;
            --bs-body-color: #697a8d;
            --bs-heading-color: #566a7f;
            --bs-card-shadow: 0 0.125rem 0.25rem rgba(161, 172, 184, 0.15);
            --bs-border-color: #d9dee3;
        }

        body {
            font-family: 'Public Sans', sans-serif;
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
        }

        /* Override Bootstrap Primary Color */
        .text-primary { color: var(--bs-primary) !important; }
        .bg-primary { background-color: var(--bs-primary) !important; }
        .btn-primary { 
            background-color: var(--bs-primary); 
            border-color: var(--bs-primary); 
            box-shadow: 0 0.125rem 0.25rem 0 rgba(105, 108, 255, 0.4);
        }
        .btn-primary:hover {
            background-color: #5f61e6;
            border-color: #5f61e6;
            transform: translateY(-1px);
        }

        /* Sidebar */
        #sidebar {
            width: 260px;
            min-height: 100vh;
            background: #ffffff;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            border-right: 1px solid var(--bs-border-color);
            transition: width 0.2s ease;
        }

        #sidebar .sidebar-brand {
            padding: 1.5rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--bs-heading-color);
            white-space: nowrap;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        #sidebar .sidebar-brand span {
            color: var(--bs-heading-color);
        }

        #sidebar .nav-item {
            margin: 0 1rem;
        }

        #sidebar .nav-link {
            color: var(--bs-body-color);
            padding: 0.6rem 1rem;
            font-size: 0.9375rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-radius: 0.375rem;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap;
            margin-bottom: 0.25rem;
        }

        #sidebar .nav-link:hover {
            background: rgba(67, 89, 113, 0.04);
            color: var(--bs-heading-color);
        }

        #sidebar .nav-link.active {
            background: rgba(105, 108, 255, 0.16) !important;
            color: var(--bs-primary) !important;
            font-weight: 600;
        }

        #sidebar .nav-label {
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            color: #a1acb8;
            padding: 1.5rem 2rem 0.5rem;
            margin-top: 0.5rem;
        }

        /* Main content */
        #main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        #topbar {
            background: #ffffff;
            margin: 1.2rem 1.5rem;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-radius: 0.5rem;
            box-shadow: var(--bs-card-shadow);
        }

        #topbar h5 {
            font-weight: 600;
            font-size: 1rem;
            margin: 0;
            color: var(--bs-heading-color);
        }

        /* Page content */
        .page-content {
            padding: 0 1.5rem 1.5rem 1.5rem;
            flex: 1;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: var(--bs-card-shadow);
            background-color: #fff;
            background-clip: padding-box;
        }

        .stat-card {
            padding: 1.5rem;
        }

        .stat-card .icon-box {
            width: 42px;
            height: 42px;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--bs-border-color);
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--bs-heading-color);
            padding: 1.25rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Table */
        .table {
            color: var(--bs-body-color);
        }
        .table th {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #566a7f;
            border-bottom: 1px solid var(--bs-border-color);
            padding: 0.625rem 1.25rem;
            background-color: #f9fafb;
        }

        .table td {
            font-size: 0.9375rem;
            vertical-align: middle;
            padding: 0.625rem 1.25rem;
            border-bottom: 1px solid var(--bs-border-color);
        }

        /* Alert */
        .alert { border-radius: 0.375rem; font-size: 0.9375rem; }

        /* Responsive */
        @media (max-width: 991px) {
            #sidebar { width: 0; overflow: hidden; }
            #main-content { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<nav id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-bar-chart-fill me-2" style="color:#4dabf7"></i>
        <span>Analitik</span> Penjualan
    </div>

    <div class="nav-label">Menu Utama</div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" id="nav-dashboard">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('import.*') ? 'active' : '' }}" href="{{ route('import.index') }}" id="nav-import">
                <i class="bi bi-upload"></i> Import Excel
            </a>
        </li>
        <li class="nav-item mt-2">
            <a class="nav-link collapsed" href="#exportSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="exportSubmenu">
                <i class="bi bi-download"></i> Export Data
                <i class="bi bi-chevron-down ms-auto" style="font-size: 0.75rem;"></i>
            </a>
            <div class="collapse" id="exportSubmenu">
                <ul class="nav flex-column ms-3" style="border-left: 1px solid rgba(255,255,255,0.1);">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('export.excel') }}">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('export.pdf') }}">
                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>

{{-- Main Content --}}
<div id="main-content">
    {{-- Topbar --}}
    <div id="topbar">
        <h5>@yield('page-title', 'Dashboard')</h5>
        <div class="ms-auto text-muted" style="font-size:0.8rem">
            <i class="bi bi-calendar3 me-1"></i>{{ now()->isoFormat('dddd, D MMMM YYYY') }}
        </div>
    </div>

    {{-- Flash Messages --}}
    <div class="page-content pb-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="flash-success">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="flash-error">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('import_errors'))
            <div class="alert alert-warning alert-dismissible fade show" id="flash-warning">
                <strong><i class="bi bi-exclamation-circle me-1"></i>Detail Import:</strong>
                <ul class="mb-0 mt-1">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- Page Content --}}
    <div class="page-content">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>

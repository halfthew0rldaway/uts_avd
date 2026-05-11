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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Mobile Nav --}}
    <div class="mobile-nav">
        <div class="d-flex align-items-center">
            <img src="{{ asset('img/logo-undira.png') }}" alt="Logo" style="height: 30px;" class="me-2" onerror="this.src='https://undira.ac.id/img/logo.png'; this.onerror=null;">
            <span class="fw-bold text-primary small">UTS AVD - Wisnu</span>
        </div>
        <button class="btn btn-white border shadow-sm" id="sidebarToggle">
            <i class="bi bi-list fs-4"></i>
        </button>
    </div>

{{-- Sidebar --}}
<nav id="sidebar">
    <div class="sidebar-brand d-flex flex-column align-items-start py-4">
        <div class="d-flex align-items-center mb-3">
            <img src="{{ asset('img/logo-undira.png') }}" alt="Undira Logo" style="height: 40px;" onerror="this.src='https://undira.ac.id/img/logo.png'; this.onerror=null;">
        </div>
        <div class="lh-sm">
            <div class="fw-bold text-primary" style="font-size: 1.1rem; letter-spacing: -0.5px;">UTS AVD</div>
            <div class="text-muted small fw-medium mt-1">Wisnu Widya Pradana</div>
            <div class="text-muted" style="font-size: 0.7rem;">NIM: 411231088</div>
        </div>
    </div>

    <div class="nav-label">Menu Utama</div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" id="nav-dashboard">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('understanding') ? 'active' : '' }}" href="{{ route('understanding') }}" id="nav-understanding">
                <i class="bi bi-info-circle"></i> Understanding
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('insight') ? 'active' : '' }}" href="{{ route('insight') }}" id="nav-insight">
                <i class="bi bi-lightbulb"></i> Data Analysis
            </a>
        </li>
    </ul>
</nav>

{{-- Main Content --}}
<div id="main-content">
    {{-- Flash Messages (Moved to Modal) --}}
    <div class="page-content pb-0">
    </div>

    {{-- Page Content --}}
    <div class="page-content">
        @yield('content')
    </div>
</div>

{{-- Global Result Modal (Success/Error/Cleansing) --}}
@if(session('success') || session('error') || session('import_errors'))
<div class="modal fade" id="modalResult" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">
                    @if(session('success'))
                        <i class="bi bi-check-circle-fill text-success me-2"></i> Import Selesai
                    @elseif(session('error'))
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i> Terjadi Kesalahan
                    @else
                        <i class="bi bi-info-circle-fill text-warning me-2"></i> Laporan Cleansing
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                @if(session('success'))
                    <div class="alert bg-label-success border-0 text-success mb-3 p-3 rounded-3">
                        <i class="bi bi-info-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert bg-label-danger border-0 text-danger mb-3 p-3 rounded-3">
                        <i class="bi bi-exclamation-octagon me-2"></i> {{ session('error') }}
                    </div>
                @endif

                @if(session('import_errors'))
                    <div class="fw-bold mb-2 small text-uppercase text-muted" style="letter-spacing: 1px;">Log Pembersihan Data:</div>
                    <div class="bg-light rounded-3 p-3 border" style="max-height: 350px; overflow-y: auto;">
                        <div class="mb-0 small text-secondary">
                            @foreach(session('import_errors') as $err)
                                <div class="mb-2 d-flex align-items-start">
                                    <span class="me-2">—</span>
                                    <span>{{ $err }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <p class="small text-muted mt-3 mb-0" style="font-size: 0.75rem;">
                        <i class="bi bi-shield-lock me-1"></i> Data yang tercatat di atas otomatis dilewati (skipped) untuk menjaga kualitas analitik dashboard.
                    </p>
                @endif
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-primary px-4 fw-bold" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('modalResult');
        if (modalEl) {
            const modalResult = new bootstrap.Modal(modalEl);
            modalResult.show();
        }
    });
</script>
@endpush
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    // Sidebar Toggle for Mobile
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (toggleBtn && sidebar && overlay) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }
    });
</script>
@stack('scripts')
</body>
</html>

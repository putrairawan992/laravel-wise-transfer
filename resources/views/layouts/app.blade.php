<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - Transfer Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #64748b;
            --sidebar-bg: #1e1b4b;
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --bg-color: #f8fafc;
        }
        body { background-color: var(--bg-color); font-family: 'Inter', system-ui, -apple-system, sans-serif; color: #0f172a; }
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); margin-bottom: 24px; background: white; }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background-color: #4338ca; border-color: #4338ca; }
        .text-muted { color: #64748b !important; }
        
        /* Layout */
        .app-container { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
        .sidebar { background-color: var(--sidebar-bg); color: white; padding: 24px 16px; position: sticky; top: 0; height: 100vh; overflow-y: auto; transition: all 0.3s ease; }
        .main-content { padding: 32px; overflow-x: hidden; background-color: #f1f5f9; }
        
        /* Sidebar */
        .sidebar-brand { padding: 0 16px 32px; font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; gap: 12px; letter-spacing: -0.025em; color: white; }
        .sidebar-brand i { font-size: 1.75rem; color: #818cf8; }
        
        .nav-link { color: #cbd5e1; padding: 12px 16px; display: flex; align-items: center; gap: 12px; text-decoration: none; transition: all 0.2s ease; border-radius: 8px; margin-bottom: 4px; font-weight: 500; }
        .nav-link:hover { color: white; background-color: var(--sidebar-hover); transform: translateX(4px); }
        .nav-link.active { color: white; background-color: var(--primary-color); font-weight: 600; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }
        .nav-link i { font-size: 1.25rem; width: 24px; text-align: center; }
        
        .nav-section { padding: 24px 16px 12px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; font-weight: 700; }
        
        /* Topbar */
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; background: white; padding: 16px 24px; border-radius: 16px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); }
        .search-input { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px 20px; border-radius: 9999px; width: 320px; transition: all 0.2s; }
        .search-input:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        
        /* Responsive */
        @media (max-width: 991.98px) {
            .app-container { grid-template-columns: 1fr; }
            .sidebar { display: none; }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <i class="bi bi-wallet2"></i> WISEPAY
            </div>
            
            <div class="nav-section">MENU</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboards
            </a>
            <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Transactions
            </a>
            <a href="{{ route('kyc.edit') }}" class="nav-link {{ request()->routeIs('kyc.*') ? 'active' : '' }}">
                <i class="bi bi-person-vcard"></i> Do KYC
            </a>
            
            <div class="nav-section">UTILITY</div>
            <a href="{{ route('send-money') }}" class="nav-link {{ request()->routeIs('send-money*') ? 'active' : '' }}">
                <i class="bi bi-send"></i> Send Money
            </a>
            <a href="{{ route('data-stock.index') }}" class="nav-link {{ request()->routeIs('data-stock.*') ? 'active' : '' }}">
                <i class="bi bi-wallet"></i> Data Stock Wallet
            </a>
            
            <div class="nav-section">ACCOUNT</div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link w-100 border-0 bg-transparent text-start">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-list d-lg-none fs-4"></i>
                    <input type="text" class="search-input" placeholder="Search...">
                </div>
                <div class="d-flex align-items-center gap-4">
                    <div class="dropdown">
                        <a href="#" class="text-dark position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell fs-5"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem;">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-0" style="width: 320px; max-height: 400px; overflow-y: auto;">
                            <li class="dropdown-header d-flex justify-content-between align-items-center bg-light py-2 px-3 border-bottom">
                                <span class="fw-bold text-dark">Notifications</span>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <form action="{{ route('notifications.markRead') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 0.75rem;">Mark all read</button>
                                    </form>
                                @endif
                            </li>
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <li>
                                    <div class="dropdown-item py-2 px-3 border-bottom">
                                        <div class="d-flex gap-2">
                                            <div class="flex-shrink-0 mt-1">
                                                <i class="bi bi-info-circle-fill text-primary"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 small text-wrap">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                                <small class="text-muted" style="font-size: 0.7rem;">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="p-3 text-center text-muted small">No new notifications</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">
                            {{ substr(Auth::user()->name ?? 'U', 0, 2) }}
                        </div>
                        <div class="d-none d-md-block">
                            <div class="fw-bold">{{ Auth::user()->name ?? 'User' }}</div>
                            <div class="small text-muted" style="font-size: 0.75rem;">Agent</div>
                        </div>
                    </div>
                </div>
            </div>

            @yield('content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

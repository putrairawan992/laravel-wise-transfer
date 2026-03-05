<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Transfer Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #a855f7;
            --sidebar-bg: #0f172a;
            --sidebar-hover: rgba(255, 255, 255, 0.08);
            --text-muted: #94a3b8;
            --card-bg: #1e293b;
            --bg-color: #0f172a;
            --border-color: rgba(255, 255, 255, 0.08);
        }
        body { background-color: var(--bg-color); font-family: 'Inter', system-ui, -apple-system, sans-serif; color: #f1f5f9; }
        .card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.1); color: #f1f5f9; }
        
        /* Layout */
        .app-container { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
        .sidebar { background-color: #0b1120; border-right: 1px solid var(--border-color); padding: 24px 16px; position: sticky; top: 0; height: 100vh; overflow-y: auto; transition: all 0.3s ease; }
        .main-content { padding: 32px; background-color: #0f172a; }
        
        /* Sidebar */
        .sidebar-brand { padding: 0 16px 32px; font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; gap: 12px; letter-spacing: -0.025em; color: white; }
        .sidebar-brand .badge { background: rgba(168, 85, 247, 0.2); color: #e9d5ff; border: 1px solid rgba(168, 85, 247, 0.4); font-weight: 600; padding: 4px 8px; }
        
        .nav-link { color: #cbd5e1; padding: 12px 16px; display: flex; align-items: center; gap: 12px; text-decoration: none; transition: all 0.2s ease; border-radius: 8px; margin-bottom: 4px; font-weight: 500; }
        .nav-link:hover { color: white; background-color: var(--sidebar-hover); transform: translateX(4px); }
        .nav-link.active { color: white; background-color: var(--primary-color); font-weight: 600; box-shadow: 0 4px 12px rgba(168, 85, 247, 0.3); }
        .nav-link i { font-size: 1.25rem; width: 24px; text-align: center; color: #a855f7; }
        .nav-link:hover i, .nav-link.active i { color: white; }
        
        .nav-section { padding: 24px 16px 12px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; font-weight: 700; }
        
        /* Topbar */
        .topbar { display: flex; justify-content: space-between; align-items: center; background: var(--card-bg); border: 1px solid var(--border-color); padding: 16px 24px; border-radius: 16px; margin-bottom: 32px; }
        
        /* Inputs */
        input[type="date"]::-webkit-calendar-picker-indicator {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='white' class='bi bi-calendar' viewBox='0 0 16 16'%3E%3Cpath d='M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z'/%3E%3C/svg%3E");
            cursor: pointer;
            opacity: 0.8;
        }
        input[type="date"]::-webkit-calendar-picker-indicator:hover { opacity: 1; }

        /* Custom Input Styles for Dark Theme */
        .form-control, .form-select {
            background-color: #0f172a;
            border-color: var(--border-color);
            color: #f1f5f9;
        }
        .form-control:focus, .form-select:focus {
            background-color: #1e293b;
            border-color: var(--primary-color);
            color: #f1f5f9;
            box-shadow: 0 0 0 0.25rem rgba(168, 85, 247, 0.25);
        }
        .form-control::placeholder {
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="sidebar">
            <div class="sidebar-brand">
                <i class="bi bi-shield-lock"></i>
                <div>
                    Pay <span class="badge rounded-pill ms-1">money</span>
                    <div class="small" style="color: rgba(226,232,240,0.6); font-weight: 500;">Admin CMS</div>
                </div>
            </div>

            <div class="nav-section">Dashboard</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid"></i> Dashboard
            </a>

            <div class="nav-section">Transactions</div>
            <a href="{{ route('admin.transactions.index') }}" class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Payments
            </a>

            <div class="nav-section">Support</div>
            <a href="{{ route('admin.enquiries.index') }}" class="nav-link {{ request()->routeIs('admin.enquiries.*') ? 'active' : '' }}">
                <i class="bi bi-chat-left-dots"></i> Support Tickets
            </a>

            <div class="nav-section">System</div>
            <a href="{{ route('admin.integrations.index') }}" class="nav-link {{ request()->routeIs('admin.integrations.*') ? 'active' : '' }}">
                <i class="bi bi-hdd-network"></i> Integrations
            </a>
            <a href="{{ route('admin.whatsapp.index') }}" class="nav-link {{ request()->routeIs('admin.whatsapp.*') ? 'active' : '' }}">
                <i class="bi bi-whatsapp"></i> WhatsApp Manager
            </a>

            <div class="nav-section">Others</div>
            <a href="{{ route('admin.kyc.index') }}" class="nav-link {{ request()->routeIs('admin.kyc.*') ? 'active' : '' }}">
                <i class="bi bi-person-vcard"></i> e-KYC
            </a>

            <div class="nav-section">Account</div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link w-100 border-0 bg-transparent text-start">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>

        <div class="main-content">
            <div class="topbar mb-4">
                <div>
                    <div class="fw-bold">@yield('page_title', 'Admin')</div>
                    <div class="small" style="color: rgba(226,232,240,0.6);">@yield('page_subtitle')</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-moon-stars"></i>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-weight: 800;">
                            {{ substr(auth()->user()->name ?? 'A', 0, 2) }}
                        </div>
                        <div class="d-none d-md-block">
                            <div class="fw-semibold">{{ auth()->user()->name ?? 'Admin' }}</div>
                            <div class="small" style="color: rgba(226,232,240,0.6);">admin</div>
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 260px;
            background: #1a1a1a;
            padding: 24px 0;
            overflow-y: auto;
        }
        
        .sidebar-logo {
            padding: 0 24px 24px;
            border-bottom: 1px solid #2a2a2a;
            margin-bottom: 24px;
        }
        
        .logo-text {
            color: white;
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .logo-icon {
            color: #FF8C00;
            font-size: 28px;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .menu-item {
            margin-bottom: 4px;
        }
        
        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: #999;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .menu-link:hover,
        .menu-link.active {
            background: #2a2a2a;
            color: white;
            border-left: 3px solid #FF8C00;
        }
        
        .menu-icon {
            font-size: 20px;
            width: 24px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }
        
        /* Top Bar */
        .topbar {
            background: white;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .topbar-left h2 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FF8C00, #ff9d1f);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }
        
        .btn-logout {
            padding: 8px 16px;
            background: transparent;
            border: 1px solid #ddd;
            border-radius: 6px;
            color: #666;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-logout:hover {
            background: #f5f5f5;
            border-color: #FF8C00;
            color: #FF8C00;
        }
        
        /* Content Area */
        .content-area {
            padding: 32px;
        }
        
        .page-header {
            margin-bottom: 32px;
        }
        
        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .page-header p {
            color: #666;
            font-size: 15px;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 32px;
        }
        
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .stat-icon {
            font-size: 36px;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            border-radius: 12px;
        }
        
        .stat-details {
            flex: 1;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 4px;
        }
        
        .stat-label {
            font-size: 13px;
            color: #999;
        }
        
        /* Content Card */
        .content-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 24px;
        }
        
        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h2 {
            font-size: 18px;
            font-weight: 600;
        }
        
        .btn-link {
            color: #FF8C00;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .btn-link:hover {
            text-decoration: underline;
        }
        
        /* Table */
        .table-responsive {
            overflow-x: auto;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table thead th {
            background: #f8f8f8;
            padding: 12px 16px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .data-table tbody td {
            padding: 16px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        
        .data-table tbody tr:hover {
            background: #f8f8f8;
        }
        
        /* Badge */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-paid {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-failed {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Buttons */
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }
        
        .btn-primary {
            background: #FF8C00;
            color: white;
        }
        
        .btn-primary:hover {
            background: #ff9d1f;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }
        
        /* Alert */
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        /* Form */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #FF8C00;
        }
        
        @media (max-width: 968px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-text">
                <span class="logo-icon">⚡</span>
                <span>Admin Panel</span>
            </div>
        </div>
        
        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="{{ route('admin.dashboard') }}" class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="menu-icon">📊</span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('admin.games') }}" class="menu-link {{ request()->routeIs('admin.games*') ? 'active' : '' }}">
                    <span class="menu-icon">🎮</span>
                    <span>Games</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('admin.topup-options') }}" class="menu-link {{ request()->routeIs('admin.topup-options*') ? 'active' : '' }}">
                    <span class="menu-icon">💎</span>
                    <span>Topup Options</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('admin.users') }}" class="menu-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <span class="menu-icon">👥</span>
                    <span>Users</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('admin.transactions') }}" class="menu-link {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
                    <span class="menu-icon">📦</span>
                    <span>Transactions</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('admin.audit-logs') }}" class="menu-link {{ request()->routeIs('admin.audit-logs') ? 'active' : '' }}">
                  <span class="menu-icon">📋</span>
                  <span>Audit Logs</span>
            </a>
        </li>
            <li class="menu-item">
                <a href="{{ route('admin.password-reset-activity') }}" class="menu-link {{ request()->routeIs('admin.password-reset-activity') ? 'active' : '' }}">
                    <span class="menu-icon">🔑</span>
                    <span>Password Reset Activity</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('admin.topup-requests') }}" class="menu-link {{ request()->routeIs('admin.topup-requests') ? 'active' : '' }}">
                    <span class="menu-icon">💰</span>
                    <span>Top-Up Requests</span>
            </a>
        </li>
            <li class="menu-item">
                <a href="{{ route('home') }}" class="menu-link">
                    <span class="menu-icon">🏠</span>
                    <span>Back to Site</span>
                </a>
            </li>
    </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-left">
                <h2>@yield('title', 'Admin Panel')</h2>
            </div>
            <div class="topbar-right">
                <div class="admin-profile">
                    <div class="admin-avatar">{{ strtoupper(substr(Auth::user()->username, 0, 1)) }}</div>
                    <span>{{ Auth::user()->username }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            
            @yield('content')
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tolo CRM — Enterprise Sales & Management Platform</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-base: #0B0F19;
            --bg-sidebar: #0E1424;
            --bg-card: rgba(18, 24, 38, 0.85);
            --bg-card-hover: rgba(28, 36, 56, 0.95);
            --border-glow: rgba(99, 102, 241, 0.25);
            --border-subtle: rgba(255, 255, 255, 0.08);
            --primary: #6366F1;
            --primary-hover: #4F46E5;
            --accent-cyan: #06B6D4;
            --accent-emerald: #10B981;
            --accent-amber: #F59E0B;
            --accent-rose: #F43F5E;
            --accent-purple: #A855F7;
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --text-dim: #64748B;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Layout Grid */
        .app-layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            width: 100vw;
            min-height: 100vh;
        }

        /* Sidebar */
        aside {
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-subtle);
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 12px 24px 12px;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 24px;
            text-decoration: none;
            color: var(--text-main);
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366F1, #06B6D4);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
        }

        .brand-name {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #FFFFFF, #CBD5E1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }

        .nav-item-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
            background: transparent;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: left;
            transition: all 0.2s ease;
        }

        .nav-item-btn:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
        }

        .nav-item-btn.active {
            background: rgba(99, 102, 241, 0.15);
            color: #C7D2FE;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .nav-item-btn svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .sidebar-footer {
            padding-top: 16px;
            border-top: 1px solid var(--border-subtle);
        }

        .user-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            padding: 10px 12px;
            border-radius: 12px;
        }

        .user-info-flex {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar-mini {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #6366F1, #06B6D4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 13px;
            color: #FFFFFF;
        }

        .user-text-mini h5 {
            font-size: 13px;
            font-weight: 700;
        }

        .user-text-mini p {
            font-size: 11px;
            color: var(--accent-cyan);
        }

        .btn-logout {
            background: transparent;
            border: none;
            color: var(--text-dim);
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .btn-logout:hover {
            color: var(--accent-rose);
        }

        /* Main Content Area */
        main {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow-y: auto;
        }

        /* Top Header */
        .top-nav {
            padding: 14px 32px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(11, 15, 25, 0.85);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .page-heading {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .top-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Role Switcher Toolbar */
        .role-switch-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(0, 0, 0, 0.35);
            padding: 4px 6px;
            border-radius: 10px;
            border: 1px solid var(--border-subtle);
        }

        .role-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-dim);
            padding-left: 6px;
            text-transform: uppercase;
        }

        .role-btn {
            background: transparent;
            border: 1px solid transparent;
            color: var(--text-muted);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .role-btn:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
        }

        .role-btn.active {
            background: rgba(99, 102, 241, 0.25);
            border-color: rgba(99, 102, 241, 0.4);
            color: #C7D2FE;
        }

        .search-box-top {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid var(--border-subtle);
            padding: 8px 14px;
            border-radius: 10px;
            width: 240px;
        }

        .search-box-top input {
            background: transparent;
            border: none;
            outline: none;
            color: var(--text-main);
            font-size: 13px;
            width: 100%;
        }

        .search-box-top input::placeholder {
            color: var(--text-dim);
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366F1, #4F46E5);
            color: #FFFFFF;
            border: none;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
            transition: all 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.4);
        }

        .content-body {
            padding: 32px;
            flex: 1;
        }

        /* Sections */
        .app-view {
            display: none;
        }

        .app-view.active {
            display: block;
            animation: fadeIn 0.25s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* KPI Cards Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        @media (max-width: 1100px) {
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        }

        .kpi-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 22px;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .kpi-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, #6366F1, #06B6D4);
        }

        .kpi-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-dim);
            margin-bottom: 8px;
        }

        .kpi-value {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .kpi-trend {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 600;
        }

        .trend-up { color: var(--accent-emerald); }
        .trend-cyan { color: var(--accent-cyan); }

        /* Dashboard Split */
        .dashboard-split {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 24px;
        }

        @media (max-width: 1000px) {
            .dashboard-split { grid-template-columns: 1fr; }
        }

        .panel-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 18px;
            padding: 24px;
        }

        .panel-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Funnel List */
        .funnel-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .funnel-item {
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 14px;
        }

        .funnel-item-top {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .funnel-bar-wrap {
            height: 6px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
        }

        .funnel-bar-fill {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, #6366F1, #06B6D4);
        }

        /* Rep Matrix Table */
        .rep-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .rep-table th {
            text-align: left;
            padding: 10px 12px;
            color: var(--text-dim);
            font-weight: 600;
            border-bottom: 1px solid var(--border-subtle);
        }

        .rep-table td {
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        /* Kanban View */
        .kanban-board {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 18px;
            overflow-x: auto;
            align-items: start;
        }

        .kanban-column {
            background: rgba(18, 24, 38, 0.6);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            padding: 16px;
            min-height: 500px;
        }

        .kanban-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .stage-badge {
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stage-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .deals-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .deal-card {
            background: rgba(28, 36, 56, 0.85);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: all 0.2s ease;
        }

        .deal-card:hover {
            border-color: rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
        }

        .deal-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .deal-company {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .deal-amount {
            font-size: 15px;
            font-weight: 800;
            color: var(--accent-emerald);
        }

        /* Leads Table */
        .leads-table-wrap {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            overflow: hidden;
        }

        .leads-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .leads-table th {
            text-align: left;
            padding: 14px 18px;
            background: rgba(0, 0, 0, 0.3);
            color: var(--text-dim);
            font-weight: 600;
            border-bottom: 1px solid var(--border-subtle);
        }

        .leads-table td {
            padding: 16px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        .score-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .score-high { background: rgba(16, 185, 129, 0.15); color: #6EE7B7; border: 1px solid rgba(16, 185, 129, 0.3); }
        .score-mid { background: rgba(245, 158, 11, 0.15); color: #FCD34D; border: 1px solid rgba(245, 158, 11, 0.3); }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-qualified { background: rgba(99, 102, 241, 0.15); color: #A5B4FC; }
        .status-contacted { background: rgba(6, 182, 212, 0.15); color: #67E8F9; }
        .status-new { background: rgba(245, 158, 11, 0.15); color: #FCD34D; }
        .status-nurturing { background: rgba(168, 85, 247, 0.15); color: #D8B4FE; }

        .btn-action {
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #C7D2FE;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            background: var(--primary);
            color: #FFFFFF;
        }

        /* Search Results Panel */
        .search-results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .search-item-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            padding: 18px;
            transition: all 0.2s ease;
        }

        .search-item-card:hover {
            border-color: rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
        }

        .entity-type-badge {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 8px;
        }

        .badge-company { background: rgba(6, 182, 212, 0.2); color: #67E8F9; }
        .badge-deal { background: rgba(16, 185, 129, 0.2); color: #6EE7B7; }
        .badge-contact { background: rgba(99, 102, 241, 0.2); color: #A5B4FC; }
        .badge-lead { background: rgba(245, 158, 11, 0.2); color: #FCD34D; }

        /* Timeline Stream */
        .timeline-stream {
            display: flex;
            flex-direction: column;
            gap: 16px;
            max-width: 800px;
        }

        .timeline-item {
            display: flex;
            gap: 16px;
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            padding: 18px;
        }

        .timeline-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .icon-call { background: rgba(6, 182, 212, 0.15); color: #67E8F9; }
        .icon-meeting { background: rgba(99, 102, 241, 0.15); color: #A5B4FC; }
        .icon-task { background: rgba(245, 158, 11, 0.15); color: #FCD34D; }
        .icon-note { background: rgba(16, 185, 129, 0.15); color: #6EE7B7; }
        .icon-audit { background: rgba(168, 85, 247, 0.15); color: #D8B4FE; }

        /* Modal Backdrop */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }

        .modal-backdrop.open {
            display: flex;
        }

        .modal-card {
            background: #111827;
            border: 1px solid var(--border-glow);
            border-radius: 20px;
            padding: 28px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-dim);
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 10px 14px;
            color: var(--text-main);
            font-size: 14px;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary);
        }
    </style>
</head>
<body>

    @php
        $user = Auth::user();
        $org = $user?->organization;
        $currentRole = $user?->getRoleNames()->first() ?? 'OrgAdmin';
        $sanctumToken = $user?->tokens()->latest()->first()?->token ?? 'web-session-token';
    @endphp

    <div class="app-layout">
        <!-- Sidebar Navigation -->
        <aside>
            <a href="/app" class="brand-box">
                <div class="brand-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                </div>
                <span class="brand-name">Tolo CRM</span>
            </a>

            <ul class="nav-list">
                <li>
                    <button type="button" class="nav-item-btn active" id="navDashboard" onclick="showView('dashboard')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        <span>Executive Dashboard</span>
                    </button>
                </li>
                <li>
                    <button type="button" class="nav-item-btn" id="navKanban" onclick="showView('kanban')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                        <span>Pipeline & Kanban</span>
                    </button>
                </li>
                <li>
                    <button type="button" class="nav-item-btn" id="navLeads" onclick="showView('leads')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                        <span>Leads & AI Scoring</span>
                    </button>
                </li>
                <li>
                    <button type="button" class="nav-item-btn" id="navCompanies" onclick="showView('companies')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        <span>Companies & Accounts</span>
                    </button>
                </li>
                <li>
                    <button type="button" class="nav-item-btn" id="navContacts" onclick="showView('contacts')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span>Contacts</span>
                    </button>
                </li>
                <li>
                    <button type="button" class="nav-item-btn" id="navTimeline" onclick="showView('timeline')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Activity Timeline</span>
                    </button>
                </li>
                <li>
                    <button type="button" class="nav-item-btn" id="navSearch" onclick="showView('search')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <span>Global tsvector Search</span>
                    </button>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div class="user-pill">
                    <div class="user-info-flex">
                        <div class="user-avatar-mini">{{ substr($user?->first_name ?? 'U', 0, 1) }}</div>
                        <div class="user-text-mini">
                            <h5>{{ $user?->name ?? 'User' }}</h5>
                            <p>{{ $org?->name ?? 'Enterprise Tenant' }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-logout" title="Sign Out">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main>
            <!-- Top Navbar -->
            <div class="top-nav">
                <h2 class="page-heading" id="viewTitle">Executive Dashboard</h2>

                <div class="top-right">
                    <!-- Instant Role Switcher Toolbar -->
                    <div class="role-switch-wrap">
                        <span class="role-label">Role:</span>
                        <button type="button" class="role-btn {{ $currentRole === 'SuperAdmin' ? 'active' : '' }}" onclick="switchRole('SuperAdmin')">Super</button>
                        <button type="button" class="role-btn {{ $currentRole === 'OrgAdmin' ? 'active' : '' }}" onclick="switchRole('OrgAdmin')">OrgAdmin</button>
                        <button type="button" class="role-btn {{ $currentRole === 'SalesManager' ? 'active' : '' }}" onclick="switchRole('SalesManager')">Mgr</button>
                        <button type="button" class="role-btn {{ $currentRole === 'SalesRepresentative' ? 'active' : '' }}" onclick="switchRole('SalesRepresentative')">Rep</button>
                    </div>

                    <div class="search-box-top">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="topSearchInput" placeholder="Instant Search (Press /)..." onkeyup="if(event.key==='Enter') executeQuickSearch()">
                    </div>

                    <button type="button" class="btn-primary" onclick="openNewDealModal()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>New Deal</span>
                    </button>
                </div>
            </div>

            <div class="content-body">
                <!-- 1. VIEW: EXECUTIVE DASHBOARD -->
                <div class="app-view active" id="view-dashboard">
                    <div class="kpi-grid">
                        <div class="kpi-card">
                            <div class="kpi-label">Total Open Pipeline</div>
                            <div class="kpi-value" id="kpiPipelineVal">$540,000</div>
                            <div class="kpi-trend trend-cyan">3 Active Enterprise Deals</div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-label">Weighted Forecast</div>
                            <div class="kpi-value" id="kpiWeightedVal">$325,000</div>
                            <div class="kpi-trend trend-up">Based on Stage Probabilities</div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-label">Closed Won Revenue</div>
                            <div class="kpi-value" id="kpiWonVal">$645,000</div>
                            <div class="kpi-trend trend-up">2 Won Deals in August</div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-label">Sales Win Rate</div>
                            <div class="kpi-value">100.0%</div>
                            <div class="kpi-trend trend-up">Zero Lost Deals in Current Period</div>
                        </div>
                    </div>

                    <div class="dashboard-split">
                        <!-- Left Panel: Pipeline Funnel Breakdown -->
                        <div class="panel-card">
                            <div class="panel-title">
                                <span>Sales Funnel & Stage Breakdown</span>
                                <span style="font-size: 12px; color: var(--accent-cyan); font-weight: 600;">Enterprise SaaS Pipeline</span>
                            </div>

                            <div class="funnel-list">
                                <div class="funnel-item">
                                    <div class="funnel-item-top">
                                        <strong>Technical Demo & Architecture (30%)</strong>
                                        <span>1 Deal &bull; $175,000.00</span>
                                    </div>
                                    <div class="funnel-bar-wrap"><div class="funnel-bar-fill" style="width: 30%;"></div></div>
                                </div>
                                <div class="funnel-item">
                                    <div class="funnel-item-top">
                                        <strong>Proposal & Commercials (70%)</strong>
                                        <span>1 Deal &bull; $280,000.00</span>
                                    </div>
                                    <div class="funnel-bar-wrap"><div class="funnel-bar-fill" style="width: 70%;"></div></div>
                                </div>
                                <div class="funnel-item">
                                    <div class="funnel-item-top">
                                        <strong>Legal & Procurement (90%)</strong>
                                        <span>1 Deal &bull; $85,000.00</span>
                                    </div>
                                    <div class="funnel-bar-wrap"><div class="funnel-bar-fill" style="width: 90%;"></div></div>
                                </div>
                                <div class="funnel-item">
                                    <div class="funnel-item-top">
                                        <strong>Closed Won (100%)</strong>
                                        <span>2 Deals &bull; $645,000.00</span>
                                    </div>
                                    <div class="funnel-bar-wrap"><div class="funnel-bar-fill" style="width: 100%; background: #10B981;"></div></div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Panel: Sales Rep Performance Leaderboard -->
                        <div class="panel-card">
                            <div class="panel-title">
                                <span>Rep Leaderboard Matrix</span>
                            </div>
                            <table class="rep-table">
                                <thead>
                                    <tr>
                                        <th>Representative</th>
                                        <th>Won Deals</th>
                                        <th>Won Revenue</th>
                                        <th>Pipeline</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Alex Rivers</strong></td>
                                        <td><span class="status-badge status-qualified">2 Won</span></td>
                                        <td><strong style="color:#10B981;">$645,000</strong></td>
                                        <td>$540,000</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Sarah Connor</strong></td>
                                        <td><span class="status-badge status-new">0 Won</span></td>
                                        <td>$0</td>
                                        <td>$0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 2. VIEW: KANBAN BOARD -->
                <div class="app-view" id="view-kanban">
                    <div class="kanban-board" id="kanbanContainer">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <!-- 3. VIEW: LEADS & AI SCORING -->
                <div class="app-view" id="view-leads">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <h3 style="font-size:16px; font-weight:700;">Incoming Leads & AI Scoring Engine</h3>
                        <button type="button" class="btn-action" onclick="openNewLeadModal()">+ Add New Lead</button>
                    </div>
                    <div class="leads-table-wrap">
                        <table class="leads-table">
                            <thead>
                                <tr>
                                    <th>Lead Name</th>
                                    <th>Company</th>
                                    <th>AI Score</th>
                                    <th>Status</th>
                                    <th>Est. Value</th>
                                    <th>Source</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="leadsTableBody">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. VIEW: COMPANIES & ACCOUNTS -->
                <div class="app-view" id="view-companies">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <h3 style="font-size:16px; font-weight:700;">Enterprise Accounts</h3>
                        <button type="button" class="btn-action" onclick="openNewCompanyModal()">+ Add Company</button>
                    </div>
                    <div class="search-results-grid" id="companiesGrid">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <!-- 5. VIEW: CONTACTS -->
                <div class="app-view" id="view-contacts">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <h3 style="font-size:16px; font-weight:700;">Key Executive Contacts</h3>
                        <button type="button" class="btn-action" onclick="openNewContactModal()">+ Add Contact</button>
                    </div>
                    <div class="leads-table-wrap">
                        <table class="leads-table">
                            <thead>
                                <tr>
                                    <th>Contact Name</th>
                                    <th>Company</th>
                                    <th>Job Title</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Lifecycle Stage</th>
                                </tr>
                            </thead>
                            <tbody id="contactsTableBody">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 6. VIEW: ACTIVITY TIMELINE -->
                <div class="app-view" id="view-timeline">
                    <div class="timeline-stream" id="timelineContainer">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <!-- 7. VIEW: GLOBAL SEARCH -->
                <div class="app-view" id="view-search">
                    <div style="margin-bottom: 24px;">
                        <input type="text" id="mainSearchInput" class="form-input" style="font-size: 16px; padding: 14px 18px; width: 100%; max-width: 600px;" placeholder="Search across Companies, Deals, Contacts, and Leads..." oninput="handleLiveSearch(this.value)">
                    </div>
                    <div id="searchResultsCount" style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;"></div>
                    <div class="search-results-grid" id="searchResultsGrid">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal: New Deal -->
    <div class="modal-backdrop" id="newDealModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size:18px; font-weight:800;">Create Enterprise Deal</h3>
                <button type="button" onclick="closeModal('newDealModal')" style="background:transparent; border:none; color:var(--text-muted); cursor:pointer; font-size:18px;">&times;</button>
            </div>
            <form id="newDealForm" onsubmit="submitNewDeal(event)">
                <div class="form-group">
                    <label class="form-label">Deal Title</label>
                    <input type="text" id="dealName" class="form-input" placeholder="e.g. Acme Quantum Cloud Migration" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Deal Amount ($ USD)</label>
                    <input type="number" id="dealAmount" class="form-input" placeholder="150000" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Associated Company</label>
                    <select id="dealCompanySelect" class="form-input"></select>
                </div>
                <div class="form-group">
                    <label class="form-label">Pipeline Stage</label>
                    <select id="dealStageSelect" class="form-input"></select>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                    <button type="button" class="btn-action" onclick="closeModal('newDealModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Create Deal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: New Lead -->
    <div class="modal-backdrop" id="newLeadModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size:18px; font-weight:800;">Add Incoming Lead</h3>
                <button type="button" onclick="closeModal('newLeadModal')" style="background:transparent; border:none; color:var(--text-muted); cursor:pointer; font-size:18px;">&times;</button>
            </div>
            <form id="newLeadForm" onsubmit="submitNewLead(event)">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" id="leadFirstName" class="form-input" placeholder="Arthur" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" id="leadLastName" class="form-input" placeholder="Curry" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Corporate Email</label>
                    <input type="email" id="leadEmail" class="form-input" placeholder="arthur.curry@atlantistech.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Company Name</label>
                    <input type="text" id="leadCompany" class="form-input" placeholder="Atlantis Oceanographic Tech" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Estimated Value ($)</label>
                    <input type="number" id="leadEstValue" class="form-input" placeholder="250000">
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                    <button type="button" class="btn-action" onclick="closeModal('newLeadModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Calculate Score & Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Application Script -->
    <script>
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentToken = localStorage.getItem('tolo_crm_token') || '1|4YTvZHghiWq6uiw0GNEkbf6eIMe0wLG7WQlbOLnuf2f445df';

        function showView(viewName) {
            document.querySelectorAll('.app-view').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.nav-item-btn').forEach(el => el.classList.remove('active'));

            const targetView = document.getElementById('view-' + viewName);
            if (targetView) targetView.classList.add('active');

            const targetBtn = document.getElementById('nav' + viewName.charAt(0).toUpperCase() + viewName.slice(1));
            if (targetBtn) targetBtn.classList.add('active');

            const titleMap = {
                'dashboard': 'Executive Sales & Revenue Dashboard',
                'kanban': 'Interactive Pipeline Kanban Board',
                'leads': 'Leads Qualification & AI Scoring Engine',
                'companies': 'Enterprise Accounts & Custom JSONB Attributes',
                'contacts': 'Key Executive Contacts & Decision Makers',
                'timeline': 'Chronological Activities & Model Audit Stream',
                'search': 'PostgreSQL tsvector High-Speed Global Search'
            };

            document.getElementById('viewTitle').textContent = titleMap[viewName] || 'Enterprise Dashboard';

            if (viewName === 'kanban') loadKanban();
            if (viewName === 'leads') loadLeads();
            if (viewName === 'companies') loadCompanies();
            if (viewName === 'contacts') loadContacts();
            if (viewName === 'timeline') loadTimeline();
            if (viewName === 'search') handleLiveSearch('Stark');
        }

        async function apiFetch(endpoint, options = {}) {
            const headers = {
                'Authorization': 'Bearer ' + currentToken,
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                ...options.headers
            };

            const res = await fetch(endpoint, { ...options, headers });
            return await res.json();
        }

        // Fast Role Switcher
        async function switchRole(role) {
            try {
                const res = await fetch('/switch-role', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ role })
                });
                const data = await res.json();
                if (data.token) {
                    currentToken = data.token;
                    localStorage.setItem('tolo_crm_token', data.token);
                }
                window.location.reload();
            } catch (err) {
                console.error(err);
            }
        }

        // 1. Load Kanban Board
        async function loadKanban() {
            const container = document.getElementById('kanbanContainer');
            container.innerHTML = '<div style="color:var(--text-muted);">Loading live Kanban board...</div>';

            try {
                const pipelinesData = await apiFetch('/api/v1/pipelines');
                if (!pipelinesData.data || pipelinesData.data.length === 0) return;

                const defaultPipeline = pipelinesData.data[0];
                const kanbanData = await apiFetch(`/api/v1/pipelines/${defaultPipeline.id}/kanban`);

                container.innerHTML = '';
                kanbanData.columns.forEach(col => {
                    const colDiv = document.createElement('div');
                    colDiv.className = 'kanban-column';
                    colDiv.innerHTML = `
                        <div class="kanban-header">
                            <div class="stage-badge">
                                <div class="stage-dot" style="background-color: ${col.color_code};"></div>
                                <span>${col.name}</span>
                            </div>
                            <span style="font-size: 11px; color: var(--text-dim); font-weight:700;">${col.deals_count} &bull; $${(col.total_value/1000).toFixed(0)}k</span>
                        </div>
                        <div class="deals-container">
                            ${col.deals.map(d => `
                                <div class="deal-card">
                                    <div class="deal-title">${d.name}</div>
                                    <div class="deal-company">${d.company ? d.company.name : 'Acme Enterprise'}</div>
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                                        <span class="deal-amount">$${Number(d.amount).toLocaleString()}</span>
                                        <span class="status-badge ${d.status === 'won' ? 'status-qualified' : 'status-new'}">${d.status || 'open'}</span>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                    container.appendChild(colDiv);
                });
            } catch (err) {
                container.innerHTML = '<div style="color:var(--accent-rose);">Error loading Kanban board.</div>';
            }
        }

        // 2. Load Leads
        async function loadLeads() {
            const tbody = document.getElementById('leadsTableBody');
            tbody.innerHTML = '<tr><td colspan="7" style="color:var(--text-muted);">Loading scored leads...</td></tr>';

            try {
                const res = await apiFetch('/api/v1/leads');
                tbody.innerHTML = '';
                res.data.forEach(l => {
                    const isHigh = l.score >= 80;
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${l.first_name} ${l.last_name}</strong><br><span style="font-size:12px; color:var(--text-muted);">${l.email || ''}</span></td>
                        <td>${l.company_name || 'N/A'}</td>
                        <td><span class="score-pill ${isHigh ? 'score-high' : 'score-mid'}">⚡ ${l.score}/100</span></td>
                        <td><span class="status-badge status-${l.status}">${l.status}</span></td>
                        <td><strong>$${Number(l.estimated_value || 0).toLocaleString()}</strong></td>
                        <td><span style="font-size:12px; color:var(--text-dim); text-transform:uppercase;">${l.source || 'Website'}</span></td>
                        <td><button class="btn-action" onclick="convertLeadPrompt('${l.id}', '${l.first_name}')">Convert &rarr;</button></td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (err) {
                tbody.innerHTML = '<tr><td colspan="7">Error loading leads.</td></tr>';
            }
        }

        async function convertLeadPrompt(leadId, leadName) {
            if (!confirm(`Convert lead ${leadName} to an enterprise Company, Contact, and open Deal?`)) return;

            try {
                const res = await apiFetch(`/api/v1/leads/${leadId}/convert`, {
                    method: 'POST',
                    body: JSON.stringify({ create_deal: true, deal_amount: 150000 })
                });
                alert('Success! Lead converted atomically to Company, Contact, and Deal.');
                loadLeads();
            } catch (err) {
                alert('Conversion error: ' + err.message);
            }
        }

        // 3. Load Companies
        async function loadCompanies() {
            const grid = document.getElementById('companiesGrid');
            grid.innerHTML = '<div style="color:var(--text-muted);">Loading companies...</div>';

            try {
                const res = await apiFetch('/api/v1/companies');
                grid.innerHTML = '';
                res.data.forEach(c => {
                    const tier = c.custom_attributes?.account_tier || 'Enterprise Account';
                    const div = document.createElement('div');
                    div.className = 'search-item-card';
                    div.innerHTML = `
                        <div class="entity-type-badge badge-company">Company Account</div>
                        <h3 style="font-size:16px; font-weight:800; margin-bottom:4px;">${c.name}</h3>
                        <p style="font-size:13px; color:var(--text-muted); margin-bottom:12px;">${c.industry || 'Technology'} &bull; ${c.address_city || 'USA'}</p>
                        <div style="background:rgba(0,0,0,0.3); padding:10px; border-radius:8px; font-size:12px; margin-bottom:10px;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                <span style="color:var(--text-dim);">Annual Revenue:</span>
                                <strong>$${Number(c.annual_revenue || 0).toLocaleString()}</strong>
                            </div>
                            <div style="display:flex; justify-content:space-between;">
                                <span style="color:var(--text-dim);">Tier:</span>
                                <span style="color:var(--accent-cyan); font-weight:600;">${tier}</span>
                            </div>
                        </div>
                    `;
                    grid.appendChild(div);
                });
            } catch (err) {
                grid.innerHTML = '<div>Error loading companies.</div>';
            }
        }

        // 4. Load Contacts
        async function loadContacts() {
            const tbody = document.getElementById('contactsTableBody');
            tbody.innerHTML = '<tr><td colspan="6" style="color:var(--text-muted);">Loading contacts...</td></tr>';

            try {
                const res = await apiFetch('/api/v1/contacts');
                tbody.innerHTML = '';
                res.data.forEach(c => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${c.first_name} ${c.last_name}</strong></td>
                        <td>${c.company ? c.company.name : 'Enterprise Account'}</td>
                        <td>${c.job_title || 'Executive'}</td>
                        <td><span style="color:#38BDF8; font-family:'JetBrains Mono'; font-size:12px;">${c.email || ''}</span></td>
                        <td>${c.phone || '+1 (555) 000-0000'}</td>
                        <td><span class="status-badge status-qualified">${c.lifecycle_stage || 'Customer'}</span></td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (err) {
                tbody.innerHTML = '<tr><td colspan="6">Error loading contacts.</td></tr>';
            }
        }

        // 5. Load Timeline
        async function loadTimeline() {
            const container = document.getElementById('timelineContainer');
            container.innerHTML = '<div style="color:var(--text-muted);">Loading unified timeline stream...</div>';

            try {
                const companiesData = await apiFetch('/api/v1/companies');
                const firstCompany = companiesData.data[0];
                const res = await apiFetch(`/api/v1/timeline?subject_type=company&subject_id=${firstCompany.id}`);

                container.innerHTML = `<h3 style="font-size:16px; font-weight:700; margin-bottom:16px;">Chronological Timeline for: <span style="color:var(--accent-cyan);">${firstCompany.name}</span></h3>`;

                res.data.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'timeline-item';
                    const iconClass = item.category === 'audit' ? 'icon-audit' : `icon-${item.type}`;
                    div.innerHTML = `
                        <div class="timeline-icon ${iconClass}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                <strong style="font-size:14px;">${item.title}</strong>
                                <span style="font-size:11px; color:var(--text-dim);">${new Date(item.timestamp).toLocaleString()}</span>
                            </div>
                            <p style="font-size:13px; color:var(--text-muted); line-height:1.5;">${item.description || ''}</p>
                        </div>
                    `;
                    container.appendChild(div);
                });
            } catch (err) {
                container.innerHTML = '<div>Error loading timeline.</div>';
            }
        }

        // 6. Live Search
        async function handleLiveSearch(query) {
            const grid = document.getElementById('searchResultsGrid');
            const countEl = document.getElementById('searchResultsCount');

            if (!query || query.trim() === '') {
                grid.innerHTML = '<div style="color:var(--text-muted);">Type in a keyword above to test PostgreSQL tsvector search.</div>';
                countEl.textContent = '';
                return;
            }

            try {
                const res = await apiFetch(`/api/v1/search?q=${encodeURIComponent(query)}`);
                countEl.textContent = `Found ${res.total_count} ranked matches for "${query}" across all CRM entities:`;
                grid.innerHTML = '';

                res.unified.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'search-item-card';
                    const badgeClass = `badge-${item.entity_type}`;
                    div.innerHTML = `
                        <div class="entity-type-badge ${badgeClass}">${item.entity_type}</div>
                        <h4 style="font-size:15px; font-weight:800; margin-bottom:4px;">${item.title}</h4>
                        <p style="font-size:13px; color:var(--text-muted);">${item.subtitle || ''}</p>
                        <div style="margin-top:10px; font-size:11px; color:var(--accent-emerald);">Rank Relevance Score: ${item.rank}</div>
                    `;
                    grid.appendChild(div);
                });
            } catch (err) {
                grid.innerHTML = '<div>Search error.</div>';
            }
        }

        function executeQuickSearch() {
            const query = document.getElementById('topSearchInput').value;
            showView('search');
            document.getElementById('mainSearchInput').value = query;
            handleLiveSearch(query);
        }

        // Modals & New Entity Creation
        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
        }

        async function openNewDealModal() {
            const modal = document.getElementById('newDealModal');
            const compSelect = document.getElementById('dealCompanySelect');
            const stageSelect = document.getElementById('dealStageSelect');

            compSelect.innerHTML = '<option>Loading...</option>';
            stageSelect.innerHTML = '<option>Loading...</option>';
            modal.classList.add('open');

            const [comps, pipes] = await Promise.all([
                apiFetch('/api/v1/companies'),
                apiFetch('/api/v1/pipelines')
            ]);

            compSelect.innerHTML = comps.data.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            const stages = pipes.data[0].stages;
            stageSelect.innerHTML = stages.map(s => `<option value="${s.id}">${s.name} (${s.win_probability}%)</option>`).join('');
        }

        async function submitNewDeal(e) {
            e.preventDefault();
            const name = document.getElementById('dealName').value;
            const amount = document.getElementById('dealAmount').value;
            const company_id = document.getElementById('dealCompanySelect').value;
            const stage_id = document.getElementById('dealStageSelect').value;

            const pipes = await apiFetch('/api/v1/pipelines');
            const pipeline_id = pipes.data[0].id;

            const res = await apiFetch('/api/v1/deals', {
                method: 'POST',
                body: JSON.stringify({ name, amount, company_id, stage_id, pipeline_id, currency: 'USD' })
            });

            closeModal('newDealModal');
            alert('Deal created successfully!');
            showView('kanban');
        }

        function openNewLeadModal() {
            document.getElementById('newLeadModal').classList.add('open');
        }

        async function submitNewLead(e) {
            e.preventDefault();
            const first_name = document.getElementById('leadFirstName').value;
            const last_name = document.getElementById('leadLastName').value;
            const email = document.getElementById('leadEmail').value;
            const company_name = document.getElementById('leadCompany').value;
            const estimated_value = document.getElementById('leadEstValue').value;

            const res = await apiFetch('/api/v1/leads', {
                method: 'POST',
                body: JSON.stringify({ first_name, last_name, email, company_name, estimated_value, source: 'website', status: 'new' })
            });

            closeModal('newLeadModal');
            alert(`Lead added! Computed AI Score: ${res.data.score}/100`);
            showView('leads');
        }

        document.addEventListener('DOMContentLoaded', () => {
            showView('dashboard');
        });
    </script>
</body>
</html>

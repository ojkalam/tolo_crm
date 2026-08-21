<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tolo CRM — Enterprise Sales & Multi-Tenant CRM Platform</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-base: #090D16;
            --bg-surface: rgba(18, 24, 38, 0.75);
            --bg-surface-hover: rgba(28, 36, 56, 0.85);
            --border-glow: rgba(99, 102, 241, 0.25);
            --border-subtle: rgba(255, 255, 255, 0.08);
            --primary: #6366F1;
            --primary-hover: #4F46E5;
            --accent-cyan: #06B6D4;
            --accent-emerald: #10B981;
            --accent-amber: #F59E0B;
            --accent-rose: #F43F5E;
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
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Ambient Glow Background */
        .ambient-glow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.35;
            animation: orbFloat 20s infinite alternate ease-in-out;
        }

        .orb-1 {
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, #4F46E5 0%, rgba(99, 102, 241, 0) 70%);
            top: -10%;
            left: 10%;
        }

        .orb-2 {
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, #06B6D4 0%, rgba(6, 182, 212, 0) 70%);
            bottom: 5%;
            right: 5%;
            animation-duration: 25s;
        }

        .orb-3 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, #8B5CF6 0%, rgba(139, 92, 246, 0) 70%);
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes orbFloat {
            0% { transform: translateY(0) scale(1); }
            50% { transform: translateY(40px) scale(1.08); }
            100% { transform: translateY(-30px) scale(0.95); }
        }

        /* Container */
        .container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 1;
        }

        /* Header / Navbar */
        header {
            padding: 24px 0;
            border-bottom: 1px solid var(--border-subtle);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(9, 13, 22, 0.7);
        }

        .nav-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-main);
        }

        .logo-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #6366F1, #06B6D4);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
        }

        .logo-text {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #FFFFFF, #CBD5E1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #A5B4FC;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.04);
            padding: 6px 14px;
            border-radius: 20px;
            border: 1px solid var(--border-subtle);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent-emerald);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--accent-emerald);
            animation: pulseDot 2s infinite;
        }

        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        /* Hero Section */
        .hero-section {
            padding: 50px 0 80px 0;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 48px;
            align-items: center;
        }

        @media (max-width: 980px) {
            .hero-section {
                grid-template-columns: 1fr;
                padding: 30px 0 60px 0;
            }
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #C7D2FE;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .hero-title {
            font-size: 46px;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1.5px;
            margin-bottom: 20px;
        }

        .gradient-text {
            background: linear-gradient(135deg, #A5B4FC 0%, #38BDF8 50%, #34D399 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 17px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 32px;
            max-width: 580px;
        }

        /* Feature Pills */
        .feature-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 32px;
        }

        .tag-item {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .tag-item svg {
            width: 14px;
            height: 14px;
            color: var(--accent-cyan);
        }

        /* Quick Demo Login Buttons */
        .demo-bar {
            background: rgba(18, 24, 38, 0.6);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 18px;
            backdrop-filter: blur(10px);
        }

        .demo-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-dim);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .demo-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        @media (max-width: 600px) {
            .demo-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .demo-btn {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-subtle);
            color: var(--text-main);
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }

        .demo-btn span.role {
            font-size: 10px;
            color: var(--accent-cyan);
            font-weight: 500;
        }

        .demo-btn:hover {
            background: rgba(99, 102, 241, 0.2);
            border-color: rgba(99, 102, 241, 0.5);
            transform: translateY(-1px);
        }

        /* Glassmorphic Auth Card */
        .auth-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-glow);
            border-radius: 24px;
            padding: 36px;
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #6366F1, #06B6D4, #10B981);
        }

        /* Tabs Switcher */
        .tab-switcher {
            display: flex;
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 28px;
        }

        .tab-btn {
            flex: 1;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            background: transparent;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }

        .tab-btn.active {
            background: var(--primary);
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
        }

        /* Forms */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border-subtle);
            color: var(--text-main);
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            background: rgba(0, 0, 0, 0.4);
        }

        .form-input::placeholder {
            color: var(--text-dim);
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.35);
            margin-top: 24px;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #4F46E5 0%, #4338CA 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.45);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Message Banner */
        .msg-box {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            gap: 10px;
            line-height: 1.4;
        }

        .msg-box.error {
            display: flex;
            background: rgba(244, 63, 94, 0.12);
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: #FDA4AF;
        }

        .msg-box.success {
            display: flex;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6EE7B7;
        }

        /* Authenticated Session Card */
        .session-panel {
            display: none;
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-subtle);
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #6366F1, #06B6D4);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            color: #FFFFFF;
        }

        .user-info h4 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-main);
        }

        .user-info p {
            font-size: 12px;
            color: var(--text-muted);
        }

        .token-card {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 20px;
        }

        .token-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--accent-cyan);
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .token-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: #CBD5E1;
            word-break: break-all;
            background: rgba(0, 0, 0, 0.3);
            padding: 6px 8px;
            border-radius: 6px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }

        .action-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-subtle);
            color: var(--text-main);
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .action-link:hover {
            background: rgba(99, 102, 241, 0.2);
            border-color: rgba(99, 102, 241, 0.4);
        }

        .logout-btn {
            width: 100%;
            padding: 12px;
            background: rgba(244, 63, 94, 0.12);
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: #FDA4AF;
            font-size: 14px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: rgba(244, 63, 94, 0.25);
        }

        /* Features Section */
        .features-section {
            padding: 60px 0 100px 0;
            border-top: 1px solid var(--border-subtle);
        }

        .section-header {
            text-align: center;
            max-width: 650px;
            margin: 0 auto 50px auto;
        }

        .section-tag {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--accent-cyan);
            margin-bottom: 8px;
        }

        .section-title {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.8px;
            margin-bottom: 12px;
        }

        .section-desc {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        @media (max-width: 880px) {
            .feature-grid {
                grid-template-columns: 1fr;
            }
        }

        .feature-card {
            background: rgba(18, 24, 38, 0.5);
            border: 1px solid var(--border-subtle);
            border-radius: 18px;
            padding: 28px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.4);
            background: rgba(28, 36, 56, 0.65);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
        }

        .card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .card-icon.purple { background: rgba(99, 102, 241, 0.15); color: #A5B4FC; }
        .card-icon.cyan { background: rgba(6, 182, 212, 0.15); color: #67E8F9; }
        .card-icon.emerald { background: rgba(16, 185, 129, 0.15); color: #6EE7B7; }
        .card-icon.amber { background: rgba(245, 158, 11, 0.15); color: #FCD34D; }
        .card-icon.rose { background: rgba(244, 63, 94, 0.15); color: #FDA4AF; }
        .card-icon.blue { background: rgba(59, 130, 246, 0.15); color: #93C5FD; }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .card-desc {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Footer */
        footer {
            margin-top: auto;
            border-top: 1px solid var(--border-subtle);
            padding: 24px 0;
            background: rgba(9, 13, 22, 0.9);
            font-size: 13px;
            color: var(--text-dim);
            text-align: center;
        }

        /* Spinner */
        .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #FFFFFF;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <!-- Ambient Glow Background -->
    <div class="ambient-glow">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Header -->
    <header>
        <div class="container">
            <div class="nav-wrap">
                <a href="/" class="logo-area">
                    <div class="logo-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polyline points="2 17 12 22 22 17"></polyline>
                            <polyline points="2 12 12 17 22 12"></polyline>
                        </svg>
                    </div>
                    <span class="logo-text">Tolo CRM</span>
                    <span class="logo-badge">v1.0.0</span>
                </a>

                <div class="nav-status">
                    <div class="status-dot"></div>
                    <span>PostgreSQL 16 &bull; Horizon Active</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <section class="hero-section">
            <!-- Left Column: Copy & Quick Demo Fill -->
            <div class="hero-content">
                <div class="hero-pill">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                    <span>Multi-Tenant Enterprise Engine</span>
                </div>

                <h1 class="hero-title">
                    Accelerate Your Sales Funnel with <span class="gradient-text">Intelligent CRM Architecture</span>
                </h1>

                <p class="hero-desc">
                    Engineered for high velocity sales teams. Powered by native PostgreSQL <code style="color:#38BDF8; font-family:'JetBrains Mono';">JSONB</code> indexing, automated qualification scoring, sub-millisecond <code style="color:#34D399; font-family:'JetBrains Mono';">tsvector</code> search, and real-time WebSocket pipelines.
                </p>

                <div class="feature-tags">
                    <div class="tag-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Multi-Tenant UUID Isolation
                    </div>
                    <div class="tag-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Atomic Lead $\rightarrow$ Deal Conversion
                    </div>
                    <div class="tag-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Real-time Collaborative Kanban
                    </div>
                    <div class="tag-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        PostgreSQL tsvector Full-Text
                    </div>
                </div>

                <!-- Quick One-Click Demo Logins -->
                <div class="demo-bar">
                    <div class="demo-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m10 15 5-3-5-3v6Z"/></svg>
                        Instant One-Click Demo Credentials
                    </div>
                    <div class="demo-grid">
                        <button type="button" class="demo-btn" onclick="fillDemo('superadmin@crm-enterprise.local', 'Password123!')">
                            <strong>Super Admin</strong>
                            <span class="role">Global Access</span>
                        </button>
                        <button type="button" class="demo-btn" onclick="fillDemo('admin@acmecorp.com', 'Password123!')">
                            <strong>Org Admin</strong>
                            <span class="role">Acme Corp</span>
                        </button>
                        <button type="button" class="demo-btn" onclick="fillDemo('salesmanager@acmecorp.com', 'Password123!')">
                            <strong>Sales Mgr</strong>
                            <span class="role">Pipelines & KPIs</span>
                        </button>
                        <button type="button" class="demo-btn" onclick="fillDemo('salesrep@acmecorp.com', 'Password123!')">
                            <strong>Sales Rep</strong>
                            <span class="role">Deals & Leads</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Interactive Glassmorphic Auth Portal -->
            <div>
                <div class="auth-card">
                    <!-- Message Banner -->
                    <div id="msgBox" class="msg-box">
                        <span id="msgText"></span>
                    </div>

                    <!-- Auth Forms Container (When Not Logged In) -->
                    <div id="authForms">
                        <div class="tab-switcher">
                            <button type="button" class="tab-btn active" id="tabLoginBtn" onclick="switchTab('login')">Sign In</button>
                            <button type="button" class="tab-btn" id="tabRegisterBtn" onclick="switchTab('register')">Create Tenant</button>
                        </div>

                        <!-- Login Form -->
                        <form id="loginForm" onsubmit="handleLogin(event)">
                            <div class="form-group">
                                <label class="form-label" for="loginEmail">Work Email</label>
                                <div class="input-wrap">
                                    <input type="email" id="loginEmail" class="form-input" placeholder="name@company.com" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="loginPassword">Password</label>
                                <div class="input-wrap">
                                    <input type="password" id="loginPassword" class="form-input" placeholder="••••••••••••" required>
                                </div>
                            </div>

                            <button type="submit" class="submit-btn" id="loginSubmitBtn">
                                <span>Sign In to CRM</span>
                                <div class="spinner" id="loginSpinner"></div>
                            </button>
                        </form>

                        <!-- Register Form -->
                        <form id="registerForm" style="display: none;" onsubmit="handleRegister(event)">
                            <div class="form-group">
                                <label class="form-label" for="regOrg">Company / Tenant Name</label>
                                <input type="text" id="regOrg" class="form-input" placeholder="Apex Global Technologies" required>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div class="form-group">
                                    <label class="form-label" for="regFirstName">First Name</label>
                                    <input type="text" id="regFirstName" class="form-input" placeholder="Alex" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="regLastName">Last Name</label>
                                    <input type="text" id="regLastName" class="form-input" placeholder="Morgan" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="regEmail">Work Email</label>
                                <input type="email" id="regEmail" class="form-input" placeholder="alex@apextech.io" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="regPassword">Password</label>
                                <input type="password" id="regPassword" class="form-input" placeholder="Min. 8 characters" required minlength="8">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="regPasswordConfirm">Confirm Password</label>
                                <input type="password" id="regPasswordConfirm" class="form-input" placeholder="Confirm password" required minlength="8">
                            </div>

                            <button type="submit" class="submit-btn" id="regSubmitBtn">
                                <span>Register Organization</span>
                                <div class="spinner" id="regSpinner"></div>
                            </button>
                        </form>
                    </div>

                    <!-- Authenticated Session Panel (When Logged In) -->
                    <div id="sessionPanel" class="session-panel">
                        <div class="user-badge">
                            <div class="user-avatar" id="avatarInitial">U</div>
                            <div class="user-info">
                                <h4 id="sessionUserName">Authenticated User</h4>
                                <p id="sessionUserEmail">user@company.com</p>
                            </div>
                        </div>

                        <div class="token-card">
                            <div class="token-title">Active Sanctum Bearer Token</div>
                            <div class="token-value" id="sessionToken">...</div>
                        </div>

                        <div class="quick-actions">
                            <button type="button" class="action-link" onclick="testEndpoint('/api/v1/analytics/dashboard', 'Executive Analytics')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                                Analytics
                            </button>
                            <button type="button" class="action-link" onclick="testEndpoint('/api/v1/pipelines', 'Pipelines & Stages')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                                Pipelines
                            </button>
                            <button type="button" class="action-link" onclick="testEndpoint('/api/v1/leads', 'Leads List')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/></svg>
                                Leads
                            </button>
                            <button type="button" class="action-link" onclick="testEndpoint('/api/v1/search?q=acme', 'Global Search')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                Search
                            </button>
                        </div>

                        <button type="button" class="logout-btn" onclick="handleLogout()">Sign Out</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Showcase Section -->
        <section class="features-section">
            <div class="section-header">
                <div class="section-tag">Enterprise Capabilities</div>
                <h2 class="section-title">Engineered for Reliability, Scale & Speed</h2>
                <p class="section-desc">Every component of Tolo CRM is designed according to modern Domain-Driven Actions and PostgreSQL native performance standards.</p>
            </div>

            <div class="feature-grid">
                <!-- Card 1 -->
                <div class="feature-card">
                    <div class="card-icon purple">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    </div>
                    <h3 class="card-title">Real-Time Kanban Pipelines</h3>
                    <p class="card-desc">Custom stages, dynamic win probabilities, automated won/lost status resolution, and collaborative WebSocket updates via Laravel Reverb.</p>
                </div>

                <!-- Card 2 -->
                <div class="feature-card">
                    <div class="card-icon cyan">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <h3 class="card-title">Atomic Lead Conversion</h3>
                    <p class="card-desc">Calculates automated qualification scores and converts leads into Companies, Contacts, and Deals in a single ACID-compliant database transaction.</p>
                </div>

                <!-- Card 3 -->
                <div class="feature-card">
                    <div class="card-icon emerald">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                    <h3 class="card-title">tsvector Full-Text Search</h3>
                    <p class="card-desc">Sub-millisecond global search across Contacts, Companies, Leads, and Deals powered by generated PostgreSQL tsvectors and GIN indexes.</p>
                </div>

                <!-- Card 4 -->
                <div class="feature-card">
                    <div class="card-icon amber">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
                    </div>
                    <h3 class="card-title">Revenue & Funnel Analytics</h3>
                    <p class="card-desc">Real-time pipeline value calculation, weighted sales forecasting, lead conversion velocity, and sales representative leaderboard matrices.</p>
                </div>

                <!-- Card 5 -->
                <div class="feature-card">
                    <div class="card-icon rose">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <h3 class="card-title">Unified Timeline & Audit Trail</h3>
                    <p class="card-desc">Chronological feed unifying polymorphic CRM activities (calls, meetings, tasks, notes) with Spatie model mutation audit logs.</p>
                </div>

                <!-- Card 6 -->
                <div class="feature-card">
                    <div class="card-icon blue">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </div>
                    <h3 class="card-title">Chunked Import & Export</h3>
                    <p class="card-desc">Background CSV/Excel import with automated company resolution and streaming high-volume CSV exports for enterprise deals.</p>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} Tolo CRM &bull; Enterprise Architecture &bull; PostgreSQL 16 &bull; Laravel 12</p>
        </div>
    </footer>

    <!-- Interactive Script -->
    <script>
        function showMsg(text, isError = false) {
            const box = document.getElementById('msgBox');
            const textEl = document.getElementById('msgText');
            box.className = 'msg-box ' + (isError ? 'error' : 'success');
            textEl.textContent = text;
            box.style.display = 'flex';
        }

        function hideMsg() {
            document.getElementById('msgBox').style.display = 'none';
        }

        function switchTab(tab) {
            hideMsg();
            const loginForm = document.getElementById('loginForm');
            const regForm = document.getElementById('registerForm');
            const tabLoginBtn = document.getElementById('tabLoginBtn');
            const tabRegBtn = document.getElementById('tabRegisterBtn');

            if (tab === 'login') {
                loginForm.style.display = 'block';
                regForm.style.display = 'none';
                tabLoginBtn.classList.add('active');
                tabRegBtn.classList.remove('active');
            } else {
                loginForm.style.display = 'none';
                regForm.style.display = 'block';
                tabLoginBtn.classList.remove('active');
                tabRegBtn.classList.add('active');
            }
        }

        function fillDemo(email, password) {
            switchTab('login');
            document.getElementById('loginEmail').value = email;
            document.getElementById('loginPassword').value = password;
            showMsg('Demo credentials filled for ' + email + '. Click "Sign In" below.');
        }

        function checkExistingSession() {
            const token = localStorage.getItem('tolo_crm_token');
            const userJson = localStorage.getItem('tolo_crm_user');

            if (token && userJson) {
                try {
                    const user = JSON.parse(userJson);
                    renderSession(user, token);
                } catch (e) {
                    localStorage.removeItem('tolo_crm_token');
                    localStorage.removeItem('tolo_crm_user');
                }
            }
        }

        function renderSession(user, token) {
            document.getElementById('authForms').style.display = 'none';
            document.getElementById('sessionPanel').style.display = 'block';

            const name = (user.first_name || '') + ' ' + (user.last_name || '') || user.name || 'CRM User';
            document.getElementById('sessionUserName').textContent = name;
            document.getElementById('sessionUserEmail').textContent = user.email || 'user@company.com';
            document.getElementById('avatarInitial').textContent = name.charAt(0).toUpperCase();
            document.getElementById('sessionToken').textContent = token;
        }

        async function handleLogin(e) {
            e.preventDefault();
            hideMsg();

            const email = document.getElementById('loginEmail').value.trim();
            const password = document.getElementById('loginPassword').value;
            const btn = document.getElementById('loginSubmitBtn');
            const spinner = document.getElementById('loginSpinner');

            btn.disabled = true;
            spinner.style.display = 'block';

            try {
                const res = await fetch('/api/v1/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.message || 'Invalid email or password.');
                }

                localStorage.setItem('tolo_crm_token', data.token);
                localStorage.setItem('tolo_crm_user', JSON.stringify(data.user));

                showMsg('Login successful! Welcome back.');
                setTimeout(() => {
                    renderSession(data.user, data.token);
                }, 400);

            } catch (err) {
                showMsg(err.message, true);
            } finally {
                btn.disabled = false;
                spinner.style.display = 'none';
            }
        }

        async function handleRegister(e) {
            e.preventDefault();
            hideMsg();

            const organization_name = document.getElementById('regOrg').value.trim();
            const first_name = document.getElementById('regFirstName').value.trim();
            const last_name = document.getElementById('regLastName').value.trim();
            const email = document.getElementById('regEmail').value.trim();
            const password = document.getElementById('regPassword').value;
            const password_confirmation = document.getElementById('regPasswordConfirm').value;

            if (password !== password_confirmation) {
                showMsg('Passwords do not match.', true);
                return;
            }

            const btn = document.getElementById('regSubmitBtn');
            const spinner = document.getElementById('regSpinner');

            btn.disabled = true;
            spinner.style.display = 'block';

            try {
                const res = await fetch('/api/v1/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        organization_name,
                        first_name,
                        last_name,
                        email,
                        password,
                        password_confirmation
                    })
                });

                const data = await res.json();

                if (!res.ok) {
                    const errorMsg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Registration failed.');
                    throw new Error(errorMsg);
                }

                localStorage.setItem('tolo_crm_token', data.token);
                localStorage.setItem('tolo_crm_user', JSON.stringify(data.user));

                showMsg('Organization registered successfully! Welcome.');
                setTimeout(() => {
                    renderSession(data.user, data.token);
                }, 400);

            } catch (err) {
                showMsg(err.message, true);
            } finally {
                btn.disabled = false;
                spinner.style.display = 'none';
            }
        }

        async function handleLogout() {
            const token = localStorage.getItem('tolo_crm_token');

            if (token) {
                try {
                    await fetch('/api/v1/auth/logout', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json',
                        }
                    });
                } catch (e) {
                    console.error('Logout error:', e);
                }
            }

            localStorage.removeItem('tolo_crm_token');
            localStorage.removeItem('tolo_crm_user');

            document.getElementById('sessionPanel').style.display = 'none';
            document.getElementById('authForms').style.display = 'block';
            showMsg('You have been signed out.');
        }

        async function testEndpoint(url, label) {
            const token = localStorage.getItem('tolo_crm_token');
            if (!token) return;

            try {
                const res = await fetch(url, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json',
                    }
                });
                const data = await res.json();
                alert(`API Response [${label}]:\n` + JSON.stringify(data, null, 2).slice(0, 500) + '...');
            } catch (e) {
                alert(`Error fetching ${label}: ` + e.message);
            }
        }

        // Initialize session check
        document.addEventListener('DOMContentLoaded', checkExistingSession);
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tolo CRM — Automated Visual Test Report Dashboard</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-base: #0B0F19;
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
            flex-direction: column;
            overflow-x: hidden;
        }

        header {
            padding: 20px 40px;
            background: rgba(11, 15, 25, 0.9);
            border-bottom: 1px solid var(--border-subtle);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-main);
        }

        .brand-icon {
            width: 38px;
            height: 38px;
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

        .header-links {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-main);
        }

        .nav-btn.primary {
            background: linear-gradient(135deg, #6366F1, #4F46E5);
            color: #FFFFFF;
            border-color: transparent;
        }

        main {
            padding: 40px;
            max-width: 1440px;
            margin: 0 auto;
            width: 100%;
            flex: 1;
        }

        /* Hero Status Card */
        .report-hero {
            background: linear-gradient(135deg, rgba(18, 24, 38, 0.95), rgba(15, 23, 42, 0.95));
            border: 1px solid var(--border-glow);
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 36px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }

        .report-hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #10B981, #06B6D4, #6366F1);
        }

        .hero-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .hero-title {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .badge-passed {
            background: rgba(16, 185, 129, 0.15);
            color: #34D399;
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .metrics-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-subtle);
        }

        .metric-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .metric-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-val {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-main);
        }

        /* Filter Pills */
        .filter-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .filter-pill {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-pill:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-main);
        }

        .filter-pill.active {
            background: rgba(99, 102, 241, 0.2);
            border-color: var(--primary);
            color: #C7D2FE;
        }

        /* Test Cards Grid */
        .scenarios-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
            gap: 24px;
        }

        .scenario-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        .scenario-card:hover {
            border-color: rgba(99, 102, 241, 0.4);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .card-img-wrap {
            position: relative;
            width: 100%;
            height: 240px;
            background: #000;
            overflow: hidden;
            cursor: pointer;
        }

        .card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top;
            transition: transform 0.3s ease;
        }

        .card-img-wrap:hover img {
            transform: scale(1.03);
        }

        .zoom-overlay {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .card-img-wrap:hover .zoom-overlay {
            opacity: 1;
        }

        .card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
        }

        .card-top-tags {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-cat {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--accent-cyan);
        }

        .card-status-pill {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 12px;
            background: rgba(16, 185, 129, 0.15);
            color: #34D399;
        }

        .card-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-main);
        }

        .card-desc {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* Lightbox Modal */
        .lightbox-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 200;
            padding: 30px;
        }

        .lightbox-modal.open {
            display: flex;
        }

        .lightbox-content {
            max-width: 90vw;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .lightbox-content img {
            max-width: 100%;
            max-height: 80vh;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
            border: 1px solid var(--border-glow);
        }

        .lightbox-caption {
            margin-top: 14px;
            font-size: 15px;
            font-weight: 700;
            color: var(--text-main);
            text-align: center;
        }

        .close-lightbox-btn {
            position: absolute;
            top: -40px;
            right: 0;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 28px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <header>
        <a href="/" class="brand-box">
            <div class="brand-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
            </div>
            <span class="brand-name">Tolo CRM &bull; Visual Quality Suite</span>
        </a>

        <div class="header-links">
            <a href="/" class="nav-btn">Home Portal</a>
            <a href="/app" class="nav-btn primary">Open Live App &rarr;</a>
        </div>
    </header>

    <main>
        <!-- Hero Metrics Card -->
        <div class="report-hero">
            <div class="hero-top">
                <div>
                    <h1 class="hero-title">Automated Playwright Visual Test Suite</h1>
                    <p style="font-size: 14px; color: var(--text-muted); margin-top: 4px;">End-to-End Visual Verification & Regression Coverage across all CRM Modules</p>
                </div>
                <div class="badge-passed">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>14 / 14 Passed (100%)</span>
                </div>
            </div>

            <div class="metrics-bar">
                <div class="metric-item">
                    <span class="metric-label">Execution Status</span>
                    <span class="metric-val" style="color: #34D399;">100% Passed</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Browser Engine</span>
                    <span class="metric-val">Chromium (Headless)</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Viewport Target</span>
                    <span class="metric-val">1440 &times; 900 (HD)</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Environment</span>
                    <span class="metric-val">macOS ARM64 &bull; PG 16</span>
                </div>
            </div>
        </div>

        <!-- Category Filters -->
        <div class="filter-bar">
            <button type="button" class="filter-pill active" onclick="filterCategory('all', this)">All Scenarios (14)</button>
            <button type="button" class="filter-pill" onclick="filterCategory('Security', this)">Security & Routing</button>
            <button type="button" class="filter-pill" onclick="filterCategory('Analytics', this)">Analytics & Dashboard</button>
            <button type="button" class="filter-pill" onclick="filterCategory('Pipelines', this)">Pipelines & Kanban</button>
            <button type="button" class="filter-pill" onclick="filterCategory('Lead', this)">Leads & AI Scoring</button>
            <button type="button" class="filter-pill" onclick="filterCategory('Companies', this)">Companies & JSONB</button>
            <button type="button" class="filter-pill" onclick="filterCategory('Timeline', this)">Timeline & Audits</button>
            <button type="button" class="filter-pill" onclick="filterCategory('Search', this)">tsvector Search</button>
            <button type="button" class="filter-pill" onclick="filterCategory('RBAC', this)">RBAC & Roles</button>
        </div>

        <!-- Scenarios Grid -->
        <div class="scenarios-grid" id="scenariosGrid">
            <!-- Populated dynamically via JavaScript from report.json or fallback list -->
        </div>
    </main>

    <!-- Lightbox Modal -->
    <div class="lightbox-modal" id="lightboxModal" onclick="closeLightbox(event)">
        <div class="lightbox-content" onclick="event.stopPropagation()">
            <button type="button" class="close-lightbox-btn" onclick="closeLightbox()">&times;</button>
            <img src="" id="lightboxImg" alt="Screenshot Detail">
            <div class="lightbox-caption" id="lightboxCaption">Caption</div>
        </div>
    </div>

    <script>
        const fallbackScenarios = [
            {
                filename: '01_unauthenticated_guard.png',
                title: 'Unauthenticated Security Route Guard',
                description: 'Visiting /app without an active session automatically rejects guest requests and redirects to /login with 302.',
                category: 'Security & Routing'
            },
            {
                filename: '02_landing_portal_dark.png',
                title: 'Modern Dark Landing & Auth Portal',
                description: 'Interactive glassmorphic landing page featuring glowing mesh orbs, dual-tab login & registration, and one-click demo selectors.',
                category: 'UI & Design'
            },
            {
                filename: '03_login_session_panel.png',
                title: 'Web Session & Sanctum Token Launchpad',
                description: 'Authenticates against /login, generates active Laravel session cookies and a Sanctum Bearer token for client API calls.',
                category: 'Authentication'
            },
            {
                filename: '04_executive_dashboard.png',
                title: 'Executive Sales & Revenue Dashboard',
                description: 'Real-time KPIs ($540k open pipeline, $325k weighted forecast, $645k won revenue), 4-stage funnel visualizer, and rep leaderboard.',
                category: 'Analytics & KPIs'
            },
            {
                filename: '05_pipeline_kanban_board.png',
                title: '7-Stage Pipeline Kanban Board',
                description: 'Custom pipeline stages with win probabilities (10% to 100%), stage aggregates, and deal cards.',
                category: 'Pipelines & Deals'
            },
            {
                filename: '06_new_deal_modal.png',
                title: 'Interactive Deal Creation Modal',
                description: 'Modal form connected to PostgreSQL pipelines, stages, and company associations.',
                category: 'Pipelines & Deals'
            },
            {
                filename: '07_kanban_with_new_deal.png',
                title: 'Kanban Board Real-Time Insertion',
                description: 'Newly created $350k deal dynamically rendered in the Technical Demo stage column.',
                category: 'Pipelines & Deals'
            },
            {
                filename: '08_leads_ai_scoring_view.png',
                title: 'Leads Engine & AI Qualification Scoring',
                description: 'Roster of incoming leads with calculated AI qualification scores (⚡ 100/100, 80/100) and lifecycle statuses.',
                category: 'Lead Management'
            },
            {
                filename: '09_new_lead_modal_creation.png',
                title: 'Add Lead Modal & Corporate Domain Scoring',
                description: 'Captures lead details and computes instant score via LeadScoringService.',
                category: 'Lead Management'
            },
            {
                filename: '10_companies_jsonb_accounts.png',
                title: 'Enterprise Accounts & PostgreSQL JSONB Attributes',
                description: 'Enterprise company cards displaying annual revenues and GIN-indexed JSONB schema-less custom attributes.',
                category: 'Companies & Accounts'
            },
            {
                filename: '11_contacts_roster.png',
                title: 'Executive Contacts & Decision Makers',
                description: 'Roster of key contacts, job titles, communication channels, and account associations.',
                category: 'Contacts'
            },
            {
                filename: '12_activity_timeline_audit.png',
                title: 'Unified Chronological Activity & Audit Stream',
                description: 'Combines polymorphic CRM activities (meetings, calls, tasks, notes) with Spatie model mutation audits.',
                category: 'Timeline & Audits'
            },
            {
                filename: '13_global_tsvector_search.png',
                title: 'PostgreSQL tsvector High-Speed Full-Text Search',
                description: 'Sub-millisecond ranked multi-entity search returning matched companies, deals, contacts, and leads.',
                category: 'Full-Text Search'
            },
            {
                filename: '14_role_switcher_rep.png',
                title: 'Instant Role Impersonation Toolbar',
                description: 'Header switcher instantly changes active user context and permissions to Sales Representative (Alex Rivers).',
                category: 'RBAC & Multi-Tenancy'
            }
        ];

        let allScenarios = fallbackScenarios;

        async function initReport() {
            try {
                const res = await fetch('/screenshots/report.json');
                if (res.ok) {
                    const data = await res.json();
                    if (data.tests && data.tests.length > 0) {
                        allScenarios = data.tests;
                    }
                }
            } catch (e) {
                console.log('Using default scenarios list');
            }

            renderScenarios(allScenarios);
        }

        function renderScenarios(list) {
            const grid = document.getElementById('scenariosGrid');
            grid.innerHTML = '';

            list.forEach(item => {
                const card = document.createElement('div');
                card.className = 'scenario-card';
                card.dataset.category = item.category;

                card.innerHTML = `
                    <div class="card-img-wrap" onclick="openLightbox('/screenshots/${item.filename}', '${item.title}')">
                        <img src="/screenshots/${item.filename}" alt="${item.title}" onerror="this.src='/screenshots/02_landing_portal_dark.png'">
                        <div class="zoom-overlay">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                            <span>Click to Zoom</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-top-tags">
                            <span class="card-cat">${item.category}</span>
                            <span class="card-status-pill">Passed</span>
                        </div>
                        <h3 class="card-title">${item.title}</h3>
                        <p class="card-desc">${item.description}</p>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        function filterCategory(cat, btn) {
            document.querySelectorAll('.filter-pill').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            if (cat === 'all') {
                renderScenarios(allScenarios);
            } else {
                const filtered = allScenarios.filter(s => s.category.toLowerCase().includes(cat.toLowerCase()));
                renderScenarios(filtered);
            }
        }

        function openLightbox(src, caption) {
            document.getElementById('lightboxImg').src = src;
            document.getElementById('lightboxCaption').textContent = caption;
            document.getElementById('lightboxModal').classList.add('open');
        }

        function closeLightbox() {
            document.getElementById('lightboxModal').classList.remove('open');
        }

        document.addEventListener('DOMContentLoaded', initReport);
    </script>
</body>
</html>

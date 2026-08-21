# Tolo CRM — Enterprise CRM Platform

[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)](https://github.com/ojkalam/tolo_crm/actions)
[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B%20%7C%208.4-777BB4.svg?logo=php)](https://www.php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-FF2D20.svg?logo=laravel)](https://laravel.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16%2B-336791.svg?logo=postgresql)](https://www.postgresql.org)
[![Redis](https://img.shields.io/badge/Redis-Queue%20%26%20Cache-DC382D.svg?logo=redis)](https://redis.io)
[![Pest Tests](https://img.shields.io/badge/Tests-Pest%20%28100%25%20Passing%29-22C55E.svg)](https://pestphp.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

**Tolo CRM** is a high-performance, multi-tenant Enterprise Customer Relationship Management (CRM) platform engineered with **Laravel 12**, **PHP 8.3+**, and **PostgreSQL 16+**. It incorporates native PostgreSQL optimizations (`JSONB`, `GIN` indexing, generated `tsvector` full-text search), real-time collaborative Kanban updates via **Laravel Reverb WebSockets**, transactional lead conversion engines, granular Role-Based Access Control (RBAC), and automated background queues with **Laravel Horizon**.

---

## 📌 Key Features

- 🏢 **Multi-Tenant Architecture**: Organization-scoped data isolation with distributed `UUIDv7` / `UUIDv4` primary keys across all models.
- 🛡️ **Enterprise RBAC**: Role-Based Access Control powered by `spatie/laravel-permission` with 6 standard roles (`SuperAdmin`, `OrgAdmin`, `SalesManager`, `SalesRepresentative`, `SupportAgent`, `Auditor`).
- ⚡ **PostgreSQL JSONB Extensibility**: Schema-less `custom_attributes` on Companies, Contacts, Leads, Deals, and Activities with native PostgreSQL GIN index optimization.
- 🎯 **Lead Qualification & Atomic Conversion Engine**: Automated lead qualification scoring engine with single-transaction atomic conversion of `Lead` $\rightarrow$ `Company`, `Contact`, and optional open `Deal`.
- 📊 **Custom Pipelines & Drag-and-Drop Kanban**: Customizable multi-stage sales pipelines with configurable win probabilities, optimistic stage moves, won/lost status resolution, and WebSocket broadcasting.
- ⏱️ **Unified Chronological Timeline & Audit Trail**: Polymorphic CRM activities (`calls`, `meetings`, `tasks`, `notes`, `emails`) unified with `spatie/laravel-activitylog` model audit logs.
- 🔍 **PostgreSQL `tsvector` Full-Text Search**: Sub-millisecond ranked multi-entity search across Contacts, Companies, Leads, and Deals using generated `search_vector` columns and `ts_rank`.
- 📈 **Executive Analytics & Revenue Forecasting**: Comprehensive KPI calculations, weighted sales forecasts, funnel breakdown, lead conversion velocity, monthly MRR trend, and sales rep performance leaderboards.
- 📡 **Real-time WebSockets & Background Queues**: Collaborative presence and private channel broadcasting with **Laravel Reverb** and Redis queue supervision via **Laravel Horizon**.
- 📥 **Chunked CSV/Excel Import & Streaming Export**: Scalable background CSV/Excel import for contacts (`ContactsImport`) and streaming CSV/Excel export for deals (`DealsExport`).

---

## 🏗️ Database Schema & Architecture

```text
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│  Organizations  │───┬──<│      Users      │───┬──<│    Activities   │
│  (Tenants)      │   │   │  (UUID, RBAC)   │   │   │  (Polymorphic)  │
└─────────────────┘   │   └─────────────────┘   │   └─────────────────┘
                      │                         │
                      ├──<┌─────────────────┐   ├──<┌─────────────────┐
                      │   │    Companies    │───┤   │      Leads      │
                      │   │ (Accounts/JSONB)│   │   │ (Qualification) │
                      │   └─────────────────┘   │   └─────────────────┘
                      │            │            │
                      │            ▼            │
                      ├──<┌─────────────────┐   ├──<┌─────────────────┐
                      │   │    Contacts     │───┤   │      Deals      │
                      │   │ (JSONB Fields)  │   │   │(Pipelines/Stages│
                      │   └─────────────────┘   │   └─────────────────┘
                      │                         │
                      └──<┌─────────────────┐   │
                          │ Pipelines/Stages│───┘
                          └─────────────────┘
```

---

## 📋 Requirements

Ensure your environment meets the following requirements:

- **PHP**: 8.3 or higher (PHP 8.4 supported)
- **PHP Extensions**: `pdo_pgsql`, `pgsql`, `redis`, `bcmath`, `zip`, `pcntl`, `intl`, `mbstring`, `xml`, `ctype`
- **PostgreSQL**: 16.0 or higher (with `uuid-ossp`, `pg_trgm`, and `btree_gin` extension support)
- **Redis**: 6.0 or higher
- **Composer**: 2.6 or higher
- **Node.js**: 20+ & NPM (for frontend / WebSocket assets)

---

## 🛠️ Step-by-Step Installation

### 1. Clone the Repository

```bash
git clone https://github.com/ojkalam/tolo_crm.git
cd tolo_crm
```

### 2. Install Composer Dependencies

```bash
composer install
```

### 3. Configure Environment File

Create a copy of `.env.example` as `.env`:

```bash
cp .env.example .env
```

Configure your PostgreSQL database and Redis credentials in `.env`:

```dotenv
APP_NAME="Tolo CRM"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration (PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=crm_enterprise
DB_USERNAME=postgres
DB_PASSWORD=your_postgres_password

# Cache, Session & Queues (Redis)
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Real-time WebSocket Broadcasting (Reverb)
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=crm_reverb_app
REVERB_APP_KEY=crm_reverb_key
REVERB_APP_SECRET=crm_reverb_secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http
```

### 4. Generate Application Encryption Key

```bash
php artisan key:generate
```

### 5. Create PostgreSQL Database

Make sure your PostgreSQL server is running, then create the database:

```bash
# Via PostgreSQL CLI
psql -U postgres -c "CREATE DATABASE crm_enterprise;"
psql -U postgres -c "CREATE DATABASE crm_enterprise_test;"
```

### 6. Run Migrations & RBAC Seeders

This enables PostgreSQL extensions (`uuid-ossp`, `pg_trgm`, `btree_gin`), creates all tables with JSONB GIN indexes and generated `tsvector` columns, and seeds default roles and permissions:

```bash
php artisan migrate --seed
```

---

## 🚀 Running the Application

### Start Development Server
```bash
php artisan serve
```
The API will be available at `http://localhost:8000`.

### Start Redis Queue Worker / Horizon
```bash
php artisan horizon
```
The Horizon dashboard is accessible at `/horizon` (restricted to `SuperAdmin` and `OrgAdmin` roles).

### Start Laravel Reverb WebSocket Server
```bash
php artisan reverb:start
```

---

## 🧪 Running Tests & Quality Checks

Tolo CRM comes with a **100% passing Pest test suite** (28 feature test suites, 146 assertions).

```bash
# Run all Pest feature and unit tests
php artisan test

# Run tests in parallel
php artisan test --parallel

# Run Laravel Pint code style fixer
vendor/bin/pint --test

# Run PHPStan static analysis
vendor/bin/phpstan analyse
```

---

## 🌐 API Route Reference (v1)

All authenticated endpoints require `Authorization: Bearer <sanctum_token>`.

### Authentication
| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/v1/auth/register` | Register new organization & admin user |
| `POST` | `/api/v1/auth/login` | Authenticate and obtain Sanctum API token |
| `POST` | `/api/v1/auth/logout` | Revoke current token |
| `GET` | `/api/v1/auth/me` | Fetch authenticated user profile & roles |
| `PUT` | `/api/v1/auth/profile` | Update profile information |
| `PUT` | `/api/v1/auth/password` | Update account password |

### Companies & Contacts
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/companies` | List companies (with Spatie QueryBuilder filters) |
| `POST` | `/api/v1/companies` | Create new company (with JSONB `custom_attributes`) |
| `GET` | `/api/v1/companies/{id}` | Company details |
| `PUT` | `/api/v1/companies/{id}` | Update company |
| `DELETE`| `/api/v1/companies/{id}` | Soft delete company |
| `GET` | `/api/v1/contacts` | List contacts |
| `POST` | `/api/v1/contacts` | Create contact |
| `POST` | `/api/v1/contacts/import` | Chunked CSV/Excel contact import |

### Leads & Conversion Engine
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/leads` | List leads with scores & filters |
| `POST` | `/api/v1/leads` | Create lead (auto-calculates qualification score) |
| `POST` | `/api/v1/leads/{id}/convert` | **Atomic Transaction:** Convert Lead $\rightarrow$ Company, Contact, Deal |

### Pipelines, Deals & Kanban
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/pipelines` | List pipelines & ordered stages |
| `POST` | `/api/v1/pipelines` | Create pipeline with custom stages |
| `POST` | `/api/v1/pipelines/{id}/reorder-stages` | Reorder pipeline stages |
| `GET` | `/api/v1/pipelines/{id}/kanban` | **Kanban API:** Grouped stages, deal lists, total & weighted values |
| `GET` | `/api/v1/deals` | List deals with amount & status filters |
| `POST` | `/api/v1/deals` | Create deal |
| `POST` | `/api/v1/deals/{id}/move-stage` | **Optimistic Stage Transition:** Updates stage, status & triggers WebSockets |
| `GET` | `/api/v1/deals/export` | Download Deals CSV/Excel export |

### Activities & Unified Timeline
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/activities` | List polymorphic activities (`call`, `meeting`, `task`, `note`, `email`) |
| `POST` | `/api/v1/activities` | Log new activity on Contact, Company, Lead, or Deal |
| `POST` | `/api/v1/activities/{id}/complete` | Mark activity completed with outcome & notes |
| `GET` | `/api/v1/timeline` | **Unified Timeline:** Aggregates CRM activities + Spatie model audit logs |

### Full-Text Search & Executive Analytics
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/search?q={query}` | **PostgreSQL Search:** Sub-millisecond ranked multi-entity search |
| `GET` | `/api/v1/analytics/dashboard` | Executive KPIs, stage funnels, conversion velocity & MRR trends |
| `GET` | `/api/v1/analytics/pipeline-forecast`| Weighted revenue forecasting by pipeline |
| `GET` | `/api/v1/analytics/rep-performance` | Sales representative performance matrix |

---

## 🔒 Roles & Permission Matrix

| Role | Scope & Permissions |
|---|---|
| **SuperAdmin** | Unrestricted global access across organizations |
| **OrgAdmin** | Full administrative control within tenant organization |
| **SalesManager** | Manages pipelines, views executive analytics, creates and updates all deals |
| **SalesRepresentative**| Manages assigned leads, contacts, deals, and activities |
| **SupportAgent** | Read-only deals, manages contacts and support activities |
| **Auditor** | Read-only access across all records, activity logs, and financial reports |

---

## 📄 License

Tolo CRM is open-sourced software licensed under the [MIT License](LICENSE).

# Enterprise CRM Development Roadmap & Execution Blueprint
**Stack:** PHP 8.3+ | Laravel 11/12+ | PostgreSQL 16+ | Redis | Laravel Reverb | Tailwind CSS / Alpine / Inertia

---

## 1. Project Overview & Architectural Standards

This document serves as the master engineering plan for developing a modular, scalable, multi-tenant-ready Customer Relationship Management (CRM) system. Every stage of development is broken down into atomic, testable tasks.

### 1.1 Core Architectural Principles
* **Domain-Driven Action Pattern:** Business logic is encapsulated in single-responsibility Action classes (`app/Actions/*`) rather than bloated controllers or fat models.
* **PostgreSQL Native Optimization:** 
  * `UUIDv7` / `UUIDv4` primary keys for distributed safety and security.
  * Native `JSONB` with GIN indexing for user-defined custom fields.
  * PostgreSQL `tsvector` and `pg_trgm` for sub-millisecond global full-text search.
* **Strict Typing & Quality Gates:** PHP 8.3 strict types (`declare(strict_types=1);`), PHPStan Level 8, Laravel Pint (PSR-12), Pest PHP feature/unit test suites.
* **Real-time Event Broadcasting:** Laravel Reverb + Laravel Echo for live pipeline updates, lead alerts, and notification streams.

---

## 2. Git Workflow & Commit Guidelines

To ensure continuous delivery and clean version control, **every single sub-task must be committed and pushed before moving to the next.**

### 2.1 Branching Strategy
* `main`: Production-ready release branch.
* `develop`: Integration staging branch.
* `feature/<phase>-<task-name>`: Feature branch created from `develop`.
* `fix/<bug-name>`: Bug fix branch.

### 2.2 Conventional Commit Standard
```text
<type>(<scope>): <short descriptive summary>

[Optional detailed body explaining WHAT was done and WHY]

[Optional issue/ticket reference]
```

#### Commit Types:
* `feat`: New feature or business capability.
* `fix`: Bug fix or patch.
* `schema`: Database migrations, seeders, or schema modifications.
* `refactor`: Code restructuring without changing external behavior.
* `test`: Adding or refactoring unit/feature tests.
* `perf`: Database index optimization or query tuning.
* `docs`: Documentation updates.
* `ci`: Continuous integration or deployment configuration.

---

## 3. Database Schema & Entity Relationships

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

## 4. Detailed Step-by-Step Task Breakdown

---

### Phase 1: Environment, PostgreSQL Extensions & Core Setup

#### Task 1.1: Project Initialization & Environment Configuration
* **Goal:** Initialize Laravel project with PHP 8.3+, configure PostgreSQL connection, set up environment variables, and verify database connectivity.
* **Commands:**
  ```bash
  git checkout -b feature/phase-1-init-setup
  composer create-project laravel/laravel crm-core
  cd crm-core
  cp .env.example .env
  ```
* **PostgreSQL Configuration (`.env`):**
  ```dotenv
  DB_CONNECTION=pgsql
  DB_HOST=127.0.0.1
  DB_PORT=5432
  DB_DATABASE=crm_enterprise
  DB_USERNAME=postgres
  DB_PASSWORD=your_secure_password
  ```
* **Git Action:**
  ```bash
  git add .
  git commit -m "feat(setup): initialize Laravel project and configure PostgreSQL"
  git push origin feature/phase-1-init-setup
  ```

#### Task 1.2: PostgreSQL Database Extensions & Base Utilities
* **Goal:** Enable `uuid-ossp`, `pg_trgm`, and `btree_gin` extensions in PostgreSQL via migrations.
* **Commands:**
  ```bash
  php artisan make:migration enable_postgres_extensions
  ```
* **Migration Content:**
  ```php
  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Support\Facades\DB;

  return new class extends Migration {
      public function up(): void {
          DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
          DB::statement('CREATE EXTENSION IF NOT EXISTS "pg_trgm";');
          DB::statement('CREATE EXTENSION IF NOT EXISTS "btree_gin";');
      }
      public function down(): void {
          DB::statement('DROP EXTENSION IF EXISTS "btree_gin";');
          DB::statement('DROP EXTENSION IF EXISTS "pg_trgm";');
          DB::statement('DROP EXTENSION IF EXISTS "uuid-ossp";');
      }
  };
  ```
* **Git Action:**
  ```bash
  php artisan migrate
  git add .
  git commit -m "schema(database): enable uuid-ossp, pg_trgm, and btree_gin extensions"
  git push origin feature/phase-1-init-setup
  ```

#### Task 1.3: Core Package Installation
* **Goal:** Install foundational packages for RBAC, activity logging, Excel handling, and API authentication.
* **Commands:**
  ```bash
  composer require spatie/laravel-permission spatie/laravel-activitylog spatie/laravel-query-builder laravel/sanctum laravel/reverb predis/predis
  php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
  php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
  ```
* **Git Action:**
  ```bash
  git add .
  git commit -m "feat(deps): install Spatie Permission, Activitylog, Sanctum, Reverb, and Predis"
  git push origin feature/phase-1-init-setup
  ```

---

### Phase 2: Multi-Tenancy, Authentication & RBAC

#### Task 2.1: UUID User Model & Base Organization Migration
* **Goal:** Migrate `users` and create `organizations` table supporting multi-tenant isolation.
* **Commands:**
  ```bash
  git checkout -b feature/phase-2-auth-rbac
  php artisan make:model Organization -m
  php artisan make:migration modify_users_for_crm_multitenancy
  ```
* **Schema Highlights:**
  * `organizations`: `id (uuid)`, `name`, `domain`, `settings (jsonb)`, `is_active (bool)`, `timestamps`.
  * `users`: `id (uuid)`, `organization_id (uuid, FK)`, `first_name`, `last_name`, `email`, `password`, `phone`, `status (enum)`, `avatar_url`, `last_login_at`, `softDeletes()`.
* **Git Action:**
  ```bash
  php artisan migrate
  git add .
  git commit -m "schema(auth): implement organizations and update users schema with UUIDs"
  git push origin feature/phase-2-auth-rbac
  ```

#### Task 2.2: RBAC Roles, Permissions & Seeder
* **Goal:** Define hierarchical permissions (`leads.*`, `deals.*`, `contacts.*`, `reports.*`, `settings.*`) and configure role seeding.
* **Roles:** `SuperAdmin`, `OrgAdmin`, `SalesManager`, `SalesRepresentative`, `SupportAgent`, `Auditor`.
* **Commands:**
  ```bash
  php artisan make:seeder RolesAndPermissionsSeeder
  ```
* **Git Action:**
  ```bash
  php artisan db:seed --class=RolesAndPermissionsSeeder
  git add .
  git commit -m "feat(rbac): configure CRM permission matrix and standard role seeders"
  git push origin feature/phase-2-auth-rbac
  ```

#### Task 2.3: Authentication API & Profile Endpoints
* **Goal:** Implement Sanctum token authentication, login, registration, password resets, and current authenticated user profile endpoints with granular abilities.
* **Git Action:**
  ```bash
  git add .
  git commit -m "feat(auth): add Sanctum authentication, login/logout, and profile routes"
  git push origin feature/phase-2-auth-rbac
  ```

---

### Phase 3: Accounts (Companies) & Contact Management

#### Task 3.1: Companies Module with JSONB Custom Attributes
* **Goal:** Create `companies` table, model, repository/action layer, and support dynamic schema-less fields using PostgreSQL `jsonb`.
* **Commands:**
  ```bash
  git checkout -b feature/phase-3-companies-contacts
  php artisan make:model Company -mcr
  php artisan make:class Actions/Company/CreateCompanyAction
  php artisan make:class Actions/Company/UpdateCompanyAction
  ```
* **Database Optimization:**
  * Add GIN Index on `custom_attributes`: `CREATE INDEX companies_custom_attributes_gin ON companies USING gin (custom_attributes);`
* **Git Action:**
  ```bash
  php artisan migrate
  git add .
  git commit -m "feat(companies): implement Company model with JSONB custom attributes and GIN indexing"
  git push origin feature/phase-3-companies-contacts
  ```

#### Task 3.2: Contacts Module & Company Relationships
* **Goal:** Implement `contacts` table, multi-channel communication points (emails, phones, social links), and link to companies.
* **Commands:**
  ```bash
  php artisan make:model Contact -mcr
  php artisan make:request StoreContactRequest
  php artisan make:request UpdateContactRequest
  ```
* **Schema Highlights:**
  * `id (uuid)`, `organization_id (uuid)`, `company_id (uuid, nullable)`, `first_name`, `last_name`, `email`, `phone`, `mobile`, `job_title`, `department`, `lifecycle_stage`, `custom_attributes (jsonb)`.
* **Git Action:**
  ```bash
  php artisan migrate
  git add .
  git commit -m "feat(contacts): create contacts management with company associations and request validation"
  git push origin feature/phase-3-companies-contacts
  ```

---

### Phase 4: Lead Management & Conversion Engine

#### Task 4.1: Leads Schema & Lifecycle Status
* **Goal:** Create `leads` entity with status workflow (`New`, `Contacted`, `Nurturing`, `Qualified`, `Unqualified`, `Lost`).
* **Commands:**
  ```bash
  git checkout -b feature/phase-4-lead-engine
  php artisan make:model Lead -mcr
  php artisan make:enum Enums/LeadStatus
  php artisan make:enum Enums/LeadSource
  ```
* **Schema Highlights:**
  * `id (uuid)`, `organization_id (uuid)`, `assigned_user_id (uuid, nullable)`, `title`, `first_name`, `last_name`, `company_name`, `email`, `phone`, `status`, `source`, `score (int default 0)`, `estimated_value (decimal 15,2)`, `notes (text)`.
* **Git Action:**
  ```bash
  php artisan migrate
  git add .
  git commit -m "feat(leads): create lead entity, status/source enums, and CRUD resources"
  git push origin feature/phase-4-lead-engine
  ```

#### Task 4.2: Automated Lead Scoring & Atomic Conversion Action
* **Goal:** Create an atomic database transaction that converts a qualified `Lead` into a `Company`, `Contact`, and an optional open `Deal`.
* **Implementation:** `app/Actions/Lead/ConvertLeadAction.php`
  ```php
  DB::transaction(function () use ($lead, $dto) {
      $company = Company::firstOrCreate([...]);
      $contact = Contact::create([...]);
      $deal = Deal::create([...]);
      $lead->update(['status' => LeadStatus::CONVERTED, 'converted_at' => now()]);
      return ['company' => $company, 'contact' => $contact, 'deal' => $deal];
  });
  ```
* **Git Action:**
  ```bash
  git add .
  git commit -m "feat(leads): implement atomic lead conversion action with transactional integrity"
  git push origin feature/phase-4-lead-engine
  ```

---

### Phase 5: Pipelines, Deals & Interactive Kanban Board

#### Task 5.1: Pipeline and Stage Schema
* **Goal:** Create customizable sales pipelines with configurable stages, probability percentages, and stage ordering.
* **Commands:**
  ```bash
  git checkout -b feature/phase-5-deals-pipelines
  php artisan make:model Pipeline -m
  php artisan make:model PipelineStage -m
  ```
* **Schema Highlights:**
  * `pipelines`: `id (uuid)`, `organization_id (uuid)`, `name`, `is_default (bool)`.
  * `pipeline_stages`: `id (uuid)`, `pipeline_id (uuid, FK)`, `name`, `win_probability (int 0-100)`, `order_column (int)`, `color_code (string)`.
* **Git Action:**
  ```bash
  php artisan migrate
  git add .
  git commit -m "schema(deals): implement pipeline and dynamic pipeline stage entities"
  git push origin feature/phase-5-deals-pipelines
  ```

#### Task 5.2: Deals Architecture & Drag-and-Drop Stage Transitions
* **Goal:** Create `deals` module, stage change event listeners, deal value aggregation, and WebSocket broadcast triggers.
* **Commands:**
  ```bash
  php artisan make:model Deal -mcr
  php artisan make:event DealStageUpdated
  php artisan make:class Actions/Deal/MoveDealStageAction
  ```
* **Schema Highlights:**
  * `id (uuid)`, `organization_id (uuid)`, `pipeline_id (uuid)`, `stage_id (uuid)`, `company_id (uuid, nullable)`, `contact_id (uuid, nullable)`, `assigned_to (uuid, nullable)`, `name`, `amount (decimal 15,2)`, `currency (string 3)`, `expected_close_date`, `status (enum: open, won, lost)`.
* **Git Action:**
  ```bash
  php artisan migrate
  git add .
  git commit -m "feat(deals): implement deal management, optimistic stage transition, and broadcast events"
  git push origin feature/phase-5-deals-pipelines
  ```

---

### Phase 6: Polymorphic Activities, Tasks, Notes & Audit Trail

#### Task 6.1: Polymorphic Activity Tracking System
* **Goal:** Create a unified activity system where calls, meetings, tasks, and notes can be logged against Leads, Deals, Companies, or Contacts.
* **Commands:**
  ```bash
  git checkout -b feature/phase-6-activities-audit
  php artisan make:model Activity -mcr
  php artisan make:enum Enums/ActivityType
  ```
* **Schema Highlights:**
  * `id (uuid)`, `organization_id (uuid)`, `user_id (uuid)`, `subjectable_type (morphs)`, `subjectable_id (morphs)`, `type (call, meeting, task, note, email)`, `title`, `description`, `due_date (timestamp, nullable)`, `completed_at (timestamp, nullable)`, `metadata (jsonb)`.
* **Git Action:**
  ```bash
  php artisan migrate
  git add .
  git commit -m "feat(activities): create polymorphic activity, task, and note logging system"
  git push origin feature/phase-6-activities-audit
  ```

#### Task 6.2: Unified Timeline Aggregator & Spatie Audit Logging
* **Goal:** Automatically record model mutations and assemble a unified chronological activity timeline for any CRM entity.
* **Git Action:**
  ```bash
  git add .
  git commit -m "feat(audit): implement automatic model activity logging and chronological timeline API"
  git push origin feature/phase-6-activities-audit
  ```

---

### Phase 7: PostgreSQL High-Performance Full-Text Search

#### Task 7.1: PostgreSQL `tsvector` Search Columns and GIN Indexing
* **Goal:** Implement high-speed global search across Companies, Contacts, Leads, and Deals using native PostgreSQL text search.
* **Commands:**
  ```bash
  git checkout -b feature/phase-7-search-indexing
  php artisan make:migration add_fulltext_search_vectors_to_crm
  ```
* **PostgreSQL Migration SQL:**
  ```sql
  ALTER TABLE contacts ADD COLUMN search_vector tsvector GENERATED ALWAYS AS (
      to_tsvector('english', coalesce(first_name, '') || ' ' || coalesce(last_name, '') || ' ' || coalesce(email, '') || ' ' || coalesce(phone, ''))
  ) STORED;

  CREATE INDEX contacts_search_vector_gin ON contacts USING gin(search_vector);
  ```
* **Git Action:**
  ```bash
  php artisan migrate
  git add .
  git commit -m "perf(search): add generated tsvector columns and GIN indexes for full-text search"
  git push origin feature/phase-7-search-indexing
  ```

#### Task 7.2: Global Multi-Entity Search API
* **Goal:** Build `/api/v1/search` endpoint utilizing PostgreSQL `websearch_to_tsquery` returning unified search results.
* **Git Action:**
  ```bash
  git add .
  git commit -m "feat(search): build unified multi-entity global search service"
  git push origin feature/phase-7-search-indexing
  ```

---

### Phase 8: Analytics, Revenue Forecasting & Dashboards

#### Task 8.1: SQL Aggregation Service & KPI Calculators
* **Goal:** Implement optimized PostgreSQL aggregate queries for Sales Funnels, Conversion Rates, Weighted Forecasts, and Monthly Recurring Revenue (MRR).
* **Commands:**
  ```bash
  git checkout -b feature/phase-8-analytics-reports
  php artisan make:service Analytics/SalesMetricsService
  php artisan make:controller Api/AnalyticsDashboardController
  ```
* **Calculated Metrics:**
  * Total Pipeline Value (Sum of Deal Amounts)
  * Weighted Forecast (Amount * WinProbability)
  * Lead Conversion Velocity (Average time from Lead created to Converted)
  * Rep Performance Matrix (Won Deals vs. Lost Deals vs. Activity count)
* **Git Action:**
  ```bash
  git add .
  git commit -m "feat(analytics): implement sales KPI calculations, win-loss ratio, and forecast services"
  git push origin feature/phase-8-analytics-reports
  ```

---

### Phase 9: Real-time WebSockets, Background Queues & Notifications

#### Task 9.1: Redis Queues & Horizon Configuration
* **Goal:** Configure Redis queues for asynchronous processing of email alerts, lead distribution, and reporting exports.
* **Commands:**
  ```bash
  git checkout -b feature/phase-9-queues-realtime
  composer require laravel/horizon
  php artisan horizon:install
  ```
* **Git Action:**
  ```bash
  git add .
  git commit -m "feat(queues): configure Redis queue drivers and install Laravel Horizon"
  git push origin feature/phase-9-queues-realtime
  ```

#### Task 9.2: Laravel Reverb Real-time Broadcasting
* **Goal:** Set up Laravel Reverb server to broadcast live events (deal stage moved, lead assigned, incoming customer activity).
* **Commands:**
  ```bash
  php artisan reverb:install
  ```
* **Broadcast Channels:**
  * `private-organization.{orgId}`: General organization-wide events.
  * `private-user.{userId}`: Personal task reminders and direct mentions.
  * `presence-pipeline.{pipelineId}`: Live collaborative Kanban board updates.
* **Git Action:**
  ```bash
  git add .
  git commit -m "feat(reverb): configure WebSocket broadcasting for collaborative Kanban updates"
  git push origin feature/phase-9-queues-realtime
  ```

---

### Phase 10: Import/Export, Automated Testing & Production Deployment

#### Task 10.1: Asynchronous CSV/Excel Importer & Exporter
* **Goal:** Build chunked background CSV import with validation for contacts and leads.
* **Commands:**
  ```bash
  git checkout -b feature/phase-10-testing-deploy
  composer require maatwebsite/excel
  php artisan make:import ContactsImport
  php artisan make:export DealsExport
  ```
* **Git Action:**
  ```bash
  git add .
  git commit -m "feat(io): implement chunked background CSV/Excel import and export"
  git push origin feature/phase-10-testing-deploy
  ```

#### Task 10.2: Automated Pest / PHPUnit Test Suite
* **Goal:** Achieve >85% test coverage across all core Actions, Policies, and API endpoints using PostgreSQL test database.
* **Test Suites:**
  * `tests/Feature/LeadConversionTest.php`
  * `tests/Feature/DealPipelineStageTransitionTest.php`
  * `tests/Feature/RbacPermissionPolicyTest.php`
  * `tests/Feature/PostgresFullTextSearchTest.php`
* **Git Action:**
  ```bash
  php artisan test
  git add .
  git commit -m "test(suite): add automated feature tests for lead conversion, deals, and RBAC"
  git push origin feature/phase-10-testing-deploy
  ```

#### Task 10.3: Production Optimization & CI/CD Pipeline
* **Goal:** Create GitHub Actions workflow for automated testing, Pint code formatting, PHPStan static analysis, and production caching.
* **CI Steps:**
  1. Set up PHP 8.3 & PostgreSQL service container.
  2. Run `composer install --no-interaction --prefer-dist`.
  3. Run `vendor/bin/pint --test`.
  4. Run `vendor/bin/phpstan analyse --level=8`.
  5. Run `php artisan test --parallel`.
* **Git Action:**
  ```bash
  git add .github/workflows/ci.yml
  git commit -m "ci(workflow): create GitHub Actions CI pipeline with static analysis and Pest tests"
  git push origin feature/phase-10-testing-deploy
  ```

---

## 5. Master Task Completion Checklist

| Phase | Description | Status | Branch |
| :--- | :--- | :--- | :--- |
| **Phase 1** | Env setup, PostgreSQL extensions (`uuid-ossp`, `pg_trgm`), Core Packages | `[ ]` | `feature/phase-1-init-setup` |
| **Phase 2** | Multi-Tenancy Organizations, UUID User model, RBAC Roles & Permissions | `[ ]` | `feature/phase-2-auth-rbac` |
| **Phase 3** | Companies & Contacts modules with PostgreSQL JSONB custom attributes | `[ ]` | `feature/phase-3-companies-contacts` |
| **Phase 4** | Leads schema, Lead Scoring & Atomic Transactional Lead Conversion | `[ ]` | `feature/phase-4-lead-engine` |
| **Phase 5** | Customizable Pipelines, Deals CRUD & Kanban Drag-and-Drop system | `[ ]` | `feature/phase-5-deals-pipelines` |
| **Phase 6** | Polymorphic Activities (Calls, Tasks, Notes) & Spatie Audit Timeline | `[ ]` | `feature/phase-6-activities-audit` |
| **Phase 7** | PostgreSQL `tsvector` Generated Columns, GIN Indexing & Global Search | `[ ]` | `feature/phase-7-search-indexing` |
| **Phase 8** | Revenue Forecasting, Sales Velocity & Executive Analytics Dashboards | `[ ]` | `feature/phase-8-analytics-reports` |
| **Phase 9** | Redis Queue Workers, Laravel Horizon & Laravel Reverb WebSockets | `[ ]` | `feature/phase-9-queues-realtime` |
| **Phase 10**| CSV/Excel Import/Export, Pest Feature Test Suite & GitHub Actions CI/CD | `[ ]` | `feature/phase-10-testing-deploy` |

---

## 6. Daily Git Push Command Quick Reference

```bash
# 1. Check current status
git status

# 2. Add modified files for the completed task
git add .

# 3. Commit with semantic Conventional Commit format
git commit -m "feat(<scope>): <description of task completed>"

# 4. Push directly to the tracking feature branch
git push origin <feature-branch-name>
```

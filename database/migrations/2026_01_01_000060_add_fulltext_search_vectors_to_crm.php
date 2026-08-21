<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Contacts Search Vector & Index
        DB::statement("
            ALTER TABLE contacts ADD COLUMN search_vector tsvector GENERATED ALWAYS AS (
                to_tsvector('english',
                    coalesce(first_name, '') || ' ' ||
                    coalesce(last_name, '') || ' ' ||
                    coalesce(email, '') || ' ' ||
                    coalesce(phone, '') || ' ' ||
                    coalesce(mobile, '') || ' ' ||
                    coalesce(job_title, '') || ' ' ||
                    coalesce(department, '')
                )
            ) STORED;
        ");
        DB::statement('CREATE INDEX contacts_search_vector_gin ON contacts USING gin(search_vector);');

        // 2. Companies Search Vector & Index
        DB::statement("
            ALTER TABLE companies ADD COLUMN search_vector tsvector GENERATED ALWAYS AS (
                to_tsvector('english',
                    coalesce(name, '') || ' ' ||
                    coalesce(domain, '') || ' ' ||
                    coalesce(industry, '') || ' ' ||
                    coalesce(phone, '') || ' ' ||
                    coalesce(website, '') || ' ' ||
                    coalesce(address_city, '') || ' ' ||
                    coalesce(address_country, '')
                )
            ) STORED;
        ");
        DB::statement('CREATE INDEX companies_search_vector_gin ON companies USING gin(search_vector);');

        // 3. Leads Search Vector & Index
        DB::statement("
            ALTER TABLE leads ADD COLUMN search_vector tsvector GENERATED ALWAYS AS (
                to_tsvector('english',
                    coalesce(first_name, '') || ' ' ||
                    coalesce(last_name, '') || ' ' ||
                    coalesce(company_name, '') || ' ' ||
                    coalesce(email, '') || ' ' ||
                    coalesce(phone, '') || ' ' ||
                    coalesce(title, '') || ' ' ||
                    coalesce(notes, '')
                )
            ) STORED;
        ");
        DB::statement('CREATE INDEX leads_search_vector_gin ON leads USING gin(search_vector);');

        // 4. Deals Search Vector & Index
        DB::statement("
            ALTER TABLE deals ADD COLUMN search_vector tsvector GENERATED ALWAYS AS (
                to_tsvector('english',
                    coalesce(name, '') || ' ' ||
                    coalesce(currency, '')
                )
            ) STORED;
        ");
        DB::statement('CREATE INDEX deals_search_vector_gin ON deals USING gin(search_vector);');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS deals_search_vector_gin;');
        DB::statement('ALTER TABLE deals DROP COLUMN IF EXISTS search_vector;');

        DB::statement('DROP INDEX IF EXISTS leads_search_vector_gin;');
        DB::statement('ALTER TABLE leads DROP COLUMN IF EXISTS search_vector;');

        DB::statement('DROP INDEX IF EXISTS companies_search_vector_gin;');
        DB::statement('ALTER TABLE companies DROP COLUMN IF EXISTS search_vector;');

        DB::statement('DROP INDEX IF EXISTS contacts_search_vector_gin;');
        DB::statement('ALTER TABLE contacts DROP COLUMN IF EXISTS search_vector;');
    }
};

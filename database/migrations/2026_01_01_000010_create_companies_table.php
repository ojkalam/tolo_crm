<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('domain')->nullable();
            $table->string('industry')->nullable();
            $table->decimal('annual_revenue', 15, 2)->nullable();
            $table->integer('employees_count')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_country')->nullable();
            $table->string('address_postal_code')->nullable();
            $table->jsonb('custom_attributes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'name']);
            $table->index('domain');
            $table->index('industry');
        });

        // PostgreSQL GIN Index for JSONB Custom Attributes
        DB::statement('CREATE INDEX companies_custom_attributes_gin ON companies USING gin (custom_attributes);');
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

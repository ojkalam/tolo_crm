<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignUuid('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->string('lifecycle_stage')->default('lead'); // lead, prospect, customer, churned, other
            $table->jsonb('custom_attributes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'email']);
            $table->index(['organization_id', 'company_id']);
            $table->index('lifecycle_stage');
        });

        // PostgreSQL GIN Index for JSONB Custom Attributes
        DB::statement('CREATE INDEX contacts_custom_attributes_gin ON contacts USING gin (custom_attributes);');
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};

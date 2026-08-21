<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignUuid('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->default('new');
            $table->string('source')->default('website');
            $table->integer('score')->default(0);
            $table->decimal('estimated_value', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->jsonb('custom_attributes')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'assigned_user_id']);
            $table->index('email');
            $table->index('score');
        });

        DB::statement('CREATE INDEX leads_custom_attributes_gin ON leads USING gin (custom_attributes);');
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

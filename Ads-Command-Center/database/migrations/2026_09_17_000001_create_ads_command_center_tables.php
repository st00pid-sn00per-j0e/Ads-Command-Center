<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('role')->default('specialist')->after('password');
            $table->string('status')->default('active')->after('role');
            $table->index(['organization_id', 'role']);
        });

        Schema::create('google_ads_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('developer_token');
            $table->text('client_id');
            $table->text('client_secret');
            $table->text('refresh_token');
            $table->unsignedBigInteger('login_customer_id')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('google_ads_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('google_ads_connection_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('manager_customer_id')->nullable();
            $table->unsignedBigInteger('parent_customer_id')->nullable();
            $table->string('name');
            $table->string('currency_code', 3)->nullable();
            $table->string('time_zone')->nullable();
            $table->string('account_type')->default('client');
            $table->string('status')->default('active');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->unique(['google_ads_connection_id', 'customer_id']);
        });

        Schema::create('google_ads_account_specialist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('google_ads_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->unique(['google_ads_account_id', 'user_id']);
        });

        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('google_ads_account_id')->constrained()->cascadeOnDelete();
            $table->string('google_resource_name')->unique();
            $table->unsignedBigInteger('google_campaign_id');
            $table->string('name');
            $table->string('status');
            $table->string('campaign_type')->nullable();
            $table->unsignedBigInteger('daily_budget_micros')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->unique(['google_ads_account_id', 'google_campaign_id']);
        });

        Schema::create('campaign_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedBigInteger('cost_micros')->default(0);
            $table->decimal('conversions', 16, 4)->default(0);
            $table->decimal('conversion_value', 18, 4)->default(0);
            $table->decimal('ctr', 10, 6)->default(0);
            $table->decimal('average_cpc_micros', 18, 4)->default(0);
            $table->decimal('cost_per_conversion_micros', 18, 4)->nullable();
            $table->timestamps();
            $table->unique(['campaign_id', 'date']);
        });

        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('google_ads_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');
            $table->string('status')->default('pending');
            $table->json('payload');
            $table->text('reason');
            $table->text('admin_comment')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->text('execution_error')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('campaign_metrics');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('google_ads_account_specialist');
        Schema::dropIfExists('google_ads_accounts');
        Schema::dropIfExists('google_ads_connections');
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['organization_id', 'role']);
            $table->dropConstrainedForeignId('organization_id');
            $table->dropColumn(['role', 'status']);
        });
        Schema::dropIfExists('organizations');
    }
};

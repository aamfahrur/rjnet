<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->cascadeOnDelete();
            $table->string('type', 50)->comment('invoice, payment, ticket, system, promotion');
            $table->string('channel', 30)->default('system');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->string('target_url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->string('sent_via')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->index('customer_id');
            $table->index('type');
            $table->index('is_read');
            $table->index('is_sent');
        });

        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('type', 30);
            $table->string('subject')->nullable();
            $table->text('body_template');
            $table->json('supported_channels')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('billing_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->integer('days_before_due')->comment('H-7, H-3, H-1, H+1, dll');
            $table->string('channel', 30);
            $table->string('status', 20)->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('status');
        });

        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('location')->nullable();
            $table->string('status', 20)->default('success');
            $table->text('failure_reason')->nullable();
            $table->timestamp('login_at');
        });

        Schema::create('scheduled_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_name', 100)->unique();
            $table->string('job_class');
            $table->string('schedule', 50)->comment('Cron expression label');
            $table->string('cron_expression', 50);
            $table->json('parameters')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->string('last_status', 20)->nullable();
            $table->text('last_error')->nullable();
            $table->float('last_execution_time_ms')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_jobs');
        Schema::dropIfExists('login_histories');
        Schema::dropIfExists('billing_reminders');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('notifications');
    }
};

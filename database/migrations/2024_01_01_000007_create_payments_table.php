<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('code', 30)->unique();
            $table->string('driver_class');
            $table->json('config')->comment('Konfigurasi API key, sandbox, dll');
            $table->string('logo')->nullable();
            $table->json('supported_methods')->nullable();
            $table->decimal('admin_fee_percentage', 5, 2)->default(0);
            $table->unsignedBigInteger('admin_fee_fixed')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sandbox')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('code');
            $table->index('is_active');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number', 50)->unique();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('payment_gateway_id')->nullable()->constrained('payment_gateways')->nullOnDelete();
            $table->string('gateway', 30)->nullable()->comment('ipaymu, duitku, midtrans');
            $table->string('gateway_transaction_id')->nullable();
            $table->string('method', 30)->nullable();
            $table->string('channel', 50)->nullable();
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('admin_fee')->default(0);
            $table->unsignedBigInteger('total_amount');
            $table->string('status', 20)->default('pending');
            $table->string('va_number')->nullable();
            $table->string('payment_url')->nullable();
            $table->string('qr_url')->nullable();
            $table->json('gateway_request')->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('callback_data')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('paid_by')->nullable();
            $table->string('notes')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('payment_number');
            $table->index('invoice_id');
            $table->index('customer_id');
            $table->index('gateway_transaction_id');
            $table->index('status');
        });

        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->cascadeOnDelete();
            $table->string('gateway', 30);
            $table->string('event', 50);
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->string('status', 20);
            $table->text('error_message')->nullable();
            $table->float('execution_time_ms')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('payment_id');
            $table->index('gateway');
            $table->index('event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_gateways');
    }
};

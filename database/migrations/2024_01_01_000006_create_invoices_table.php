<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
            $table->unsignedBigInteger('subtotal')->comment('Subtotal dalam rupiah');
            $table->unsignedBigInteger('tax_amount')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('paid_amount')->default(0);
            $table->unsignedBigInteger('remaining_amount');
            $table->string('status', 20)->default(\App\Enums\InvoiceStatus::PENDING->value);
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('billing_period_start');
            $table->date('billing_period_end');
            $table->timestamp('paid_at')->nullable();
            $table->string('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('invoice_number');
            $table->index('customer_id');
            $table->index('status');
            $table->index('due_date');
            $table->index('billing_period_start');
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->unsignedBigInteger('unit_price');
            $table->unsignedBigInteger('subtotal');
            $table->string('type', 30)->default('package')->comment('package, addon, tax, discount, prorate');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('internet_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price')->comment('Harga dalam rupiah');
            $table->unsignedBigInteger('setup_fee')->default(0);
            $table->unsignedBigInteger('download_speed_bps')->comment('Download speed in bps');
            $table->unsignedBigInteger('upload_speed_bps')->comment('Upload speed in bps');
            $table->unsignedBigInteger('fup_limit_bytes')->nullable()->comment('FUP limit in bytes, null = unlimited');
            $table->string('mikrotik_profile', 100)->nullable()->comment('Nama profile di Mikrotik');
            $table->string('mikrotik_parent_queue', 100)->nullable();
            $table->integer('priority')->default(8)->comment('1-8, 1 tertinggi');
            $table->integer('burst_limit_bps')->nullable();
            $table->integer('burst_threshold_bps')->nullable();
            $table->string('burst_time', 10)->nullable();
            $table->integer('limit_bytes_in')->nullable()->comment('Burst limit in bytes');
            $table->integer('limit_bytes_out')->nullable();
            $table->string('address_list', 50)->nullable();
            $table->string('ip_pool', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('is_active');
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('internet_packages')->cascadeOnDelete();
            $table->foreignId('router_id')->constrained('routers');
            $table->string('connection_type', 20)->default(\App\Enums\ConnectionType::PPPOE->value);
            $table->string('status', 20)->default(\App\Enums\CustomerStatus::ACTIVE->value);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('price_override')->nullable()->comment('Override harga paket');
            $table->date('billing_date')->comment('Tanggal billing setiap bulan (1-28)');
            $table->boolean('auto_renewal')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('customer_id');
            $table->index('package_id');
            $table->index('router_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('internet_packages');
    }
};

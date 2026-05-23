<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('pppoe_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->foreignId('router_id')->constrained('routers');
            $table->string('username', 100);
            $table->string('password');
            $table->string('profile', 100)->nullable();
            $table->string('service', 50)->default('pppoe');
            $table->string('local_address', 45)->nullable();
            $table->string('remote_address', 45)->nullable();
            $table->string('caller_id', 50)->nullable();
            $table->boolean('disabled')->default(false);
            $table->string('comment')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['router_id', 'username']);
            $table->index('customer_id');
            $table->index('disabled');
        });

        Schema::create('hotspot_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->foreignId('router_id')->constrained('routers');
            $table->string('username', 100);
            $table->string('password');
            $table->string('profile', 100)->nullable();
            $table->string('server', 50)->nullable();
            $table->string('mac_address', 17)->nullable();
            $table->string('uptime_limit', 10)->nullable();
            $table->bigInteger('bytes_in')->default(0);
            $table->bigInteger('bytes_out')->default(0);
            $table->boolean('disabled')->default(false);
            $table->string('comment')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['router_id', 'username']);
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotspot_accounts');
        Schema::dropIfExists('pppoe_accounts');
    }
};

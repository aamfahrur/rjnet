<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('traffic_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained('routers')->cascadeOnDelete();
            $table->string('interface_name', 100);
            $table->bigInteger('rx_bytes')->default(0);
            $table->bigInteger('tx_bytes')->default(0);
            $table->bigInteger('rx_packets')->default(0);
            $table->bigInteger('tx_packets')->default(0);
            $table->timestamp('recorded_at');

            $table->index(['router_id', 'interface_name']);
            $table->index('recorded_at');
        });

        Schema::create('router_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained('routers')->cascadeOnDelete();
            $table->float('cpu_load')->nullable()->comment('CPU load percentage');
            $table->float('memory_usage')->nullable()->comment('Memory usage percentage');
            $table->bigInteger('total_memory')->nullable();
            $table->bigInteger('free_memory')->nullable();
            $table->bigInteger('hdd_total')->nullable();
            $table->bigInteger('hdd_free')->nullable();
            $table->float('uptime_seconds')->nullable();
            $table->integer('active_connections')->nullable();
            $table->integer('pppoe_sessions')->nullable();
            $table->integer('hotspot_sessions')->nullable();
            $table->integer('dhcp_leases')->nullable();
            $table->json('interface_stats')->nullable();
            $table->timestamp('recorded_at');

            $table->index(['router_id', 'recorded_at']);
        });

        Schema::create('ping_results', function (Blueprint $table) {
            $table->id();
            $table->string('target_host', 100);
            $table->string('target_type', 30)->default('router')->comment('router, node, gateway, dns');
            $table->foreignId('router_id')->nullable()->constrained('routers')->cascadeOnDelete();
            $table->float('latency_ms')->nullable();
            $table->boolean('is_successful')->default(false);
            $table->integer('packet_loss')->default(0)->comment('Percentage 0-100');
            $table->timestamp('tested_at');

            $table->index('target_host');
            $table->index('tested_at');
        });

        Schema::create('online_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained('routers')->cascadeOnDelete();
            $table->string('session_type', 20)->comment('pppoe, hotspot, dhcp');
            $table->string('username', 100);
            $table->string('ip_address', 45)->nullable();
            $table->string('mac_address', 17)->nullable();
            $table->string('calling_id', 50)->nullable();
            $table->bigInteger('uptime_seconds')->nullable();
            $table->bigInteger('bytes_in')->default(0);
            $table->bigInteger('bytes_out')->default(0);
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('recorded_at');

            $table->index(['router_id', 'session_type']);
            $table->index('username');
            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_sessions');
        Schema::dropIfExists('ping_results');
        Schema::dropIfExists('router_metrics');
        Schema::dropIfExists('traffic_logs');
    }
};

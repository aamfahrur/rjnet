<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('network_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('type', 30)->comment('router, switch, olt, odp, onu, pop, server');
            $table->string('status', 20)->default('offline');
            $table->foreignId('router_id')->nullable()->constrained('routers')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('network_nodes')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('mac_address', 17)->nullable();
            $table->string('port', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('position')->nullable()->comment('{x, y} untuk React Flow canvas position');
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('parent_id');
            $table->index('router_id');
        });

        Schema::create('network_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_node_id')->constrained('network_nodes')->cascadeOnDelete();
            $table->foreignId('target_node_id')->constrained('network_nodes')->cascadeOnDelete();
            $table->string('type', 30)->default('backbone')->comment('backbone, distribution, access, wireless, fiber');
            $table->string('status', 20)->default('active');
            $table->string('media_type', 30)->nullable()->comment('fiber, wireless, copper');
            $table->unsignedBigInteger('bandwidth_bps')->nullable();
            $table->string('source_port', 20)->nullable();
            $table->string('target_port', 20)->nullable();
            $table->float('length_meters')->nullable();
            $table->integer('attenuation_db')->nullable();
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('source_node_id');
            $table->index('target_node_id');
            $table->index('type');
        });

        Schema::create('network_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('node_id')->nullable()->constrained('network_nodes')->cascadeOnDelete();
            $table->foreignId('link_id')->nullable()->constrained('network_links')->cascadeOnDelete();
            $table->foreignId('router_id')->nullable()->constrained('routers')->cascadeOnDelete();
            $table->string('event_type', 50)->comment('online, offline, high_cpu, high_traffic, error, etc');
            $table->string('severity', 20)->default('info');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('event_type');
            $table->index('severity');
            $table->index('is_resolved');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('network_events');
        Schema::dropIfExists('network_links');
        Schema::dropIfExists('network_nodes');
    }
};

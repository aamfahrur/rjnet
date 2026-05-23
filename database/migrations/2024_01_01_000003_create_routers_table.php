<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('router_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description')->nullable();
            $table->string('location', 200)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_group_id')->nullable()->constrained('router_groups')->nullOnDelete();
            $table->string('name', 100);
            $table->string('host', 100)->comment('IP Address atau hostname');
            $table->unsignedSmallInteger('api_port')->default(8728);
            $table->unsignedSmallInteger('api_ssl_port')->default(8729);
            $table->string('username', 100);
            $table->string('password')->nullable();
            $table->string('ssh_port')->default(22);
            $table->string('snmp_community')->nullable();
            $table->boolean('use_ssl')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status', 20)->default(\App\Enums\RouterStatus::OFFLINE->value);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('capabilities')->nullable()->comment('JSON: supported features');
            $table->string('router_os_version')->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('board_name')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('last_seen_at')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('host');
            $table->index('status');
            $table->index('router_group_id');
        });

        Schema::create('router_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained('routers')->cascadeOnDelete();
            $table->string('action', 50)->comment('connect, disconnect, error, command');
            $table->string('command')->nullable();
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->string('status', 20)->default('success');
            $table->text('error_message')->nullable();
            $table->float('execution_time_ms')->nullable();
            $table->timestamps();

            $table->index('router_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('router_logs');
        Schema::dropIfExists('routers');
        Schema::dropIfExists('router_groups');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 30)->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->comment('User yang membuat ticket (bisa admin)')->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->comment('Teknisi assigned')->constrained('users')->nullOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->string('category', 50)->default('general');
            $table->string('status', 20)->default(\App\Enums\TicketStatus::OPEN->value);
            $table->string('priority', 20)->default(\App\Enums\TicketPriority::MEDIUM->value);
            $table->timestamp('sla_deadline')->nullable();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('rating')->nullable()->comment('Customer rating 1-5');
            $table->text('resolution')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('ticket_number');
            $table->index('customer_id');
            $table->index('assigned_to');
            $table->index('status');
            $table->index('priority');
            $table->index('category');
        });

        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->text('message');
            $table->boolean('is_internal')->default(false)->comment('Internal note, tidak visible ke customer');
            $table->json('attachments')->nullable()->comment('JSON array of file paths');
            $table->timestamps();

            $table->index('ticket_id');
            $table->index('user_id');
        });

        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('ticket_reply_id')->nullable()->constrained('ticket_replies')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type', 100)->nullable();
            $table->bigInteger('size')->nullable();
            $table->timestamps();

            $table->index('ticket_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('tickets');
    }
};

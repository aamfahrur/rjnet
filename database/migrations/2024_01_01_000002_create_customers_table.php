<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code', 20)->unique()->comment('Kode pelanggan unik, e.g. CUS-00001');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('full_name', 200);
            $table->string('email', 100)->nullable();
            $table->string('phone', 20);
            $table->string('phone_alt', 20)->nullable();
            $table->string('id_number', 50)->nullable()->comment('NIK/KTP');
            $table->string('id_card_image')->nullable()->comment('Path foto KTP');
            $table->string('house_photo')->nullable()->comment('Path foto rumah');
            $table->string('status', 20)->default(\App\Enums\CustomerStatus::ACTIVE->value);
            $table->date('registration_date');
            $table->date('termination_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('customer_code');
            $table->index('status');
            $table->index('phone');
        });

        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('label', 50)->default('Rumah')->comment('Rumah, Kantor, dll');
            $table->text('address');
            $table->string('village', 100)->nullable()->comment('Desa/Kelurahan');
            $table->string('district', 100)->nullable()->comment('Kecamatan');
            $table->string('city', 100)->nullable()->comment('Kabupaten/Kota');
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index('customer_id');
        });

        Schema::create('customer_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('type', 50)->comment('ktp, kk, selfie, contract, other');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type', 100)->nullable();
            $table->bigInteger('size')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['customer_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_documents');
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('customers');
    }
};

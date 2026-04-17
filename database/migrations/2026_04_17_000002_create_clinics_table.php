<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();
            $table->string('name', 255);
            $table->string('short_name', 120)->nullable();
            $table->string('slug', 170)->unique();
            $table->string('clinic_code', 80)->nullable()->unique();

            $table->string('clinic_type', 100)->nullable();

            $table->string('email', 255)->nullable();
            $table->string('phone_number', 32)->nullable();
            $table->string('alternative_phone_number', 32)->nullable();
            $table->string('whatsapp_number', 32)->nullable();
            $table->string('website', 255)->nullable();

            $table->string('logo', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->json('gallery')->nullable();

            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();

            $table->string('address_line_1', 255)->nullable();
            $table->string('address_line_2', 255)->nullable();
            $table->string('landmark', 255)->nullable();
            $table->string('area', 150)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('country', 120)->default('India');
            $table->string('pincode', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('map_url', 500)->nullable();

            $table->json('timings')->nullable();
            $table->json('facilities')->nullable();
            $table->json('social_links')->nullable();

            $table->boolean('online_consultation_available')->default(false);
            $table->boolean('appointment_booking_available')->default(true);

            $table->string('status', 20)->default('active');
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->ipAddress('created_at_ip')->nullable();

            $table->softDeletes();
            $table->json('metadata')->nullable();

            $table->index('name');
            $table->index('clinic_type');
            $table->index('city');
            $table->index('state');
            $table->index('status');
            $table->index('sort_order');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};

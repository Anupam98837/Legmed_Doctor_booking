<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();
            $table->string('doctor_code', 80)->nullable()->unique();
            $table->string('slug', 170)->unique();

            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            $table->foreignId('designation_id')->nullable()->constrained('designations')->nullOnDelete();
            $table->foreignId('primary_hospital_id')->nullable()->constrained('hospitals')->nullOnDelete();
            $table->foreignId('primary_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('primary_specialization_id')->nullable()->constrained('specializations')->nullOnDelete();
            $table->foreignId('registration_council_id')->nullable()->constrained('registration_councils')->nullOnDelete();

            $table->string('qualification_summary', 255)->nullable();
            $table->unsignedInteger('years_of_experience')->default(0);
            $table->string('medical_registration_number', 120)->nullable()->unique();
            $table->year('registration_year')->nullable();

            $table->string('short_bio', 500)->nullable();
            $table->longText('about_doctor')->nullable();

            $table->string('cover_photo', 255)->nullable();
            $table->json('gallery')->nullable();

            $table->decimal('consultation_fee', 10, 2)->nullable();
            $table->decimal('followup_fee', 10, 2)->nullable();
            $table->decimal('video_consultation_fee', 10, 2)->nullable();
            $table->decimal('home_visit_fee', 10, 2)->nullable();

            $table->boolean('online_consultation_available')->default(false);
            $table->boolean('in_person_consultation_available')->default(true);
            $table->boolean('home_visit_available')->default(false);
            $table->boolean('appointment_booking_available')->default(true);

            $table->unsignedInteger('total_patients_treated')->default(0);
            $table->unsignedInteger('total_surgeries')->default(0);
            $table->unsignedInteger('total_consultations')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('review_count')->default(0);

            $table->boolean('featured_status')->default(false);
            $table->string('verification_status', 20)->default('pending');
            $table->string('profile_visibility', 20)->default('public');
            $table->string('status', 20)->default('active');
            $table->integer('sort_order')->default(0);
            $table->unsignedTinyInteger('profile_completion_percentage')->default(0);

            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 500)->nullable();

            $table->timestamps();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->ipAddress('created_at_ip')->nullable();
            $table->ipAddress('updated_at_ip')->nullable();

            $table->softDeletes();
            $table->json('metadata')->nullable();

            $table->index('doctor_code');
            $table->index('designation_id');
            $table->index('primary_hospital_id');
            $table->index('primary_department_id');
            $table->index('primary_specialization_id');
            $table->index('registration_council_id');
            $table->index('status');
            $table->index('verification_status');
            $table->index('featured_status');
            $table->index('sort_order');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};

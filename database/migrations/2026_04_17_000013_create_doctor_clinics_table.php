<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_clinics', function (Blueprint $table) {
            $table->id();

            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();

            $table->boolean('is_primary')->default(false);

            $table->decimal('consultation_fee', 10, 2)->nullable();
            $table->decimal('followup_fee', 10, 2)->nullable();
            $table->decimal('video_consultation_fee', 10, 2)->nullable();

            $table->boolean('online_consultation_available')->default(false);
            $table->boolean('in_person_consultation_available')->default(true);
            $table->boolean('appointment_booking_available')->default(true);

            $table->string('room_no', 60)->nullable();
            $table->string('visit_note', 255)->nullable();
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->unique(['doctor_id', 'clinic_id']);
            $table->index(['doctor_id', 'is_primary']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_clinics');
    }
};

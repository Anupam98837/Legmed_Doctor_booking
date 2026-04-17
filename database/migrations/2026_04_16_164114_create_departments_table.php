<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();

            // Identifiers
            $table->uuid('uuid')->unique();
            $table->string('name', 150);
            $table->string('short_form', 20)->nullable();
            $table->string('slug', 170)->unique();

            // Department details
            $table->string('image', 255)->nullable();
            $table->text('description')->nullable();

            // Status / ordering
            $table->string('status', 20)->default('active');
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->ipAddress('created_at_ip')->nullable();

            // Soft delete + metadata
            $table->softDeletes();
            $table->json('metadata')->nullable();

            // Extra indexes
            $table->index('name');
            $table->index('short_form');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
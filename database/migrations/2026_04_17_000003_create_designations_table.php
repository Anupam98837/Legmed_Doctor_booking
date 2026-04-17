<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();
            $table->string('name', 150)->unique();
            $table->string('short_form', 30)->nullable();
            $table->string('slug', 170)->unique();

            $table->text('description')->nullable();

            $table->string('status', 20)->default('active');
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->ipAddress('created_at_ip')->nullable();

            $table->softDeletes();
            $table->json('metadata')->nullable();

            $table->index('name');
            $table->index('short_form');
            $table->index('status');
            $table->index('sort_order');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};

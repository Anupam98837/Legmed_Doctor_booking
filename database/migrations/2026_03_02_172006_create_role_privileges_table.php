<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_privileges', function (Blueprint $table) {

            $table->id();

            // UUID
            $table->uuid('uuid')->unique();

            /**
             * ✅ Role name (VARCHAR) — NOT restricted / NOT enum.
             * Examples: admin, author, faculty, student, etc
             */
            $table->string('role', 80);

            /**
             * ✅ TREE JSON STRUCTURE (same as user_privileges.privileges)
             * [
             *   {
             *     "id": 1,                // header (dashboard_menu.is_dropdown_head = 1)
             *     "children": [
             *       {
             *         "id": 2,            // page (dashboard_menu.id)
             *         "privileges": [
             *           { "id": 1, "action": "add" },
             *           { "id": 2, "action": "edit" }
             *         ]
             *       }
             *     ]
             *   }
             * ]
             */
            $table->json('privileges')->nullable();

            // Audit
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->string('created_at_ip', 45)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // ✅ One row per role
            $table->unique('role', 'unique_role_privileges_role');

            // Helpful indexes
            $table->index(['role', 'deleted_at'], 'idx_role_privileges_role_deleted');

            // FK: assigned_by -> users.id
            $table->foreign('assigned_by')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_privileges');
    }
};
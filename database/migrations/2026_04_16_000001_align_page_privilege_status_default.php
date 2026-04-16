<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Keep privilege status values consistent with controller logic.
        DB::table('page_privilege')
            ->where('status', 'Active')
            ->update(['status' => 'active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('page_privilege')
            ->where('status', 'active')
            ->update(['status' => 'Active']);
    }
};

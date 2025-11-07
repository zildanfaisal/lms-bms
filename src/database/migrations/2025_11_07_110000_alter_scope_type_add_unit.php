<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Assumes MySQL. Extend enum to include 'unit' for both tables
        DB::statement("ALTER TABLE learning_plan_proposals MODIFY COLUMN scope_type ENUM('direktorat','divisi','unit') NOT NULL");
        DB::statement("ALTER TABLE learning_recommendations MODIFY COLUMN scope_type ENUM('direktorat','divisi','unit') NOT NULL");
    }

    public function down(): void
    {
        // Revert to previous two values (may fail if existing rows have 'unit')
        DB::statement("ALTER TABLE learning_plan_proposals MODIFY COLUMN scope_type ENUM('direktorat','divisi') NOT NULL");
        DB::statement("ALTER TABLE learning_recommendations MODIFY COLUMN scope_type ENUM('direktorat','divisi') NOT NULL");
    }
};

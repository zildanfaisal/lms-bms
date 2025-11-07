<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('learning_plan_proposals', function (Blueprint $table) {
            $table->boolean('only_subordinate_jabatans')->default(false)->after('target_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('learning_plan_proposals', function (Blueprint $table) {
            $table->dropColumn('only_subordinate_jabatans');
        });
    }
};

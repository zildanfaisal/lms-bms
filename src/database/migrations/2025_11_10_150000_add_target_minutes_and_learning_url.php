<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add target_minutes to proposal recommendations
        if (Schema::hasTable('learning_plan_recommendations') && !Schema::hasColumn('learning_plan_recommendations', 'target_minutes')) {
            Schema::table('learning_plan_recommendations', function (Blueprint $table) {
                $table->unsignedInteger('target_minutes')->nullable()->after('url');
            });
        }

        // Add target_minutes to applied recommendations
        if (Schema::hasTable('learning_recommendations') && !Schema::hasColumn('learning_recommendations', 'target_minutes')) {
            Schema::table('learning_recommendations', function (Blueprint $table) {
                $table->unsignedInteger('target_minutes')->nullable()->after('url');
            });
        }

        // Add learning_url to logs, separate from evidence_url
        if (Schema::hasTable('learning_logs') && !Schema::hasColumn('learning_logs', 'learning_url')) {
            Schema::table('learning_logs', function (Blueprint $table) {
                $table->string('learning_url')->nullable()->after('duration_minutes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('learning_plan_recommendations') && Schema::hasColumn('learning_plan_recommendations', 'target_minutes')) {
            Schema::table('learning_plan_recommendations', function (Blueprint $table) {
                $table->dropColumn('target_minutes');
            });
        }
        if (Schema::hasTable('learning_recommendations') && Schema::hasColumn('learning_recommendations', 'target_minutes')) {
            Schema::table('learning_recommendations', function (Blueprint $table) {
                $table->dropColumn('target_minutes');
            });
        }
        if (Schema::hasTable('learning_logs') && Schema::hasColumn('learning_logs', 'learning_url')) {
            Schema::table('learning_logs', function (Blueprint $table) {
                $table->dropColumn('learning_url');
            });
        }
    }
};

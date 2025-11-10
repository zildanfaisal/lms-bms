<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('learning_logs') && !Schema::hasColumn('learning_logs','recommendation_id')) {
            Schema::table('learning_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('recommendation_id')->nullable()->after('platform_id');
                $table->index('recommendation_id','learning_logs_recommendation_id_index');
                if (Schema::hasTable('learning_recommendations')) {
                    $table->foreign('recommendation_id')
                        ->references('id')->on('learning_recommendations')
                        ->onDelete('set null');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('learning_logs') && Schema::hasColumn('learning_logs','recommendation_id')) {
            Schema::table('learning_logs', function (Blueprint $table) {
                try { $table->dropForeign(['recommendation_id']); } catch (\Throwable $e) {}
                try { $table->dropIndex('learning_logs_recommendation_id_index'); } catch (\Throwable $e) {}
                $table->dropColumn('recommendation_id');
            });
        }
    }
};

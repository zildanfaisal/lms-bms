<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('learning_recommendations', function (Blueprint $table) {
            if (!Schema::hasColumn('learning_recommendations', 'jabatan_id')) {
                $table->foreignId('jabatan_id')->nullable()->after('scope_id')->constrained('jabatan')->nullOnDelete();
            }
        });
        try {
            DB::statement('CREATE INDEX lr_period_scope_jabatan_idx ON learning_recommendations (period_id, scope_type, scope_id, jabatan_id)');
        } catch (\Throwable $e) {
            // ignore if index already exists
        }
    }

    public function down(): void
    {
        try {
            DB::statement('DROP INDEX lr_period_scope_jabatan_idx ON learning_recommendations');
        } catch (\Throwable $e) {
            // ignore if not exists
        }
        Schema::table('learning_recommendations', function (Blueprint $table) {
            if (Schema::hasColumn('learning_recommendations', 'jabatan_id')) {
                $table->dropConstrainedForeignId('jabatan_id');
            }
        });
    }
};

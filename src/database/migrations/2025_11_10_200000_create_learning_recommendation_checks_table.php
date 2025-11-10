<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Feature manual checklist dihapus. Pastikan tidak ada tabel tersisa.
        if (Schema::hasTable('learning_recommendation_checks')) {
            Schema::drop('learning_recommendation_checks');
        }
        // Tidak membuat tabel baru.
    }

    public function down(): void
    {
        // No-op: tidak mengembalikan tabel manual checklist.
    }
};

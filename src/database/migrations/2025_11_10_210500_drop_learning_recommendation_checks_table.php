<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Safeguard: past production mungkin sudah pernah membuat tabel lama.
        if (Schema::hasTable('learning_recommendation_checks')) {
            Schema::drop('learning_recommendation_checks');
        }
    }

    public function down(): void
    {
        // Tidak mengembalikan tabel yang sudah deprecated.
    }
};

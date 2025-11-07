<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('learning_recommendation_events')) {
            Schema::create('learning_recommendation_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('recommendation_id');
                $table->unsignedBigInteger('user_id');
                $table->string('event_type', 32); // clicked|logged (future)
                $table->timestamps();
                // Use short, explicit index name to avoid MySQL 64-char limit
                $table->index(['recommendation_id','event_type'], 'lre_rec_evt_idx');
            });
        } else {
            // Table exists (possibly created before index was added). Ensure the short index exists.
            try {
                Schema::table('learning_recommendation_events', function (Blueprint $table) {
                    $table->index(['recommendation_id','event_type'], 'lre_rec_evt_idx');
                });
            } catch (\Throwable $e) {
                // Ignore if index already exists or cannot be created due to duplicate
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_recommendation_events');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('learning_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('learning_periods')->cascadeOnDelete();
            $table->enum('scope_type', ['direktorat', 'divisi']);
            $table->unsignedBigInteger('scope_id');
            $table->string('title');
            $table->string('url')->nullable();
            $table->foreignId('platform_id')->nullable()->constrained('learning_platforms')->nullOnDelete();
            $table->foreignId('approved_proposal_id')->nullable()->constrained('learning_plan_proposals')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['period_id', 'scope_type', 'scope_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_recommendations');
    }
};

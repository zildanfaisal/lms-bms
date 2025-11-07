<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('learning_plan_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('learning_plan_proposals')->cascadeOnDelete();
            $table->string('title');
            $table->string('url')->nullable();
            $table->foreignId('platform_id')->nullable()->constrained('learning_platforms')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_plan_recommendations');
    }
};

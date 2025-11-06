<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->foreignId('platform_id')->constrained('learning_platforms')->restrictOnDelete();
            $table->foreignId('period_id')->constrained('learning_periods')->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('started_at');
            $table->date('ended_at');
            $table->unsignedInteger('duration_minutes');
            $table->string('evidence_url')->nullable();
            $table->string('evidence_path')->nullable();
            $table->enum('status', ['draft','pending','approved','rejected'])->default('draft');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reject_reason')->nullable();
            $table->timestamps();

            $table->index(['karyawan_id','period_id']);
            $table->index(['status','period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_logs');
    }
};

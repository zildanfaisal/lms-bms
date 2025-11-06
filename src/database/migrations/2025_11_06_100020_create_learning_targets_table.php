<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('learning_periods')->cascadeOnDelete();
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawan')->cascadeOnDelete();
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatan')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('unit')->cascadeOnDelete();
            $table->foreignId('divisi_id')->nullable()->constrained('divisi')->cascadeOnDelete();
            $table->foreignId('direktorat_id')->nullable()->constrained('direktorat')->cascadeOnDelete();
            $table->unsignedInteger('target_minutes');
            $table->timestamps();

            $table->index(['period_id','karyawan_id']);
            $table->index(['period_id','jabatan_id']);
            $table->index(['period_id','unit_id']);
            $table->index(['period_id','divisi_id']);
            $table->index(['period_id','direktorat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_targets');
    }
};

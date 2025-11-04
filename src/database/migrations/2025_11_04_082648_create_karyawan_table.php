<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('direktorat_id')->constrained('direktorat');
            $table->foreignId('divisi_id')->constrained('divisi');
            $table->foreignId('unit_id')->nullable()->constrained('unit');
            $table->foreignId('jabatan_id')->constrained('jabatan');
            $table->foreignId('posisi_id')->nullable()->constrained('posisi');
            $table->string('nama');
            $table->string('status_karyawan');
            $table->string('no_wa')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};

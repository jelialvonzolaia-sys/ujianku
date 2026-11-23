<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas', 100);
            $table->string('tingkat', 10)->default('X');
            $table->foreignId('jurusan_id')->constrained('jurusans')->restrictOnDelete();
            $table->foreignId('wali_kelas_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->restrictOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['nama_kelas', 'tahun_ajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};

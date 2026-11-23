<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->restrictOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('kode_soal', 50)->unique();
            $table->string('nama_bank_soal');
            $table->string('tingkat_kelas', 20)->default('X');
            $table->string('materi')->nullable();
            $table->enum('mode_jawaban', ['ABC', 'ABCD', 'ABCDE'])->default('ABCDE');
            $table->boolean('acak_soal')->default(true);
            $table->boolean('acak_jawaban')->default(false);
            $table->enum('skala_nilai', ['skala_100', 'skor_total'])->default('skala_100');
            $table->decimal('skor_tidak_menjawab', 8, 2)->default(0);
            $table->boolean('continuous_mode')->default(false);
            $table->boolean('harus_menjawab')->default(false);
            $table->boolean('redirect_after_save')->default(true);
            $table->longText('petunjuk_pengerjaan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_soals');
    }
};

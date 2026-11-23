<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_soal_id')->constrained('bank_soals')->cascadeOnDelete();
            $table->unsignedInteger('nomor_soal');
            $table->enum('jenis_soal', [
                'pilihan_ganda',
                'pg_kompleks',
                'benar_salah',
                'menjodohkan',
                'isian_singkat',
                'essay',
            ])->default('pilihan_ganda');
            $table->longText('pertanyaan');
            $table->decimal('bobot', 8, 2)->default(1);
            $table->text('kunci_jawaban')->nullable();
            $table->longText('pembahasan')->nullable();
            $table->timestamps();
        });

        Schema::create('soal_opsis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soal_id')->constrained('soals')->cascadeOnDelete();
            $table->string('label', 5);
            $table->longText('teks_opsi');
            $table->decimal('skor', 8, 2)->default(0);
            $table->boolean('is_benar')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal_opsis');
        Schema::dropIfExists('soals');
    }
};

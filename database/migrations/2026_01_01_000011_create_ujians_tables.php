<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('bank_soal_id')->constrained('bank_soals')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->restrictOnDelete();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->nullOnDelete();
            $table->string('kode_ujian', 50)->unique();
            $table->string('nama_ujian');
            $table->string('token', 12)->nullable();
            $table->boolean('wajib_token')->default(true);
            $table->unsignedInteger('durasi_menit')->default(60);
            $table->timestamp('waktu_mulai')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');
            $table->boolean('tampilkan_nilai')->default(true);
            $table->text('instruksi_tambahan')->nullable();
            $table->timestamps();
        });

        Schema::create('ujian_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujians')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['ujian_id', 'kelas_id']);
        });

        Schema::create('ujian_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujians')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->enum('status', ['belum_mulai', 'sedang_mengerjakan', 'selesai'])->default('belum_mulai');
            $table->json('urutan_soal')->nullable();
            $table->timestamp('mulai_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->unsignedInteger('sisa_detik')->nullable();
            $table->decimal('nilai_pg', 8, 2)->default(0);
            $table->decimal('nilai_essay', 8, 2)->default(0);
            $table->decimal('nilai_akhir', 8, 2)->default(0);
            $table->enum('status_koreksi', ['otomatis', 'menunggu_essay', 'selesai'])->default('otomatis');
            $table->timestamps();

            $table->unique(['ujian_id', 'siswa_id']);
        });

        Schema::create('ujian_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_peserta_id')->constrained('ujian_pesertas')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('soals')->cascadeOnDelete();
            $table->foreignId('soal_opsi_id')->nullable()->constrained('soal_opsis')->nullOnDelete();
            $table->json('jawaban_json')->nullable();
            $table->longText('jawaban_teks')->nullable();
            $table->boolean('is_ragu')->default(false);
            $table->decimal('skor_diperoleh', 8, 2)->default(0);
            $table->boolean('is_dikoreksi')->default(false);
            $table->text('catatan_guru')->nullable();
            $table->timestamps();

            $table->unique(['ujian_peserta_id', 'soal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_jawabans');
        Schema::dropIfExists('ujian_pesertas');
        Schema::dropIfExists('ujian_kelas');
        Schema::dropIfExists('ujians');
    }
};

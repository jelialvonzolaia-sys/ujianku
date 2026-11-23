<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusans')->nullOnDelete();
            $table->string('nis', 30)->unique();
            $table->string('nisn', 30)->unique();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->string('agama', 50)->nullable();
            $table->string('no_hp', 25)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};

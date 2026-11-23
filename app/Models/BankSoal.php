<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankSoal extends Model
{
    protected $fillable = [
        'guru_id',
        'mata_pelajaran_id',
        'kelas_id',
        'kode_soal',
        'nama_bank_soal',
        'tingkat_kelas',
        'materi',
        'mode_jawaban',
        'acak_soal',
        'acak_jawaban',
        'skala_nilai',
        'skor_tidak_menjawab',
        'continuous_mode',
        'harus_menjawab',
        'redirect_after_save',
        'petunjuk_pengerjaan',
    ];

    protected function casts(): array
    {
        return [
            'acak_soal' => 'boolean',
            'acak_jawaban' => 'boolean',
            'continuous_mode' => 'boolean',
            'harus_menjawab' => 'boolean',
            'redirect_after_save' => 'boolean',
            'skor_tidak_menjawab' => 'decimal:2',
        ];
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class)->orderBy('nomor_soal');
    }

    public function ujians(): HasMany
    {
        return $this->hasMany(Ujian::class);
    }
}

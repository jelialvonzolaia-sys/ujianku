<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Soal extends Model
{
    protected $fillable = [
        'bank_soal_id',
        'nomor_soal',
        'jenis_soal',
        'pertanyaan',
        'bobot',
        'kunci_jawaban',
        'pembahasan',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'decimal:2',
        ];
    }

    public function bankSoal(): BelongsTo
    {
        return $this->belongsTo(BankSoal::class);
    }

    public function opsis(): HasMany
    {
        return $this->hasMany(SoalOpsi::class)->orderBy('label');
    }

    public function jawabans(): HasMany
    {
        return $this->hasMany(UjianJawaban::class);
    }

    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis_soal) {
            'pilihan_ganda' => 'Pilihan Ganda',
            'pg_kompleks' => 'Pilihan Ganda Kompleks',
            'benar_salah' => 'Benar / Salah',
            'menjodohkan' => 'Menjodohkan',
            'isian_singkat' => 'Isian Singkat',
            'essay' => 'Uraian / Essay',
            default => 'Pilihan Ganda',
        };
    }

    public function getMaxSkorAttribute(): float
    {
        if (in_array($this->jenis_soal, ['pilihan_ganda', 'benar_salah'], true)) {
            $maxOpsi = (float) $this->opsis->max('skor');
            return $maxOpsi > 0 ? $maxOpsi : (float) $this->bobot;
        }
        if ($this->jenis_soal === 'pg_kompleks') {
            $sumPos = (float) $this->opsis->where('skor', '>', 0)->sum('skor');
            return $sumPos > 0 ? $sumPos : (float) $this->bobot;
        }

        return (float) $this->bobot;
    }
}

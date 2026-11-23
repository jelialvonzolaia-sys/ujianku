<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UjianJawaban extends Model
{
    protected $fillable = [
        'ujian_peserta_id',
        'soal_id',
        'soal_opsi_id',
        'jawaban_json',
        'jawaban_teks',
        'is_ragu',
        'skor_diperoleh',
        'is_dikoreksi',
        'catatan_guru',
    ];

    protected function casts(): array
    {
        return [
            'jawaban_json' => 'array',
            'is_ragu' => 'boolean',
            'is_dikoreksi' => 'boolean',
            'skor_diperoleh' => 'decimal:2',
        ];
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(UjianPeserta::class, 'ujian_peserta_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class);
    }

    public function opsi(): BelongsTo
    {
        return $this->belongsTo(SoalOpsi::class, 'soal_opsi_id');
    }
}

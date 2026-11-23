<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UjianPeserta extends Model
{
    protected $fillable = [
        'ujian_id',
        'siswa_id',
        'status',
        'urutan_soal',
        'mulai_at',
        'selesai_at',
        'sisa_detik',
        'nilai_pg',
        'nilai_essay',
        'nilai_akhir',
        'status_koreksi',
    ];

    protected function casts(): array
    {
        return [
            'urutan_soal' => 'array',
            'mulai_at' => 'datetime',
            'selesai_at' => 'datetime',
            'nilai_pg' => 'decimal:2',
            'nilai_essay' => 'decimal:2',
            'nilai_akhir' => 'decimal:2',
        ];
    }

    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jawabans(): HasMany
    {
        return $this->hasMany(UjianJawaban::class);
    }
}

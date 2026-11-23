<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoalOpsi extends Model
{
    protected $fillable = [
        'soal_id',
        'label',
        'teks_opsi',
        'skor',
        'is_benar',
    ];

    protected function casts(): array
    {
        return [
            'skor' => 'decimal:2',
            'is_benar' => 'boolean',
        ];
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class);
    }
}

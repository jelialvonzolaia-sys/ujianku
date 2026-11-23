<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    protected $fillable = [
        'tahun',
        'semester',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    public function ujians(): HasMany
    {
        return $this->hasMany(Ujian::class);
    }

    public function getLabelAttribute(): string
    {
        return "{$this->tahun} ({$this->semester})";
    }
}

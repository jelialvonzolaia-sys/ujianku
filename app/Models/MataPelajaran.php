<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataPelajaran extends Model
{
    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function kelasMataPelajarans(): HasMany
    {
        return $this->hasMany(KelasMataPelajaran::class);
    }

    public function bankSoals(): HasMany
    {
        return $this->hasMany(BankSoal::class);
    }

    public function ujians(): HasMany
    {
        return $this->hasMany(Ujian::class);
    }
}

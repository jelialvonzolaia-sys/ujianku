<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'no_hp',
        'tingkat_kelas',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tingkat_kelas' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelasWali(): HasMany
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'aktor_nama',
        'aksi',
        'deskripsi',
        'tipe',
        'ip_address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(?User $user, string $aksi, ?string $deskripsi = null, string $tipe = 'info', ?string $ip = null): void
    {
        static::create([
            'user_id' => $user?->id,
            'aktor_nama' => $user?->name ?? 'Sistem',
            'aksi' => $aksi,
            'deskripsi' => $deskripsi,
            'tipe' => $tipe,
            'ip_address' => $ip,
        ]);
    }
}

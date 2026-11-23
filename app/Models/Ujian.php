<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ujian extends Model
{
    protected $fillable = [
        'guru_id',
        'bank_soal_id',
        'mata_pelajaran_id',
        'tahun_ajaran_id',
        'kode_ujian',
        'nama_ujian',
        'token',
        'wajib_token',
        'durasi_menit',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'tampilkan_nilai',
        'instruksi_tambahan',
    ];

    protected function casts(): array
    {
        return [
            'wajib_token' => 'boolean',
            'tampilkan_nilai' => 'boolean',
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function bankSoal(): BelongsTo
    {
        return $this->belongsTo(BankSoal::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'ujian_kelas')->withTimestamps();
    }

    public function pesertas(): HasMany
    {
        return $this->hasMany(UjianPeserta::class);
    }

    public function isCurrentlyOpen(): bool
    {
        if ($this->status !== 'aktif') {
            return false;
        }
        $now = now();
        if ($this->waktu_mulai && $now->lt($this->waktu_mulai)) {
            return false;
        }
        if ($this->waktu_selesai && $now->gt($this->waktu_selesai)) {
            return false;
        }

        return true;
    }
}

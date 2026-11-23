@extends('layouts.siswa')

@section('title', 'Ujian Tersedia | Ujianku')
@section('page-title', 'Daftar Ujian Tersedia')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-primary">Ujian Tersedia Untuk Kelas {{ $siswa?->kelas?->nama_kelas ?? '-' }}</h2>
        <p class="text-sm text-on-surface-variant mt-1">Pilih ujian yang sedang berlangsung untuk memasukkan token dan mulai mengerjakan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($ujians as $ujian)
            @php
                $peserta = $pesertaMap->get($ujian->id);
                $isOpen = $ujian->isCurrentlyOpen();
                $isDone = $peserta && $peserta->status === 'selesai';
                $isInProgress = $peserta && $peserta->status === 'sedang_mengerjakan';
            @endphp
            <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/40 p-6 flex flex-col justify-between gap-4">
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $isOpen ? 'bg-green-100 text-green-700' : 'bg-surface-container text-on-surface-variant' }}">
                            {{ $isDone ? 'Selesai' : ($isInProgress ? 'Sedang Dikerjakan' : ($isOpen ? 'Tersedia / Aktif' : 'Terjadwal')) }}
                        </span>
                        <span class="font-mono text-xs text-text-muted">{{ $ujian->kode_ujian }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-primary">{{ $ujian->nama_ujian }}</h3>
                    <p class="text-sm text-secondary font-medium mb-3">{{ $ujian->mataPelajaran?->nama_mapel }} • {{ $ujian->guru?->nama_lengkap }}</p>
                    <div class="flex flex-wrap gap-4 text-xs text-text-muted">
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">timer</span> {{ $ujian->durasi_menit }} Menit</span>
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">quiz</span> {{ $ujian->bankSoal?->soals()->count() ?? 0 }} Soal</span>
                        @if($ujian->wajib_token)
                            <span class="flex items-center gap-1 text-tertiary font-semibold"><span class="material-symbols-outlined text-[16px]">key</span> Memerlukan Token</span>
                        @endif
                    </div>
                </div>

                @if($isDone)
                    <a href="{{ route('siswa.ujian.result', $ujian) }}" class="w-full py-2.5 bg-secondary-fixed text-secondary text-center rounded-lg text-sm font-semibold">Lihat Hasil</a>
                @elseif($isInProgress)
                    <a href="{{ route('siswa.ujian.run', $ujian) }}" class="w-full py-2.5 bg-tertiary text-white text-center rounded-lg text-sm font-semibold">Lanjutkan Ujian</a>
                @elseif($isOpen)
                    <a href="{{ route('siswa.ujian.confirm', $ujian) }}" class="w-full py-2.5 bg-primary text-white text-center rounded-lg text-sm font-semibold hover:bg-primary-container">Mulai Ujian</a>
                @else
                    <button disabled class="w-full py-2.5 bg-surface-container text-text-muted rounded-lg text-sm font-semibold cursor-not-allowed">Belum Dimulai</button>
                @endif
            </div>
        @empty
            <div class="md:col-span-2 bg-surface-white p-10 rounded-xl border border-outline-variant/30 text-center text-text-muted">
                Tidak ada ujian aktif untuk kelas Anda saat ini.
            </div>
        @endforelse
    </div>
</div>
@endsection

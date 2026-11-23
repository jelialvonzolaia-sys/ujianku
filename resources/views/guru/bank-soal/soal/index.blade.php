@extends('layouts.guru')

@section('title', 'List Soal | ' . $bankSoal->nama_bank_soal)

@section('breadcrumb')
    <a href="{{ route('guru.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('guru.bank-soal.index') }}" class="hover:text-primary">Bank Soal</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">List Soal</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Bank Soal Info Card -->
    <div class="bg-surface-white rounded-xl custom-shadow p-6 border border-outline-variant/20">
        <div class="flex flex-col md:flex-row justify-between items-start gap-4">
            <div>
                <h2 class="text-2xl font-bold text-primary mb-1">{{ $bankSoal->nama_bank_soal }}</h2>
                <div class="flex flex-wrap items-center gap-3 text-sm text-text-muted">
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">book</span>{{ $bankSoal->mataPelajaran?->nama_mapel }}</span>
                    <span>•</span>
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">school</span>Kelas {{ $bankSoal->tingkat_kelas }} {{ $bankSoal->kelas ? '(' . $bankSoal->kelas->nama_kelas . ')' : '' }}</span>
                    <span>•</span>
                    <span class="font-mono text-xs bg-surface-container-low px-2 py-0.5 rounded">{{ $bankSoal->kode_soal }}</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('guru.bank-soal.soal.create', $bankSoal) }}" class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    Tambah Soal
                </a>
                <a href="{{ route('guru.bank-soal.soal.import', $bankSoal) }}" class="px-5 py-2.5 border-2 border-secondary text-secondary rounded-lg text-sm font-semibold hover:bg-secondary/5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">upload</span>
                    Import Soal
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 border-t border-outline-variant/30 pt-5">
            <div class="flex items-center gap-3 p-4 bg-surface-container-low rounded-lg">
                <div class="w-10 h-10 rounded-full bg-secondary-container/20 flex items-center justify-center text-secondary"><span class="material-symbols-outlined">description</span></div>
                <div><p class="text-xs text-text-muted uppercase">Total Soal</p><p class="text-lg font-bold">{{ $totalSoal }}</p></div>
            </div>
            <div class="flex items-center gap-3 p-4 bg-surface-container-low rounded-lg">
                <div class="w-10 h-10 rounded-full bg-primary-container/20 flex items-center justify-center text-primary"><span class="material-symbols-outlined">checklist</span></div>
                <div><p class="text-xs text-text-muted uppercase">Pilihan Ganda</p><p class="text-lg font-bold">{{ $totalPg }}</p></div>
            </div>
            <div class="flex items-center gap-3 p-4 bg-surface-container-low rounded-lg">
                <div class="w-10 h-10 rounded-full bg-success-green/10 flex items-center justify-center text-success-green"><span class="material-symbols-outlined">military_tech</span></div>
                <div><p class="text-xs text-text-muted uppercase">Maksimum Skor</p><p class="text-lg font-bold">{{ $maksimumSkor }}</p></div>
            </div>
        </div>
    </div>

    @if($soals->isEmpty())
        <div class="bg-surface-white rounded-xl p-12 text-center soft-shadow border border-outline-variant/20 space-y-4">
            <div class="w-16 h-16 bg-primary-fixed text-primary rounded-full flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-3xl">quiz</span>
            </div>
            <h3 class="text-xl font-bold text-primary">Bank Soal Masih Kosong</h3>
            <p class="text-sm text-text-muted max-w-md mx-auto">Klik tombol <strong>Tambah Soal</strong> untuk membuat pertanyaan secara manual atau gunakan <strong>Import Soal</strong> untuk mengunggah dari file Excel.</p>
            <div class="flex justify-center gap-3 pt-2">
                <a href="{{ route('guru.bank-soal.soal.create', $bankSoal) }}" class="px-6 py-2.5 bg-primary text-white rounded-lg text-sm font-semibold">Tambah Soal Pertama</a>
                <a href="{{ route('guru.bank-soal.soal.import', $bankSoal) }}" class="px-6 py-2.5 border border-secondary text-secondary rounded-lg text-sm font-semibold">Import dari Excel</a>
            </div>
        </div>
    @else
        <!-- Main Grid: Active Question Detail & Navigation Panel -->
        <div class="grid grid-cols-12 gap-6 items-start">
            <div class="col-span-12 lg:col-span-8 space-y-5">
                @if($activeSoal)
                    @php
                        $currentIndex = $soals->search(fn($item) => $item->id === $activeSoal->id);
                        $prevSoal = $currentIndex > 0 ? $soals->get($currentIndex - 1) : null;
                        $nextSoal = $currentIndex < ($soals->count() - 1) ? $soals->get($currentIndex + 1) : null;
                    @endphp
                    <div class="bg-surface-white rounded-xl custom-shadow overflow-hidden border border-outline-variant/20">
                        <div class="px-6 py-4 border-b border-outline-variant/30 flex flex-wrap items-center justify-between gap-3 bg-surface-container-lowest">
                            <div class="flex items-center gap-3">
                                <span class="text-xl font-bold text-primary">{{ $activeSoal->nomor_soal }}.</span>
                                <span class="bg-secondary-container/10 text-secondary px-3 py-1 rounded-full text-xs font-semibold border border-secondary/20">{{ $activeSoal->jenis_label }}</span>
                                <form method="POST" action="{{ route('guru.bank-soal.soal.duplicate', [$bankSoal, $activeSoal]) }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-1 px-3 py-1 text-text-muted hover:text-secondary hover:bg-secondary/5 rounded text-xs font-medium">
                                        <span class="material-symbols-outlined text-sm">content_copy</span>
                                        Duplikasi Soal
                                    </button>
                                </form>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($prevSoal)
                                    <a href="{{ route('guru.bank-soal.soal.index', ['bankSoal' => $bankSoal, 'nomor' => $prevSoal->nomor_soal]) }}" class="px-3 py-1.5 bg-surface-container rounded-lg text-xs font-semibold hover:bg-surface-variant flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">chevron_left</span> Prev
                                    </a>
                                @endif
                                @if($nextSoal)
                                    <a href="{{ route('guru.bank-soal.soal.index', ['bankSoal' => $bankSoal, 'nomor' => $nextSoal->nomor_soal]) }}" class="px-3 py-1.5 bg-surface-container rounded-lg text-xs font-semibold hover:bg-surface-variant flex items-center gap-1">
                                        Next <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="mb-6 text-base text-on-surface leading-relaxed whitespace-pre-line">{{ $activeSoal->pertanyaan }}</div>

                            @if($activeSoal->opsis->isNotEmpty())
                                <div class="space-y-3">
                                    @foreach($activeSoal->opsis as $opsi)
                                        <div class="flex items-center gap-4 p-4 rounded-xl border {{ $opsi->skor > 0 ? 'border-2 border-primary bg-primary-fixed/15' : 'border-outline-variant/60 bg-surface-white' }}">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0 {{ $opsi->skor > 0 ? 'bg-primary text-white' : 'border-2 border-outline-variant text-outline' }}">
                                                {{ $opsi->label }}
                                            </div>
                                            <p class="flex-1 text-sm text-on-surface">{{ $opsi->teks_opsi }}</p>
                                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $opsi->skor > 0 ? 'bg-primary text-white' : 'bg-surface-container-high text-text-muted' }}">
                                                skor: {{ $opsi->skor }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 rounded-xl bg-surface-container-low text-sm space-y-1">
                                    <p class="font-semibold text-primary">Bobot Maksimal: {{ $activeSoal->bobot }}</p>
                                    @if($activeSoal->kunci_jawaban)
                                        <p class="text-text-muted">Kunci / Referensi: {{ $activeSoal->kunci_jawaban }}</p>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-6 pt-5 border-t border-outline-variant/30 flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('guru.bank-soal.soal.edit', [$bankSoal, $activeSoal]) }}" class="flex items-center gap-1.5 px-5 py-2 bg-secondary/10 text-secondary border border-secondary/20 rounded-lg hover:bg-secondary hover:text-white transition-all text-xs font-semibold">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('guru.bank-soal.soal.destroy', [$bankSoal, $activeSoal]) }}" onsubmit="return confirm('Hapus soal ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="flex items-center gap-1.5 px-5 py-2 bg-error/10 text-error border border-error/20 rounded-lg hover:bg-error hover:text-white transition-all text-xs font-semibold">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                                <div class="flex items-center gap-4 text-xs font-semibold text-success-green">
                                    @if($bankSoal->acak_soal)
                                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">check_circle</span> Acak Soal</span>
                                    @endif
                                    @if($bankSoal->acak_jawaban)
                                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">check_circle</span> Acak Jawaban</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Question Number Panel -->
            <div class="col-span-12 lg:col-span-4">
                <div class="bg-surface-white rounded-xl custom-shadow p-6 border border-outline-variant/20 sticky top-24">
                    <h3 class="text-center font-bold text-xs text-on-surface-variant tracking-widest mb-4 uppercase">NOMOR SOAL</h3>
                    <div class="grid grid-cols-5 gap-2.5">
                        @foreach($soals as $s)
                            @php $isCurrent = $activeSoal && $s->id === $activeSoal->id; @endphp
                            <a href="{{ route('guru.bank-soal.soal.index', ['bankSoal' => $bankSoal, 'nomor' => $s->nomor_soal]) }}"
                               class="aspect-square flex items-center justify-center rounded-full font-bold text-xs transition-all {{ $isCurrent ? 'bg-primary text-white shadow-md scale-105 border-2 border-primary-fixed' : 'bg-surface-container-high text-on-surface-variant hover:bg-outline-variant/50' }}">
                                {{ str_pad((string) $s->nomor_soal, 2, '0', STR_PAD_LEFT) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

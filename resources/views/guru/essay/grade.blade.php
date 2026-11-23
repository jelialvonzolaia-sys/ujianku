@extends('layouts.guru')

@section('title', 'Koreksi Essay | Ujianku')

@section('breadcrumb')
    <a href="{{ route('guru.essay.index') }}" class="hover:text-primary">Penilaian Essay</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">{{ $peserta->siswa?->nama_lengkap }}</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/20 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-primary">{{ $peserta->ujian?->nama_ujian }}</h2>
            <p class="text-sm text-text-muted">Siswa: <strong>{{ $peserta->siswa?->nama_lengkap }}</strong> ({{ $peserta->siswa?->kelas?->nama_kelas }})</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-text-muted uppercase">Nilai Saat Ini</p>
            <p class="text-2xl font-bold text-secondary">{{ $peserta->nilai_akhir }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('guru.essay.store', $peserta) }}" class="space-y-5">
        @csrf
        @foreach($essayJawabans as $jawaban)
            @php $soal = $jawaban->soal; @endphp
            <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-outline-variant/30 pb-3">
                    <span class="font-bold text-primary">Soal Nomor {{ $soal->nomor_soal }} ({{ $soal->jenis_label }})</span>
                    <span class="px-3 py-1 bg-primary-fixed text-primary rounded-full text-xs font-bold">Bobot Maks: {{ $soal->bobot }}</span>
                </div>
                <p class="text-sm text-on-surface leading-relaxed">{{ $soal->pertanyaan }}</p>
                @if($soal->kunci_jawaban)
                    <div class="p-3 bg-surface-container-low rounded-lg text-xs text-text-muted">
                        <strong>Rubrik / Pedoman:</strong> {{ $soal->kunci_jawaban }}
                    </div>
                @endif
                <div class="p-4 rounded-xl bg-blue-50/60 border border-primary/20">
                    <p class="text-xs font-bold text-primary uppercase mb-1">Jawaban Siswa:</p>
                    <p class="text-sm text-on-surface whitespace-pre-line">{{ $jawaban->jawaban_teks ?: '(Tidak dijawab)' }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-semibold mb-1">Skor Diberikan (0 - {{ $soal->bobot }})</label>
                        <input type="number" step="0.5" min="0" max="{{ $soal->bobot }}" name="skor[{{ $jawaban->id }}]" value="{{ old('skor.' . $jawaban->id, $jawaban->skor_diperoleh) }}" required class="w-full h-11 px-3 rounded-lg border border-outline-variant text-sm font-bold text-primary">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold mb-1">Catatan / Feedback Guru (Opsional)</label>
                        <input type="text" name="catatan[{{ $jawaban->id }}]" value="{{ old('catatan.' . $jawaban->id, $jawaban->catatan_guru) }}" placeholder="Berikan masukan konstruktif untuk siswa..." class="w-full h-11 px-3 rounded-lg border border-outline-variant text-sm">
                    </div>
                </div>
            </div>
        @endforeach

        <div class="flex justify-end gap-3">
            <a href="{{ route('guru.essay.index') }}" class="h-12 px-6 flex items-center rounded-lg border border-outline-variant text-sm font-semibold">Kembali</a>
            <button type="submit" class="h-12 px-7 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan Penilaian Essay
            </button>
        </div>
    </form>
</div>
@endsection

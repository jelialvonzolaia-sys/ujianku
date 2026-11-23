@extends('layouts.guru')

@section('title', ($soal->exists ? 'Edit Soal' : 'Tambah Soal') . ' | Ujianku')

@section('breadcrumb')
    <a href="{{ route('guru.bank-soal.index') }}" class="hover:text-primary">Bank Soal</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('guru.bank-soal.soal.index', $bankSoal) }}" class="hover:text-primary">List Soal</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">{{ $soal->exists ? 'Edit Soal' : 'Tambah Soal' }}</span>
@endsection

@section('content')
@php
    $isEdit = $soal->exists;
    $action = $isEdit ? route('guru.bank-soal.soal.update', [$bankSoal, $soal]) : route('guru.bank-soal.soal.store', $bankSoal);
    $existingOpsis = $isEdit ? $soal->opsis : collect();
    $defaultLabels = match($bankSoal->mode_jawaban) {
        'ABC' => ['A', 'B', 'C'],
        'ABCD' => ['A', 'B', 'C', 'D'],
        default => ['A', 'B', 'C', 'D', 'E'],
    };
@endphp

<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl md:text-3xl font-bold text-primary">{{ $isEdit ? 'Edit Pertanyaan #' . $soal->nomor_soal : 'Buat Pertanyaan Baru' }}</h2>
        <p class="text-sm text-text-muted mt-1">Bank Soal: <strong>{{ $bankSoal->nama_bank_soal }}</strong></p>
    </div>

    <form method="POST" action="{{ $action }}" class="space-y-6">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <section class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-4 md:col-span-3">
                    <label class="block text-sm font-semibold mb-1.5">Nomor Soal</label>
                    <input name="nomor_soal" type="number" min="1" value="{{ old('nomor_soal', $nextNomor) }}" class="w-full h-12 bg-surface-container-low border border-outline-variant rounded-lg px-4 text-center font-bold text-primary">
                </div>
                <div class="col-span-8 md:col-span-6">
                    <label class="block text-sm font-semibold mb-1.5">Jenis Soal</label>
                    <select name="jenis_soal" id="jenis_soal" onchange="toggleOptionsSection()" class="w-full h-12 bg-surface-white border border-outline-variant rounded-lg px-4 text-sm">
                        @foreach([
                            'pilihan_ganda' => 'Pilihan Ganda',
                            'pg_kompleks' => 'Pilihan Ganda Kompleks',
                            'benar_salah' => 'Benar / Salah',
                            'isian_singkat' => 'Isian Singkat',
                            'essay' => 'Uraian / Essay',
                        ] as $val => $label)
                            <option value="{{ $val }}" {{ old('jenis_soal', $soal->jenis_soal) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-sm font-semibold mb-1.5">Bobot Skor</label>
                    <input name="bobot" type="number" step="0.5" min="0" value="{{ old('bobot', $soal->bobot ?? 1) }}" class="w-full h-12 border border-outline-variant rounded-lg px-4 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5">Uraian Soal <span class="text-error">*</span></label>
                <textarea name="pertanyaan" rows="5" required placeholder="Tuliskan pertanyaan di sini..." class="w-full p-4 rounded-xl border border-outline-variant text-sm leading-relaxed">{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
            </div>

            <!-- Section Opsi Jawaban (Pilihan Ganda / PG Kompleks / Benar Salah) -->
            <div id="opsi-section" class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-primary">Opsi Jawaban</h3>
                    <span class="text-xs text-text-muted">Isi skor &gt; 0 untuk menandai jawaban benar</span>
                </div>

                <div id="opsi-container" class="space-y-3">
                    @foreach($defaultLabels as $idx => $lbl)
                        @php
                            $opsiItem = $existingOpsis->firstWhere('label', $lbl);
                            $teksVal = old("opsi_teks.$idx", $opsiItem?->teks_opsi ?? ($lbl === 'A' && !$isEdit ? '' : ''));
                            $skorVal = old("opsi_skor.$idx", $opsiItem?->skor ?? 0);
                        @endphp
                        <div class="opsi-row border border-outline-variant rounded-xl p-4 bg-surface-white flex gap-4 items-start">
                            <div class="w-11 shrink-0">
                                <input class="opsi-label w-full text-center font-bold text-secondary bg-secondary-fixed/35 border-none rounded-lg py-2.5 text-sm" readonly value="{{ $lbl }}">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs text-text-muted mb-1">Uraian Jawaban</label>
                                <textarea name="opsi_teks[]" rows="2" placeholder="Tuliskan jawaban..." class="w-full p-2.5 rounded-lg border border-outline-variant text-sm">{{ $teksVal }}</textarea>
                            </div>
                            <div class="w-24 shrink-0">
                                <label class="block text-xs text-text-muted mb-1 text-center">Skor</label>
                                <input name="opsi_skor[]" type="number" step="0.5" value="{{ $skorVal }}" class="w-full text-center border border-outline-variant rounded-lg py-2.5 text-sm">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Kunci Isian / Rubrik Essay -->
            <div id="kunci-section" class="hidden space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Kunci Jawaban / Pedoman Penilaian</label>
                    <textarea name="kunci_jawaban" rows="3" placeholder="Untuk Isian Singkat: tuliskan jawaban persis. Untuk Essay: tuliskan poin rubrik penilaian..." class="w-full p-4 rounded-lg border border-outline-variant text-sm">{{ old('kunci_jawaban', $soal->kunci_jawaban) }}</textarea>
                </div>
            </div>
        </section>

        <div class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20 flex justify-between items-center">
            <a href="{{ route('guru.bank-soal.soal.index', $bankSoal) }}" class="px-6 py-2.5 text-sm font-semibold text-text-muted hover:text-primary">Batal</a>
            <button type="submit" class="px-8 py-3 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-container flex items-center gap-2 shadow-md">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan Soal
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function toggleOptionsSection() {
        const jenis = document.getElementById('jenis_soal').value;
        const opsiSec = document.getElementById('opsi-section');
        const kunciSec = document.getElementById('kunci-section');
        if (['pilihan_ganda', 'pg_kompleks', 'benar_salah'].includes(jenis)) {
            opsiSec.classList.remove('hidden');
            kunciSec.classList.add('hidden');
        } else {
            opsiSec.classList.add('hidden');
            kunciSec.classList.remove('hidden');
        }
    }
    toggleOptionsSection();
</script>
@endpush

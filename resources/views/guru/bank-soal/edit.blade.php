@extends('layouts.guru')

@section('title', 'Edit Bank Soal | Ujianku')

@section('breadcrumb')
    <a href="{{ route('guru.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('guru.bank-soal.index') }}" class="hover:text-primary">Bank Soal</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Edit</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-primary">Edit Bank Soal — {{ $bankSoal->nama_bank_soal }}</h2>
    </div>

    <form method="POST" action="{{ route('guru.bank-soal.update', $bankSoal) }}" class="space-y-6">
        @csrf @method('PUT')

        <section class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold mb-1.5">Nama Bank Soal</label>
                    <input name="nama_bank_soal" value="{{ old('nama_bank_soal', $bankSoal->nama_bank_soal) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" {{ (string) old('mata_pelajaran_id', $bankSoal->mata_pelajaran_id) === (string) $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Kode Soal</label>
                    <input name="kode_soal" value="{{ old('kode_soal', $bankSoal->kode_soal) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm uppercase">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Tingkat Kelas</label>
                    <select name="tingkat_kelas" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                        @foreach(['X', 'XI', 'XII'] as $t)
                            <option value="{{ $t }}" {{ old('tingkat_kelas', $bankSoal->tingkat_kelas) === $t ? 'selected' : '' }}>Kelas {{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Kelas Spesifik</label>
                    <select name="kelas_id" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ (string) old('kelas_id', $bankSoal->kelas_id) === (string) $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold mb-1.5">Materi / Topik</label>
                    <input name="materi" value="{{ old('materi', $bankSoal->materi) }}" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                </div>
            </div>
        </section>

        <section class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Mode Jawaban</label>
                    <select name="mode_jawaban" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                        @foreach(['ABC', 'ABCD', 'ABCDE'] as $mode)
                            <option value="{{ $mode }}" {{ old('mode_jawaban', $bankSoal->mode_jawaban) === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Acak Soal</label>
                    <label class="flex items-center gap-3 p-3 rounded-lg bg-surface-container-low cursor-pointer">
                        <input type="checkbox" name="acak_soal" value="1" {{ old('acak_soal', $bankSoal->acak_soal) ? 'checked' : '' }} class="w-5 h-5 rounded text-primary">
                        <span class="text-sm">Acak urutan soal</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Acak Jawaban</label>
                    <label class="flex items-center gap-3 p-3 rounded-lg bg-surface-container-low cursor-pointer">
                        <input type="checkbox" name="acak_jawaban" value="1" {{ old('acak_jawaban', $bankSoal->acak_jawaban) ? 'checked' : '' }} class="w-5 h-5 rounded text-primary">
                        <span class="text-sm">Acak opsi jawaban</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Skala Nilai</label>
                    <select name="skala_nilai" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                        <option value="skala_100" {{ old('skala_nilai', $bankSoal->skala_nilai) === 'skala_100' ? 'selected' : '' }}>Skala 100</option>
                        <option value="skor_total" {{ old('skala_nilai', $bankSoal->skala_nilai) === 'skor_total' ? 'selected' : '' }}>Sesuai Skor Total</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Skor Bila Tidak Menjawab</label>
                    <input name="skor_tidak_menjawab" type="number" step="0.5" value="{{ old('skor_tidak_menjawab', $bankSoal->skor_tidak_menjawab) }}" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                </div>
            </div>

            <input type="hidden" name="continuous_mode" value="{{ $bankSoal->continuous_mode ? '1' : '0' }}">
            <input type="hidden" name="harus_menjawab" value="{{ $bankSoal->harus_menjawab ? '1' : '0' }}">
            <input type="hidden" name="redirect_after_save" value="{{ $bankSoal->redirect_after_save ? '1' : '0' }}">

            <div>
                <label class="block text-sm font-semibold mb-1.5">Petunjuk Pengerjaan</label>
                <textarea name="petunjuk_pengerjaan" rows="3" class="w-full p-4 rounded-lg border border-outline-variant text-sm">{{ old('petunjuk_pengerjaan', $bankSoal->petunjuk_pengerjaan) }}</textarea>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('guru.bank-soal.index') }}" class="h-12 px-6 flex items-center rounded-lg border border-outline-variant text-sm font-semibold">Batal</a>
            <button type="submit" class="h-12 px-7 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

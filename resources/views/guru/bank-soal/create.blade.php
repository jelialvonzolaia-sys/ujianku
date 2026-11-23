@extends('layouts.guru')

@section('title', 'Tambah Bank Soal | Ujianku')

@section('breadcrumb')
    <a href="{{ route('guru.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('guru.bank-soal.index') }}" class="hover:text-primary">Bank Soal</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Tambah Bank Soal</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl md:text-3xl font-bold text-primary">Tambah Bank Soal</h2>
        <p class="text-sm text-on-surface-variant mt-1">Buat paket soal baru untuk kuis atau ujian.</p>
    </div>

    <div class="bg-secondary-fixed/50 p-5 rounded-xl flex gap-3 border-l-4 border-primary">
        <span class="material-symbols-outlined text-primary text-2xl">info</span>
        <div>
            <h4 class="text-sm font-bold text-primary mb-1">Mohon perhatikan petunjuk dan keterangan pada setiap isian form di bawah ini.</h4>
            <p class="text-xs text-on-surface-variant leading-relaxed">Konfigurasi pengacakan dan penilaian akan diterapkan secara otomatis pada setiap sesi ujian yang menggunakan bank soal ini.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('guru.bank-soal.store') }}" class="space-y-6">
        @csrf

        <!-- Group 1: Identitas Paket Soal -->
        <section class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-5">
            <div class="flex items-center gap-2 border-b border-surface-container pb-3">
                <span class="material-symbols-outlined text-primary">badge</span>
                <h3 class="text-lg font-bold text-on-surface">Identitas Paket Soal</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold mb-1.5">Nama Bank Soal <span class="text-error">*</span></label>
                    <input name="nama_bank_soal" value="{{ old('nama_bank_soal') }}" required placeholder="Contoh: Ujian Akhir Semester Genap Matematika" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Mata Pelajaran <span class="text-error">*</span></label>
                    <select name="mata_pelajaran_id" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" {{ (string) old('mata_pelajaran_id') === (string) $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Kode Soal (Opsional)</label>
                    <input name="kode_soal" value="{{ old('kode_soal') }}" placeholder="Otomatis jika dikosongkan" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm uppercase">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Tingkat Kelas <span class="text-error">*</span></label>
                    <select name="tingkat_kelas" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                        @foreach(['X', 'XI', 'XII'] as $t)
                            <option value="{{ $t }}" {{ old('tingkat_kelas', 'X') === $t ? 'selected' : '' }}>Kelas {{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Kelas Spesifik (Opsional)</label>
                    <select name="kelas_id" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                        <option value="">Semua Kelas di Tingkat Ini</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ (string) old('kelas_id') === (string) $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold mb-1.5">Materi / Topik</label>
                    <input name="materi" value="{{ old('materi') }}" placeholder="Contoh: Trigonometri & Kalkulus" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                </div>
            </div>
        </section>

        <!-- Group 2: Konfigurasi Soal -->
        <section class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-5">
            <div class="flex items-center gap-2 border-b border-surface-container pb-3">
                <span class="material-symbols-outlined text-primary">settings_suggest</span>
                <h3 class="text-lg font-bold text-on-surface">Konfigurasi Soal</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Mode Jawaban</label>
                    <select name="mode_jawaban" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                        <option value="ABC" {{ old('mode_jawaban') === 'ABC' ? 'selected' : '' }}>ABC</option>
                        <option value="ABCD" {{ old('mode_jawaban') === 'ABCD' ? 'selected' : '' }}>ABCD</option>
                        <option value="ABCDE" {{ old('mode_jawaban', 'ABCDE') === 'ABCDE' ? 'selected' : '' }}>ABCDE</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Acak Soal</label>
                    <label class="flex items-center gap-3 p-3 rounded-lg bg-surface-container-low cursor-pointer">
                        <input type="checkbox" name="acak_soal" value="1" {{ old('acak_soal', '1') ? 'checked' : '' }} class="w-5 h-5 rounded text-primary">
                        <span class="text-sm">Acak urutan soal</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Acak Jawaban</label>
                    <label class="flex items-center gap-3 p-3 rounded-lg bg-surface-container-low cursor-pointer">
                        <input type="checkbox" name="acak_jawaban" value="1" {{ old('acak_jawaban') ? 'checked' : '' }} class="w-5 h-5 rounded text-primary">
                        <span class="text-sm">Acak opsi jawaban</span>
                    </label>
                </div>
            </div>
        </section>

        <!-- Group 3: Penilaian & Aturan -->
        <section class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-5">
            <div class="flex items-center gap-2 border-b border-surface-container pb-3">
                <span class="material-symbols-outlined text-primary">equalizer</span>
                <h3 class="text-lg font-bold text-on-surface">Penilaian & Aturan Pengerjaan</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Skala Nilai</label>
                    <select name="skala_nilai" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                        <option value="skala_100" {{ old('skala_nilai', 'skala_100') === 'skala_100' ? 'selected' : '' }}>Skala 100</option>
                        <option value="skor_total" {{ old('skala_nilai') === 'skor_total' ? 'selected' : '' }}>Sesuai Skor Total</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Skor Bila Tidak Menjawab</label>
                    <input name="skor_tidak_menjawab" type="number" step="0.5" value="{{ old('skor_tidak_menjawab', 0) }}" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold mb-1">Continuous Mode</label>
                    <select name="continuous_mode" class="w-full h-10 px-3 rounded-lg border border-outline-variant text-sm bg-surface-container-low">
                        <option value="0" selected>Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Harus Menjawab</label>
                    <select name="harus_menjawab" class="w-full h-10 px-3 rounded-lg border border-outline-variant text-sm bg-surface-container-low">
                        <option value="0" selected>Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Redirect ke List Soal</label>
                    <select name="redirect_after_save" class="w-full h-10 px-3 rounded-lg border border-outline-variant text-sm bg-surface-container-low">
                        <option value="1" selected>Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>
            </div>
        </section>

        <!-- Petunjuk Pengerjaan -->
        <section class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-4">
            <div class="flex items-center gap-2 border-b border-surface-container pb-3">
                <span class="material-symbols-outlined text-primary">edit_note</span>
                <h3 class="text-lg font-bold text-on-surface">Petunjuk Pengerjaan</h3>
            </div>
            <textarea name="petunjuk_pengerjaan" rows="4" placeholder="Tuliskan instruksi pengerjaan untuk siswa di sini..." class="w-full p-4 rounded-lg border border-outline-variant text-sm">{{ old('petunjuk_pengerjaan', 'Bacalah setiap soal dengan teliti sebelum memilih atau menuliskan jawaban.') }}</textarea>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('guru.bank-soal.index') }}" class="h-12 px-6 flex items-center rounded-lg border border-outline-variant text-sm font-semibold">Batal</a>
            <button type="submit" class="h-12 px-7 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan Bank Soal
            </button>
        </div>
    </form>
</div>
@endsection

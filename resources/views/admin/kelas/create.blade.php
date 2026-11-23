@extends('layouts.admin')

@section('title', 'Tambah Kelas | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('admin.kelas.index') }}" class="hover:text-primary">Kelas</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Tambah Kelas</span>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('admin.kelas.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline mb-2">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Data Kelas
        </a>
        <h2 class="text-2xl font-bold text-primary">Tambah Kelas</h2>
        <p class="text-sm text-on-surface-variant">Buat data kelas baru untuk mengelompokkan siswa dan mengatur wali kelas.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.kelas.store') }}" class="bg-surface-white rounded-xl soft-shadow p-6 md:p-8 border border-outline-variant/20 space-y-6">
                @csrf
                <h3 class="text-lg font-bold border-b border-outline-variant/30 pb-3">Informasi Kelas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Nama Kelas <span class="text-error">*</span></label>
                        <input name="nama_kelas" value="{{ old('nama_kelas') }}" required placeholder="Contoh: X IPA 1" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Tingkat Kelas <span class="text-error">*</span></label>
                        <select name="tingkat" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                            <option value="X" {{ old('tingkat') === 'X' ? 'selected' : '' }}>Kelas X</option>
                            <option value="XI" {{ old('tingkat') === 'XI' ? 'selected' : '' }}>Kelas XI</option>
                            <option value="XII" {{ old('tingkat') === 'XII' ? 'selected' : '' }}>Kelas XII</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Jurusan <span class="text-error">*</span></label>
                        <select name="jurusan_id" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                            <option value="">Pilih Jurusan</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j->id }}" {{ (string) old('jurusan_id') === (string) $j->id ? 'selected' : '' }}>{{ $j->kode_jurusan }} — {{ $j->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Wali Kelas</label>
                        <select name="wali_kelas_id" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                            <option value="">Pilih Wali Kelas (Opsional)</option>
                            @foreach($gurus as $g)
                                <option value="{{ $g->id }}" {{ (string) old('wali_kelas_id') === (string) $g->id ? 'selected' : '' }}>{{ $g->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">Tahun Ajaran <span class="text-error">*</span></label>
                        <select name="tahun_ajaran_id" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ (string) old('tahun_ajaran_id') === (string) $ta->id ? 'selected' : '' }}>
                                    {{ $ta->tahun }} ({{ $ta->semester }}) {{ $ta->is_active ? '— Aktif' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">Status Kelas</label>
                        <div class="flex gap-8">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }}>
                                <span class="text-sm">Aktif</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="is_active" value="0" {{ old('is_active') === '0' ? 'checked' : '' }}>
                                <span class="text-sm">Nonaktif</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant/30">
                    <a href="{{ route('admin.kelas.index') }}" class="h-12 px-6 flex items-center rounded-lg border-2 border-secondary text-secondary text-sm font-semibold">Batal</a>
                    <button type="submit" class="h-12 px-6 rounded-lg bg-secondary text-white text-sm font-semibold hover:bg-primary flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Simpan Kelas
                    </button>
                </div>
            </form>
        </div>

        <div class="space-y-5">
            <div class="bg-primary-fixed p-6 rounded-xl border border-secondary/10">
                <div class="flex items-center gap-2 text-primary mb-2">
                    <span class="material-symbols-outlined">info</span>
                    <h4 class="text-sm font-bold">Panduan Form</h4>
                </div>
                <p class="text-xs text-on-primary-fixed-variant leading-relaxed">
                    Form Tambah Kelas digunakan untuk membuat data kelas baru. Data kelas ini dipakai untuk mengelompokkan siswa, mengatur wali kelas, mata pelajaran, dan peserta ujian.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

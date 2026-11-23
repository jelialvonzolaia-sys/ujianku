@extends('layouts.admin')

@section('title', 'Tambah Tahun Pelajaran | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('admin.tahun-ajaran.index') }}" class="hover:text-primary">Tahun Pelajaran</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Tambah</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.tahun-ajaran.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-secondary hover:underline mb-2">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Daftar
        </a>
        <h2 class="text-2xl font-bold text-primary">Tambah Tahun Pelajaran</h2>
        <p class="text-sm text-on-surface-variant">Tambahkan periode tahun ajaran dan semester baru.</p>
    </div>

    <form method="POST" action="{{ route('admin.tahun-ajaran.store') }}" class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 p-6 md:p-8 space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-on-surface mb-2" for="tahun">Tahun Pelajaran</label>
            <input id="tahun" name="tahun" value="{{ old('tahun') }}" required placeholder="Contoh: 2026/2027"
                   class="w-full h-12 px-4 rounded-lg border border-outline-variant focus:border-secondary focus:ring-2 focus:ring-secondary/20 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-on-surface mb-2" for="semester">Semester</label>
            <select id="semester" name="semester" required class="w-full h-12 px-4 rounded-lg border border-outline-variant focus:border-secondary focus:ring-2 focus:ring-secondary/20 text-sm">
                <option value="Ganjil" {{ old('semester') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                <option value="Genap" {{ old('semester') === 'Genap' ? 'selected' : '' }}>Genap</option>
            </select>
        </div>

        <label class="flex items-center gap-3 p-4 rounded-xl bg-surface-container-low cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }} class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary">
            <div>
                <p class="text-sm font-semibold text-on-surface">Jadikan Tahun Pelajaran Aktif</p>
                <p class="text-xs text-text-muted">Mengaktifkan tahun pelajaran ini akan menonaktifkan tahun pelajaran aktif lainnya.</p>
            </div>
        </label>

        <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant/30">
            <a href="{{ route('admin.tahun-ajaran.index') }}" class="h-12 px-6 flex items-center rounded-lg border border-outline-variant text-sm font-semibold text-on-surface-variant hover:bg-surface-container-low">Batal</a>
            <button type="submit" class="h-12 px-6 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Tambah Tahun Pelajaran
            </button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Edit Tahun Pelajaran | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('admin.tahun-ajaran.index') }}" class="hover:text-primary">Tahun Pelajaran</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Edit</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.tahun-ajaran.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-secondary hover:underline mb-2">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Daftar
        </a>
        <h2 class="text-2xl font-bold text-primary">Edit Tahun Pelajaran</h2>
    </div>

    <form method="POST" action="{{ route('admin.tahun-ajaran.update', $tahunAjaran) }}" class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 p-6 md:p-8 space-y-6">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-semibold text-on-surface mb-2" for="tahun">Tahun Pelajaran</label>
            <input id="tahun" name="tahun" value="{{ old('tahun', $tahunAjaran->tahun) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-on-surface mb-2" for="semester">Semester</label>
            <select id="semester" name="semester" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                <option value="Ganjil" {{ old('semester', $tahunAjaran->semester) === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                <option value="Genap" {{ old('semester', $tahunAjaran->semester) === 'Genap' ? 'selected' : '' }}>Genap</option>
            </select>
        </div>

        <label class="flex items-center gap-3 p-4 rounded-xl bg-surface-container-low cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tahunAjaran->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded border-outline-variant text-primary">
            <span class="text-sm font-semibold text-on-surface">Aktifkan Tahun Pelajaran Ini</span>
        </label>

        <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant/30">
            <a href="{{ route('admin.tahun-ajaran.index') }}" class="h-12 px-6 flex items-center rounded-lg border border-outline-variant text-sm font-semibold text-on-surface-variant">Batal</a>
            <button type="submit" class="h-12 px-6 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

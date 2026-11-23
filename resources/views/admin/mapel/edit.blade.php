@extends('layouts.admin')

@section('title', 'Edit Mata Pelajaran | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('admin.mapel.index') }}" class="hover:text-primary">Mata Pelajaran</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Edit</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.mapel.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-secondary hover:underline mb-2">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Mata Pelajaran
        </a>
        <h2 class="text-2xl font-bold text-primary">Edit Mata Pelajaran</h2>
    </div>

    <form method="POST" action="{{ route('admin.mapel.update', $mapel) }}" class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 p-6 md:p-8 space-y-6">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-semibold mb-2" for="kode_mapel">Kode Mapel</label>
                <input id="kode_mapel" name="kode_mapel" value="{{ old('kode_mapel', $mapel->kode_mapel) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm uppercase">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-2" for="nama_mapel">Nama Mata Pelajaran</label>
                <input id="nama_mapel" name="nama_mapel" value="{{ old('nama_mapel', $mapel->nama_mapel) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-2" for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" class="w-full p-4 rounded-lg border border-outline-variant text-sm">{{ old('deskripsi', $mapel->deskripsi) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-2">Status</label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="is_active" value="1" {{ old('is_active', $mapel->is_active ? '1' : '0') === '1' ? 'checked' : '' }}>
                    <span class="text-sm">Aktif</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="is_active" value="0" {{ old('is_active', $mapel->is_active ? '1' : '0') === '0' ? 'checked' : '' }}>
                    <span class="text-sm">Nonaktif</span>
                </label>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant/30">
            <a href="{{ route('admin.mapel.index') }}" class="h-12 px-6 flex items-center rounded-lg border border-outline-variant text-sm font-semibold">Batal</a>
            <button type="submit" class="h-12 px-6 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

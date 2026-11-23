@extends('layouts.admin')

@section('title', 'Edit Kelas | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('admin.kelas.index') }}" class="hover:text-primary">Kelas</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Edit Kelas</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.kelas.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline mb-2">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Data Kelas
        </a>
        <h2 class="text-2xl font-bold text-primary">Edit Kelas — {{ $kelas->nama_kelas }}</h2>
    </div>

    <form method="POST" action="{{ route('admin.kelas.update', $kelas) }}" class="bg-surface-white rounded-xl soft-shadow p-6 md:p-8 border border-outline-variant/20 space-y-6">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold mb-2">Nama Kelas</label>
                <input name="nama_kelas" value="{{ old('nama_kelas', $kelas->nama_kelas) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Tingkat Kelas</label>
                <select name="tingkat" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    @foreach(['X', 'XI', 'XII'] as $t)
                        <option value="{{ $t }}" {{ old('tingkat', $kelas->tingkat) === $t ? 'selected' : '' }}>Kelas {{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Jurusan</label>
                <select name="jurusan_id" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    @foreach($jurusans as $j)
                        <option value="{{ $j->id }}" {{ (string) old('jurusan_id', $kelas->jurusan_id) === (string) $j->id ? 'selected' : '' }}>{{ $j->kode_jurusan }} — {{ $j->nama_jurusan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Wali Kelas</label>
                <select name="wali_kelas_id" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    <option value="">Tidak ada</option>
                    @foreach($gurus as $g)
                        <option value="{{ $g->id }}" {{ (string) old('wali_kelas_id', $kelas->wali_kelas_id) === (string) $g->id ? 'selected' : '' }}>{{ $g->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-2">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" {{ (string) old('tahun_ajaran_id', $kelas->tahun_ajaran_id) === (string) $ta->id ? 'selected' : '' }}>{{ $ta->tahun }} ({{ $ta->semester }})</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-2">Status Kelas</label>
                <div class="flex gap-8">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', $kelas->is_active ? '1' : '0') === '1' ? 'checked' : '' }}>
                        <span class="text-sm">Aktif</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_active" value="0" {{ old('is_active', $kelas->is_active ? '1' : '0') === '0' ? 'checked' : '' }}>
                        <span class="text-sm">Nonaktif</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant/30">
            <a href="{{ route('admin.kelas.index') }}" class="h-12 px-6 flex items-center rounded-lg border border-outline-variant text-sm font-semibold">Batal</a>
            <button type="submit" class="h-12 px-6 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Tambah Jurusan | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('admin.jurusan.index') }}" class="hover:text-primary">Jurusan</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Tambah</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.jurusan.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-secondary hover:underline mb-2">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Jurusan
        </a>
        <h2 class="text-2xl font-bold text-primary">Tambah Jurusan</h2>
        <p class="text-sm text-on-surface-variant">Tambahkan program keahlian atau peminatan baru.</p>
    </div>

    <form method="POST" action="{{ route('admin.jurusan.store') }}" class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 p-6 md:p-8 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2" for="kode_jurusan">Kode Jurusan <span class="text-error">*</span></label>
                <input id="kode_jurusan" name="kode_jurusan" value="{{ old('kode_jurusan') }}" required placeholder="Misal: MIPA / TKJ" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm uppercase">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-on-surface mb-2" for="nama_jurusan">Nama Jurusan <span class="text-error">*</span></label>
                <input id="nama_jurusan" name="nama_jurusan" value="{{ old('nama_jurusan') }}" required placeholder="Contoh: Matematika dan Ilmu Pengetahuan Alam" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-on-surface mb-2" for="deskripsi">Deskripsi Jurusan</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Keterangan singkat mengenai jurusan..." class="w-full p-4 rounded-lg border border-outline-variant text-sm">{{ old('deskripsi') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-on-surface mb-2">Status Jurusan</label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="is_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }} class="w-4 h-4 text-primary">
                    <span class="text-sm">Aktif</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="is_active" value="0" {{ old('is_active') === '0' ? 'checked' : '' }} class="w-4 h-4 text-primary">
                    <span class="text-sm">Nonaktif</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant/30">
            <a href="{{ route('admin.jurusan.index') }}" class="h-12 px-6 flex items-center rounded-lg border border-outline-variant text-sm font-semibold text-on-surface-variant">Batal</a>
            <button type="submit" class="h-12 px-6 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan Jurusan
            </button>
        </div>
    </form>
</div>
@endsection

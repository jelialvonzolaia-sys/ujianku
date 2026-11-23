@extends('layouts.admin')

@section('title', 'Pengaturan Sistem | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Pengaturan</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl md:text-3xl font-bold text-primary">Pengaturan Sistem & Sekolah</h2>
        <p class="text-sm text-on-surface-variant mt-1">Konfigurasi identitas instansi dan standar pelaksanaan ujian online.</p>
    </div>

    <form method="POST" action="{{ route('admin.pengaturan.update') }}" class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 p-6 md:p-8 space-y-6">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1.5">Nama Sekolah / Institusi</label>
                <input name="school_name" value="{{ old('school_name', $settings['school_name']) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">NPSN</label>
                <input name="school_npsn" value="{{ old('school_npsn', $settings['school_npsn']) }}" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">KKM Default</label>
                <input name="kkm_default" type="number" min="0" max="100" value="{{ old('kkm_default', $settings['kkm_default']) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1.5">Alamat Sekolah</label>
                <textarea name="school_address" rows="2" class="w-full p-4 rounded-lg border border-outline-variant text-sm">{{ old('school_address', $settings['school_address']) }}</textarea>
            </div>
        </div>

        <label class="flex items-center gap-3 p-4 rounded-xl bg-surface-container-low cursor-pointer">
            <input type="checkbox" name="anti_cheat_blur" value="1" {{ old('anti_cheat_blur', $settings['anti_cheat_blur']) === '1' ? 'checked' : '' }} class="w-5 h-5 rounded border-outline-variant text-primary">
            <div>
                <p class="text-sm font-semibold text-on-surface">Aktifkan Peringatan Pindah Tab (Anti-Cheat)</p>
                <p class="text-xs text-text-muted">Menampilkan peringatan jika siswa meninggalkan jendela ujian saat sesi berlangsung.</p>
            </div>
        </label>

        <div class="flex justify-end pt-4 border-t border-outline-variant/30">
            <button type="submit" class="h-12 px-7 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection

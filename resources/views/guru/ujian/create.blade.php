@extends('layouts.guru')

@section('title', 'Buat Ujian Baru | Ujianku')

@section('breadcrumb')
    <a href="{{ route('guru.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('guru.ujian.index') }}" class="hover:text-primary">Ujian Saya</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Buat Ujian</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl md:text-3xl font-bold text-primary">Buat Ujian Baru</h2>
        <p class="text-sm text-on-surface-variant mt-1">Hubungkan bank soal dengan kelas peserta, atur durasi, jadwal, dan token ujian.</p>
    </div>

    @if($bankSoals->isEmpty())
        <div class="bg-tertiary-fixed/50 border-l-4 border-tertiary p-5 rounded-r-xl flex items-center justify-between gap-4">
            <p class="text-sm text-on-tertiary-fixed">Anda belum memiliki Bank Soal. Buat Bank Soal dan tambahkan soal terlebih dahulu sebelum membuat ujian.</p>
            <a href="{{ route('guru.bank-soal.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg text-xs font-semibold shrink-0">Buat Bank Soal</a>
        </div>
    @endif

    <form method="POST" action="{{ route('guru.ujian.store') }}" class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 p-6 md:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1.5">Nama Ujian <span class="text-error">*</span></label>
                <input name="nama_ujian" value="{{ old('nama_ujian') }}" required placeholder="Contoh: Penilaian Tengah Semester Matematika" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5">Bank Soal <span class="text-error">*</span></label>
                <select name="bank_soal_id" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    <option value="">Pilih Bank Soal</option>
                    @foreach($bankSoals as $bs)
                        <option value="{{ $bs->id }}" {{ (string) old('bank_soal_id') === (string) $bs->id ? 'selected' : '' }}>
                            {{ $bs->nama_bank_soal }} ({{ $bs->soals_count }} soal)
                        </option>
                    @endforeach
                </select>
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

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-2">Pilih Kelas Peserta <span class="text-error">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 p-4 rounded-xl bg-surface-container-low border border-outline-variant/40">
                    @foreach($kelasList as $k)
                        <label class="flex items-center gap-2 p-2.5 bg-white rounded-lg border border-outline-variant/40 cursor-pointer hover:border-primary">
                            <input type="checkbox" name="kelas_ids[]" value="{{ $k->id }}" {{ in_array($k->id, old('kelas_ids', [])) ? 'checked' : '' }} class="rounded text-primary">
                            <span class="text-xs font-semibold">{{ $k->nama_kelas }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5">Durasi (Menit) <span class="text-error">*</span></label>
                <input name="durasi_menit" type="number" min="5" max="360" value="{{ old('durasi_menit', 90) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5">Status Ujian</label>
                <select name="status" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Langsung Aktif</option>
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Simpan sebagai Draft</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5">Waktu Mulai (Opsional)</label>
                <input name="waktu_mulai" type="datetime-local" value="{{ old('waktu_mulai') }}" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5">Waktu Selesai (Opsional)</label>
                <input name="waktu_selesai" type="datetime-local" value="{{ old('waktu_selesai') }}" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5">Token Ujian (Opsional, otomatis jika kosong)</label>
                <input name="token" value="{{ old('token') }}" maxlength="12" placeholder="Misal: MTK202" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm font-mono uppercase">
            </div>

            <div class="flex flex-col justify-center gap-2 pt-3">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="wajib_token" value="1" {{ old('wajib_token', '1') ? 'checked' : '' }} class="w-4 h-4 rounded text-primary">
                    <span class="text-sm font-medium">Wajibkan siswa memasukkan token sebelum mulai</span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="tampilkan_nilai" value="1" {{ old('tampilkan_nilai', '1') ? 'checked' : '' }} class="w-4 h-4 rounded text-primary">
                    <span class="text-sm font-medium">Tampilkan nilai ke siswa setelah selesai</span>
                </label>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1.5">Instruksi Tambahan</label>
                <textarea name="instruksi_tambahan" rows="3" placeholder="Catatan khusus sebelum siswa memulai ujian..." class="w-full p-4 rounded-lg border border-outline-variant text-sm">{{ old('instruksi_tambahan') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant/30">
            <a href="{{ route('guru.ujian.index') }}" class="h-12 px-6 flex items-center rounded-lg border border-outline-variant text-sm font-semibold">Batal</a>
            <button type="submit" class="h-12 px-7 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan Jadwal Ujian
            </button>
        </div>
    </form>
</div>
@endsection

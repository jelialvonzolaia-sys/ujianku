@extends('layouts.guru')

@section('title', 'Edit Ujian | Ujianku')

@section('breadcrumb')
    <a href="{{ route('guru.ujian.index') }}" class="hover:text-primary">Ujian Saya</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Edit Ujian</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-primary">Edit Ujian — {{ $ujian->nama_ujian }}</h2>
    </div>

    <form method="POST" action="{{ route('guru.ujian.update', $ujian) }}" class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 p-6 md:p-8 space-y-6">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1.5">Nama Ujian</label>
                <input name="nama_ujian" value="{{ old('nama_ujian', $ujian->nama_ujian) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Bank Soal</label>
                <select name="bank_soal_id" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    @foreach($bankSoals as $bs)
                        <option value="{{ $bs->id }}" {{ (string) old('bank_soal_id', $ujian->bank_soal_id) === (string) $bs->id ? 'selected' : '' }}>{{ $bs->nama_bank_soal }} ({{ $bs->soals_count }} soal)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Mata Pelajaran</label>
                <select name="mata_pelajaran_id" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    @foreach($mapels as $m)
                        <option value="{{ $m->id }}" {{ (string) old('mata_pelajaran_id', $ujian->mata_pelajaran_id) === (string) $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-2">Kelas Peserta</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 p-4 rounded-xl bg-surface-container-low">
                    @foreach($kelasList as $k)
                        <label class="flex items-center gap-2 p-2.5 bg-white rounded-lg border border-outline-variant/40 cursor-pointer">
                            <input type="checkbox" name="kelas_ids[]" value="{{ $k->id }}" {{ in_array($k->id, old('kelas_ids', $selectedKelas)) ? 'checked' : '' }} class="rounded text-primary">
                            <span class="text-xs font-semibold">{{ $k->nama_kelas }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Durasi (Menit)</label>
                <input name="durasi_menit" type="number" min="5" max="360" value="{{ old('durasi_menit', $ujian->durasi_menit) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Status</label>
                <select name="status" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    @foreach(['draft', 'aktif', 'selesai'] as $st)
                        <option value="{{ $st }}" {{ old('status', $ujian->status) === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Waktu Mulai</label>
                <input name="waktu_mulai" type="datetime-local" value="{{ old('waktu_mulai', $ujian->waktu_mulai?->format('Y-m-d\TH:i')) }}" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Waktu Selesai</label>
                <input name="waktu_selesai" type="datetime-local" value="{{ old('waktu_selesai', $ujian->waktu_selesai?->format('Y-m-d\TH:i')) }}" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Token</label>
                <input name="token" value="{{ old('token', $ujian->token) }}" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm font-mono uppercase">
            </div>
            <div class="flex flex-col justify-center gap-2 pt-3">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="wajib_token" value="1" {{ old('wajib_token', $ujian->wajib_token) ? 'checked' : '' }} class="w-4 h-4 rounded text-primary">
                    <span class="text-sm font-medium">Wajibkan Token Ujian</span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="tampilkan_nilai" value="1" {{ old('tampilkan_nilai', $ujian->tampilkan_nilai) ? 'checked' : '' }} class="w-4 h-4 rounded text-primary">
                    <span class="text-sm font-medium">Tampilkan Nilai ke Siswa</span>
                </label>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant/30">
            <a href="{{ route('guru.ujian.show', $ujian) }}" class="h-12 px-6 flex items-center rounded-lg border border-outline-variant text-sm font-semibold">Batal</a>
            <button type="submit" class="h-12 px-7 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

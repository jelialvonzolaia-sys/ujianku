@extends('layouts.admin')

@section('title', 'Detail Siswa | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('admin.siswa.index') }}" class="hover:text-primary">Data Siswa</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Detail Siswa</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <a href="{{ route('admin.siswa.index') }}" class="inline-flex items-center gap-1.5 text-secondary font-semibold text-sm mb-2 hover:underline">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Data Siswa
            </a>
            <h2 class="text-2xl md:text-3xl font-bold text-primary">Detail Siswa</h2>
            <p class="text-sm text-on-surface-variant">Informasi lengkap akun dan aktivitas siswa dalam sistem Ujianku.</p>
        </div>
        <div class="flex gap-3">
            <form method="POST" action="{{ route('admin.siswa.toggle', $siswa) }}">
                @csrf @method('PATCH')
                <button type="submit" class="h-11 px-5 border rounded-lg text-sm font-semibold {{ $siswa->is_active ? 'border-error text-error hover:bg-error/5' : 'border-success-green text-success-green hover:bg-success-green/5' }}">
                    {{ $siswa->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                </button>
            </form>
            <a href="{{ route('admin.siswa.edit', $siswa) }}" class="h-11 px-5 bg-primary text-white rounded-lg text-sm font-semibold flex items-center gap-2 hover:bg-primary-container">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                Edit Siswa
            </a>
        </div>
    </div>

    <section class="bg-surface-white rounded-xl soft-shadow p-6 flex flex-col md:flex-row items-center gap-6 border border-outline-variant/20">
        <div class="relative">
            <div class="w-24 h-24 rounded-full bg-secondary-fixed flex items-center justify-center text-secondary text-3xl font-bold border-4 border-surface-container-low">
                {{ $siswa->user?->initials ?? 'S' }}
            </div>
            <div class="absolute bottom-1 right-1 w-6 h-6 border-4 border-white rounded-full {{ $siswa->is_active ? 'bg-success-green' : 'bg-error' }}"></div>
        </div>
        <div class="flex-1 text-center md:text-left">
            <div class="flex flex-col md:flex-row md:items-center gap-2 mb-1.5">
                <h3 class="text-xl font-bold text-primary">{{ $siswa->nama_lengkap }}</h3>
                <span class="px-3 py-0.5 rounded-full text-xs font-semibold w-fit mx-auto md:mx-0 {{ $siswa->is_active ? 'bg-success-green/10 text-success-green' : 'bg-error/10 text-error' }}">{{ $siswa->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
            <div class="flex flex-wrap justify-center md:justify-start gap-4 text-xs text-on-surface-variant">
                <span>NIS: <strong>{{ $siswa->nis }}</strong></span>
                <span>NISN: <strong>{{ $siswa->nisn }}</strong></span>
                <span>Kelas: <strong>{{ $siswa->kelas?->nama_kelas ?? '-' }}</strong></span>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-surface-white p-4 rounded-xl soft-shadow border border-outline-variant/20 text-center">
            <span class="text-2xl font-bold text-primary block">{{ $totalUjian }}</span>
            <span class="text-xs text-on-surface-variant">Total Ujian</span>
        </div>
        <div class="bg-surface-white p-4 rounded-xl soft-shadow border border-outline-variant/20 text-center">
            <span class="text-2xl font-bold text-success-green block">{{ $ujianSelesai }}</span>
            <span class="text-xs text-on-surface-variant">Ujian Selesai</span>
        </div>
        <div class="bg-surface-white p-4 rounded-xl soft-shadow border border-outline-variant/20 text-center">
            <span class="text-2xl font-bold text-tertiary block">{{ $belumDikerjakan }}</span>
            <span class="text-xs text-on-surface-variant">Belum Dikerjakan</span>
        </div>
        <div class="bg-surface-white p-4 rounded-xl soft-shadow border border-outline-variant/20 text-center">
            <span class="text-2xl font-bold text-secondary block">{{ $rataNilai }}</span>
            <span class="text-xs text-on-surface-variant">Rata-rata Nilai</span>
        </div>
        <div class="bg-surface-white p-4 rounded-xl soft-shadow border border-outline-variant/20 text-center col-span-2 md:col-span-1">
            <span class="text-sm font-bold text-on-surface block mt-1">{{ $siswa->user?->last_login_at?->format('d/m H:i') ?? '-' }}</span>
            <span class="text-xs text-on-surface-variant">Terakhir Login</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 p-6 space-y-4">
            <h4 class="text-base font-bold text-primary border-b border-outline-variant/30 pb-3">Kontak & Identitas</h4>
            <div class="space-y-3 text-sm">
                <div><p class="text-xs text-outline uppercase">Email</p><p class="font-medium">{{ $siswa->user?->email }}</p></div>
                <div><p class="text-xs text-outline uppercase">Nomor HP</p><p class="font-medium">{{ $siswa->no_hp ?: '-' }}</p></div>
                <div><p class="text-xs text-outline uppercase">Jenis Kelamin</p><p class="font-medium">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p></div>
                <div><p class="text-xs text-outline uppercase">Agama</p><p class="font-medium">{{ $siswa->agama ?: '-' }}</p></div>
                <div><p class="text-xs text-outline uppercase">Kelas / Jurusan</p><p class="font-medium">{{ $siswa->kelas?->nama_kelas ?? '-' }} / {{ $siswa->jurusan?->kode_jurusan ?? '-' }}</p></div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 overflow-hidden">
            <div class="p-6 border-b border-outline-variant/30">
                <h4 class="text-base font-bold text-primary">Riwayat Ujian Siswa</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-surface-container-low text-xs font-semibold uppercase">
                        <tr>
                            <th class="px-5 py-3">Nama Ujian</th>
                            <th class="px-5 py-3">Mapel</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20 text-sm">
                        @forelse($pesertas as $p)
                            <tr>
                                <td class="px-5 py-3.5 font-medium">{{ $p->ujian?->nama_ujian }}</td>
                                <td class="px-5 py-3.5 text-text-muted">{{ $p->ujian?->mataPelajaran?->nama_mapel }}</td>
                                <td class="px-5 py-3.5"><span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-fixed text-primary">{{ ucfirst(str_replace('_', ' ', $p->status)) }}</span></td>
                                <td class="px-5 py-3.5 text-right font-bold text-primary">{{ $p->status === 'selesai' ? $p->nilai_akhir : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-text-muted">Belum ada riwayat ujian.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.guru')

@section('title', 'Ujian Saya | Ujianku')

@section('breadcrumb')
    <a href="{{ route('guru.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Ujian Saya</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-primary">Ujian Saya</h2>
            <p class="text-sm text-on-surface-variant mt-1">Kelola jadwal ujian, token masuk peserta, serta pantau progres siswa secara langsung.</p>
        </div>
        <a href="{{ route('guru.ujian.create') }}" class="h-12 px-6 bg-primary text-white rounded-lg text-sm font-semibold flex items-center gap-2 hover:bg-primary-container shadow-md">
            <span class="material-symbols-outlined">add</span>
            Buat Ujian Baru
        </a>
    </div>

    <form method="GET" action="{{ route('guru.ujian.index') }}" class="bg-surface-white p-4 rounded-xl soft-shadow border border-outline-variant/20 flex flex-wrap gap-3">
        <input name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode ujian..." class="flex-1 min-w-[200px] h-11 px-4 rounded-lg border border-outline-variant text-sm">
        <select name="status" onchange="this.form.submit()" class="h-11 px-4 rounded-lg border border-outline-variant text-sm">
            <option value="">Semua Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
        </select>
        <button type="submit" class="h-11 px-5 bg-primary text-white rounded-lg text-sm font-semibold">Cari</button>
    </form>

    <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low text-xs font-semibold text-primary uppercase">
                    <tr>
                        <th class="px-5 py-4">Nama Ujian</th>
                        <th class="px-5 py-4">Mapel & Bank Soal</th>
                        <th class="px-5 py-4">Kelas</th>
                        <th class="px-5 py-4">Durasi</th>
                        <th class="px-5 py-4">Token</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-sm">
                    @forelse($ujians as $u)
                        <tr class="hover:bg-surface-container-low/40">
                            <td class="px-5 py-4">
                                <a href="{{ route('guru.ujian.show', $u) }}" class="font-bold text-primary hover:underline">{{ $u->nama_ujian }}</a>
                                <p class="text-xs text-text-muted font-mono">{{ $u->kode_ujian }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium">{{ $u->mataPelajaran?->nama_mapel }}</p>
                                <p class="text-xs text-text-muted">{{ $u->bankSoal?->nama_bank_soal }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($u->kelas as $k)
                                        <span class="px-2 py-0.5 bg-primary-fixed/60 text-primary rounded text-xs font-semibold">{{ $k->nama_kelas }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-4">{{ $u->durasi_menit }} menit</td>
                            <td class="px-5 py-4">
                                @if($u->wajib_token)
                                    <span class="font-mono font-bold bg-secondary-fixed text-secondary px-2.5 py-1 rounded text-xs">{{ $u->token }}</span>
                                @else
                                    <span class="text-xs text-text-muted">Tanpa Token</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $u->status === 'aktif' ? 'bg-success-green/10 text-success-green' : ($u->status === 'selesai' ? 'bg-surface-dim text-on-surface-variant' : 'bg-primary-fixed text-primary') }}">
                                    {{ ucfirst($u->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end items-center gap-1.5">
                                    <form method="POST" action="{{ route('guru.ujian.toggle', $u) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg border border-outline-variant text-xs font-semibold hover:bg-surface-container">
                                            {{ $u->status === 'draft' ? 'Aktifkan' : ($u->status === 'aktif' ? 'Selesaikan' : 'Buka Draft') }}
                                        </button>
                                    </form>
                                    <a href="{{ route('guru.ujian.show', $u) }}" class="p-1.5 text-primary hover:bg-primary-fixed rounded-lg" title="Detail & Token"><span class="material-symbols-outlined text-[18px]">visibility</span></a>
                                    <a href="{{ route('guru.ujian.edit', $u) }}" class="p-1.5 text-secondary hover:bg-secondary-fixed rounded-lg" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                                    <form method="POST" action="{{ route('guru.ujian.destroy', $u) }}" onsubmit="return confirm('Hapus ujian ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-error hover:bg-error-container rounded-lg"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-text-muted">Belum ada jadwal ujian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ujians->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/20">{{ $ujians->links() }}</div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Monitoring Ujian | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Ujian</span>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl md:text-3xl font-bold text-primary">Monitoring Ujian Sekolah</h2>
        <p class="text-sm text-on-surface-variant mt-1">Pantau seluruh jadwal ujian yang dibuat oleh guru serta kontrol status pelaksanaannya.</p>
    </div>

    <form method="GET" action="{{ route('admin.ujian.index') }}" class="bg-surface-white p-4 rounded-xl soft-shadow border border-outline-variant/20 flex flex-col md:flex-row gap-4">
        <input name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode ujian..." class="flex-1 h-11 px-4 rounded-lg border border-outline-variant text-sm">
        <select name="mata_pelajaran_id" onchange="this.form.submit()" class="h-11 px-4 rounded-lg border border-outline-variant text-sm">
            <option value="">Semua Mata Pelajaran</option>
            @foreach($mapels as $m)
                <option value="{{ $m->id }}" {{ (string) request('mata_pelajaran_id') === (string) $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()" class="h-11 px-4 rounded-lg border border-outline-variant text-sm">
            <option value="">Semua Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
        </select>
        <button type="submit" class="h-11 px-5 bg-primary text-white rounded-lg text-sm font-semibold">Filter</button>
    </form>

    <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low text-xs font-semibold text-primary uppercase">
                    <tr>
                        <th class="px-5 py-4">Kode & Nama Ujian</th>
                        <th class="px-5 py-4">Mapel</th>
                        <th class="px-5 py-4">Guru</th>
                        <th class="px-5 py-4">Kelas</th>
                        <th class="px-5 py-4">Token</th>
                        <th class="px-5 py-4">Peserta</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Ubah Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-sm">
                    @forelse($ujians as $u)
                        <tr class="hover:bg-surface-container-low/40">
                            <td class="px-5 py-4">
                                <p class="font-bold text-primary">{{ $u->nama_ujian }}</p>
                                <p class="text-xs text-text-muted">{{ $u->kode_ujian }} • {{ $u->durasi_menit }} menit</p>
                            </td>
                            <td class="px-5 py-4">{{ $u->mataPelajaran?->nama_mapel }}</td>
                            <td class="px-5 py-4 text-text-muted">{{ $u->guru?->nama_lengkap }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($u->kelas as $k)
                                        <span class="px-2 py-0.5 rounded bg-surface-container text-xs">{{ $k->nama_kelas }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-4 font-mono font-bold text-secondary">{{ $u->wajib_token ? $u->token : '-' }}</td>
                            <td class="px-5 py-4 font-semibold">{{ $u->pesertas_count }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $u->status === 'aktif' ? 'bg-success-green/10 text-success-green' : ($u->status === 'selesai' ? 'bg-primary-fixed text-primary' : 'bg-surface-container text-on-surface-variant') }}">
                                    {{ ucfirst($u->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <form method="POST" action="{{ route('admin.ujian.status', $u) }}" class="inline-flex gap-1">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="h-9 px-2.5 rounded-lg border border-outline-variant text-xs">
                                        <option value="draft" {{ $u->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="aktif" {{ $u->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="selesai" {{ $u->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-text-muted">Belum ada ujian yang dijadwalkan.</td></tr>
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

@extends('layouts.guru')

@section('title', 'Detail & Monitoring Ujian | Ujianku')

@section('breadcrumb')
    <a href="{{ route('guru.ujian.index') }}" class="hover:text-primary">Ujian Saya</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">{{ $ujian->nama_ujian }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-primary">{{ $ujian->nama_ujian }}</h2>
            <p class="text-sm text-text-muted">{{ $ujian->mataPelajaran?->nama_mapel }} • Bank Soal: {{ $ujian->bankSoal?->nama_bank_soal }} ({{ $ujian->bankSoal?->soals->count() }} soal)</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('guru.hasil.show', $ujian) }}" class="h-10 px-4 bg-success-green text-white rounded-lg text-xs font-semibold flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">analytics</span> Rekap Nilai
            </a>
            <a href="{{ route('guru.ujian.edit', $ujian) }}" class="h-10 px-4 border border-secondary text-secondary rounded-lg text-xs font-semibold flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">edit</span> Edit Ujian
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20">
            <p class="text-xs text-text-muted uppercase font-semibold">Status Ujian</p>
            <p class="text-xl font-bold text-primary mt-1">{{ ucfirst($ujian->status) }}</p>
            <form method="POST" action="{{ route('guru.ujian.toggle', $ujian) }}" class="mt-3">
                @csrf @method('PATCH')
                <button type="submit" class="text-xs font-semibold text-secondary hover:underline">Ubah Status →</button>
            </form>
        </div>
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20">
            <p class="text-xs text-text-muted uppercase font-semibold">Token Akses</p>
            <p class="text-2xl font-mono font-bold text-secondary mt-1">{{ $ujian->wajib_token ? $ujian->token : 'Bebas' }}</p>
            @if($ujian->wajib_token)
                <form method="POST" action="{{ route('guru.ujian.token', $ujian) }}" class="mt-2">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">refresh</span> Acak Token Baru
                    </button>
                </form>
            @endif
        </div>
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20">
            <p class="text-xs text-text-muted uppercase font-semibold">Durasi Pengerjaan</p>
            <p class="text-2xl font-bold text-on-surface mt-1">{{ $ujian->durasi_menit }} Menit</p>
            <p class="text-xs text-text-muted mt-1">{{ $ujian->kelas->pluck('nama_kelas')->implode(', ') }}</p>
        </div>
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20">
            <p class="text-xs text-text-muted uppercase font-semibold">Total Peserta</p>
            <p class="text-2xl font-bold text-success-green mt-1">{{ $ujian->pesertas->count() }} Siswa</p>
            <p class="text-xs text-text-muted mt-1">{{ $ujian->pesertas->where('status', 'selesai')->count() }} selesai</p>
        </div>
    </div>

    <!-- Live Monitoring Peserta -->
    <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 overflow-hidden">
        <div class="px-6 py-4 border-b border-outline-variant/30 flex justify-between items-center">
            <h3 class="font-bold text-primary">Monitoring Peserta Ujian</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low text-xs font-semibold uppercase">
                    <tr>
                        <th class="px-5 py-3.5">Nama Siswa</th>
                        <th class="px-5 py-3.5">Kelas</th>
                        <th class="px-5 py-3.5">Mulai</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-center">Nilai Akhir</th>
                        <th class="px-5 py-3.5 text-right">Koreksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-sm">
                    @forelse($ujian->pesertas as $p)
                        <tr>
                            <td class="px-5 py-4 font-semibold">{{ $p->siswa?->nama_lengkap }} <span class="text-xs text-text-muted font-normal">({{ $p->siswa?->nis }})</span></td>
                            <td class="px-5 py-4">{{ $p->siswa?->kelas?->nama_kelas }}</td>
                            <td class="px-5 py-4 text-xs text-text-muted">{{ $p->mulai_at?->format('d/m H:i') ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $p->status === 'selesai' ? 'bg-success-green/10 text-success-green' : 'bg-tertiary-fixed text-tertiary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center font-bold text-primary">{{ $p->status === 'selesai' ? $p->nilai_akhir : '-' }}</td>
                            <td class="px-5 py-4 text-right">
                                @if($p->status === 'selesai' && $p->status_koreksi === 'menunggu_essay')
                                    <a href="{{ route('guru.essay.grade', $p) }}" class="px-3 py-1 bg-error text-white rounded text-xs font-semibold">Nilai Essay</a>
                                @else
                                    <span class="text-xs text-text-muted">{{ ucfirst(str_replace('_', ' ', $p->status_koreksi)) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-text-muted">Belum ada siswa yang memulai ujian ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.guru')

@section('title', 'Penilaian Essay | Ujianku')

@section('breadcrumb')
    <a href="{{ route('guru.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Penilaian Essay</span>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl md:text-3xl font-bold text-primary">Penilaian Essay Siswa</h2>
        <p class="text-sm text-on-surface-variant mt-1">Berikan skor dan catatan feedback langsung pada jawaban uraian siswa.</p>
    </div>

    <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low text-xs font-semibold text-primary uppercase">
                    <tr>
                        <th class="px-5 py-4">Siswa</th>
                        <th class="px-5 py-4">Kelas</th>
                        <th class="px-5 py-4">Ujian</th>
                        <th class="px-5 py-4">Status Koreksi</th>
                        <th class="px-5 py-4 text-center">Nilai Sementara / Akhir</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-sm">
                    @forelse($pesertas as $p)
                        <tr>
                            <td class="px-5 py-4 font-semibold text-primary">{{ $p->siswa?->nama_lengkap }}</td>
                            <td class="px-5 py-4">{{ $p->siswa?->kelas?->nama_kelas }}</td>
                            <td class="px-5 py-4">
                                <p class="font-medium">{{ $p->ujian?->nama_ujian }}</p>
                                <p class="text-xs text-text-muted">{{ $p->ujian?->mataPelajaran?->nama_mapel }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $p->status_koreksi === 'menunggu_essay' ? 'bg-error-container text-error' : 'bg-success-green/10 text-success-green' }}">
                                    {{ $p->status_koreksi === 'menunggu_essay' ? 'Menunggu Penilaian' : 'Sudah Dinilai' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center font-bold">{{ $p->nilai_akhir }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('guru.essay.grade', $p) }}" class="px-4 py-2 bg-primary text-white rounded-lg text-xs font-semibold hover:bg-primary-container">
                                    {{ $p->status_koreksi === 'menunggu_essay' ? 'Nilai Sekarang' : 'Edit Nilai' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-text-muted">Tidak ada jawaban essay yang perlu dinilai saat ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pesertas->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/20">{{ $pesertas->links() }}</div>
        @endif
    </div>
</div>
@endsection

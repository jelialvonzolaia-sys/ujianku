@extends('layouts.guru')

@section('title', 'Import Soal | Ujianku')

@section('breadcrumb')
    <a href="{{ route('guru.bank-soal.index') }}" class="hover:text-primary">Bank Soal</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('guru.bank-soal.soal.index', $bankSoal) }}" class="hover:text-primary">{{ $bankSoal->nama_bank_soal }}</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Import Soal</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl md:text-3xl font-bold text-primary">Import Soal dari File</h2>
        <p class="text-sm text-on-surface-variant mt-1">Unggah kumpulan soal Excel ke dalam paket <strong>{{ $bankSoal->nama_bank_soal }}</strong> secara massal.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('guru.bank-soal.soal.import.store', $bankSoal) }}" enctype="multipart/form-data" class="bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/20 space-y-6">
                @csrf
                <div class="border-2 border-dashed border-outline-variant rounded-xl p-10 text-center bg-surface-container-lowest">
                    <span class="material-symbols-outlined text-5xl text-secondary mb-3">cloud_upload</span>
                    <p class="text-sm font-semibold text-on-surface mb-2">Pilih File Excel (.xlsx / .xls / .csv)</p>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="mx-auto block text-sm text-on-surface-variant">
                    <p class="text-xs text-outline mt-2">Maksimal ukuran file 5MB</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-surface-container">
                    <a href="{{ route('guru.bank-soal.soal.index', $bankSoal) }}" class="px-5 py-2.5 rounded-lg border border-outline text-sm font-semibold text-on-surface-variant">Batal</a>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary-container text-white text-sm font-semibold flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">sync_alt</span>
                        Proses Import
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/20 space-y-4 h-fit">
            <div class="flex items-center gap-2 text-primary">
                <span class="material-symbols-outlined">info</span>
                <h3 class="text-base font-bold">Informasi Penting</h3>
            </div>
            <p class="text-xs text-on-surface-variant leading-relaxed">
                Pastikan kolom pada file Excel mengikuti format template resmi sistem (Jenis Soal, Pertanyaan, Opsi A–E, Kunci, Bobot).
            </p>
            <div class="p-4 bg-surface-container-low rounded-lg border border-outline-variant/30 space-y-3">
                <p class="text-xs font-bold text-primary uppercase">Download Template</p>
                <a href="{{ route('guru.soal.template') }}" class="w-full flex items-center gap-3 p-3 bg-surface-white border border-outline-variant rounded-lg hover:border-secondary transition-all">
                    <div class="w-9 h-9 bg-success-green/10 rounded flex items-center justify-center text-success-green">
                        <span class="material-symbols-outlined">table_chart</span>
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-semibold text-on-surface">Template Soal Ms.Excel</p>
                        <p class="text-[11px] text-on-surface-variant">Format .xlsx Terbaru</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

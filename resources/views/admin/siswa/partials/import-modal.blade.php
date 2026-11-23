<!-- Modal Import Data Siswa -->
<div class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 modal-overlay" id="importModal">
    <div class="bg-surface-white w-full max-w-xl rounded-2xl custom-shadow overflow-hidden">
        <div class="px-7 pt-7 pb-4 border-b border-outline-variant/30">
            <div class="flex justify-between items-start mb-1">
                <h3 class="text-xl font-bold text-primary">Import Data Siswa</h3>
                <button type="button" class="text-outline hover:text-error" onclick="document.getElementById('importModal').classList.add('hidden')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <p class="text-xs text-on-surface-variant">Unduh template Excel terlebih dahulu, isi data siswa sesuai format, kemudian unggah kembali.</p>
        </div>

        <form method="POST" action="{{ route('admin.siswa.import') }}" enctype="multipart/form-data" class="p-7 space-y-5">
            @csrf
            @if(!empty($kelasId))
                <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
            @endif

            <div class="space-y-2">
                <label class="text-sm font-semibold text-primary block">Langkah 1: Unduh Template</label>
                <a href="{{ route('admin.siswa.template') }}" class="flex items-center justify-center gap-2 w-full h-11 border-2 border-secondary text-secondary rounded-lg text-sm font-semibold hover:bg-secondary/5">
                    <span class="material-symbols-outlined text-[20px]">download</span>
                    Unduh Template Excel (.xlsx)
                </a>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-primary block">Langkah 2: Unggah File Excel</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="w-full p-3 border-2 border-dashed border-outline-variant rounded-xl text-sm bg-surface-container-lowest">
                <p class="text-[11px] text-outline">Format yang didukung: .xlsx, .xls, .csv (Maks. 5MB)</p>
            </div>

            <div class="flex gap-3 pt-3">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="flex-1 h-11 rounded-lg border border-outline-variant text-sm font-semibold text-on-surface-variant">Batal</button>
                <button type="submit" class="flex-1 h-11 bg-primary hover:bg-primary-container text-white text-sm font-semibold rounded-lg">Import Data</button>
            </div>
        </form>
    </div>
</div>

@extends('layouts.admin')

@section('title', 'Detail Kelas | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('admin.kelas.index') }}" class="hover:text-primary">Kelas</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">{{ $kelas->nama_kelas }}</span>
@endsection

@section('content')
@php $activeTab = request('tab', 'siswa'); @endphp
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <a href="{{ route('admin.kelas.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-secondary hover:underline mb-1">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Data Kelas
            </a>
            <h2 class="text-2xl md:text-3xl font-bold text-primary">Detail Kelas — {{ $kelas->nama_kelas }}</h2>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.siswa.per-kelas', $kelas) }}" class="h-11 px-5 border border-secondary text-secondary rounded-lg text-sm font-semibold flex items-center gap-2 hover:bg-secondary/5">
                <span class="material-symbols-outlined text-[18px]">group</span>
                Kelola Siswa Kelas
            </a>
            <a href="{{ route('admin.kelas.edit', $kelas) }}" class="h-11 px-5 bg-primary text-white rounded-lg text-sm font-semibold flex items-center gap-2 hover:bg-primary-container">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                Edit Kelas
            </a>
        </div>
    </div>

    <!-- Header Class Card -->
    <section class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 p-6 flex flex-col md:flex-row gap-6 items-start md:items-center">
        <div class="w-24 h-24 bg-primary-container/10 rounded-xl flex items-center justify-center text-primary shrink-0">
            <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1;">school</span>
        </div>
        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full">
            <div>
                <p class="text-xs text-on-surface-variant uppercase">Nama Kelas</p>
                <p class="text-lg font-bold text-primary">{{ $kelas->nama_kelas }}</p>
            </div>
            <div>
                <p class="text-xs text-on-surface-variant uppercase">Tahun Ajaran</p>
                <p class="text-base font-semibold">{{ $kelas->tahunAjaran?->tahun }} ({{ $kelas->tahunAjaran?->semester }})</p>
            </div>
            <div>
                <p class="text-xs text-on-surface-variant uppercase">Wali Kelas</p>
                <p class="text-base font-semibold">{{ $kelas->waliKelas?->nama_lengkap ?? '-' }}</p>
            </div>
            <div class="flex items-center justify-between sm:justify-start sm:gap-6">
                <div>
                    <p class="text-xs text-on-surface-variant uppercase">Jurusan</p>
                    <p class="text-base font-semibold">{{ $kelas->jurusan?->kode_jurusan }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $kelas->is_active ? 'bg-success-green/10 text-success-green' : 'bg-error-container text-error' }}">
                    {{ $kelas->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border-l-4 border-primary">
            <p class="text-xs font-semibold text-on-surface-variant uppercase">Total Siswa</p>
            <h3 class="text-3xl font-bold text-primary mt-1">{{ $totalSiswa }}</h3>
        </div>
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border-l-4 border-success-green">
            <p class="text-xs font-semibold text-on-surface-variant uppercase">Siswa Aktif</p>
            <h3 class="text-3xl font-bold text-success-green mt-1">{{ $siswaAktif }}</h3>
        </div>
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border-l-4 border-secondary">
            <p class="text-xs font-semibold text-on-surface-variant uppercase">Mata Pelajaran Terdaftar</p>
            <h3 class="text-3xl font-bold text-secondary mt-1">{{ $mapelsKelas->count() }}</h3>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 overflow-hidden">
        <div class="flex border-b border-outline-variant/30 px-6">
            <button type="button" onclick="switchTab('siswa')" id="btn-tab-siswa"
                    class="px-6 py-4 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'siswa' ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-primary' }}">
                Daftar Siswa ({{ $totalSiswa }})
            </button>
            <button type="button" onclick="switchTab('mapel')" id="btn-tab-mapel"
                    class="px-6 py-4 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'mapel' ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-primary' }}">
                Mata Pelajaran ({{ $mapelsKelas->count() }})
            </button>
        </div>

        <!-- Tab Daftar Siswa -->
        <div id="tab-siswa" class="p-6 space-y-5 {{ $activeTab === 'siswa' ? '' : 'hidden' }}">
            <div class="flex flex-col md:flex-row justify-between gap-4">
                <form method="GET" action="{{ route('admin.kelas.show', $kelas) }}" class="flex flex-1 gap-3">
                    <div class="relative flex-1 max-w-md">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                        <input name="search" value="{{ request('search') }}" placeholder="Cari nama siswa, NIS..." class="w-full h-11 pl-10 pr-4 bg-surface-container-low rounded-lg border border-outline-variant/50 text-sm">
                    </div>
                    <select name="sort" onchange="this.form.submit()" class="h-11 px-4 rounded-lg border border-outline-variant/50 text-sm">
                        <option value="nama_lengkap" {{ request('sort') === 'nama_lengkap' ? 'selected' : '' }}>Urutkan: Nama</option>
                        <option value="nis" {{ request('sort') === 'nis' ? 'selected' : '' }}>Urutkan: NIS</option>
                        <option value="nisn" {{ request('sort') === 'nisn' ? 'selected' : '' }}>Urutkan: NISN</option>
                    </select>
                </form>
                <button type="button" onclick="openModal('modal-tambah-siswa')" class="bg-primary hover:bg-primary-container text-white h-11 px-5 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                    Tambah Siswa ke Kelas
                </button>
            </div>

            <div class="overflow-x-auto rounded-lg border border-outline-variant/30">
                <table class="w-full text-left">
                    <thead class="bg-surface-container-low">
                        <tr>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase">No</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase">Nama Siswa</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase">NIS</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase">NISN</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase">Email</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase">Status</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20">
                        @forelse($siswas as $idx => $s)
                            <tr class="hover:bg-surface-container-low/40">
                                <td class="px-5 py-3.5 text-sm">{{ $siswas->firstItem() + $idx }}</td>
                                <td class="px-5 py-3.5 font-medium text-sm text-primary">{{ $s->nama_lengkap }}</td>
                                <td class="px-5 py-3.5 text-sm font-mono">{{ $s->nis }}</td>
                                <td class="px-5 py-3.5 text-sm font-mono">{{ $s->nisn }}</td>
                                <td class="px-5 py-3.5 text-sm text-text-muted">{{ $s->user?->email }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $s->is_active ? 'bg-success-green/10 text-success-green' : 'bg-error/10 text-error' }}">
                                        {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <div class="flex justify-center gap-1">
                                        <a href="{{ route('admin.siswa.show', $s) }}" class="p-1.5 text-primary hover:bg-primary/10 rounded"><span class="material-symbols-outlined text-[18px]">visibility</span></a>
                                        <a href="{{ route('admin.siswa.edit', $s) }}" class="p-1.5 text-secondary hover:bg-secondary/10 rounded"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-8 text-center text-sm text-text-muted">Belum ada siswa di kelas ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($siswas->hasPages())
                <div>{{ $siswas->links() }}</div>
            @endif
        </div>

        <!-- Tab Mata Pelajaran -->
        <div id="tab-mapel" class="p-6 space-y-5 {{ $activeTab === 'mapel' ? '' : 'hidden' }}">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-bold text-primary">Mata Pelajaran Terdaftar</h3>
                <button type="button" onclick="openModal('modal-atur-mapel')" class="bg-primary hover:bg-primary-container text-white h-11 px-5 rounded-lg text-sm font-semibold flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    Atur Mata Pelajaran
                </button>
            </div>

            <div class="overflow-x-auto rounded-lg border border-outline-variant/30">
                <table class="w-full text-left">
                    <thead class="bg-surface-container-low">
                        <tr>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase">No</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase">Kode Mapel</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase">Nama Mata Pelajaran</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase">Guru Pengampu</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase">Status</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20">
                        @forelse($mapelsKelas as $idx => $km)
                            <tr>
                                <td class="px-5 py-3.5 text-sm">{{ $idx + 1 }}</td>
                                <td class="px-5 py-3.5"><span class="px-2 py-0.5 rounded bg-primary-fixed text-primary text-xs font-bold">{{ $km->mataPelajaran?->kode_mapel }}</span></td>
                                <td class="px-5 py-3.5 text-sm font-semibold">{{ $km->mataPelajaran?->nama_mapel }}</td>
                                <td class="px-5 py-3.5 text-sm text-text-muted">{{ $km->guru?->nama_lengkap ?? '-' }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $km->is_active ? 'bg-success-green/10 text-success-green' : 'bg-error-container text-error' }}">
                                        {{ $km->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <form method="POST" action="{{ route('admin.kelas.mapel.destroy', [$kelas, $km]) }}" onsubmit="return confirm('Hapus mata pelajaran ini dari kelas?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-error hover:bg-error/10 rounded"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-8 text-center text-sm text-text-muted">Belum ada mata pelajaran diatur untuk kelas ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Siswa ke Kelas -->
<div id="modal-tambah-siswa" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 modal-overlay">
    <div class="bg-surface-white w-full max-w-xl rounded-2xl soft-shadow overflow-hidden">
        <div class="px-6 py-5 border-b border-outline-variant/40 flex justify-between items-start">
            <div>
                <h3 class="text-xl font-bold text-primary">Tambah Siswa - {{ $kelas->nama_kelas }}</h3>
                <p class="text-xs text-text-muted mt-0.5">Jurusan: {{ $kelas->jurusan?->kode_jurusan }} | Wali Kelas: {{ $kelas->waliKelas?->nama_lengkap ?? '-' }}</p>
            </div>
            <button type="button" onclick="closeModal('modal-tambah-siswa')" class="text-text-muted hover:text-error"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form method="POST" action="{{ route('admin.siswa.store') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
            <input type="hidden" name="jurusan_id" value="{{ $kelas->jurusan_id }}">
            <input type="hidden" name="redirect_to" value="kelas">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1">NISN <span class="text-error">*</span></label>
                    <input name="nisn" required placeholder="Masukkan NISN siswa" class="w-full h-11 px-3.5 rounded-lg border border-outline-variant text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">NIS <span class="text-error">*</span></label>
                    <input name="nis" required placeholder="Masukkan NIS siswa" class="w-full h-11 px-3.5 rounded-lg border border-outline-variant text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1">Nama Siswa <span class="text-error">*</span></label>
                    <input name="nama_lengkap" required placeholder="Masukkan nama lengkap siswa" class="w-full h-11 px-3.5 rounded-lg border border-outline-variant text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Buat Password <span class="text-error">*</span></label>
                    <input name="password" type="password" required placeholder="Min. 6 karakter" class="w-full h-11 px-3.5 rounded-lg border border-outline-variant text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Konfirmasi Password <span class="text-error">*</span></label>
                    <input name="password_confirmation" type="password" required placeholder="Ulangi password" class="w-full h-11 px-3.5 rounded-lg border border-outline-variant text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Jenis Kelamin <span class="text-error">*</span></label>
                    <select name="jenis_kelamin" required class="w-full h-11 px-3.5 rounded-lg border border-outline-variant text-sm">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Agama</label>
                    <select name="agama" class="w-full h-11 px-3.5 rounded-lg border border-outline-variant text-sm">
                        @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $ag)
                            <option value="{{ $ag }}">{{ $ag }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant/30">
                <button type="button" onclick="closeModal('modal-tambah-siswa')" class="h-11 px-6 rounded-lg border border-secondary text-secondary text-sm font-semibold">Batal</button>
                <button type="submit" class="h-11 px-6 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Siswa
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Atur Mata Pelajaran -->
<div id="modal-atur-mapel" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 modal-overlay">
    <div class="bg-surface-white w-full max-w-lg rounded-2xl soft-shadow overflow-hidden">
        <div class="px-6 py-5 border-b border-outline-variant/40 flex justify-between items-start">
            <div>
                <h3 class="text-xl font-bold text-primary">Atur Mata Pelajaran - {{ $kelas->nama_kelas }}</h3>
                <p class="text-xs text-text-muted mt-0.5">Jurusan: {{ $kelas->jurusan?->kode_jurusan }} | TA {{ $kelas->tahunAjaran?->tahun }}</p>
            </div>
            <button type="button" onclick="closeModal('modal-atur-mapel')" class="text-text-muted hover:text-error"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form method="POST" action="{{ route('admin.kelas.mapel.store', $kelas) }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-1.5">Mata Pelajaran</label>
                <select name="mata_pelajaran_id" id="subject_select" onchange="updateKodeMapel()" required class="w-full h-11 px-3.5 rounded-lg border border-outline-variant text-sm">
                    <option value="">Pilih Mata Pelajaran</option>
                    @foreach($allMapels as $m)
                        <option value="{{ $m->id }}" data-kode="{{ $m->kode_mapel }}">{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Kode Mata Pelajaran</label>
                <input id="subject_code" readonly value="-" class="w-full h-11 px-3.5 rounded-lg border border-outline-variant bg-surface-container-low text-text-muted text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Guru Pengampu</label>
                <select name="guru_id" required class="w-full h-11 px-3.5 rounded-lg border border-outline-variant text-sm">
                    <option value="">Pilih Guru</option>
                    @foreach($allGurus as $g)
                        <option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Status Mata Pelajaran</label>
                <select name="is_active" class="w-full h-11 px-3.5 rounded-lg border border-outline-variant text-sm">
                    <option value="1" selected>Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant/30">
                <button type="button" onclick="closeModal('modal-atur-mapel')" class="h-11 px-6 rounded-lg border border-secondary text-secondary text-sm font-semibold">Batal</button>
                <button type="submit" class="h-11 px-6 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Mata Pelajaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchTab(tab) {
        const siswaPanel = document.getElementById('tab-siswa');
        const mapelPanel = document.getElementById('tab-mapel');
        const btnSiswa = document.getElementById('btn-tab-siswa');
        const btnMapel = document.getElementById('btn-tab-mapel');

        if (tab === 'siswa') {
            siswaPanel.classList.remove('hidden');
            mapelPanel.classList.add('hidden');
            btnSiswa.classList.add('border-primary', 'text-primary');
            btnSiswa.classList.remove('border-transparent', 'text-on-surface-variant');
            btnMapel.classList.remove('border-primary', 'text-primary');
            btnMapel.classList.add('border-transparent', 'text-on-surface-variant');
        } else {
            mapelPanel.classList.remove('hidden');
            siswaPanel.classList.add('hidden');
            btnMapel.classList.add('border-primary', 'text-primary');
            btnMapel.classList.remove('border-transparent', 'text-on-surface-variant');
            btnSiswa.classList.remove('border-primary', 'text-primary');
            btnSiswa.classList.add('border-transparent', 'text-on-surface-variant');
        }
    }

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function updateKodeMapel() {
        const sel = document.getElementById('subject_select');
        const opt = sel.options[sel.selectedIndex];
        document.getElementById('subject_code').value = opt?.dataset?.kode || '-';
    }
</script>
@endpush

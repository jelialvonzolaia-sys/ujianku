<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\KelasMataPelajaranController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\UjianMonitorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Guru\BankSoalController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\HasilController;
use App\Http\Controllers\Guru\SoalController;
use App\Http\Controllers\Guru\UjianController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\ExamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::patch('/tahun-ajaran/{tahunAjaran}/toggle', [TahunAjaranController::class, 'toggleActive'])
            ->name('tahun-ajaran.toggle');
        Route::resource('tahun-ajaran', TahunAjaranController::class)
            ->except(['show'])
            ->parameters(['tahun-ajaran' => 'tahunAjaran']);

        Route::resource('jurusan', JurusanController::class)->except(['show']);
        Route::resource('mapel', MataPelajaranController::class)->except(['show']);

        Route::patch('/guru/{guru}/toggle', [GuruController::class, 'toggleActive'])->name('guru.toggle');
        Route::resource('guru', GuruController::class);

        Route::post('/kelas/{kelas}/mapel', [KelasMataPelajaranController::class, 'store'])
            ->name('kelas.mapel.store');
        Route::delete('/kelas/{kelas}/mapel/{kelasMataPelajaran}', [KelasMataPelajaranController::class, 'destroy'])
            ->name('kelas.mapel.destroy');
        Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);

        Route::get('/siswa/template', [SiswaController::class, 'downloadTemplate'])->name('siswa.template');
        Route::post('/siswa/import', [SiswaController::class, 'importExcel'])->name('siswa.import');
        Route::get('/siswa/kelas/{kelas}', [SiswaController::class, 'perKelas'])->name('siswa.per-kelas');
        Route::patch('/siswa/{siswa}/toggle', [SiswaController::class, 'toggleActive'])->name('siswa.toggle');
        Route::resource('siswa', SiswaController::class);

        Route::get('/ujian', [UjianMonitorController::class, 'ujianIndex'])->name('ujian.index');
        Route::patch('/ujian/{ujian}/status', [UjianMonitorController::class, 'updateStatus'])->name('ujian.status');
        Route::get('/hasil-ujian', [UjianMonitorController::class, 'hasilIndex'])->name('hasil-ujian.index');
        Route::get('/laporan', [UjianMonitorController::class, 'laporanIndex'])->name('laporan.index');
        Route::get('/laporan/export', [UjianMonitorController::class, 'exportLaporan'])->name('laporan.export');
        Route::get('/pengaturan', [UjianMonitorController::class, 'pengaturanIndex'])->name('pengaturan.index');
        Route::put('/pengaturan', [UjianMonitorController::class, 'pengaturanUpdate'])->name('pengaturan.update');
    });

Route::prefix('guru')
    ->name('guru.')
    ->middleware(['auth', 'role:guru'])
    ->group(function () {
        Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');

        Route::post('/bank-soal/{bankSoal}/duplicate', [BankSoalController::class, 'duplicate'])
            ->name('bank-soal.duplicate');
        Route::resource('bank-soal', BankSoalController::class)
            ->except(['show'])
            ->parameters(['bank-soal' => 'bankSoal']);

        Route::get('/soal/template', [SoalController::class, 'downloadTemplate'])->name('soal.template');
        Route::get('/bank-soal/{bankSoal}/soal', [SoalController::class, 'index'])->name('bank-soal.soal.index');
        Route::get('/bank-soal/{bankSoal}/soal/create', [SoalController::class, 'create'])->name('bank-soal.soal.create');
        Route::post('/bank-soal/{bankSoal}/soal', [SoalController::class, 'store'])->name('bank-soal.soal.store');
        Route::get('/bank-soal/{bankSoal}/soal/import', [SoalController::class, 'importForm'])->name('bank-soal.soal.import');
        Route::post('/bank-soal/{bankSoal}/soal/import', [SoalController::class, 'importStore'])->name('bank-soal.soal.import.store');
        Route::get('/bank-soal/{bankSoal}/soal/{soal}/edit', [SoalController::class, 'edit'])->name('bank-soal.soal.edit');
        Route::put('/bank-soal/{bankSoal}/soal/{soal}', [SoalController::class, 'update'])->name('bank-soal.soal.update');
        Route::post('/bank-soal/{bankSoal}/soal/{soal}/duplicate', [SoalController::class, 'duplicate'])->name('bank-soal.soal.duplicate');
        Route::delete('/bank-soal/{bankSoal}/soal/{soal}', [SoalController::class, 'destroy'])->name('bank-soal.soal.destroy');

        Route::post('/ujian/{ujian}/token', [UjianController::class, 'regenerateToken'])->name('ujian.token');
        Route::patch('/ujian/{ujian}/toggle', [UjianController::class, 'toggleStatus'])->name('ujian.toggle');
        Route::resource('ujian', UjianController::class);

        Route::get('/hasil', [HasilController::class, 'hasilIndex'])->name('hasil.index');
        Route::get('/hasil/{ujian}', [HasilController::class, 'hasilShow'])->name('hasil.show');
        Route::get('/hasil/{ujian}/export', [HasilController::class, 'exportExcel'])->name('hasil.export');

        Route::get('/essay', [HasilController::class, 'essayIndex'])->name('essay.index');
        Route::get('/essay/{peserta}', [HasilController::class, 'essayGrade'])->name('essay.grade');
        Route::post('/essay/{peserta}', [HasilController::class, 'essayStore'])->name('essay.store');

        Route::get('/profil', [HasilController::class, 'profilIndex'])->name('profil.index');
        Route::put('/profil', [HasilController::class, 'profilUpdate'])->name('profil.update');
    });

Route::prefix('siswa')
    ->name('siswa.')
    ->middleware(['auth', 'role:siswa'])
    ->group(function () {
        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
        Route::get('/ujian', [SiswaDashboardController::class, 'ujianTersedia'])->name('ujian.index');
        Route::get('/ujian/{ujian}/konfirmasi', [ExamController::class, 'confirm'])->name('ujian.confirm');
        Route::post('/ujian/{ujian}/mulai', [ExamController::class, 'start'])->name('ujian.start');
        Route::get('/ujian/{ujian}/kerjakan', [ExamController::class, 'run'])->name('ujian.run');
        Route::post('/ujian/{ujian}/jawab', [ExamController::class, 'saveAnswer'])->name('ujian.save-answer');
        Route::post('/ujian/{ujian}/selesai', [ExamController::class, 'finish'])->name('ujian.finish');
        Route::get('/ujian/{ujian}/hasil', [ExamController::class, 'result'])->name('ujian.result');

        Route::get('/riwayat', [SiswaDashboardController::class, 'riwayat'])->name('riwayat.index');
        Route::get('/nilai', [SiswaDashboardController::class, 'nilai'])->name('nilai.index');
        Route::get('/profil', [SiswaDashboardController::class, 'profil'])->name('profil.index');
        Route::put('/profil', [SiswaDashboardController::class, 'profilUpdate'])->name('profil.update');
    });

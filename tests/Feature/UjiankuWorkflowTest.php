<?php

namespace Tests\Feature;

use App\Models\BankSoal;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\TahunAjaran;
use App\Models\Ujian;
use App\Models\UjianPeserta;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UjiankuWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_landing_and_login_pages_render_and_support_email_nip_nis_login(): void
    {
        $this->get('/')->assertOk()->assertSee('Ujianku');
        $this->get('/login')->assertOk()->assertSee('Masuk ke Akun Ujianku');

        // Guest redirected from protected portal
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/guru/dashboard')->assertRedirect('/login');
        $this->get('/siswa/dashboard')->assertRedirect('/login');

        // Login as Admin via email
        $this->post('/login', [
            'login' => 'admin@ujianku.sch.id',
            'password' => 'password',
        ])->assertRedirect('/admin/dashboard');
        $this->post('/logout')->assertRedirect('/login');

        // Login as Guru via NIP
        $this->post('/login', [
            'login' => '198501152010011001',
            'password' => 'password',
        ])->assertRedirect('/guru/dashboard');
        $this->post('/logout')->assertRedirect('/login');

        // Login as Siswa via NIS
        $this->post('/login', [
            'login' => '2026001',
            'password' => 'password',
        ])->assertRedirect('/siswa/dashboard');

        // Role isolation guard redirects user back to their own portal
        $this->get('/admin/dashboard')->assertRedirect('/siswa/dashboard');
        $this->get('/guru/dashboard')->assertRedirect('/siswa/dashboard');
    }

    public function test_admin_pages_crud_and_exports_work(): void
    {
        $admin = User::where('email', 'admin@ujianku.sch.id')->firstOrFail();
        $this->actingAs($admin);

        $kelas = Kelas::firstOrFail();
        $guru = Guru::firstOrFail();
        $siswa = Siswa::firstOrFail();

        $adminRoutes = [
            '/admin/dashboard',
            '/admin/tahun-ajaran',
            '/admin/tahun-ajaran/create',
            '/admin/jurusan',
            '/admin/jurusan/create',
            '/admin/mapel',
            '/admin/mapel/create',
            '/admin/guru',
            '/admin/guru/create',
            "/admin/guru/{$guru->id}",
            "/admin/guru/{$guru->id}/edit",
            '/admin/kelas',
            '/admin/kelas/create',
            "/admin/kelas/{$kelas->id}",
            "/admin/kelas/{$kelas->id}/edit",
            '/admin/siswa',
            '/admin/siswa/create',
            "/admin/siswa/kelas/{$kelas->id}",
            "/admin/siswa/{$siswa->id}",
            "/admin/siswa/{$siswa->id}/edit",
            '/admin/ujian',
            '/admin/hasil-ujian',
            '/admin/laporan',
            '/admin/pengaturan',
        ];

        foreach ($adminRoutes as $uri) {
            $this->get($uri)->assertOk();
        }

        // Excel template & laporan export
        $this->get('/admin/siswa/template')
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->get('/admin/laporan/export')
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Create Jurusan
        $this->post('/admin/jurusan', [
            'kode_jurusan' => 'TKJ',
            'nama_jurusan' => 'Teknik Komputer Jaringan',
            'deskripsi' => 'Kejuruan jaringan komputer',
            'is_active' => '1',
        ])->assertRedirect('/admin/jurusan');

        $this->assertDatabaseHas('jurusans', [
            'kode_jurusan' => 'TKJ',
            'nama_jurusan' => 'Teknik Komputer Jaringan',
        ]);

        // Update Pengaturan
        $this->put('/admin/pengaturan', [
            'school_name' => 'SMA Ujianku Unggul',
            'school_npsn' => '20109988',
            'kkm_default' => 78,
            'school_address' => 'Jl. Cendekia No. 10',
            'anti_cheat_blur' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('school_settings', [
            'key' => 'school_name',
            'value' => 'SMA Ujianku Unggul',
        ]);
    }

    public function test_guru_bank_soal_soal_and_ujian_management_work(): void
    {
        $guruUser = User::where('email', 'budi@ujianku.sch.id')->firstOrFail();
        $this->actingAs($guruUser);

        $bankSoal = BankSoal::where('kode_soal', 'BS-MTK-X01')->firstOrFail();
        $ujian = Ujian::where('kode_ujian', 'UJN-MTK-001')->firstOrFail();

        $guruRoutes = [
            '/guru/dashboard',
            '/guru/bank-soal',
            '/guru/bank-soal/create',
            "/guru/bank-soal/{$bankSoal->id}/edit",
            "/guru/bank-soal/{$bankSoal->id}/soal",
            "/guru/bank-soal/{$bankSoal->id}/soal/create",
            "/guru/bank-soal/{$bankSoal->id}/soal/import",
            '/guru/ujian',
            '/guru/ujian/create',
            "/guru/ujian/{$ujian->id}",
            "/guru/ujian/{$ujian->id}/edit",
            '/guru/hasil',
            "/guru/hasil/{$ujian->id}",
            '/guru/essay',
            '/guru/profil',
        ];

        foreach ($guruRoutes as $uri) {
            $this->get($uri)->assertOk();
        }

        // Download template & export hasil
        $this->get('/guru/soal/template')->assertOk();
        $this->get("/guru/hasil/{$ujian->id}/export")->assertOk();

        // Create Bank Soal
        $mapel = MataPelajaran::where('kode_mapel', 'MAT001')->firstOrFail();
        $kelas = Kelas::where('nama_kelas', 'X IPA 1')->firstOrFail();

        $this->post('/guru/bank-soal', [
            'nama_bank_soal' => 'Bank Soal Trigonometri Lanjut',
            'kode_soal' => 'BS-MTK-TRIG',
            'mata_pelajaran_id' => $mapel->id,
            'tingkat_kelas' => 'X',
            'kelas_id' => $kelas->id,
            'materi' => 'Sinus & Cosinus',
            'mode_jawaban' => 'ABCD',
            'skala_nilai' => 'skala_100',
            'skor_tidak_menjawab' => 0,
            'continuous_mode' => '0',
            'harus_menjawab' => '0',
            'redirect_after_save' => '1',
            'acak_soal' => '1',
        ])->assertRedirect();

        $newBank = BankSoal::where('kode_soal', 'BS-MTK-TRIG')->firstOrFail();

        // Add Soal PG
        $this->post("/guru/bank-soal/{$newBank->id}/soal", [
            'nomor_soal' => 1,
            'jenis_soal' => 'pilihan_ganda',
            'pertanyaan' => 'Nilai sin 30° adalah ...',
            'bobot' => 10,
            'opsi_teks' => ['1/2', '1', '0', '1/3'],
            'opsi_skor' => [10, 0, 0, 0],
        ])->assertRedirect();

        $this->assertDatabaseHas('soals', [
            'bank_soal_id' => $newBank->id,
            'pertanyaan' => 'Nilai sin 30° adalah ...',
        ]);

        // Duplicate Bank Soal
        $this->post("/guru/bank-soal/{$newBank->id}/duplicate")->assertRedirect();
        $this->assertEquals(3, BankSoal::where('guru_id', $guruUser->guru->id)->count());

        // Create Ujian & Regenerate Token
        $this->post('/guru/ujian', [
            'nama_ujian' => 'Ujian Tengah Semester Trigonometri',
            'bank_soal_id' => $newBank->id,
            'mata_pelajaran_id' => $mapel->id,
            'kelas_ids' => [$kelas->id],
            'durasi_menit' => 90,
            'token' => 'TRIG99',
            'wajib_token' => '1',
            'status' => 'aktif',
            'tampilkan_nilai' => '1',
        ])->assertRedirect();

        $createdUjian = Ujian::where('nama_ujian', 'Ujian Tengah Semester Trigonometri')->firstOrFail();
        $this->post("/guru/ujian/{$createdUjian->id}/token")->assertRedirect();
        $this->assertNotEquals('TRIG99', $createdUjian->fresh()->token);
    }

    public function test_end_to_end_exam_execution_auto_scoring_and_essay_grading(): void
    {
        $andiUser = User::where('email', 'andi@ujianku.sch.id')->firstOrFail();
        $budiUser = User::where('email', 'budi@ujianku.sch.id')->firstOrFail();
        $ujian = Ujian::with('bankSoal.soals.opsis')->where('kode_ujian', 'UJN-MTK-001')->firstOrFail();

        $this->actingAs($andiUser);

        foreach (['/siswa/dashboard', '/siswa/ujian', '/siswa/riwayat', '/siswa/nilai', '/siswa/profil'] as $uri) {
            $this->get($uri)->assertOk();
        }

        $this->get("/siswa/ujian/{$ujian->id}/konfirmasi")->assertOk()->assertSee($ujian->nama_ujian);

        // Wrong token blocked
        $this->post("/siswa/ujian/{$ujian->id}/mulai", ['token' => 'SALAH1'])
            ->assertSessionHasErrors('token');

        // Correct token starts attempt
        $this->post("/siswa/ujian/{$ujian->id}/mulai", ['token' => 'MTK202'])
            ->assertRedirect("/siswa/ujian/{$ujian->id}/kerjakan");

        $this->get("/siswa/ujian/{$ujian->id}/kerjakan")->assertOk();

        $soals = $ujian->bankSoal->soals->keyBy('nomor_soal');

        // Soal 1 PG (mark ragu-ragu)
        $opsi1 = $soals[1]->opsis->firstWhere('is_benar', true);
        $this->postJson("/siswa/ujian/{$ujian->id}/jawab", [
            'soal_id' => $soals[1]->id,
            'soal_opsi_id' => $opsi1->id,
            'is_ragu' => true,
        ])->assertOk()->assertJson(['ok' => true, 'is_answered' => true, 'is_ragu' => true]);

        // Soal 2 PG
        $opsi2 = $soals[2]->opsis->firstWhere('is_benar', true);
        $this->postJson("/siswa/ujian/{$ujian->id}/jawab", [
            'soal_id' => $soals[2]->id,
            'soal_opsi_id' => $opsi2->id,
            'is_ragu' => false,
        ])->assertOk()->assertJson(['ok' => true, 'is_answered' => true]);

        // Soal 3 Benar/Salah
        $opsi3 = $soals[3]->opsis->firstWhere('is_benar', true);
        $this->postJson("/siswa/ujian/{$ujian->id}/jawab", [
            'soal_id' => $soals[3]->id,
            'soal_opsi_id' => $opsi3->id,
        ])->assertOk();

        // Soal 4 Isian Singkat
        $this->postJson("/siswa/ujian/{$ujian->id}/jawab", [
            'soal_id' => $soals[4]->id,
            'jawaban_teks' => '16',
        ])->assertOk();

        // Soal 5 Essay
        $this->postJson("/siswa/ujian/{$ujian->id}/jawab", [
            'soal_id' => $soals[5]->id,
            'jawaban_teks' => 'Fungsi memasangkan setiap anggota domain dengan tepat satu kodomain.',
        ])->assertOk();

        // Finish exam
        $this->post("/siswa/ujian/{$ujian->id}/selesai")
            ->assertRedirect("/siswa/ujian/{$ujian->id}/hasil");

        $peserta = UjianPeserta::where('ujian_id', $ujian->id)
            ->where('siswa_id', $andiUser->siswa->id)
            ->firstOrFail();

        $this->assertEquals('selesai', $peserta->status);
        $this->assertEquals('menunggu_essay', $peserta->status_koreksi);
        $this->assertEquals(80.0, (float) $peserta->nilai_pg);
        $this->assertEquals(80.0, (float) $peserta->nilai_akhir);

        $this->get("/siswa/ujian/{$ujian->id}/hasil")->assertOk()->assertSee('80');

        // Guru grades essay
        $this->actingAs($budiUser);
        $this->get("/guru/essay/{$peserta->id}")->assertOk();

        $essayJawaban = $peserta->jawabans()->where('soal_id', $soals[5]->id)->firstOrFail();

        $this->post("/guru/essay/{$peserta->id}", [
            'skor' => [$essayJawaban->id => 20],
            'catatan' => [$essayJawaban->id => 'Penjelasan sangat tepat dan jelas!'],
        ])->assertRedirect('/guru/essay');

        $peserta->refresh();
        $this->assertEquals('selesai', $peserta->status_koreksi);
        $this->assertEquals(20.0, (float) $peserta->nilai_essay);
        $this->assertEquals(100.0, (float) $peserta->nilai_akhir);

        // Siswa sees final score and feedback
        $this->actingAs($andiUser);
        $this->get('/siswa/nilai')
            ->assertOk()
            ->assertSee('100.0')
            ->assertSee('Penjelasan sangat tepat dan jelas!');

        $this->get('/siswa/riwayat')
            ->assertOk()
            ->assertSee($ujian->nama_ujian);
    }
}

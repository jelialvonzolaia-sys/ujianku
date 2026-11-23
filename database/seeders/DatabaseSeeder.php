<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\BankSoal;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\KelasMataPelajaran;
use App\Models\MataPelajaran;
use App\Models\SchoolSetting;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\SoalOpsi;
use App\Models\TahunAjaran;
use App\Models\Ujian;
use App\Models\UjianJawaban;
use App\Models\UjianPeserta;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        SchoolSetting::setValue('school_name', 'SMA Negeri 1 Akademika');
        SchoolSetting::setValue('school_npsn', '20109988');
        SchoolSetting::setValue('kkm_default', '75');
        SchoolSetting::setValue('school_address', 'Jl. Pendidikan Raya No. 1, Jakarta Pusat');
        SchoolSetting::setValue('anti_cheat_blur', '1');

        $taAktif = TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Genap',
            'is_active' => false,
        ]);

        $ipa = Jurusan::create([
            'kode_jurusan' => 'IPA',
            'nama_jurusan' => 'Ilmu Pengetahuan Alam',
            'deskripsi' => 'Peminatan Matematika dan Ilmu Pengetahuan Alam',
            'is_active' => true,
        ]);

        $ips = Jurusan::create([
            'kode_jurusan' => 'IPS',
            'nama_jurusan' => 'Ilmu Pengetahuan Sosial',
            'deskripsi' => 'Peminatan Ilmu Sosial dan Humaniora',
            'is_active' => true,
        ]);

        Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'deskripsi' => 'Peminatan Teknologi Informasi dan Pemrograman',
            'is_active' => true,
        ]);

        $mtk = MataPelajaran::create(['kode_mapel' => 'MAT001', 'nama_mapel' => 'Matematika Wajib', 'deskripsi' => 'Aljabar, Fungsi, dan Trigonometri']);
        $fis = MataPelajaran::create(['kode_mapel' => 'FIS001', 'nama_mapel' => 'Fisika', 'deskripsi' => 'Mekanika, Kinematika, dan Termodinamika']);
        $bio = MataPelajaran::create(['kode_mapel' => 'BIO001', 'nama_mapel' => 'Biologi', 'deskripsi' => 'Sel, Genetika, dan Ekosistem']);
        $ind = MataPelajaran::create(['kode_mapel' => 'IND001', 'nama_mapel' => 'Bahasa Indonesia', 'deskripsi' => 'Literasi, Teks Akademik, dan Tata Bahasa']);

        $adminUser = User::create([
            'name' => 'Administrator Ujianku',
            'email' => 'admin@ujianku.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $budiUser = User::create([
            'name' => 'Budi Santoso, S.Pd.',
            'email' => 'budi@ujianku.sch.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'is_active' => true,
        ]);

        $budi = Guru::create([
            'user_id' => $budiUser->id,
            'nip' => '198501152010011001',
            'nama_lengkap' => 'Budi Santoso, S.Pd.',
            'no_hp' => '081234567890',
            'tingkat_kelas' => ['X', 'XI', 'XII'],
            'is_active' => true,
        ]);

        $rinaUser = User::create([
            'name' => 'Rina Kusuma, M.Pd.',
            'email' => 'rina@ujianku.sch.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'is_active' => true,
        ]);

        $rina = Guru::create([
            'user_id' => $rinaUser->id,
            'nip' => '198804202014022003',
            'nama_lengkap' => 'Rina Kusuma, M.Pd.',
            'no_hp' => '081298765432',
            'tingkat_kelas' => ['X', 'XI'],
            'is_active' => true,
        ]);

        $kelasXIpa1 = Kelas::create([
            'nama_kelas' => 'X IPA 1',
            'tingkat' => 'X',
            'jurusan_id' => $ipa->id,
            'wali_kelas_id' => $budi->id,
            'tahun_ajaran_id' => $taAktif->id,
            'is_active' => true,
        ]);

        $kelasXIIpa1 = Kelas::create([
            'nama_kelas' => 'XII MIPA 1',
            'tingkat' => 'XII',
            'jurusan_id' => $ipa->id,
            'wali_kelas_id' => $rina->id,
            'tahun_ajaran_id' => $taAktif->id,
            'is_active' => true,
        ]);

        KelasMataPelajaran::create(['kelas_id' => $kelasXIpa1->id, 'mata_pelajaran_id' => $mtk->id, 'guru_id' => $budi->id]);
        KelasMataPelajaran::create(['kelas_id' => $kelasXIpa1->id, 'mata_pelajaran_id' => $fis->id, 'guru_id' => $rina->id]);
        KelasMataPelajaran::create(['kelas_id' => $kelasXIpa1->id, 'mata_pelajaran_id' => $ind->id, 'guru_id' => $rina->id]);
        KelasMataPelajaran::create(['kelas_id' => $kelasXIIpa1->id, 'mata_pelajaran_id' => $mtk->id, 'guru_id' => $budi->id]);
        KelasMataPelajaran::create(['kelas_id' => $kelasXIIpa1->id, 'mata_pelajaran_id' => $bio->id, 'guru_id' => $rina->id]);

        $andiUser = User::create([
            'name' => 'Andi Pratama',
            'email' => 'andi@ujianku.sch.id',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);
        $andi = Siswa::create([
            'user_id' => $andiUser->id,
            'kelas_id' => $kelasXIpa1->id,
            'jurusan_id' => $ipa->id,
            'nis' => '2026001',
            'nisn' => '0098765431',
            'nama_lengkap' => 'Andi Pratama',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'no_hp' => '085611223344',
            'is_active' => true,
        ]);

        $sitiUser = User::create([
            'name' => 'Siti Rahmawati',
            'email' => 'siti@ujianku.sch.id',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);
        $siti = Siswa::create([
            'user_id' => $sitiUser->id,
            'kelas_id' => $kelasXIpa1->id,
            'jurusan_id' => $ipa->id,
            'nis' => '2026002',
            'nisn' => '0098765432',
            'nama_lengkap' => 'Siti Rahmawati',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'no_hp' => '085622334455',
            'is_active' => true,
        ]);

        $bimaUser = User::create([
            'name' => 'Bima Nugraha',
            'email' => 'bima@ujianku.sch.id',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);
        Siswa::create([
            'user_id' => $bimaUser->id,
            'kelas_id' => $kelasXIIpa1->id,
            'jurusan_id' => $ipa->id,
            'nis' => '2026003',
            'nisn' => '0098765433',
            'nama_lengkap' => 'Bima Nugraha',
            'jenis_kelamin' => 'L',
            'agama' => 'Kristen',
            'no_hp' => '085633445566',
            'is_active' => true,
        ]);

        $bankMtk = BankSoal::create([
            'guru_id' => $budi->id,
            'mata_pelajaran_id' => $mtk->id,
            'kelas_id' => $kelasXIpa1->id,
            'kode_soal' => 'BS-MTK-X01',
            'nama_bank_soal' => 'Bank Soal Matematika Kelas X - Aljabar & Fungsi',
            'tingkat_kelas' => 'X',
            'materi' => 'Persamaan Kuadrat & Fungsi Linear',
            'mode_jawaban' => 'ABCDE',
            'acak_soal' => false,
            'acak_jawaban' => false,
            'skala_nilai' => 'skala_100',
            'skor_tidak_menjawab' => 0,
            'petunjuk_pengerjaan' => 'Bacalah setiap soal dengan teliti. Periksa kembali jawaban Anda sebelum menekan tombol Selesai Ujian.',
        ]);

        $s1 = Soal::create([
            'bank_soal_id' => $bankMtk->id,
            'nomor_soal' => 1,
            'jenis_soal' => 'pilihan_ganda',
            'pertanyaan' => 'Akar-akar persamaan kuadrat x² - 5x + 6 = 0 adalah ...',
            'bobot' => 20,
            'kunci_jawaban' => 'C',
            'pembahasan' => '(x - 2)(x - 3) = 0 sehingga x = 2 atau x = 3.',
        ]);
        $s1OpsiBenar = null;
        foreach ([
            ['A', '1 dan 6', 0, false],
            ['B', '-2 dan -3', 0, false],
            ['C', '2 dan 3', 20, true],
            ['D', '2 dan -3', 0, false],
            ['E', '-1 dan 6', 0, false],
        ] as [$label, $teks, $skor, $benar]) {
            $opt = SoalOpsi::create([
                'soal_id' => $s1->id,
                'label' => $label,
                'teks_opsi' => $teks,
                'skor' => $skor,
                'is_benar' => $benar,
            ]);
            if ($benar) {
                $s1OpsiBenar = $opt;
            }
        }

        $s2 = Soal::create([
            'bank_soal_id' => $bankMtk->id,
            'nomor_soal' => 2,
            'jenis_soal' => 'pilihan_ganda',
            'pertanyaan' => 'Diketahui fungsi f(x) = 2x + 4. Nilai dari f(3) adalah ...',
            'bobot' => 20,
            'kunci_jawaban' => 'B',
            'pembahasan' => 'f(3) = 2(3) + 4 = 10.',
        ]);
        $s2OpsiBenar = null;
        foreach ([
            ['A', '8', 0, false],
            ['B', '10', 20, true],
            ['C', '12', 0, false],
            ['D', '14', 0, false],
            ['E', '6', 0, false],
        ] as [$label, $teks, $skor, $benar]) {
            $opt = SoalOpsi::create([
                'soal_id' => $s2->id,
                'label' => $label,
                'teks_opsi' => $teks,
                'skor' => $skor,
                'is_benar' => $benar,
            ]);
            if ($benar) {
                $s2OpsiBenar = $opt;
            }
        }

        $s3 = Soal::create([
            'bank_soal_id' => $bankMtk->id,
            'nomor_soal' => 3,
            'jenis_soal' => 'benar_salah',
            'pertanyaan' => 'Gradien garis lurus dengan persamaan y = 3x - 7 adalah 3.',
            'bobot' => 20,
            'kunci_jawaban' => 'A',
            'pembahasan' => 'Bentuk eksplisit y = mx + c memiliki gradien m = 3.',
        ]);
        $s3OpsiBenar = SoalOpsi::create(['soal_id' => $s3->id, 'label' => 'A', 'teks_opsi' => 'Benar', 'skor' => 20, 'is_benar' => true]);
        SoalOpsi::create(['soal_id' => $s3->id, 'label' => 'B', 'teks_opsi' => 'Salah', 'skor' => 0, 'is_benar' => false]);

        $s4 = Soal::create([
            'bank_soal_id' => $bankMtk->id,
            'nomor_soal' => 4,
            'jenis_soal' => 'isian_singkat',
            'pertanyaan' => 'Hasil dari perpangkatan 2⁴ adalah ...',
            'bobot' => 20,
            'kunci_jawaban' => '16',
            'pembahasan' => '2 × 2 × 2 × 2 = 16.',
        ]);

        $s5 = Soal::create([
            'bank_soal_id' => $bankMtk->id,
            'nomor_soal' => 5,
            'jenis_soal' => 'essay',
            'pertanyaan' => 'Jelaskan perbedaan antara relasi dan fungsi beserta satu contoh sederhana!',
            'bobot' => 20,
            'kunci_jawaban' => 'Fungsi memasangkan setiap anggota domain dengan tepat satu anggota kodomain, sedangkan relasi tidak memiliki syarat tunggal tersebut.',
        ]);

        $bankFis = BankSoal::create([
            'guru_id' => $rina->id,
            'mata_pelajaran_id' => $fis->id,
            'kelas_id' => $kelasXIpa1->id,
            'kode_soal' => 'BS-FIS-X01',
            'nama_bank_soal' => 'Quiz Hukum Newton & Kinematika',
            'tingkat_kelas' => 'X',
            'materi' => 'Gerak Lurus dan Gaya',
            'mode_jawaban' => 'ABCD',
            'acak_soal' => false,
            'skala_nilai' => 'skala_100',
            'petunjuk_pengerjaan' => 'Pilih satu jawaban yang paling tepat pada setiap soal.',
        ]);

        $f1 = Soal::create([
            'bank_soal_id' => $bankFis->id,
            'nomor_soal' => 1,
            'jenis_soal' => 'pilihan_ganda',
            'pertanyaan' => 'Satuan Sistem Internasional (SI) untuk besaran gaya adalah ...',
            'bobot' => 50,
            'kunci_jawaban' => 'A',
        ]);
        foreach ([
            ['A', 'Newton', 50, true],
            ['B', 'Joule', 0, false],
            ['C', 'Watt', 0, false],
            ['D', 'Pascal', 0, false],
        ] as [$label, $teks, $skor, $benar]) {
            SoalOpsi::create(['soal_id' => $f1->id, 'label' => $label, 'teks_opsi' => $teks, 'skor' => $skor, 'is_benar' => $benar]);
        }

        $f2 = Soal::create([
            'bank_soal_id' => $bankFis->id,
            'nomor_soal' => 2,
            'jenis_soal' => 'pilihan_ganda',
            'pertanyaan' => 'Nilai standar percepatan gravitasi permukaan bumi mendekati ...',
            'bobot' => 50,
            'kunci_jawaban' => 'B',
        ]);
        foreach ([
            ['A', '5,6 m/s²', 0, false],
            ['B', '9,8 m/s²', 50, true],
            ['C', '12,4 m/s²', 0, false],
            ['D', '15,0 m/s²', 0, false],
        ] as [$label, $teks, $skor, $benar]) {
            SoalOpsi::create(['soal_id' => $f2->id, 'label' => $label, 'teks_opsi' => $teks, 'skor' => $skor, 'is_benar' => $benar]);
        }

        $ujianMtk = Ujian::create([
            'guru_id' => $budi->id,
            'bank_soal_id' => $bankMtk->id,
            'mata_pelajaran_id' => $mtk->id,
            'tahun_ajaran_id' => $taAktif->id,
            'kode_ujian' => 'UJN-MTK-001',
            'nama_ujian' => 'Penilaian Harian Matematika Bab 1',
            'token' => 'MTK202',
            'wajib_token' => true,
            'durasi_menit' => 60,
            'waktu_mulai' => now()->subHour(),
            'waktu_selesai' => now()->addDays(14),
            'status' => 'aktif',
            'tampilkan_nilai' => true,
            'instruksi_tambahan' => 'Gunakan token MTK202 untuk memulai ujian.',
        ]);
        $ujianMtk->kelas()->attach([$kelasXIpa1->id, $kelasXIIpa1->id]);

        $ujianFis = Ujian::create([
            'guru_id' => $rina->id,
            'bank_soal_id' => $bankFis->id,
            'mata_pelajaran_id' => $fis->id,
            'tahun_ajaran_id' => $taAktif->id,
            'kode_ujian' => 'UJN-FIS-001',
            'nama_ujian' => 'Kuis Kinematika Gerak Lurus',
            'token' => 'FIS101',
            'wajib_token' => false,
            'durasi_menit' => 45,
            'waktu_mulai' => now()->subDay(),
            'waktu_selesai' => now()->addDays(14),
            'status' => 'aktif',
            'tampilkan_nilai' => true,
        ]);
        $ujianFis->kelas()->attach([$kelasXIpa1->id]);

        // Pre-populate 1 completed attempt by Siti waiting for Budi's essay grading
        $pesertaSiti = UjianPeserta::create([
            'ujian_id' => $ujianMtk->id,
            'siswa_id' => $siti->id,
            'status' => 'selesai',
            'urutan_soal' => [$s1->id, $s2->id, $s3->id, $s4->id, $s5->id],
            'mulai_at' => now()->subMinutes(50),
            'selesai_at' => now()->subMinutes(15),
            'sisa_detik' => 0,
            'nilai_pg' => 80,
            'nilai_essay' => 0,
            'nilai_akhir' => 80,
            'status_koreksi' => 'menunggu_essay',
        ]);

        UjianJawaban::create(['ujian_peserta_id' => $pesertaSiti->id, 'soal_id' => $s1->id, 'soal_opsi_id' => $s1OpsiBenar->id, 'skor_diperoleh' => 20, 'is_dikoreksi' => true]);
        UjianJawaban::create(['ujian_peserta_id' => $pesertaSiti->id, 'soal_id' => $s2->id, 'soal_opsi_id' => $s2OpsiBenar->id, 'skor_diperoleh' => 20, 'is_dikoreksi' => true]);
        UjianJawaban::create(['ujian_peserta_id' => $pesertaSiti->id, 'soal_id' => $s3->id, 'soal_opsi_id' => $s3OpsiBenar->id, 'skor_diperoleh' => 20, 'is_dikoreksi' => true]);
        UjianJawaban::create(['ujian_peserta_id' => $pesertaSiti->id, 'soal_id' => $s4->id, 'jawaban_teks' => '16', 'skor_diperoleh' => 20, 'is_dikoreksi' => true]);
        UjianJawaban::create([
            'ujian_peserta_id' => $pesertaSiti->id,
            'soal_id' => $s5->id,
            'jawaban_teks' => 'Relasi menghubungkan himpunan A ke B secara bebas, sedangkan fungsi mengharuskan setiap anggota domain memiliki tepat satu pasangan di kodomain. Contoh: setiap siswa dipasangkan dengan satu NIS.',
            'skor_diperoleh' => 0,
            'is_dikoreksi' => false,
        ]);

        ActivityLog::create([
            'user_id' => $adminUser->id,
            'aktor_nama' => $adminUser->name,
            'aksi' => 'Sistem Diinisialisasi',
            'deskripsi' => 'Data akademik awal, akun pengguna, bank soal, dan jadwal ujian berhasil dimuat.',
            'tipe' => 'info',
            'ip_address' => '127.0.0.1',
        ]);

        ActivityLog::create([
            'user_id' => $budiUser->id,
            'aktor_nama' => $budi->nama_lengkap,
            'aksi' => 'Ujian Dipublikasikan',
            'deskripsi' => 'Penilaian Harian Matematika Bab 1 (UJN-MTK-001) diaktifkan dengan token MTK202.',
            'tipe' => 'ujian',
            'ip_address' => '127.0.0.1',
        ]);
    }
}

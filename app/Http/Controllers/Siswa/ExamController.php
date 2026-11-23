<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SchoolSetting;
use App\Models\Ujian;
use App\Models\UjianJawaban;
use App\Models\UjianPeserta;
use App\Services\ExamScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function __construct(private readonly ExamScoringService $scoringService)
    {
    }

    public function confirm(Request $request, Ujian $ujian): View|RedirectResponse
    {
        $siswa = $request->user()->siswa;
        $this->authorizeStudentClass($siswa?->kelas_id, $ujian);

        $ujian->load(['mataPelajaran', 'guru', 'bankSoal.soals']);
        $peserta = UjianPeserta::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        if ($peserta && $peserta->status === 'selesai') {
            return redirect()->route('siswa.ujian.result', $ujian);
        }

        if ($peserta && $peserta->status === 'sedang_mengerjakan') {
            return redirect()->route('siswa.ujian.run', $ujian);
        }

        return view('siswa.ujian.confirm', compact('ujian', 'siswa'));
    }

    public function start(Request $request, Ujian $ujian): RedirectResponse
    {
        $siswa = $request->user()->siswa;
        $this->authorizeStudentClass($siswa?->kelas_id, $ujian);

        if (! $ujian->isCurrentlyOpen()) {
            return back()->with('error', 'Ujian belum dibuka atau sudah berakhir.');
        }

        $existing = UjianPeserta::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        if ($existing) {
            if ($existing->status === 'selesai') {
                return redirect()->route('siswa.ujian.result', $ujian);
            }

            return redirect()->route('siswa.ujian.run', $ujian);
        }

        if ($ujian->wajib_token) {
            $request->validate(['token' => ['required', 'string']], ['token.required' => 'Token ujian wajib diisi.']);
            if (strtoupper(trim($request->input('token'))) !== strtoupper((string) $ujian->token)) {
                return back()->withInput()->withErrors(['token' => 'Token ujian tidak valid. Silakan periksa kembali kepada guru pengawas.']);
            }
        }

        $ujian->load('bankSoal.soals');
        $soalIds = $ujian->bankSoal->soals->pluck('id')->all();
        if (empty($soalIds)) {
            return back()->with('error', 'Bank soal untuk ujian ini belum memiliki pertanyaan.');
        }

        if ($ujian->bankSoal->acak_soal) {
            shuffle($soalIds);
        }

        UjianPeserta::create([
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa->id,
            'status' => 'sedang_mengerjakan',
            'urutan_soal' => $soalIds,
            'mulai_at' => now(),
            'sisa_detik' => $ujian->durasi_menit * 60,
        ]);

        ActivityLog::record($request->user(), 'Memulai ujian', $ujian->nama_ujian, 'ujian', $request->ip());

        return redirect()->route('siswa.ujian.run', $ujian);
    }

    public function run(Request $request, Ujian $ujian): View|RedirectResponse
    {
        $siswa = $request->user()->siswa;
        $this->authorizeStudentClass($siswa?->kelas_id, $ujian);

        $peserta = UjianPeserta::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        if (! $peserta) {
            return redirect()->route('siswa.ujian.confirm', $ujian);
        }
        if ($peserta->status === 'selesai') {
            return redirect()->route('siswa.ujian.result', $ujian);
        }

        $totalSeconds = $ujian->durasi_menit * 60;
        $elapsed = (int) $peserta->mulai_at->diffInSeconds(now());
        $remainingSeconds = max(0, $totalSeconds - $elapsed);

        if ($remainingSeconds <= 0) {
            $this->finalizeExam($peserta, $request);

            return redirect()->route('siswa.ujian.result', $ujian)->with('success', 'Waktu ujian telah habis. Jawaban Anda otomatis dikumpulkan.');
        }

        $ujian->load(['mataPelajaran', 'bankSoal.soals.opsis']);
        $soalMap = $ujian->bankSoal->soals->keyBy('id');
        $orderedIds = $peserta->urutan_soal ?: $ujian->bankSoal->soals->pluck('id')->all();

        $orderedSoals = collect($orderedIds)
            ->map(fn ($id) => $soalMap->get($id))
            ->filter()
            ->values();

        if ($ujian->bankSoal->acak_jawaban) {
            $orderedSoals->each(function ($soal) use ($peserta) {
                $seed = crc32($peserta->id . '-' . $soal->id);
                $opsis = $soal->opsis->all();
                mt_srand($seed);
                usort($opsis, fn () => mt_rand(-1, 1));
                mt_srand();
                $soal->setRelation('opsis', collect($opsis));
            });
        }

        $jawabans = UjianJawaban::where('ujian_peserta_id', $peserta->id)
            ->get()
            ->keyBy('soal_id');

        $antiCheatBlur = SchoolSetting::getValue('anti_cheat_blur', '1') === '1';

        return view('siswa.ujian.run', compact(
            'ujian',
            'siswa',
            'peserta',
            'orderedSoals',
            'jawabans',
            'remainingSeconds',
            'antiCheatBlur'
        ));
    }

    public function saveAnswer(Request $request, Ujian $ujian): JsonResponse
    {
        $siswa = $request->user()->siswa;
        $peserta = UjianPeserta::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa?->id)
            ->firstOrFail();

        if ($peserta->status === 'selesai') {
            return response()->json(['ok' => false, 'message' => 'Ujian sudah selesai.'], 409);
        }

        $validated = $request->validate([
            'soal_id' => ['required', 'integer'],
            'soal_opsi_id' => ['nullable', 'integer'],
            'jawaban_json' => ['nullable', 'array'],
            'jawaban_teks' => ['nullable', 'string'],
            'is_ragu' => ['nullable', 'boolean'],
        ]);

        $soal = $ujian->bankSoal->soals()->with('opsis')->where('id', $validated['soal_id'])->firstOrFail();

        $jawaban = DB::transaction(function () use ($peserta, $soal, $validated, $request) {
            $jawaban = UjianJawaban::firstOrNew([
                'ujian_peserta_id' => $peserta->id,
                'soal_id' => $soal->id,
            ]);

            if ($request->has('is_ragu')) {
                $jawaban->is_ragu = $request->boolean('is_ragu');
            }
            if ($request->exists('soal_opsi_id')) {
                $jawaban->soal_opsi_id = $validated['soal_opsi_id'];
            }
            if ($request->exists('jawaban_json')) {
                $jawaban->jawaban_json = $validated['jawaban_json'];
            }
            if ($request->exists('jawaban_teks')) {
                $jawaban->jawaban_teks = $validated['jawaban_teks'];
            }

            $jawaban->save();
            $this->scoringService->scoreObjectiveAnswer($jawaban, $soal);

            return $jawaban;
        });

        $isAnswered = $jawaban->soal_opsi_id !== null
            || ! empty($jawaban->jawaban_json)
            || trim((string) $jawaban->jawaban_teks) !== '';

        return response()->json([
            'ok' => true,
            'soal_id' => $soal->id,
            'is_answered' => $isAnswered,
            'is_ragu' => (bool) $jawaban->is_ragu,
        ]);
    }

    public function finish(Request $request, Ujian $ujian): RedirectResponse
    {
        $siswa = $request->user()->siswa;
        $peserta = UjianPeserta::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa?->id)
            ->firstOrFail();

        if ($peserta->status !== 'selesai') {
            $this->finalizeExam($peserta, $request);
        }

        return redirect()->route('siswa.ujian.result', $ujian)->with('success', 'Ujian selesai! Jawaban Anda telah disimpan.');
    }

    public function result(Request $request, Ujian $ujian): View
    {
        $siswa = $request->user()->siswa;
        $peserta = UjianPeserta::with(['ujian.mataPelajaran', 'ujian.bankSoal.soals', 'jawabans'])
            ->where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa?->id)
            ->firstOrFail();

        return view('siswa.ujian.result', compact('ujian', 'siswa', 'peserta'));
    }

    private function finalizeExam(UjianPeserta $peserta, Request $request): void
    {
        DB::transaction(function () use ($peserta, $request) {
            $peserta->update([
                'status' => 'selesai',
                'selesai_at' => now(),
                'sisa_detik' => 0,
            ]);

            $this->scoringService->recalculatePeserta($peserta->fresh());

            ActivityLog::record(
                $request->user(),
                'Menyelesaikan ujian',
                $peserta->ujian?->nama_ujian,
                'ujian',
                $request->ip()
            );
        });
    }

    private function authorizeStudentClass(?int $kelasId, Ujian $ujian): void
    {
        abort_unless(
            $kelasId && $ujian->kelas()->where('kelas.id', $kelasId)->exists(),
            403,
            'Ujian ini tidak ditujukan untuk kelas Anda.'
        );
    }
}

@extends('layouts.base')

@section('title', 'Ujianku - ' . $ujian->nama_ujian)
@section('body-class', 'bg-surface-bright text-on-surface min-h-screen')

@section('body')
<!-- Top AppBar Execution -->
<header class="fixed top-0 w-full z-50 flex justify-between items-center h-16 px-4 md:px-10 bg-surface-white shadow-[0_10px_24px_rgba(0,52,97,0.06)]">
    <div class="flex items-center gap-4 min-w-0">
        <div class="flex items-center gap-2 shrink-0">
            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">school</span>
            <h1 class="text-xl font-bold text-primary">Ujianku</h1>
        </div>
        <div class="h-6 w-px bg-outline-variant hidden sm:block"></div>
        <div class="hidden sm:flex flex-col min-w-0">
            <span class="text-sm font-semibold text-primary truncate">{{ $ujian->nama_ujian }}</span>
            <span class="text-xs text-text-muted truncate">{{ $ujian->mataPelajaran?->nama_mapel }} • {{ $siswa->nama_lengkap }}</span>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <div id="save-indicator" class="hidden md:flex items-center gap-1 text-xs text-success-green font-medium">
            <span class="material-symbols-outlined text-[16px]">cloud_done</span>
            <span id="save-indicator-text">Tersimpan</span>
        </div>
        <div id="timer-container" class="flex items-center gap-2 bg-error-container text-on-error-container px-4 py-2 rounded-xl font-mono font-bold text-sm">
            <span class="material-symbols-outlined text-[18px]">timer</span>
            <span id="exam-timer">00:00:00</span>
        </div>
    </div>
</header>

<main class="pt-24 pb-28 md:pb-16 px-4 md:px-10 max-w-[1280px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-screen">
    <!-- Left: Question Area -->
    <section class="lg:col-span-8 flex flex-col gap-6">
        <!-- Progress Bar -->
        <div class="w-full bg-surface-container-highest rounded-full h-2 overflow-hidden">
            <div id="progress-bar" class="bg-gradient-to-r from-secondary-container to-success-green h-full transition-all duration-300" style="width: 0%;"></div>
        </div>

        <!-- Questions Slides -->
        @foreach($orderedSoals as $idx => $soal)
            @php
                $jawaban = $jawabans->get($soal->id);
                $selectedOpsiId = $jawaban?->soal_opsi_id;
                $selectedJson = $jawaban?->jawaban_json ?? [];
                $jawabanTeks = $jawaban?->jawaban_teks ?? '';
                $isRagu = (bool) ($jawaban?->is_ragu ?? false);
            @endphp
            <div class="question-slide bg-surface-white p-6 md:p-8 rounded-2xl shadow-[0_8px_30px_rgba(0,52,97,0.04)] border border-outline-variant/20 {{ $idx === 0 ? '' : 'hidden' }}"
                 data-index="{{ $idx }}"
                 data-soal-id="{{ $soal->id }}"
                 data-jenis="{{ $soal->jenis_soal }}"
                 data-ragu="{{ $isRagu ? '1' : '0' }}">

                <div class="flex justify-between items-center mb-5">
                    <span class="bg-primary-fixed/50 text-primary px-3.5 py-1 rounded-full text-xs font-semibold">
                        Soal {{ $idx + 1 }} dari {{ $orderedSoals->count() }} ({{ $soal->jenis_label }})
                    </span>
                    <span class="text-xs text-text-muted font-medium">Bobot: {{ $soal->max_skor }}</span>
                </div>

                <h2 class="text-base md:text-lg font-medium text-text-main mb-6 leading-relaxed whitespace-pre-line">{{ $soal->pertanyaan }}</h2>

                @if(in_array($soal->jenis_soal, ['pilihan_ganda', 'benar_salah'], true))
                    <div class="grid grid-cols-1 gap-3.5">
                        @foreach($soal->opsis as $oIdx => $opsi)
                            @php $displayLetter = chr(65 + $oIdx); @endphp
                            <label class="cursor-pointer group block">
                                <input type="radio"
                                       name="soal_{{ $soal->id }}"
                                       value="{{ $opsi->id }}"
                                       class="sr-only peer"
                                       {{ (int) $selectedOpsiId === (int) $opsi->id ? 'checked' : '' }}
                                       onchange="saveObjective({{ $soal->id }}, {{ $opsi->id }}, {{ $idx }})">
                                <div class="flex items-center p-4 border border-outline-variant rounded-xl transition-all peer-checked:border-2 peer-checked:border-primary peer-checked:bg-primary-fixed/20 hover:border-primary hover:bg-surface-container-low">
                                    <div class="w-8 h-8 rounded-full border-2 border-outline-variant peer-checked:border-primary flex items-center justify-center mr-4 font-bold text-xs shrink-0 {{ (int) $selectedOpsiId === (int) $opsi->id ? 'bg-primary text-white border-primary' : 'text-on-surface-variant' }}"
                                         data-badge-for="{{ $soal->id }}">
                                        {{ $displayLetter }}
                                    </div>
                                    <span class="text-sm md:text-base text-text-main">{{ $opsi->teks_opsi }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @elseif($soal->jenis_soal === 'pg_kompleks')
                    <div class="grid grid-cols-1 gap-3.5">
                        <p class="text-xs text-secondary font-medium">* Anda dapat memilih lebih dari satu jawaban benar</p>
                        @foreach($soal->opsis as $oIdx => $opsi)
                            @php
                                $displayLetter = chr(65 + $oIdx);
                                $isChecked = in_array($opsi->id, $selectedJson);
                            @endphp
                            <label class="cursor-pointer group block">
                                <input type="checkbox"
                                       name="soal_multi_{{ $soal->id }}[]"
                                       value="{{ $opsi->id }}"
                                       class="sr-only peer"
                                       {{ $isChecked ? 'checked' : '' }}
                                       onchange="saveMulti({{ $soal->id }}, {{ $idx }})">
                                <div class="flex items-center p-4 border border-outline-variant rounded-xl transition-all peer-checked:border-2 peer-checked:border-primary peer-checked:bg-primary-fixed/20 hover:border-primary">
                                    <div class="w-8 h-8 rounded-lg border-2 border-outline-variant flex items-center justify-center mr-4 font-bold text-xs shrink-0">
                                        {{ $displayLetter }}
                                    </div>
                                    <span class="text-sm md:text-base text-text-main">{{ $opsi->teks_opsi }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @elseif($soal->jenis_soal === 'isian_singkat')
                    <div>
                        <label class="block text-xs font-semibold text-text-muted mb-2">Jawaban Singkat:</label>
                        <input type="text"
                               value="{{ $jawabanTeks }}"
                               placeholder="Ketik jawaban singkat Anda..."
                               oninput="debouncedTextSave({{ $soal->id }}, this.value, {{ $idx }})"
                               class="w-full h-12 px-4 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm">
                    </div>
                @else
                    <div>
                        <label class="block text-xs font-semibold text-text-muted mb-2">Jawaban Uraian / Essay:</label>
                        <textarea rows="6"
                                  placeholder="Tuliskan jawaban uraian Anda secara lengkap dan jelas..."
                                  oninput="debouncedTextSave({{ $soal->id }}, this.value, {{ $idx }})"
                                  class="w-full p-4 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm leading-relaxed">{{ $jawabanTeks }}</textarea>
                    </div>
                @endif
            </div>
        @endforeach

        <!-- Desktop Bottom Navigation -->
        <div class="hidden md:flex justify-between items-center bg-surface-white p-4 rounded-xl soft-shadow border border-outline-variant/20">
            <button type="button" id="prev-btn" onclick="prevQuestion()" class="flex items-center gap-2 px-5 py-2.5 border border-outline-variant text-primary font-semibold text-sm rounded-lg hover:bg-surface-container-low disabled:opacity-40">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Sebelumnya
            </button>

            <button type="button" id="flag-btn" onclick="toggleFlag()" class="flex items-center gap-2 px-5 py-2.5 border border-tertiary text-tertiary font-semibold text-sm rounded-lg hover:bg-tertiary hover:text-white transition-all">
                <span class="material-symbols-outlined text-[18px]" id="flag-icon">outlined_flag</span>
                <span id="flag-text">Tandai Ragu-ragu</span>
            </button>

            <button type="button" id="next-btn" onclick="nextQuestion()" class="flex items-center gap-2 px-7 py-2.5 bg-primary text-white font-semibold text-sm rounded-lg hover:bg-primary-container shadow-md">
                Selanjutnya <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </button>
        </div>
    </section>

    <!-- Right: Question Grid Navigation -->
    <aside class="lg:col-span-4 flex flex-col gap-5 h-fit">
        <div class="bg-surface-white p-6 rounded-2xl shadow-[0_8px_30px_rgba(0,52,97,0.04)] border border-outline-variant/20">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-bold text-primary">Daftar Soal</h3>
                <span class="text-xs font-semibold text-text-muted"><span id="answered-counter">0</span>/{{ $orderedSoals->count() }} Dijawab</span>
            </div>

            <div class="grid grid-cols-5 gap-2.5 max-h-[380px] overflow-y-auto custom-scrollbar pr-1" id="question-grid">
                @foreach($orderedSoals as $idx => $soal)
                    @php
                        $j = $jawabans->get($soal->id);
                        $hasAns = $j && ($j->soal_opsi_id !== null || !empty($j->jawaban_json) || trim((string) $j->jawaban_teks) !== '');
                        $hasRagu = (bool) ($j?->is_ragu ?? false);
                    @endphp
                    <button type="button"
                            onclick="goToQuestion({{ $idx }})"
                            data-grid-idx="{{ $idx }}"
                            data-answered="{{ $hasAns ? '1' : '0' }}"
                            data-ragu="{{ $hasRagu ? '1' : '0' }}"
                            class="grid-num-btn w-10 h-10 rounded-lg flex items-center justify-center font-semibold text-xs transition-all cursor-pointer">
                        {{ $idx + 1 }}
                    </button>
                @endforeach
            </div>

            <div class="mt-6 pt-5 border-t border-outline-variant/40 grid grid-cols-2 gap-3 text-xs text-text-muted">
                <div class="flex items-center gap-2"><div class="w-4 h-4 bg-primary rounded"></div><span>Dijawab</span></div>
                <div class="flex items-center gap-2"><div class="w-4 h-4 bg-tertiary-fixed-dim rounded"></div><span>Ragu-ragu</span></div>
                <div class="flex items-center gap-2"><div class="w-4 h-4 bg-surface-container-highest rounded"></div><span>Belum</span></div>
                <div class="flex items-center gap-2"><div class="w-4 h-4 border-2 border-primary rounded"></div><span>Dibuka</span></div>
            </div>

            <button type="button" onclick="openFinishModal()" class="w-full mt-6 py-3.5 bg-success-green text-white font-semibold text-sm rounded-xl hover:brightness-95 transition-all flex justify-center items-center gap-2 shadow-md cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                Selesai Ujian
            </button>
        </div>
    </aside>
</main>

<!-- Mobile Bottom Navigation -->
<nav class="md:hidden fixed bottom-0 left-0 w-full z-40 flex justify-around items-center px-4 py-3 bg-surface-white border-t border-outline-variant shadow-lg">
    <button type="button" onclick="prevQuestion()" class="flex flex-col items-center text-on-surface-variant text-xs">
        <span class="material-symbols-outlined">arrow_back</span> Sblm
    </button>
    <button type="button" onclick="toggleFlag()" class="flex flex-col items-center text-tertiary text-xs">
        <span class="material-symbols-outlined">outlined_flag</span> Ragu
    </button>
    <button type="button" onclick="openFinishModal()" class="flex flex-col items-center text-success-green text-xs font-semibold">
        <span class="material-symbols-outlined">check_circle</span> Selesai
    </button>
    <button type="button" onclick="nextQuestion()" class="flex flex-col items-center bg-primary text-white rounded-xl px-4 py-1.5 text-xs font-semibold">
        <span class="material-symbols-outlined">arrow_forward</span> Lanjut
    </button>
</nav>

<!-- Finish Confirmation Modal -->
<div class="fixed inset-0 z-[100] modal-overlay hidden items-center justify-center p-4" id="finish-modal">
    <div class="bg-surface-white w-full max-w-md rounded-2xl p-7 shadow-2xl space-y-5">
        <div class="w-14 h-14 bg-error-container text-error rounded-full flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl">warning</span>
        </div>
        <div class="text-center">
            <h3 class="text-xl font-bold text-primary mb-2">Selesai Ujian?</h3>
            <p class="text-sm text-text-muted" id="finish-modal-desc">
                Apakah Anda yakin ingin mengakhiri sesi ujian ini?
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="button" onclick="closeFinishModal()" class="flex-1 py-3 border border-outline-variant rounded-xl font-semibold text-sm text-primary hover:bg-surface-container-low">
                Lanjutkan Ujian
            </button>
            <form method="POST" action="{{ route('siswa.ujian.finish', $ujian) }}" id="finish-exam-form" class="flex-1">
                @csrf
                <button type="submit" class="w-full py-3 bg-primary text-white rounded-xl font-semibold text-sm hover:bg-primary-container">
                    Ya, Selesai
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const totalQuestions = {{ $orderedSoals->count() }};
    const saveUrl = @json(route('siswa.ujian.save-answer', $ujian));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const continuousMode = @json((bool) $ujian->bankSoal->continuous_mode);
    const harusMenjawab = @json((bool) $ujian->bankSoal->harus_menjawab);
    const antiCheatBlur = @json($antiCheatBlur);

    let currentIdx = 0;
    let remainingSeconds = {{ (int) $remainingSeconds }};
    let textDebounceTimer = null;

    function getSlide(idx) {
        return document.querySelector(`.question-slide[data-index="${idx}"]`);
    }

    function getGridBtn(idx) {
        return document.querySelector(`.grid-num-btn[data-grid-idx="${idx}"]`);
    }

    function renderGridStyles() {
        let answeredCount = 0;
        document.querySelectorAll('.grid-num-btn').forEach(btn => {
            const idx = Number(btn.dataset.gridIdx);
            const answered = btn.dataset.answered === '1';
            const ragu = btn.dataset.ragu === '1';
            if (answered) answeredCount++;

            btn.className = 'grid-num-btn w-10 h-10 rounded-lg flex items-center justify-center font-semibold text-xs transition-all cursor-pointer ';
            if (idx === currentIdx) {
                btn.className += 'border-2 border-primary text-primary bg-primary-fixed/40 shadow-sm ';
            } else if (ragu) {
                btn.className += 'bg-tertiary-fixed-dim text-on-tertiary-fixed ';
            } else if (answered) {
                btn.className += 'bg-primary text-white ';
            } else {
                btn.className += 'bg-surface-container-highest text-on-surface-variant hover:bg-outline-variant/40 ';
            }
        });

        document.getElementById('answered-counter').textContent = answeredCount;
        const pct = totalQuestions > 0 ? Math.round((answeredCount / totalQuestions) * 100) : 0;
        document.getElementById('progress-bar').style.width = `${pct}%`;

        // Sync flag button
        const curBtn = getGridBtn(currentIdx);
        const flagBtn = document.getElementById('flag-btn');
        const flagIcon = document.getElementById('flag-icon');
        const flagText = document.getElementById('flag-text');
        if (curBtn && flagBtn) {
            const isRagu = curBtn.dataset.ragu === '1';
            if (isRagu) {
                flagBtn.classList.add('bg-tertiary', 'text-white');
                flagBtn.classList.remove('text-tertiary');
                flagIcon.textContent = 'flag';
                flagText.textContent = 'Ragu-ragu (Aktif)';
            } else {
                flagBtn.classList.remove('bg-tertiary', 'text-white');
                flagBtn.classList.add('text-tertiary');
                flagIcon.textContent = 'outlined_flag';
                flagText.textContent = 'Tandai Ragu-ragu';
            }
        }

        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        if (prevBtn) prevBtn.disabled = currentIdx === 0;
        if (nextBtn) nextBtn.textContent = currentIdx === totalQuestions - 1 ? 'Selesai →' : 'Selanjutnya →';
    }

    function goToQuestion(idx) {
        if (idx < 0 || idx >= totalQuestions) return;
        if (harusMenjawab && idx > currentIdx) {
            const curGrid = getGridBtn(currentIdx);
            if (curGrid && curGrid.dataset.answered !== '1') {
                alert('Anda wajib menjawab soal ini terlebih dahulu sebelum pindah ke nomor berikutnya.');
                return;
            }
        }
        document.querySelectorAll('.question-slide').forEach(el => el.classList.add('hidden'));
        getSlide(idx)?.classList.remove('hidden');
        currentIdx = idx;
        renderGridStyles();
    }

    function prevQuestion() {
        goToQuestion(currentIdx - 1);
    }

    function nextQuestion() {
        if (currentIdx >= totalQuestions - 1) {
            openFinishModal();
        } else {
            goToQuestion(currentIdx + 1);
        }
    }

    async function sendSave(payload, idx) {
        const ind = document.getElementById('save-indicator-text');
        if (ind) ind.textContent = 'Menyimpan...';
        try {
            const res = await fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });
            const data = await res.json();
            if (data.ok) {
                const btn = getGridBtn(idx);
                if (btn) {
                    btn.dataset.answered = data.is_answered ? '1' : '0';
                    btn.dataset.ragu = data.is_ragu ? '1' : '0';
                }
                renderGridStyles();
                if (ind) ind.textContent = 'Tersimpan';
            }
        } catch (err) {
            if (ind) ind.textContent = 'Gagal menyimpan';
        }
    }

    function saveObjective(soalId, opsiId, idx) {
        // Highlight radio letter badge visually
        document.querySelectorAll(`[data-badge-for="${soalId}"]`).forEach(el => {
            el.classList.remove('bg-primary', 'text-white', 'border-primary');
        });
        const checkedInput = document.querySelector(`input[name="soal_${soalId}"][value="${opsiId}"]`);
        checkedInput?.closest('label')?.querySelector('[data-badge-for]')?.classList.add('bg-primary', 'text-white', 'border-primary');

        sendSave({ soal_id: soalId, soal_opsi_id: opsiId }, idx).then(() => {
            if (continuousMode && idx < totalQuestions - 1) {
                setTimeout(() => goToQuestion(idx + 1), 250);
            }
        });
    }

    function saveMulti(soalId, idx) {
        const checked = Array.from(document.querySelectorAll(`input[name="soal_multi_${soalId}[]"]:checked`)).map(el => Number(el.value));
        sendSave({ soal_id: soalId, jawaban_json: checked }, idx);
    }

    function debouncedTextSave(soalId, text, idx) {
        clearTimeout(textDebounceTimer);
        textDebounceTimer = setTimeout(() => {
            sendSave({ soal_id: soalId, jawaban_teks: text }, idx);
        }, 450);
    }

    function toggleFlag() {
        const slide = getSlide(currentIdx);
        const btn = getGridBtn(currentIdx);
        if (!slide || !btn) return;
        const nextRagu = btn.dataset.ragu !== '1';
        sendSave({ soal_id: Number(slide.dataset.soalId), is_ragu: nextRagu }, currentIdx);
    }

    function openFinishModal() {
        const answeredCount = document.querySelectorAll('.grid-num-btn[data-answered="1"]').length;
        const unanswered = totalQuestions - answeredCount;
        const desc = document.getElementById('finish-modal-desc');
        if (unanswered > 0) {
            desc.innerHTML = `Kamu masih memiliki <span class="font-bold text-error">${unanswered} soal</span> yang belum dijawab. Apakah kamu yakin ingin mengakhiri ujian?`;
        } else {
            desc.textContent = 'Seluruh soal telah dijawab. Apakah kamu yakin ingin mengumpulkan ujian sekarang?';
        }
        const modal = document.getElementById('finish-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeFinishModal() {
        const modal = document.getElementById('finish-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function updateTimerDisplay() {
        const h = Math.floor(remainingSeconds / 3600);
        const m = Math.floor((remainingSeconds % 3600) / 60);
        const s = remainingSeconds % 60;
        document.getElementById('exam-timer').textContent =
            `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        if (remainingSeconds < 300) {
            document.getElementById('timer-container').classList.add('animate-pulse');
        }
    }

    updateTimerDisplay();
    renderGridStyles();

    const timerInterval = setInterval(() => {
        remainingSeconds--;
        if (remainingSeconds <= 0) {
            remainingSeconds = 0;
            clearInterval(timerInterval);
            updateTimerDisplay();
            document.getElementById('finish-exam-form').submit();
            return;
        }
        updateTimerDisplay();
    }, 1000);

    if (antiCheatBlur) {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                alert('Peringatan: Harap tetap berada di halaman ujian selama sesi berlangsung!');
            }
        });
    }
</script>
@endpush

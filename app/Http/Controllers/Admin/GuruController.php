<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Guru;
use App\Models\KelasMataPelajaran;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function index(Request $request): View
    {
        $query = Guru::with('user')->latest();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'aktif');
        }

        if ($tingkat = $request->input('tingkat')) {
            $query->whereJsonContains('tingkat_kelas', $tingkat);
        }

        $gurus = $query->paginate(10)->withQueryString();
        $totalGuru = Guru::count();
        $guruAktif = Guru::where('is_active', true)->count();
        $guruNonaktif = Guru::where('is_active', false)->count();
        $mapelTerhubung = KelasMataPelajaran::distinct('mata_pelajaran_id')->count('mata_pelajaran_id');

        return view('admin.guru.index', compact(
            'gurus',
            'totalGuru',
            'guruAktif',
            'guruNonaktif',
            'mapelTerhubung'
        ));
    }

    public function create(): View
    {
        return view('admin.guru.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'nip' => ['required', 'string', 'max:30', 'unique:gurus,nip'],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'tingkat_kelas' => ['nullable', 'array'],
            'tingkat_kelas.*' => [Rule::in(['X', 'XI', 'XII'])],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        $isActive = $validated['is_active'] === '1';

        DB::transaction(function () use ($validated, $isActive, $request) {
            $user = User::create([
                'name' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'guru',
                'is_active' => $isActive,
            ]);

            Guru::create([
                'user_id' => $user->id,
                'nip' => $validated['nip'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'no_hp' => $validated['no_hp'] ?? null,
                'tingkat_kelas' => $validated['tingkat_kelas'] ?? [],
                'is_active' => $isActive,
            ]);

            ActivityLog::record($request->user(), 'Menambah akun guru', $validated['nama_lengkap'], 'admin', $request->ip());
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function show(Guru $guru): View
    {
        $guru->load(['user', 'kelasMataPelajarans.mataPelajaran', 'kelasMataPelajarans.kelas', 'bankSoals', 'ujians']);
        $recentLogs = ActivityLog::where('user_id', $guru->user_id)->latest()->take(6)->get();

        $totalUjian = $guru->ujians()->count();
        $totalBankSoal = $guru->bankSoals()->count();
        $totalMapel = $guru->kelasMataPelajarans()->distinct('mata_pelajaran_id')->count('mata_pelajaran_id');

        return view('admin.guru.show', compact('guru', 'recentLogs', 'totalUjian', 'totalBankSoal', 'totalMapel'));
    }

    public function edit(Guru $guru): View
    {
        $guru->load('user');

        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($guru->user_id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'nip' => ['required', 'string', 'max:30', Rule::unique('gurus', 'nip')->ignore($guru->id)],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'tingkat_kelas' => ['nullable', 'array'],
            'tingkat_kelas.*' => [Rule::in(['X', 'XI', 'XII'])],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        $isActive = $validated['is_active'] === '1';

        DB::transaction(function () use ($guru, $validated, $isActive) {
            $userData = [
                'name' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'is_active' => $isActive,
            ];
            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            $guru->user->update($userData);

            $guru->update([
                'nip' => $validated['nip'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'no_hp' => $validated['no_hp'] ?? null,
                'tingkat_kelas' => $validated['tingkat_kelas'] ?? [],
                'is_active' => $isActive,
            ]);
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function toggleActive(Guru $guru): RedirectResponse
    {
        $next = ! $guru->is_active;
        DB::transaction(function () use ($guru, $next) {
            $guru->update(['is_active' => $next]);
            $guru->user->update(['is_active' => $next]);
        });

        return back()->with('success', 'Status akun guru berhasil diubah.');
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        DB::transaction(function () use ($guru) {
            $user = $guru->user;
            $guru->delete();
            $user?->delete();
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil dihapus.');
    }
}

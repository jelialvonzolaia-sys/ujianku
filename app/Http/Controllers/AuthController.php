<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Email, NIP, atau NIS/NISN wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $identifier = trim($validated['login']);
        $remember = $request->boolean('remember');

        $user = User::where('email', $identifier)->first();

        if (! $user) {
            $siswa = Siswa::with('user')
                ->where('nis', $identifier)
                ->orWhere('nisn', $identifier)
                ->first();
            if ($siswa?->user) {
                $user = $siswa->user;
            }
        }

        if (! $user) {
            $guru = Guru::with('user')
                ->where('nip', $identifier)
                ->first();
            if ($guru?->user) {
                $user = $guru->user;
            }
        }

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return back()
                ->withInput($request->only('login', 'remember'))
                ->withErrors(['login' => 'Kredensial tidak sesuai. Periksa kembali Email/NIP/NIS dan password Anda.']);
        }

        if (! $user->is_active) {
            return back()
                ->withInput($request->only('login', 'remember'))
                ->withErrors(['login' => 'Akun Anda sedang dinonaktifkan oleh Administrator.']);
        }

        if ($user->role === 'guru' && $user->guru && ! $user->guru->is_active) {
            return back()
                ->withInput($request->only('login', 'remember'))
                ->withErrors(['login' => 'Akun guru Anda belum aktif. Hubungi Administrator sekolah.']);
        }

        if ($user->role === 'siswa' && $user->siswa && ! $user->siswa->is_active) {
            return back()
                ->withInput($request->only('login', 'remember'))
                ->withErrors(['login' => 'Akun siswa Anda sedang nonaktif. Hubungi Administrator sekolah.']);
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        ActivityLog::record(
            $user,
            'Login ke sistem',
            'Login sebagai '.strtoupper($user->role),
            'auth',
            $request->ip()
        );

        return $this->redirectByRole($user);
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            ActivityLog::record($user, 'Logout dari sistem', 'Keluar dari sesi '.$user->role, 'auth', $request->ip());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar dari sistem.');
    }

    private function redirectByRole(User $user): RedirectResponse
    {
        $defaultRoute = match ($user->role) {
            'admin' => route('admin.dashboard'),
            'guru' => route('guru.dashboard'),
            default => route('siswa.dashboard'),
        };

        $intended = (string) session()->pull('url.intended', '');
        $intendedPath = ltrim((string) parse_url($intended, PHP_URL_PATH), '/');

        if ($intended !== '' && str_starts_with($intendedPath, $user->role . '/')) {
            return redirect()->to($intended);
        }

        return redirect()->to($defaultRoute);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        $institution = Institution::first();
        $demoUsers = [
            [
                'role' => 'admin',
                'label' => 'Staff Pemda / Administrator',
                'identifier' => 'admin_pemda',
                'nip' => '198503152010011005',
                'name' => 'Rudi Hermawan, S.STP, M.Si',
                'color' => 'blue',
            ],
            [
                'role' => 'kepala_desa',
                'label' => 'Kepala Desa Sukamaju',
                'identifier' => 'kades_sukamaju',
                'nip' => '197906122008011012',
                'name' => 'H. Asep Sunandar, S.Sos',
                'color' => 'emerald',
            ],
            [
                'role' => 'pimpinan',
                'label' => 'Bupati / Pimpinan Daerah',
                'identifier' => '197204181997031002',
                'nip' => '197204181997031002',
                'name' => 'Dr. H. Aris Munandar, M.Si',
                'color' => 'purple',
            ],
        ];

        return view('auth.login', compact('institution', 'demoUsers'));
    }

    /**
     * Handle authentication attempt
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ], [
            'login_id.required' => 'NIP atau Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginId = trim($request->input('login_id'));
        $password = $request->input('password');

        // Find user by NIP, Username, or Email
        $user = User::where('nip', $loginId)
            ->orWhere('username', $loginId)
            ->orWhere('email', $loginId)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return back()->withInput($request->only('login_id', 'remember'))
                ->withErrors(['login_id' => 'NIP/Username atau kata sandi yang Anda masukkan salah.']);
        }

        if (!$user->is_active) {
            return back()->withInput($request->only('login_id'))
                ->withErrors(['login_id' => 'Akun Anda sedang dinonaktifkan oleh administrator. Silakan hubungi DPMD.']);
        }

        // Login the user
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Record activity log
        ActivityLog::create([
            'user_id' => $user->id,
            'village_id' => $user->village_id,
            'action' => 'LOGIN',
            'description' => $user->name . ' (' . $user->role_label . ') berhasil masuk ke dalam sistem.',
            'ip_address' => $request->ip(),
        ]);

        return $this->redirectByRole($user)->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            ActivityLog::create([
                'user_id' => $user->id,
                'village_id' => $user->village_id,
                'action' => 'LOGOUT',
                'description' => $user->name . ' keluar dari sistem.',
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Redirect user to corresponding dashboard by role
     */
    protected function redirectByRole(User $user): RedirectResponse
    {
        return match ($user->role) {
            'admin' => redirect()->route('staff.dashboard'),
            'pimpinan' => redirect()->route('pimpinan.dashboard'),
            'kepala_desa' => redirect()->route('desa.dashboard'),
            default => redirect()->route('login'),
        };
    }
}

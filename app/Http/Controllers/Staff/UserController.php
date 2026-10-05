<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('village')->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('username', 'like', "%{$s}%")
                    ->orWhere('nip', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();
        $villages = Village::where('is_active', true)->get();

        return view('staff.users.index', compact('users', 'villages'));
    }

    public function create(): View
    {
        $villages = Village::where('is_active', true)->get();
        return view('staff.users.create', compact('villages'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:100|unique:users,username',
            'nip' => 'nullable|string|max:30|unique:users,nip',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,kepala_desa,pimpinan',
            'village_id' => 'required_if:role,kepala_desa|nullable|exists:villages,id',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
        ], [
            'village_id.required_if' => 'Untuk peran Kepala Desa, desa binaan wajib dipilih.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'nip' => $request->input('nip'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
            'village_id' => $request->input('role') === 'kepala_desa' ? $request->input('village_id') : null,
            'position' => $request->input('position'),
            'phone' => $request->input('phone'),
            'is_active' => true,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $user->village_id,
            'action' => 'BUAT_PENGGUNA',
            'description' => 'Membuat akun pengguna baru: ' . $user->name . ' (' . $user->role_label . ')',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('staff.users.index')->with('success', 'Akun pengguna berhasil dibuat.');
    }

    public function edit(int $id): View
    {
        $user = User::findOrFail($id);
        $villages = Village::where('is_active', true)->get();
        return view('staff.users.edit', compact('user', 'villages'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:100|unique:users,username,' . $user->id,
            'nip' => 'nullable|string|max:30|unique:users,nip,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,kepala_desa,pimpinan',
            'village_id' => 'required_if:role,kepala_desa|nullable|exists:villages,id',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->nip = $request->input('nip');
        $user->email = $request->input('email');
        $user->role = $request->input('role');
        $user->village_id = $request->input('role') === 'kepala_desa' ? $request->input('village_id') : null;
        $user->position = $request->input('position');
        $user->phone = $request->input('phone');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $user->village_id,
            'action' => 'UBAH_PENGGUNA',
            'description' => 'Memperbarui profil pengguna ' . $user->name,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('staff.users.index')->with('success', 'Data akun pengguna berhasil diperbarui.');
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $user->village_id,
            'action' => 'STATUS_PENGGUNA',
            'description' => ($user->is_active ? 'Mengaktifkan' : 'Menonaktifkan') . ' akun pengguna ' . $user->name,
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Status akun ' . $user->name . ' berhasil diubah menjadi ' . ($user->is_active ? 'Aktif' : 'Nonaktif') . '.');
    }

    public function resetPassword(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make('password123');
        $user->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $user->village_id,
            'action' => 'RESET_PASSWORD',
            'description' => 'Mereset kata sandi akun ' . $user->name . ' ke default',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Kata sandi pengguna ' . $user->name . ' berhasil direset menjadi: password123');
    }
}

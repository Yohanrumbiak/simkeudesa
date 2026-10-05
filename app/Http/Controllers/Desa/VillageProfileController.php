<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class VillageProfileController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $village = $user->village;

        return view('desa.profile.index', compact('user', 'village'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'phone' => 'nullable|string|max:30',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ], [
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'new_password.min' => 'Kata sandi baru minimal 6 karakter.',
        ]);

        if ($request->filled('phone')) {
            $user->phone = $request->input('phone');
        }

        if ($request->filled('new_password')) {
            if (!Hash::check($request->input('current_password'), $user->password)) {
                return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
            }
            $user->password = Hash::make($request->input('new_password'));
        }

        $user->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'village_id' => $user->village_id,
            'action' => 'UPDATE_PROFIL_DESA',
            'description' => 'Kepala Desa memperbarui informasi profil / kata sandi.',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Profil dan akun berhasil diperbarui.');
    }
}

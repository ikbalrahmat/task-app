<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', [
            'user' => Auth::user()
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'department' => 'nullable|string|max:255',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->department = $request->department;
        $user->save();

        ActivityLogger::log('user.profile.updated', 'User memperbarui data profil.', $user->id);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ]
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.'
        ]);

        // Verify current password
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
        }

        $newPassword = $request->input('password');

        // Verify that the new password does not match any of the last 3 passwords (Requirement 2e)
        $historicalPasswords = DB::table('password_histories')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->pluck('password');

        foreach ($historicalPasswords as $histHash) {
            if (Hash::check($newPassword, $histHash)) {
                return back()->withErrors(['password' => 'Password baru tidak boleh sama dengan 3 password sebelumnya yang pernah digunakan.']);
            }
        }

        // Update password
        $hashedPassword = Hash::make($newPassword);
        $user->password = $hashedPassword;
        $user->password_changed_at = now();
        $user->save();

        // Record history
        DB::table('password_histories')->insert([
            'user_id' => $user->id,
            'password' => $hashedPassword,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        ActivityLogger::log('user.password.changed', 'User updated password via profile page.', $user->id);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}

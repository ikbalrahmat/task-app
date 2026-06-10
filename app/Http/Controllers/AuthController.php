<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard');

        return view('auth.login', [
            'recaptchaSiteKey' => config('services.recaptcha.site_key'),
        ]);
    }

    public function login(Request $request)
    {
        // Enforce all data inputs filled before checking (Requirement 4)
        $request->validate([
            'email'    => 'required',
            'password' => 'required',
            'g-recaptcha-response' => 'required',
        ], [
            'email.required'               => 'Kredensial login tidak valid.',
            'password.required'            => 'Kredensial login tidak valid.',
            'g-recaptcha-response.required' => 'Harap selesaikan verifikasi reCAPTCHA.',
        ]);

        $genericError = 'Kredensial yang dimasukkan tidak valid atau akun dinonaktifkan.';

        // Verify Google reCAPTCHA v2 (Requirement 3)
        $recaptchaToken    = $request->input('g-recaptcha-response');
        $recaptchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret_key'),
            'response' => $recaptchaToken,
            'remoteip' => $request->ip(),
        ]);

        if (!$recaptchaResponse->json('success')) {
            ActivityLogger::log('auth.captcha.failed', 'Login failed due to failed reCAPTCHA verification.');
            return back()->withErrors(['email' => $genericError])->onlyInput('email');
        }

        // Find user case-insensitively
        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            // Check if account is locked (Requirement 2f/2g)
            if ($user->is_locked) {
                ActivityLogger::log('auth.failed.locked', 'Attempt to log in to locked account.', $user->id);
                return back()->withErrors(['email' => $genericError])->onlyInput('email');
            }

            // Attempt login (Requirement 4 - single validation point)
            $credentials = [
                'email' => $email,
                'password' => $request->input('password')
            ];

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                // Login Success
                $user->login_attempts = 0;
                $user->save();

                ActivityLogger::log('auth.login.success', 'User logged in successfully.', $user->id);

                // Terminate concurrent sessions (Requirement 22)
                Auth::logoutOtherDevices($request->input('password'));

                $request->session()->regenerate();

                return redirect()->intended(route('dashboard'))
                    ->with('success', 'Selamat datang, ' . $user->name . '!');
            } else {
                // Login Failed - Increment attempts
                $user->login_attempts += 1;
                
                if ($user->login_attempts >= 3) {
                    $user->is_locked = true;
                    ActivityLogger::log('auth.lockout', 'Account locked due to 3 consecutive failed login attempts.', $user->id);
                } else {
                    ActivityLogger::log('auth.login.failed', "Failed login attempt {$user->login_attempts} of 3.", $user->id);
                }
                
                $user->save();
                return back()->withErrors(['email' => $genericError])->onlyInput('email');
            }
        }

        // Timing attack defense for non-existent users
        Hash::check('dummy-password', '$2y$12$DummyHashForTimingAttackDefenseOnlyDoNotUseInProd');
        ActivityLogger::log('auth.login.failed', 'Failed login attempt for non-existent email: ' . $email);

        return back()->withErrors(['email' => $genericError])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLogger::log('auth.logout', 'User logged out.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Berhasil keluar.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email'], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);
        
        // Log request password reset
        ActivityLogger::log('auth.password.reset_request', 'Reset password link requested for: ' . $request->input('email'));
        
        return back()->with('info', 'Jika email terdaftar, link reset password telah dikirim.');
    }



    // Password Change views and processing (Requirement 2b/2d/2e)
    public function showChangePassword()
    {
        if (!Auth::check()) return redirect()->route('login');
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        if (!Auth::check()) return redirect()->route('login');

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
        $historicalPasswords = \Illuminate\Support\Facades\DB::table('password_histories')
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
        \Illuminate\Support\Facades\DB::table('password_histories')->insert([
            'user_id' => $user->id,
            'password' => $hashedPassword,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        ActivityLogger::log('user.password.changed', 'User updated password and history was updated.', $user->id);

        return redirect()->route('dashboard')->with('success', 'Password berhasil diperbarui.');
    }
}

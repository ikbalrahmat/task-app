<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SecurityRequirementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard roles
        $this->admin = User::create([
            'name'                => 'Admin Security',
            'email'               => 'admin@security.com',
            'password'            => Hash::make('AdminSec#2026'),
            'role'                => 'Admin',
            'password_changed_at' => now(),
        ]);

        $this->user = User::create([
            'name'                => 'John Doe',
            'email'               => 'john@security.com',
            'password'            => Hash::make('JohnDoe#2026'),
            'role'                => 'Anggota Tim',
            'password_changed_at' => now(),
        ]);
    }

    /**
     * Helper: fake a successful reCAPTCHA response from Google.
     */
    private function fakeRecaptchaSuccess(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
            ], 200),
        ]);
    }

    /**
     * Helper: fake a failed reCAPTCHA response from Google.
     */
    private function fakeRecaptchaFailure(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success'     => false,
                'error-codes' => ['invalid-input-response'],
            ], 200),
        ]);
    }

    /** @test */
    public function login_requires_correct_captcha_and_returns_generic_error()
    {
        // 1. Submit without reCAPTCHA token → validation required error
        $response = $this->post(route('login'), [
            'email'    => 'john@security.com',
            'password' => 'JohnDoe#2026',
            // g-recaptcha-response intentionally omitted
        ]);
        $response->assertSessionHasErrors(['g-recaptcha-response']);

        // 2. Submit with a bad reCAPTCHA token → Google returns success:false
        $this->fakeRecaptchaFailure();
        $response = $this->post(route('login'), [
            'email'                => 'john@security.com',
            'password'             => 'JohnDoe#2026',
            'g-recaptcha-response' => 'bad-token',
        ]);
        $response->assertSessionHasErrors(['email']);
        $this->assertEquals(
            'Kredensial yang dimasukkan tidak valid atau akun dinonaktifkan.',
            session('errors')->first('email')
        );

        // 3. Submit with valid reCAPTCHA but wrong password → fails gracefully
        $this->fakeRecaptchaSuccess();
        $response = $this->post(route('login'), [
            'email'                => 'john@security.com',
            'password'             => 'WrongPass123!',
            'g-recaptcha-response' => 'valid-token',
        ]);
        $response->assertSessionHasErrors(['email']);
        $this->assertEquals(
            'Kredensial yang dimasukkan tidak valid atau akun dinonaktifkan.',
            session('errors')->first('email')
        );
    }

    /** @test */
    public function account_is_locked_after_3_failed_login_attempts_and_can_be_unlocked_by_admin()
    {
        // Fail 1
        $this->fakeRecaptchaSuccess();
        $this->post(route('login'), [
            'email'                => 'john@security.com',
            'password'             => 'WrongPass!',
            'g-recaptcha-response' => 'valid-token',
        ]);
        $this->assertEquals(1, $this->user->fresh()->login_attempts);

        // Fail 2
        $this->fakeRecaptchaSuccess();
        $this->post(route('login'), [
            'email'                => 'john@security.com',
            'password'             => 'WrongPass!',
            'g-recaptcha-response' => 'valid-token',
        ]);
        $this->assertEquals(2, $this->user->fresh()->login_attempts);
        $this->assertFalse($this->user->fresh()->is_locked);

        // Fail 3 → triggers lockout
        $this->fakeRecaptchaSuccess();
        $this->post(route('login'), [
            'email'                => 'john@security.com',
            'password'             => 'WrongPass!',
            'g-recaptcha-response' => 'valid-token',
        ]);
        $this->assertEquals(3, $this->user->fresh()->login_attempts);
        $this->assertTrue($this->user->fresh()->is_locked);

        // Try to log in with correct credentials while locked → fails
        $this->fakeRecaptchaSuccess();
        $response = $this->post(route('login'), [
            'email'                => 'john@security.com',
            'password'             => 'JohnDoe#2026',
            'g-recaptcha-response' => 'valid-token',
        ]);
        $response->assertSessionHasErrors(['email']);
        $this->assertTrue($this->user->fresh()->is_locked);

        // Admin unlocks the user
        $response = $this->actingAs($this->admin)->post(route('users.unlock', $this->user->id));
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertFalse($this->user->fresh()->is_locked);
        $this->assertEquals(0, $this->user->fresh()->login_attempts);
    }

    /** @test */
    public function passwords_must_comply_with_complexity_rules()
    {
        // Try creating user with weak password
        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'name'                  => 'Weak Password User',
            'email'                 => 'weak@security.com',
            'password'              => '12345678',
            'password_confirmation' => '12345678',
            'role'                  => 'Anggota Tim',
        ]);
        $response->assertSessionHasErrors(['password']);

        // Try creating user with complex password
        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'name'                  => 'Strong Password User',
            'email'                 => 'strong@security.com',
            'password'              => 'Stro#ngPass99!',
            'password_confirmation' => 'Stro#ngPass99!',
            'role'                  => 'Anggota Tim',
        ]);
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'strong@security.com']);
    }

    /** @test */
    public function force_password_change_middleware_redirects_correctly()
    {
        $this->user->password_changed_at = null;
        $this->user->save();

        $this->withSession(['test_force_password_change' => true]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertRedirect(route('change-password'));
    }

    /** @test */
    public function password_history_prevents_reusing_last_3_passwords()
    {
        $user = $this->user;

        \Illuminate\Support\Facades\DB::table('password_histories')->insert([
            ['user_id' => $user->id, 'password' => Hash::make('Original#123'), 'created_at' => now()],
        ]);

        // 1. Change password first time
        $response = $this->actingAs($user)->post(route('change-password.post'), [
            'current_password'      => 'JohnDoe#2026',
            'password'              => 'SecondPass#99!',
            'password_confirmation' => 'SecondPass#99!',
        ]);
        $response->assertRedirect(route('dashboard'));

        // 2. Change password second time
        $response = $this->actingAs($user)->post(route('change-password.post'), [
            'current_password'      => 'SecondPass#99!',
            'password'              => 'ThirdPass#88!',
            'password_confirmation' => 'ThirdPass#88!',
        ]);
        $response->assertRedirect(route('dashboard'));

        // 3. Attempt to reuse one of the last 3 passwords
        $response = $this->actingAs($user)->post(route('change-password.post'), [
            'current_password'      => 'ThirdPass#88!',
            'password'              => 'SecondPass#99!', // reused!
            'password_confirmation' => 'SecondPass#99!',
        ]);
        $response->assertSessionHasErrors(['password']);
    }
}

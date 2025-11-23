<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ==================== REGISTRATION ====================

    /** @test - Halaman register dapat diakses oleh guest */
    public function guest_can_view_register_page()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    /** @test - Guest dapat melakukan registrasi dengan data valid */
    public function guest_can_register()
    {
        $response = $this->post(route('register'), [
            'nama_lengkap' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'alamat' => 'Jl. Test No. 123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('pengguna', [
            'email' => 'test@example.com',
            'role' => 'student',
        ]);
    }

    /** @test - Registrasi gagal jika data tidak valid*/
    public function registration_requires_valid_data()
    {
        $response = $this->post(route('register'), [
            'nama_lengkap' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456',
        ]);

        $response->assertSessionHasErrors(['nama_lengkap', 'email', 'password']);
    }

    /** @test - Email harus unik saat registrasi */
    public function registration_requires_unique_email()
    {
        // Buat user dengan email yang sudah ada
        Pengguna::factory()->create(['email' => 'existing@test.com']);

        $response = $this->post(route('register'), [
            'nama_lengkap' => 'Test User',
            'email' => 'existing@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ==================== LOGIN ====================

    /** @test - Halaman login dapat diakses oleh guest*/
    public function guest_can_view_login_page()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /** @test - User dapat login dengan kredensial yang benar*/
    public function user_can_login_with_valid_credentials()
    {
        $user = Pengguna::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test - Admin diarahkan ke admin dashboard setelah login*/
    public function admin_redirected_to_admin_dashboard_after_login()
    {
        $admin = Pengguna::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    /** @test - Login gagal dengan password salah*/
    public function user_cannot_login_with_invalid_credentials()
    {
        Pengguna::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test - Login gagal dengan email yang tidak terdaftar */
    public function user_cannot_login_with_nonexistent_email()
    {
        $response = $this->post(route('login'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // ==================== LOGOUT ====================

    /** @test - User yang sudah login dapat logout*/
    public function authenticated_user_can_logout()
    {
        $user = Pengguna::factory()->create();

        // Simulasi sebagai user yang login
        $response = $this->actingAs($user)
            ->post(route('logout'));

        $response->assertRedirect('/');
        // Cek sudah logout
        $this->assertGuest();
    }

    // ==================== MIDDLEWARE ====================

    /** @test - Guest tidak bisa akses dashboard*/
    public function guest_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');

        // Redirect ke login
        $response->assertRedirect(route('login'));
    }

    /** @test - Guest tidak bisa akses route admin*/
    public function guest_cannot_access_admin_routes()
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('login'));
    }

    /** @test - Student tidak bisa akses route admin*/
    public function student_cannot_access_admin_routes()
    {
        $student = Pengguna::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)
            ->get('/admin/dashboard');

        // Forbidden
        $response->assertStatus(403);
    }

    /** @test - Admin dapat mengakses route admin*/
    public function admin_can_access_admin_routes()
    {
        $admin = Pengguna::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    /** @test - User yang sudah login tidak bisa akses halaman login*/
    public function authenticated_user_cannot_access_login_page()
    {
        $user = Pengguna::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('login'));

        // Redirect ke dashboard
        $response->assertRedirect('/dashboard');
    }
}

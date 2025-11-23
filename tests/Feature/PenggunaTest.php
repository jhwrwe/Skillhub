<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenggunaTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Pengguna::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
        ]);
    }

    // ==================== CRUD PESERTA ====================

    /** @test - Admin dapat melihat daftar peserta*/
    public function admin_can_view_peserta_index()
    {
        Pengguna::factory()->count(5)->create(['role' => 'student']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.peserta.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.peserta.index');
    }

    /** @test - Admin dapat melihat form tambah peserta*/
    public function admin_can_view_create_peserta_form()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.peserta.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.peserta.create');
    }

    /** @test - Admin dapat menambah peserta baru*/
    public function admin_can_create_peserta()
    {
        $pesertaData = [
            'nama_lengkap' => 'Test Peserta',
            'email' => 'peserta@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'alamat' => 'Jl. Test No. 123',
            'role' => 'student',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.peserta.store'), $pesertaData);

        $response->assertRedirect(route('admin.peserta.index'));
        $this->assertDatabaseHas('pengguna', ['email' => 'peserta@test.com']);
    }

    /** @test - Admin dapat melihat detail peserta*/
    public function admin_can_view_peserta_detail()
    {
        $peserta = Pengguna::factory()->create(['role' => 'student']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.peserta.show', $peserta));

        $response->assertStatus(200);
        $response->assertViewIs('admin.peserta.show');
    }

    /** @test - Admin dapat mengupdate data peserta*/
    public function admin_can_update_peserta()
    {
        $peserta = Pengguna::factory()->create(['role' => 'student']);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.peserta.update', $peserta), [
                'nama_lengkap' => 'Updated Name',
                'email' => $peserta->email,
                'alamat' => 'New Address',
                'role' => 'student',
            ]);

        $response->assertRedirect(route('admin.peserta.index'));
        $this->assertDatabaseHas('pengguna', ['nama_lengkap' => 'Updated Name']);
    }

    /** @test - Admin dapat mengupdate password peserta*/
    public function admin_can_update_peserta_password()
    {
        $peserta = Pengguna::factory()->create(['role' => 'student']);
        $oldPassword = $peserta->password;

        $response = $this->actingAs($this->admin)
            ->put(route('admin.peserta.update', $peserta), [
                'nama_lengkap' => $peserta->nama_lengkap,
                'email' => $peserta->email,
                'alamat' => $peserta->alamat,
                'role' => 'student',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertRedirect(route('admin.peserta.index'));
        // Cek password berubah
        $this->assertNotEquals($oldPassword, $peserta->fresh()->password);
    }

    /** @test - Admin dapat menghapus peserta*/
    public function admin_can_delete_peserta()
    {
        $peserta = Pengguna::factory()->create(['role' => 'student']);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.peserta.destroy', $peserta));

        $response->assertRedirect(route('admin.peserta.index'));
        $this->assertDatabaseMissing('pengguna', ['id' => $peserta->id]);
    }

    /** @test - Admin tidak bisa menghapus akun sendiri*/
    public function admin_cannot_delete_own_account()
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.peserta.destroy', $this->admin));

        $response->assertSessionHas('error');
        // Cek admin masih ada di database
        $this->assertDatabaseHas('pengguna', ['id' => $this->admin->id]);
    }

    /** @test - Email peserta harus unik*/
    public function peserta_email_must_be_unique()
    {
        Pengguna::factory()->create(['email' => 'existing@test.com']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.peserta.store'), [
                'nama_lengkap' => 'Test',
                // Email sudah ada
                'email' => 'existing@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'student',
            ]);

        $response->assertSessionHasErrors('email');
    }

    // ==================== PENDAFTARAN PESERTA KE KELAS ====================

    /** @test - Admin dapat mendaftarkan peserta ke kelas*/
    public function admin_can_register_peserta_to_kelas()
    {
        $peserta = Pengguna::factory()->create(['role' => 'student']);
        $kelas = Kelas::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.peserta.daftar-kelas', $peserta), [
                'kelas_id' => $kelas->id,
            ]);

        $response->assertRedirect();
        // Cek relasi many-to-many berhasil
        $this->assertTrue($peserta->fresh()->kelas->contains($kelas->id));
    }

    /** @test - Admin dapat membatalkan pendaftaran peserta dari kelas*/
    public function admin_can_cancel_peserta_registration()
    {
        $peserta = Pengguna::factory()->create(['role' => 'student']);
        $kelas = Kelas::factory()->create();
        // Daftarkan dulu
        $peserta->kelas()->attach($kelas->id, ['registration_date' => now()]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.peserta.batal-kelas', [$peserta, $kelas]));

        $response->assertRedirect();
        // Cek sudah tidak terdaftar
        $this->assertFalse($peserta->fresh()->kelas->contains($kelas->id));
    }

    /** @test - Tidak bisa mendaftarkan peserta ke kelas yang sama dua kali*/
    public function cannot_register_peserta_to_same_kelas_twice()
    {
        $peserta = Pengguna::factory()->create(['role' => 'student']);
        $kelas = Kelas::factory()->create();
        // Daftarkan pertama kali
        $peserta->kelas()->attach($kelas->id, ['registration_date' => now()]);

        // Coba daftarkan lagi
        $response = $this->actingAs($this->admin)
            ->post(route('admin.peserta.daftar-kelas', $peserta), [
                'kelas_id' => $kelas->id,
            ]);

        $response->assertSessionHas('error');
    }

    /** @test - Relasi peserta ke kelas berfungsi dengan benar*/
    public function peserta_kelas_relationship_works()
    {
        $peserta = Pengguna::factory()->create(['role' => 'student']);
        $kelas = Kelas::factory()->count(3)->create();
        // Daftarkan ke 3 kelas
        $peserta->kelas()->attach($kelas->pluck('id'), ['registration_date' => now()]);
        // Cek peserta punya 3 kelas
        $this->assertCount(3, $peserta->fresh()->kelas);
    }

    /** @test  Relasi kelas ke pengguna berfungsi dengan benar*/
    public function kelas_pengguna_relationship_works()
    {
        $kelas = Kelas::factory()->create();
        $peserta = Pengguna::factory()->count(5)->create(['role' => 'student']);
        // Daftarkan 5 peserta ke kelas
        $kelas->pengguna()->attach($peserta->pluck('id'), ['registration_date' => now()]);
        // Cek kelas punya 5 peserta
        $this->assertCount(5, $kelas->fresh()->pengguna);
    }
}

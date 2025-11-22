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

    /** @test */
    public function admin_can_view_peserta_index()
    {
        Pengguna::factory()->count(5)->create(['role' => 'student']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.peserta.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.peserta.index');
    }

    /** @test */
    public function admin_can_view_create_peserta_form()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.peserta.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.peserta.create');
    }

    /** @test */
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

    /** @test */
    public function admin_can_view_peserta_detail()
    {
        $peserta = Pengguna::factory()->create(['role' => 'student']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.peserta.show', $peserta));

        $response->assertStatus(200);
        $response->assertViewIs('admin.peserta.show');
    }

    /** @test */
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

    /** @test */
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
        $this->assertNotEquals($oldPassword, $peserta->fresh()->password);
    }

    /** @test */
    public function admin_can_delete_peserta()
    {
        $peserta = Pengguna::factory()->create(['role' => 'student']);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.peserta.destroy', $peserta));

        $response->assertRedirect(route('admin.peserta.index'));
        $this->assertDatabaseMissing('pengguna', ['id' => $peserta->id]);
    }

    /** @test */
    public function admin_cannot_delete_own_account()
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.peserta.destroy', $this->admin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('pengguna', ['id' => $this->admin->id]);
    }

    /** @test */
    public function peserta_email_must_be_unique()
    {
        Pengguna::factory()->create(['email' => 'existing@test.com']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.peserta.store'), [
                'nama_lengkap' => 'Test',
                'email' => 'existing@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'student',
            ]);

        $response->assertSessionHasErrors('email');
    }

    // ==================== PENDAFTARAN PESERTA KE KELAS ====================

    /** @test */
    public function admin_can_register_peserta_to_kelas()
    {
        $peserta = Pengguna::factory()->create(['role' => 'student']);
        $kelas = Kelas::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.peserta.daftar-kelas', $peserta), [
                'kelas_id' => $kelas->id,
            ]);

        $response->assertRedirect();
        $this->assertTrue($peserta->fresh()->kelas->contains($kelas->id));
    }

    /** @test */
    public function admin_can_cancel_peserta_registration()
    {
        $peserta = Pengguna::factory()->create(['role' => 'student']);
        $kelas = Kelas::factory()->create();
        $peserta->kelas()->attach($kelas->id, ['registration_date' => now()]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.peserta.batal-kelas', [$peserta, $kelas]));

        $response->assertRedirect();
        $this->assertFalse($peserta->fresh()->kelas->contains($kelas->id));
    }

    /** @test */
    public function cannot_register_peserta_to_same_kelas_twice()
    {
        $peserta = Pengguna::factory()->create(['role' => 'student']);
        $kelas = Kelas::factory()->create();
        $peserta->kelas()->attach($kelas->id, ['registration_date' => now()]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.peserta.daftar-kelas', $peserta), [
                'kelas_id' => $kelas->id,
            ]);

        $response->assertSessionHas('error');
    }

    /** @test */
    public function peserta_kelas_relationship_works()
    {
        $peserta = Pengguna::factory()->create(['role' => 'student']);
        $kelas = Kelas::factory()->count(3)->create();

        $peserta->kelas()->attach($kelas->pluck('id'), ['registration_date' => now()]);

        $this->assertCount(3, $peserta->fresh()->kelas);
    }

    /** @test */
    public function kelas_pengguna_relationship_works()
    {
        $kelas = Kelas::factory()->create();
        $peserta = Pengguna::factory()->count(5)->create(['role' => 'student']);

        $kelas->pengguna()->attach($peserta->pluck('id'), ['registration_date' => now()]);

        $this->assertCount(5, $kelas->fresh()->pengguna);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class KelasTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Pengguna::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
        ]);

        $this->student = Pengguna::factory()->create([
            'role' => 'student',
            'email' => 'student@test.com',
        ]);
    }

    // ==================== CRUD KELAS ====================

    /** @test */
    public function admin_can_view_kelas_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.kelas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.kelas.index');
    }

    /** @test */
    public function admin_can_create_kelas()
    {
        $kelasData = [
            'nama_kelas' => 'Laravel Testing',
            'deskripsi' => 'Belajar testing dengan PHPUnit',
            'instruktor' => 'John Doe',
            'waktu_mulai' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
            'waktu_selesai' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.kelas.store'), $kelasData);

        $response->assertRedirect(route('admin.kelas.index'));
        $this->assertDatabaseHas('kelas', ['nama_kelas' => 'Laravel Testing']);
    }

    /** @test */
    public function admin_can_update_kelas()
    {
        $kelas = Kelas::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put(route('admin.kelas.update', $kelas), [
                'nama_kelas' => 'Updated Kelas Name',
                'deskripsi' => 'Updated description',
                'instruktor' => 'Jane Doe',
                'waktu_mulai' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
                'waktu_selesai' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect(route('admin.kelas.index'));
        $this->assertDatabaseHas('kelas', ['nama_kelas' => 'Updated Kelas Name']);
    }

    /** @test */
    public function admin_can_delete_kelas()
    {
        $kelas = Kelas::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.kelas.destroy', $kelas));

        $response->assertRedirect(route('admin.kelas.index'));
        $this->assertDatabaseMissing('kelas', ['id' => $kelas->id]);
    }

    /** @test */
    public function kelas_requires_valid_dates()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.kelas.store'), [
                'nama_kelas' => 'Test Kelas',
                'instruktor' => 'Test Instructor',
                'waktu_mulai' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
                'waktu_selesai' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
            ]);

        $response->assertSessionHasErrors('waktu_selesai');
    }

    /** @test */
    public function kelas_status_calculated_as_upcoming()
    {
        $kelas = Kelas::factory()->create([
            'waktu_mulai' => Carbon::now()->addDays(5),
            'waktu_selesai' => Carbon::now()->addDays(10),
        ]);

        $this->assertEquals('upcoming', $kelas->calculateStatus());
    }

    /** @test */
    public function kelas_status_calculated_as_ongoing()
    {
        $kelas = Kelas::factory()->create([
            'waktu_mulai' => Carbon::now()->subDays(1),
            'waktu_selesai' => Carbon::now()->addDays(5),
        ]);

        $this->assertEquals('ongoing', $kelas->calculateStatus());
    }

    /** @test */
    public function kelas_status_calculated_as_completed()
    {
        $kelas = Kelas::factory()->create([
            'waktu_mulai' => Carbon::now()->subDays(10),
            'waktu_selesai' => Carbon::now()->subDays(5),
        ]);

        $this->assertEquals('completed', $kelas->calculateStatus());
    }

    // ==================== STUDENT KELAS ====================

    /** @test */
    public function student_can_view_available_kelas()
    {
        Kelas::factory()->count(3)->create();

        $response = $this->actingAs($this->student)
            ->get(route('kelas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('kelas.index');
    }

    /** @test */
    public function student_can_join_kelas()
    {
        $kelas = Kelas::factory()->create([
            'waktu_mulai' => Carbon::now()->addDays(1),
            'waktu_selesai' => Carbon::now()->addDays(5),
        ]);

        $response = $this->actingAs($this->student)
            ->post(route('kelas.join', $kelas));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Cek langsung ke pivot table
        $this->assertDatabaseHas('pengguna_kelas', [
            'pengguna_id' => $this->student->id,
            'kelas_id' => $kelas->id,
        ]);
    }

    /** @test */
    public function student_cannot_join_completed_kelas()
    {
        $kelas = Kelas::factory()->create([
            'waktu_mulai' => Carbon::now()->subDays(10),
            'waktu_selesai' => Carbon::now()->subDays(5),
        ]);

        $response = $this->actingAs($this->student)
            ->post(route('kelas.join', $kelas));

        $response->assertSessionHas('error');
        $this->assertFalse($this->student->fresh()->kelas->contains($kelas->id));
    }

    /** @test */
    public function student_can_leave_kelas()
    {
        $kelas = Kelas::factory()->create();
        $this->student->kelas()->attach($kelas->id, ['registration_date' => now()]);

        $response = $this->actingAs($this->student)
            ->post(route('kelas.leave', $kelas));

        $response->assertRedirect();
        $this->assertFalse($this->student->fresh()->kelas->contains($kelas->id));
    }

    /** @test */
    public function student_can_view_my_kelas()
    {
        $kelas = Kelas::factory()->count(2)->create();
        $this->student->kelas()->attach($kelas->pluck('id'), ['registration_date' => now()]);

        $response = $this->actingAs($this->student)
            ->get(route('kelas.my'));

        $response->assertStatus(200);
        $response->assertViewIs('kelas.my-kelas');
    }
}

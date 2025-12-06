<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\KKN;
use App\Models\Mahasiswa;
use App\Models\EvaluasiMahasiswa;
use App\Models\EvaluasiMahasiswaDetail;
use App\Models\KriteriaMonev;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class EvaluasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_evaluasi_page()
    {
        // Create role first
        $role = Role::factory()->create(['id' => 1, 'nama' => 'Admin']);

        // Create admin user
        $admin = User::factory()->create();
        $admin->userRoles()->create(['id_role' => 1]); // Assuming role 1 is Admin

        // Create KKN
        $kkn = KKN::factory()->create();

        // Create mahasiswa
        $mahasiswa = Mahasiswa::factory()->create(['id_kkn' => $kkn->id]);

        // Create kriteria monev
        $kriteria = KriteriaMonev::factory()->create(['id_kkn' => $kkn->id]);

        // Act as admin
        $this->actingAs($admin);

        // Test accessing evaluasi page
        $response = $this->get(route('admin.evaluasi.index', ['kkn_id' => $kkn->id]));

        $response->assertStatus(200);
        $response->assertViewHas('kkn');
        $response->assertViewHas('mahasiswa');
        $response->assertViewHas('kriteriaList');
    }

    public function test_admin_can_store_evaluasi()
    {
        // Create role first
        $role = Role::factory()->create(['id' => 1, 'nama' => 'Admin']);

        // Create admin user
        $admin = User::factory()->create();
        $admin->userRoles()->create(['id_role' => 1]);

        // Create KKN
        $kkn = KKN::factory()->create();

        // Create mahasiswa
        $mahasiswa = Mahasiswa::factory()->create(['id_kkn' => $kkn->id]);

        // Create kriteria monev
        $kriteria = KriteriaMonev::factory()->create(['id_kkn' => $kkn->id]);

        // Act as admin
        $this->actingAs($admin);

        // Test storing evaluasi
        $data = [
            'kkn_id' => $kkn->id,
            'evaluasi' => [
                $mahasiswa->id => [
                    $kriteria->id => '3' // Good score
                ]
            ]
        ];

        $response = $this->post(route('admin.evaluasi.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check if data was stored
        $this->assertDatabaseHas('evaluasi_mahasiswa', [
            'id_mahasiswa' => $mahasiswa->id
        ]);

        $this->assertDatabaseHas('evaluasi_mahasiswa_detail', [
            'id_kriteria_monev' => $kriteria->id,
            'nilai' => 3
        ]);
    }

    public function test_evaluasi_export_works()
    {
        // Create admin user
        $admin = User::factory()->create();
        $admin->userRoles()->create(['id_role' => 1]);

        // Create KKN
        $kkn = KKN::factory()->create();

        // Create mahasiswa
        $mahasiswa = Mahasiswa::factory()->create(['id_kkn' => $kkn->id]);

        // Create evaluasi
        $evaluasi = EvaluasiMahasiswa::factory()->create(['id_mahasiswa' => $mahasiswa->id]);

        // Act as admin
        $this->actingAs($admin);

        // Test export
        $response = $this->get(route('admin.evaluasi.export', ['kkn_id' => $kkn->id]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}

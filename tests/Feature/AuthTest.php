<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Dosen;
use App\Models\Dpl;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials_redirects_to_dpl_dashboard()
    {
        // Create role
        $role = Role::factory()->create(['nama' => 'DPL']);

        // Create user with email Jackson@gmail.test and password default
        $user = User::factory()->create([
            'email' => 'Jackson@gmail.test',
            'password' => Hash::make('default'),
        ]);

        // Assign role to user
        $user->userRoles()->create(['id_role' => $role->id]);

        // Create dosen and assign as DPL
        $dosen = Dosen::factory()->create(['id_user' => $user->id]);
        Dpl::factory()->create(['id_dosen' => $dosen->id]);

        // Test login
        $response = $this->post(route('login'), [
            'email' => 'Jackson@gmail.test',
            'password' => 'default',
        ]);

        // Assert redirect to dashboard
        $response->assertRedirect(route('dashboard'));

        // Assert user is authenticated
        $this->assertAuthenticatedAs($user);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LppmAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mencari atau membuat user LPPM
        $user = User::firstOrCreate(
            ['email' => 'lppm@uad.ac.id'],
            [
                'nama' => 'Admin LPPM UAD',
                'password' => bcrypt('12341234'),
            ]
        );

        // Mencari role Admin
        $role = Role::where('nama_role', 'Admin')->first();

        // Pastikan role ditemukan sebelum menghubungkan relasi
        if ($role) {
            UserRole::firstOrCreate([
                'id_user' => $user->id,
                'id_role' => $role->id,
            ]);
        } else {
            throw new \Exception('Role "Admin" not found. Silakan jalankan RoleSeeder terlebih dahulu.');
        }
    }
}
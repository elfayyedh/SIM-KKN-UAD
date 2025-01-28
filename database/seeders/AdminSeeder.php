<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mencari atau membuat user dengan email admin@gmail.test
        $user = User::firstOrCreate(
            ['email' => 'gemilangtirto2002@gmail.com'],
            [
                'nama' => 'User Admin',
                'password' => bcrypt('12341234'),
            ]
        );

        // Mencari role Admin
        $role = Role::where('nama_role', 'Admin')->first();

        // Pastikan role ditemukan sebelum melanjutkan
        if ($role) {
            // Membuat entry pada tabel userRole
            UserRole::create([
                'id_user' => $user->id,
                'id_role' => $role->id,
            ]);
        } else {
            // Optionally, handle the case where the role is not found
            // For example, throw an exception or log an error
            throw new \Exception('Role "Admin" not found.');
        }
    }
}

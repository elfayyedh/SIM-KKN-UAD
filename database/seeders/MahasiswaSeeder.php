<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'nama' => 'Nagita',
            'jenis_kelamin' => 'P',
            'email' => 'mhs@gmail.test',
            'password' => bcrypt('12341234'),

        ]);
    }
}

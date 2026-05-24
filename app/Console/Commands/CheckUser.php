<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use App\Models\Dosen;
use App\Models\TimMonev;
use App\Models\Dpl;
use App\Models\KKN;
use Illuminate\Console\Command;

class CheckUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if Jackson user exists and create if not';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for Jackson user...');

        $user = User::where('email', 'Jackson@gmail.test')->first();

        if ($user) {
            $this->info('User found: ' . $user->nama);
            $this->info('Email: ' . $user->email);
            $this->info('Password hash: ' . $user->password);

            // Check if password is correct
            if (\Hash::check('jackson', $user->password)) {
                $this->info('Password verification: CORRECT');
            } else {
                $this->error('Password verification: INCORRECT');
            }

            // Check roles
            $roles = $user->userRoles()->with('role')->get();
            $this->info('Roles:');
            foreach ($roles as $role) {
                $this->info('- ' . $role->role->nama_role);
            }

        } else {
            $this->warn('User not found. Creating Jackson user...');

            // Create roles if they don't exist
            $role_admin = Role::firstOrCreate(['nama_role' => 'Admin']);
            $role_monev = Role::firstOrCreate(['nama_role' => 'Tim Monev']);
            $role_dpl = Role::firstOrCreate(['nama_role' => 'DPL']);
            $role_mhs = Role::firstOrCreate(['nama_role' => 'Mahasiswa']);

            // Create KKN if it doesn't exist
            $kkn = KKN::firstOrCreate(
                ['nama' => 'KKN Alternatif 119', 'thn_ajaran' => '2025/2026'],
                [
                    'tanggal_mulai' => '2025-07-19',
                    'tanggal_selesai' => '2026-03-31',
                    'tanggal_cutoff_penilaian' => '2026-01-01',
                    'status' => 1,
                ]
            );

            // Create user
            $user = User::firstOrCreate(
                ['email' => 'Jackson@gmail.test'],
                [
                    'password' => bcrypt('jackson'),
                    'nama' => 'Jackson',
                    'jenis_kelamin' => 'L',
                    'no_telp' => '0832632323',
                ]
            );

            // Create user roles
            UserRole::firstOrCreate(
                ['id_user' => $user->id, 'id_role' => $role_dpl->id],
                ['id_kkn' => $kkn->id]
            );

            UserRole::firstOrCreate(
                ['id_user' => $user->id, 'id_role' => $role_monev->id],
                ['id_kkn' => $kkn->id]
            );

            // Create dosen
            $dosen = Dosen::firstOrCreate(
                ['nip' => '757568776'],
                ['id_user' => $user->id]
            );

            // Create DPL and Tim Monev assignments
            Dpl::firstOrCreate(
                ['id_dosen' => $dosen->id, 'id_kkn' => $kkn->id]
            );

            TimMonev::firstOrCreate(
                ['id_dosen' => $dosen->id, 'id_kkn' => $kkn->id]
            );

            $this->info('User Jackson created successfully!');
            $this->info('Email: Jackson@gmail.test');
            $this->info('Password: jackson');
        }
    }
}

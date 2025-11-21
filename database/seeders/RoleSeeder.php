<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\TimMonev;
use App\Models\Dpl;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\KKN;
use App\Models\Lokasi;
use App\Models\Mahasiswa;
use App\Models\Prodi; 
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {        
        $role_admin = Role::firstOrCreate(['nama_role' => 'Admin']);
        $role_monev = Role::firstOrCreate(['nama_role' => 'Tim Monev']); 
        $role_dpl = Role::firstOrCreate(['nama_role' => 'DPL']);
        $role_mhs = Role::firstOrCreate(['nama_role' => 'Mahasiswa']);

        $user_mahasiswa = User::firstOrCreate(
            ['email' => 'Nagita@gmail.test'],
            [
                'password' => bcrypt('nagita'),
                'nama' => 'Nagita',
                'jenis_kelamin' => 'P',
                'no_telp' => '08376326723',
            ]
        );

        $user_dosen_jackson = User::firstOrCreate(
            ['email' => 'Jackson@gmail.test'],
            [
                'password' => bcrypt('jackson'),
                'nama' => 'Jackson',
                'jenis_kelamin' => 'L',
                'no_telp' => '0832632323',
            ]
        );
        $kkn = KKN::firstOrCreate(
            ['nama' => 'KKN Reguler 119', 'thn_ajaran' => '2025/2026'],
            [
                'tanggal_mulai' => '2025-07-19',
                'tanggal_selesai' => '2026-03-31',
                'status' => 1,
            ]
        );
        
        $user_role_mahasiswa = UserRole::firstOrCreate(
            ['id_user' => $user_mahasiswa->id, 'id_role' => $role_mhs->id],
            ['id_kkn' => $kkn->id]
        );

        $user_role_dpl = UserRole::firstOrCreate(
            ['id_user' => $user_dosen_jackson->id, 'id_role' => $role_dpl->id],
            ['id_kkn' => $kkn->id]
        );

        $user_role_monev = UserRole::firstOrCreate(
            ['id_user' => $user_dosen_jackson->id, 'id_role' => $role_monev->id],
            ['id_kkn' => $kkn->id]
        );


        $dosen_jackson = Dosen::firstOrCreate(
            ['nip' => '757568776'], 
            [
                'id_user' => $user_dosen_jackson->id 
            ] 
        );
        
        $dpl_assignment = Dpl::firstOrCreate(
            ['id_dosen' => $dosen_jackson->id, 'id_kkn' => $kkn->id]
        );

        $monev_assignment = TimMonev::firstOrCreate(
            ['id_dosen' => $dosen_jackson->id, 'id_kkn' => $kkn->id]
        );

        $prodi = Prodi::firstOrCreate(
            ['nama_prodi' => 'Informatika']
        );

        $kabupaten = Kabupaten::firstOrCreate(['nama' => 'Bantul']);
        $kecamatan = Kecamatan::firstOrCreate(
            ['nama' => 'Kretek', 'id_kabupaten' => $kabupaten->id]
        );
        $lokasi = Lokasi::firstOrCreate(
            ['nama' => 'Busuran, Donotirto, Kretek'],
            [
                'link_lokasi' => 'https://maps.app.goo.gl/2MggxbQj2b7uyAyDA',
                'id_kecamatan' => $kecamatan->id
            ]
        );

        $unit = Unit::firstOrCreate(
            ['nama' => 'XXI.A.1'],
            [
                'id_dpl' => $dpl_assignment->id, 
                'tanggal_penerjunan' => '2025-07-22',
                'tanggal_penarikan' => '2026-03-31',
                'id_lokasi' => $lokasi->id,
                'id_kkn' => $kkn->id,
            ]
        );

        $mahasiswa = Mahasiswa::firstOrCreate(
            ['nim' => '2340952390'],
            [
                'id_user_role' => $user_role_mahasiswa->id,
                'id_prodi' => $prodi->id,
                'id_unit' => $unit->id,
                'id_kkn' => $kkn->id,
            ]
        );
    }
}
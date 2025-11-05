<?php

namespace App\Jobs;

use App\Models\QueueProgress;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dpl;
use App\Models\Dosen; // 👈 1. PASTIKAN INI DI-IMPORT
use App\Models\Unit;
use App\Models\Prodi;
use App\Models\Lokasi;
use App\Models\Kecamatan;
use App\Models\Kabupaten;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntriDataKKN implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jsonData;
    protected $id_kkn;
    protected $progress;

    /**
     * Create a new job instance.
     *
     * @param array $jsonData
     */
    public function __construct($jsonData, $id_kkn, $progress)
    {
        $this->jsonData = $jsonData;
        $this->id_kkn = $id_kkn;
        $this->progress = $progress;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $totalSteps = 0;
        try {
            foreach ($this->jsonData as $dplData) {
                $totalSteps++; 
                foreach ($dplData['unit'] as $unitData) {
                    $totalSteps++; 
                    $totalSteps += count($unitData['anggota']); 
                }
            }
            $currentStep = 0;
            QueueProgress::where('id', $this->progress)->update([
                'total' => $totalSteps,
                'status' => 'in_progress',
                'message' => 'Processing started'
            ]);

            try {
                foreach ($this->jsonData as $dplData) {
                    $namaLengkapDpl = $dplData['DPL']; 
                    $emailDpl = $dplData['email']; 
                    $nipDpl = $dplData['password']; 
                    $passwordDefault = 'password'; 
                    $user = User::firstOrCreate([
                        'email' => $emailDpl,
                    ], [
                        'nama' => $namaLengkapDpl,
                        'password' => bcrypt($passwordDefault),
                    ]);
                    $dosen = Dosen::firstOrCreate([
                        'nip' => $nipDpl, 
                    ], [
                        'id_user' => $user->id, 
                    ]);
                    $roleId = Role::where('nama_role', 'DPL')->value('id');
                    $role = UserRole::firstOrCreate([
                        'id_user' => $user->id, 
                        'id_role' => $roleId, 
                        'id_kkn' => $this->id_kkn
                    ]);
                    $dpl = Dpl::firstOrCreate([
                        'id_dosen' => $dosen->id,
                        'id_kkn' => $this->id_kkn,
                    ]);
                    $currentStep++;
                    $proses = number_format(($currentStep / $totalSteps) * 100, 2);
                    QueueProgress::where('id', $this->progress)->update([
                        'step' => $currentStep,
                        'progress' => $proses,
                        'status' => 'in_progress',
                        'message' => 'Processing DPL data'
                    ]);

                    foreach ($dplData['unit'] as $unitData) {
                        $kabupaten = Kabupaten::firstOrCreate([
                            'nama' => $unitData['kabupaten'],
                        ]);
                        $kecamatan = Kecamatan::firstOrCreate([
                            'nama' => $unitData['kecamatan'],
                            'id_kabupaten' => $kabupaten->id,
                        ]);
                        $lokasi = Lokasi::firstOrCreate([
                            'nama' => $unitData['lokasi'],
                            'id_kecamatan' => $kecamatan->id,
                        ]);
                        $unit = Unit::firstOrCreate([
                            'nama' => $unitData['nama'],
                            'tanggal_penerjunan' => $unitData['tanggal_penerjunan'],
                            'id_kkn' => $this->id_kkn,
                            'id_lokasi' => $lokasi->id,
                            'id_dpl' => $dpl->id, 
                        ]);

                        $currentStep++;
                        QueueProgress::where('id', $this->progress)->update([
                            'step' => $currentStep,
                            'progress' => $proses,
                            'status' => 'in_progress',
                            'message' => 'Processing unit data'
                        ]);
                        foreach ($unitData['anggota'] as $anggotaData) {
                            try {
                                $prodi = Prodi::firstOrCreate([
                                    'nama_prodi' => $anggotaData['prodi'],
                                ]);

                                $user = User::firstOrCreate([
                                    'email' => $anggotaData['email'],
                                ], [
                                    'nama' => $anggotaData['nama'],
                                    'password' => bcrypt($anggotaData['nim']),
                                    'no_telp' => $anggotaData['nomorHP'],
                                    'jenis_kelamin' => $anggotaData['jenisKelamin'],
                                ]);

                                $roleId = Role::where('nama_role', 'Mahasiswa')->value('id');
                                $role = UserRole::firstOrCreate(['id_user' => $user->id, 'id_role' => $roleId, 'id_kkn' => $this->id_kkn]);

                                $mahasiswa = Mahasiswa::firstOrCreate([
                                    'id_user_role' => $role->id,
                                    'id_kkn' => $this->id_kkn,
                                    'id_prodi' => $prodi->id,
                                    'nim' => $anggotaData['nim'],
                                    'id_unit' => $unit->id,
                                ]);

                                $currentStep++;
                                $proses = number_format(($currentStep / $totalSteps) * 100, 2);
                                QueueProgress::where('id', $this->progress)->update([
                                    'step' => $currentStep,
                                    'progress' => $proses,
                                    'status' => 'in_progress',
                                    'message' => 'Processing member data'
                                ]);
                            } catch (\Exception $e) {
                                QueueProgress::where('id', $this->progress)->update([
                                    'status' => 'failed',
                                    'message' => $e->getMessage(),
                                    'step' => $currentStep,
                                    'progress' => $proses
                                ]);
                                Log::error("Error processing member data: " . $e->getMessage());
                                return;
                            }
                        }
                    }
                }
                QueueProgress::where('id', $this->progress)->update([
                    'status' => 'completed',
                    'message' => 'Processing completed'
                ]);
                $progressId = $this->progress;
                $this->deleteAfterSeconds($progressId, 5);
            } catch (\Exception $e) {
                $proses = number_format(($currentStep / $totalSteps) * 100, 2);
                QueueProgress::where('id', $this->progress)->update([
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                    'step' => $currentStep,
                    'progress' => $proses
                ]);
                Log::error("Error processing DPL data: " . $e->getMessage());
            }
        } catch (\Exception $e) {
            QueueProgress::where('id', $this->progress)->update([
                'total' => 0,
                'status' => 'failed',
                'message' => 'Proses gagal saat start'
            ]);
        }
    }
    private function deleteAfterSeconds($id, $seconds)
    {
        sleep($seconds);
        QueueProgress::where('id', $id)->delete();
    }
}
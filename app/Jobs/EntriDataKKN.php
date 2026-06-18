<?php

namespace App\Jobs;

use App\Models\QueueProgress;
use App\Models\User;
use App\Models\Mahasiswa;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EntriDataKKN implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jsonData;
    protected $id_kkn;
    protected $progress;
    protected $namaKkn;

    public function __construct($jsonData, $id_kkn, $progress, $namaKkn)
    {
        $this->jsonData = $jsonData;
        $this->id_kkn = $id_kkn;
        $this->progress = $progress;
        $this->namaKkn = $namaKkn;
    }

    public function handle()
    {
        $totalSteps = 0;
        $currentStep = 0;

        try {
            if (!is_array($this->jsonData)) {
                throw new \Exception("Data Excel tidak terbaca sebagai Array.");
            }

            foreach ($this->jsonData as $unitData) {
                $totalSteps++; 
                if (isset($unitData['anggota']) && is_array($unitData['anggota'])) {
                    $totalSteps += count($unitData['anggota']);
                }
            }

            if ($totalSteps === 0) {
                throw new \Exception("Data kosong atau format Excel salah.");
            }

            // Update status awal
            QueueProgress::where('id', $this->progress)->update([
                'total' => $totalSteps,
                'status' => 'in_progress',
                'message' => 'Memulai proses import...',
                'progress' => 0
            ]);

            // Ambil ID Role Mahasiswa 
            $roleMhsId = Role::where('nama_role', 'Mahasiswa')->value('id');

            if (!$roleMhsId) {
                throw new \Exception("Role 'Mahasiswa' tidak ditemukan di database.");
            }

            foreach ($this->jsonData as $unitData) {
                try {
                    DB::beginTransaction();
                    // Simpan Wilayah
                    $kabupaten = Kabupaten::firstOrCreate(['nama' => $unitData['kabupaten']]);
                    $kecamatan = Kecamatan::firstOrCreate(['nama' => $unitData['kecamatan'], 'id_kabupaten' => $kabupaten->id]);
                    $lokasi = Lokasi::firstOrCreate(['nama' => $unitData['lokasi'], 'id_kecamatan' => $kecamatan->id]);

                    $namaKknLower = strtolower($this->namaKkn);
                    $tanggalPenerjunanRaw = $unitData['tanggal_penerjunan'] ?? now()->format('Y-m-d');
                    $carbonPenerjunan = \Carbon\Carbon::parse($tanggalPenerjunanRaw);

                    if (str_contains($namaKknLower, 'alternatif')) {
                        $tanggalPenarikan = $carbonPenerjunan->copy()->addDays(59)->format('Y-m-d');
                    } else {
                        $tanggalPenarikan = $carbonPenerjunan->copy()->addDays(29)->format('Y-m-d');
                    }

                    // Simpan Unit 
                    $unit = Unit::firstOrCreate([
                        'nama' => $unitData['nama'],
                        'id_kkn' => $this->id_kkn,
                    ], [
                        'tanggal_penerjunan' => $unitData['tanggal_penerjunan'] ?? now(),
                        'tanggal_penarikan'  => $tanggalPenarikan,
                        'id_lokasi' => $lokasi->id,
                        'id_dpl' => null,
                    ]);

                    DB::commit();

                    $currentStep++;
                    $this->updateProgressDB($currentStep, $totalSteps, 'Unit: ' . $unitData['nama']);

                    if (isset($unitData['anggota'])) {
                        foreach ($unitData['anggota'] as $anggotaData) {
                            try {
                                DB::beginTransaction();

                                // Prodi
                                $prodi = Prodi::firstOrCreate(['nama_prodi' => $anggotaData['prodi']]);

                                // User Mahasiswa
                                $userMhs = User::firstOrCreate(
                                    ['email' => $anggotaData['email']],
                                    [
                                        'nama' => $anggotaData['nama'],
                                        'password' => bcrypt(\Illuminate\Support\Str::random(40)),
                                        'no_telp' => $anggotaData['nomorHP'] ?? '-',
                                        'jenis_kelamin' => $anggotaData['jenisKelamin'] ?? 'L',
                                    ]
                                );

                                // User Role
                                $roleMhs = UserRole::firstOrCreate([
                                    'id_user' => $userMhs->id, 
                                    'id_role' => $roleMhsId, 
                                    'id_kkn' => $this->id_kkn
                                ]);

                                // Data Mahasiswa (Link ke Unit)
                                Mahasiswa::updateOrCreate(
                                    [
                                        'nim' => $anggotaData['nim'],
                                        'id_kkn' => $this->id_kkn
                                    ],
                                    [
                                        'id_user_role' => $roleMhs->id,
                                        'id_prodi' => $prodi->id,
                                        'id_unit' => $unit->id,
                                    ]
                                );

                                DB::commit();

                                $currentStep++;
                                $this->updateProgressDB($currentStep, $totalSteps, 'Mhs: ' . $anggotaData['nama']);

                            } catch (\Exception $e) {
                                DB::rollBack();
                                Log::error("Gagal Import Mhs " . ($anggotaData['nim'] ?? '?') . ": " . $e->getMessage());
                                $currentStep++;
                                $this->updateProgressDB($currentStep, $totalSteps, 'Skip Error Mhs: ' . ($anggotaData['nim'] ?? '?'));
                            }
                        }
                    }

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Gagal Import Unit " . ($unitData['nama'] ?? '?') . ": " . $e->getMessage());
                    
                    $skippedSteps = 1 + (isset($unitData['anggota']) ? count($unitData['anggota']) : 0);
                    $currentStep += $skippedSteps;
                    
                    $this->updateProgressDB($currentStep, $totalSteps, 'Gagal Unit: ' . ($unitData['nama'] ?? '?'));
                }
            }

            // Finish
            QueueProgress::where('id', $this->progress)->update([
                'status' => 'completed',
                'message' => 'Data berhasil disimpan!',
                'progress' => 100,
                'step' => $totalSteps
            ]);

        } catch (\Exception $e) {
            // Global Error Handling
            $errorLine = $e->getLine();
            $errorMessage = $e->getMessage();
            Log::error("Job EntriDataKKN Critical Error: " . $errorMessage);

            $safeErrorMessage = substr("Error: $errorMessage (Line: $errorLine)", 0, 250);

            QueueProgress::where('id', $this->progress)->update([
                'status' => 'failed',
                'message' => $safeErrorMessage,
                'step' => $currentStep,
                'progress' => ($totalSteps > 0) ? number_format(($currentStep / $totalSteps) * 100, 2) : 0
            ]);
        }
    }

    private function updateProgressDB($step, $total, $msg)
    {
        $percentage = ($total > 0) ? number_format(($step / $total) * 100, 2) : 0;
        
        QueueProgress::where('id', $this->progress)->update([
            'step' => $step,
            'progress' => $percentage,
            'message' => substr($msg, 0, 250) // Safety cut text
        ]);
    }
}
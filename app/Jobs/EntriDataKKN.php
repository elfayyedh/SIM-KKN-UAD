<?php

namespace App\Jobs;

use App\Models\QueueProgress;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dpl;
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

        // Hitung total langkah
        try {
            foreach ($this->jsonData as $dplData) {
                $totalSteps++; // Langkah untuk DPL
                foreach ($dplData['unit'] as $unitData) {
                    $totalSteps++; // Langkah untuk unit
                    $totalSteps += count($unitData['anggota']); // Langkah untuk anggota
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
                    // Buat password dpl
                    $namaLengkapDpl = $dplData['DPL'];       // Contoh: "Jefree Fahana S.T., M.Kom."
                    $emailDpl = $dplData['email'];         // Contoh: "jefree.fahana@tif.uad.ac.id"
                    $nipDpl = $dplData['password'];      // NIP disimpan di key 'password' oleh worker

                    $passwordDefault = 'password';
                    // Simpan data DPL
                    $user = User::firstOrCreate([
                        'email' => $emailDpl,
                    ], [
                        'nama' => $namaLengkapDpl,
                        'password' => bcrypt($passwordDefault),
                    ]);

                    // ! !!!! Perlu no_telp dan jenis_kelamin !!!

                    // Menambahkan role DPL ke user
                    $roleId = Role::where('nama_role', 'DPL')->value('id');
                    $role = UserRole::firstOrCreate(['id_user' => $user->id, 'id_role' => $roleId, 'id_kkn' => $this->id_kkn]);

                    // Membuat atau mendapatkan data DPL
                    $dpl = Dpl::firstOrCreate([
                        'id_user_role' => $role->id,
                        'id_kkn' => $this->id_kkn,
                        'nip' => $nipDpl,
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
                        // Simpan data Kabupaten
                        $kabupaten = Kabupaten::firstOrCreate([
                            'nama' => $unitData['kabupaten'],
                        ]);
                        // Simpan data Kecamatan
                        $kecamatan = Kecamatan::firstOrCreate([
                            'nama' => $unitData['kecamatan'],
                            'id_kabupaten' => $kabupaten->id,
                        ]);
                        // Simpan data lokasi
                        $lokasi = Lokasi::firstOrCreate([
                            'nama' => $unitData['lokasi'],
                            'id_kecamatan' => $kecamatan->id,
                        ]);
                        // Simpan data unit
                        $unit = Unit::firstOrCreate([
                            'nama' => $unitData['nama'],
                            'tanggal_penerjunan' => $unitData['tanggal_penerjunan'],
                            'id_kkn' => $this->id_kkn,
                            'id_lokasi' => $lokasi->id,
                            'id_dpl' => $dpl->id,
                        ]);

                        $currentStep++;
                        $proses = number_format(($currentStep / $totalSteps) * 100, 2);
                        QueueProgress::where('id', $this->progress)->update([
                            'step' => $currentStep,
                            'progress' => $proses,
                            'status' => 'in_progress',
                            'message' => 'Processing unit data'
                        ]);

                        foreach ($unitData['anggota'] as $anggotaData) {
                            try {
                                // Simpan data prodi
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

                                // Menambahkan role Mahasiswa ke user
                                $roleId = Role::where('nama_role', 'Mahasiswa')->value('id');
                                $role = UserRole::firstOrCreate(['id_user' => $user->id, 'id_role' => $roleId, 'id_kkn' => $this->id_kkn]);

                                // Simpan data mahasiswa
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
                                // Update progress dengan informasi kesalahan
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

                // Jika berhasil, update status menjadi completed
                QueueProgress::where('id', $this->progress)->update([
                    'status' => 'completed',
                    'message' => 'Processing completed'
                ]);

                // Hapus data setelah 5 detik
                $progressId = $this->progress;
                $this->deleteAfterSeconds($progressId, 5);
            } catch (\Exception $e) {
                // Update progress dengan informasi kesalahan
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

    // Tambahkan method deleteAfterSeconds
    private function deleteAfterSeconds($id, $seconds)
    {
        sleep($seconds);
        QueueProgress::where('id', $id)->delete();
    }
}

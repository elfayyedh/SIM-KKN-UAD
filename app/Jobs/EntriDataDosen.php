<?php

namespace App\Jobs;

use App\Models\QueueProgress;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EntriDataDosen implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jsonData;
    protected $progress;

    /**
     * Create a new job instance.
     *
     * @param array $jsonData
     */
    public function __construct($jsonData, $progress)
    {
        $this->jsonData = $jsonData;
        $this->progress = $progress;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $totalSteps = count($this->jsonData);
        $currentStep = 0;

        try {
            QueueProgress::where('id', $this->progress)->update([
                'total' => $totalSteps,
                'status' => 'in_progress',
                'message' => 'Processing started'
            ]);

            $roleId = Role::where('nama_role', 'Dosen')->value('id');

            foreach ($this->jsonData as $dosenData) {
                try {
                    // Create or update user
                    $user = User::firstOrCreate([
                        'email' => $dosenData['email'],
                    ], [
                        'nama' => $dosenData['nama'],
                        'password' => bcrypt($dosenData['nidn']),
                        'no_telp' => $dosenData['nomorHP'] ?? null,
                        'jenis_kelamin' => $dosenData['jenisKelamin'] ?? 'L',
                    ]);

                    // Create dosen record (using nip field for NIDN)
                    $dosen = Dosen::firstOrCreate([
                        'id_user' => $user->id,
                    ], [
                        'nip' => $dosenData['nidn'],
                    ]);

                    // Create user role if not exists
                    if ($roleId) {
                        UserRole::firstOrCreate([
                            'id_user' => $user->id,
                            'id_role' => $roleId,
                        ]);
                    }

                    $currentStep++;
                    $progress = number_format(($currentStep / $totalSteps) * 100, 2);
                    QueueProgress::where('id', $this->progress)->update([
                        'step' => $currentStep,
                        'progress' => $progress,
                        'status' => 'in_progress',
                        'message' => 'Processing dosen data'
                    ]);
                } catch (\Exception $e) {
                    Log::error("Error processing dosen data: " . $e->getMessage());
                    QueueProgress::where('id', $this->progress)->update([
                        'status' => 'failed',
                        'message' => 'Error: ' . $e->getMessage(),
                        'step' => $currentStep,
                        'progress' => number_format(($currentStep / $totalSteps) * 100, 2)
                    ]);
                    return;
                }
            }

            QueueProgress::where('id', $this->progress)->update([
                'status' => 'completed',
                'message' => 'Processing completed'
            ]);

            $progressId = $this->progress;
            $this->deleteAfterSeconds($progressId, 5);
        } catch (\Exception $e) {
            QueueProgress::where('id', $this->progress)->update([
                'status' => 'failed',
                'message' => 'Process failed: ' . $e->getMessage()
            ]);
            Log::error("Error in EntriDataDosen: " . $e->getMessage());
        }
    }

    private function deleteAfterSeconds($id, $seconds)
    {
        sleep($seconds);
        QueueProgress::where('id', $id)->delete();
    }
}

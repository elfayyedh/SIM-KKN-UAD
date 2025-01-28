<?php

namespace App\Observers;

use App\Models\BidangProker;
use App\Models\Kegiatan;
use App\Models\Mahasiswa;
use App\Models\Proker;
use Illuminate\Support\Facades\Log;

class KegiatanObserver
{
    public function created(Kegiatan $kegiatan)
    {
        $this->updateTotalJkem($kegiatan, $kegiatan->total_jkem);
    }

    /**
     * Handle the Kegiatan "updated" event.
     */
    public function updated(Kegiatan $kegiatan)
    {
        $this->updateTotalJkem($kegiatan, $kegiatan->total_jkem - $kegiatan->getOriginal('total_jkem'));
    }

    /**
     * Handle the Kegiatan "deleted" event.
     */
    public function deleted(Kegiatan $kegiatan)
    {
        $this->updateTotalJkem($kegiatan, -$kegiatan->total_jkem);
    }

    private function updateTotalJkem(Kegiatan $kegiatan, $amount)
    {
        // Ambil Proker dan Bidang Proker
        $proker = Proker::find($kegiatan->id_proker);
        $bidangProker = BidangProker::find($proker->id_bidang);

        if ($bidangProker->tipe == 'unit' || $bidangProker->tipe == 'bersama') {
            // Jika bidang proker tipe unit/bersama, update total_jkem untuk semua anggota unit
            $mahasiswas = Mahasiswa::where('id_unit', $proker->id_unit)->get();
            foreach ($mahasiswas as $mahasiswa) {
                $mahasiswa->total_jkem += $amount;
                $mahasiswa->save();
            }
        } else {
            // Jika bidang proker tipe individu, update total_jkem untuk satu mahasiswa
            $mahasiswa = Mahasiswa::find($kegiatan->id_mahasiswa);
            $mahasiswa->total_jkem += $amount;
            $mahasiswa->save();
            Log::info('Updated total_jkem to ' . $mahasiswa->total_jkem, $mahasiswa->toArray());
        }
    }
}

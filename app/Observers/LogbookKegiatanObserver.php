<?php

namespace App\Observers;

use App\Models\LogbookHarian;
use App\Models\LogbookKegiatan;
use App\Models\Mahasiswa;

class LogbookKegiatanObserver
{
    /**
     * Handle the LogbookKegiatan "created" event.
     */
    public function created(LogbookKegiatan $logbookKegiatan)
    {
        $this->updateLogbookHarian($logbookKegiatan, 'tambah');
    }

    public function updated(LogbookKegiatan $logbookKegiatan)
    {
        $this->updateLogbookHarian($logbookKegiatan, 'ubah');
    }

    public function deleted(LogbookKegiatan $logbookKegiatan)
    {
        $this->updateLogbookHarian($logbookKegiatan, 'hapus');
    }

    private function updateLogbookHarian(LogbookKegiatan $logbookKegiatan, $action)
    {
        $logbookHarian = LogbookHarian::where('id', $logbookKegiatan->id_logbook_harian)->first();
        $jenis = '';
        $proker = $logbookKegiatan->kegiatan->proker;

        if ($proker && $proker->bidang) {
            $jenis = $proker->bidang->tipe;
        }

        if ($jenis == 'unit') {
            $mahasiswa_unit = Mahasiswa::where('id_unit', $logbookKegiatan->mahasiswa->id_unit)->get();

            foreach ($mahasiswa_unit as $mhs) {
                $logbookHarianMhs = LogbookHarian::firstOrCreate([
                    'id_mahasiswa' => $mhs->id,
                    'tanggal' => $logbookHarian->tanggal,
                    'id_unit' => $mhs->id_unit
                ], [
                    'status' => 'belum diisi',
                    'total_jkem' => 0
                ]);

                if ($logbookHarianMhs) {
                    $this->updateTotalJkem($logbookHarianMhs, $logbookKegiatan, $action);
                }
            }
        } else {
            $this->updateTotalJkem($logbookHarian, $logbookKegiatan, $action);
        }
    }

    private function updateTotalJkem($logbookHarian, $logbookKegiatan, $action)
    {
        if ($action == 'tambah') {
            $logbookHarian->total_jkem += $logbookKegiatan->total_jkem;
            $logbookHarian->status = 'sudah diisi';
        } elseif ($action == 'ubah') {
            $logbookHarian->total_jkem -= $logbookKegiatan->getOriginal('total_jkem');
            $logbookHarian->total_jkem += $logbookKegiatan->total_jkem;
            if ($logbookHarian->total_jkem <= 0) {
                $logbookHarian->status = 'belum diisi';
            } else {
                $logbookHarian->status = 'sudah diisi';
            }
        } elseif ($action == 'hapus') {
            $logbookHarian->total_jkem -= $logbookKegiatan->total_jkem;
            if ($logbookHarian->total_jkem <= 0) {
                $logbookHarian->status = 'belum diisi';
            } else {
                $logbookHarian->status = 'sudah diisi';
            }
        }
        $logbookHarian->save();
    }
}

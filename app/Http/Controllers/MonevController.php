<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Unit;
use App\Models\Mahasiswa;
use App\Models\EvaluasiMahasiswa;
use App\Models\EvaluasiMahasiswaDetail;
use App\Models\KriteriaMonev;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class MonevController extends Controller
{
    /**
     * Helper: Ambil Assignment Monev Aktif
     */
    private function getActiveMonevAssignment($dosen)
    {
        $allMonevAssignments = $dosen->timMonevAssignments()->with('kkn')->get();

        if ($allMonevAssignments->isEmpty()) {
            throw new \Exception('Dosen ini tidak memiliki penugasan Tim Monev.');
        }

        $activeAssignmentId = session('active_monev_assignment_id');
        $activeMonevAssignment = $allMonevAssignments->find($activeAssignmentId);

        if (!$activeMonevAssignment) {
            $activeMonevAssignment = $allMonevAssignments->first();
            session(['active_monev_assignment_id' => $activeMonevAssignment->id]);
        }
        
        return [
            'active' => $activeMonevAssignment,
            'all' => $allMonevAssignments
        ];
    }

    public function setActiveKkn(Request $request)
    {
        $dosen = Auth::user()->dosen;
        $request->validate([
            'assignment_id' => [
                'required',
                'uuid',
                Rule::exists('tim_monev', 'id')->where('id_dosen', $dosen->id)
            ]
        ]);
        session(['active_monev_assignment_id' => $request->assignment_id]);
        return redirect()->route('monev.evaluasi.index');
    }

    public function index(Request $request)
    {
        try {
            $dosen = Auth::user()->dosen;
            if (!$dosen) throw new \Exception('Profil Dosen tidak ditemukan.');
            
            $allAssignmentIds = $dosen->timMonevAssignments()->pluck('id');

            $units = Unit::with(['lokasi', 'dpl.dosen.user', 'prokers.kegiatan', 'kkn']) 
                        ->whereIn('id_tim_monev', $allAssignmentIds)
                        ->orderBy('id_kkn', 'desc') 
                        ->get();

            return view('tim monev.evaluasi.evaluasi-unit', compact('units'));

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    public function showMahasiswaPage($id_unit)
    {
        try {
            $dosen = Auth::user()->dosen;
            $monevData = $this->getActiveMonevAssignment($dosen);
            $monevAssignment = $monevData['active']; 

            $unit = Unit::with([
                'kkn', 
                'mahasiswa.userRole.user', 
                'dpl.dosen.user',
                'mahasiswa.kegiatan',
                'mahasiswa.logbookSholat',
            ])->find($id_unit);

            // Validasi Unit & Akses
            if (!$unit) return redirect()->back()->with('error', 'Unit tidak ditemukan.');

            if ($unit->id_tim_monev != $monevAssignment->id) {
                $isMyAssignment = $monevData['all']->contains('id', $unit->id_tim_monev);
                if ($isMyAssignment) {
                    session(['active_monev_assignment_id' => $unit->id_tim_monev]);
                    $monevAssignment = $monevData['all']->find($unit->id_tim_monev);
                } else {
                    return redirect()->back()->with('error', 'Akses Ditolak.');
                }
            }

            $kriteriaList = KriteriaMonev::where('id_kkn', $unit->id_kkn)->orderBy('urutan', 'asc')->get();
            $mhsIds = $unit->mahasiswa->pluck('id');
            $existingEvaluations = EvaluasiMahasiswa::with('evaluasiMahasiswaDetail')
                ->where('id_tim_monev', $monevAssignment->id)
                ->whereIn('id_mahasiswa', $mhsIds)
                ->get();

            $mappedNilai = [];
            foreach ($existingEvaluations as $eval) {
                foreach ($eval->evaluasiMahasiswaDetail as $detail) {
                    $mappedNilai[$eval->id_mahasiswa][$detail->id_kriteria_monev] = $detail->nilai;
                }
            }

            $kknData = $unit->kkn;
            $rawTglSelesai = $kknData->tanggal_cutoff_penilaian ?? $unit->tanggal_penarikan ?? $kknData->tanggal_selesai;
            $rawTglMulai   = $unit->tanggal_penerjunan ?? $kknData->tanggal_mulai;

            $tglMulai   = Carbon::parse($rawTglMulai)->startOfDay();
            $tglSelesai = Carbon::parse($rawTglSelesai)->endOfDay(); 

            foreach ($unit->mahasiswa as $mhs) {
                $mhs->hitung_jkem = $mhs->kegiatan->sum('total_jkem');
                $persenSholat = 0;
                if ($tglMulai && $tglSelesai) {
                    $totalHari = intval($tglMulai->diffInDays($tglSelesai)) + 1;
                    $isAlternatif = stripos($unit->kkn->nama ?? '', 'alternatif') !== false;
                    $targetPerHari = $isAlternatif ? 3 : 5;
                    $validPrayers = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];
                    $totalWajib = $totalHari * $targetPerHari;
                    
                    $logbookSholatFiltered = $mhs->logbookSholat->filter(function($log) use ($tglMulai, $tglSelesai) {
                          if (empty($log->tanggal)) return false;
                          return Carbon::parse($log->tanggal)->between($tglMulai, $tglSelesai);
                    });

                    if ($isAlternatif) {
                        $logbookGrouped = $logbookSholatFiltered
                            ->whereIn('waktu', $validPrayers)
                            ->groupBy(function($item) { return Carbon::parse($item->tanggal)->format('Y-m-d'); });

                        $totalBerjamaah = $logbookGrouped->map(function ($items) use ($targetPerHari) {
                             return min($items->where('status', 'sholat berjamaah')->count(), $targetPerHari);
                        })->sum();
                        $totalHalangan = $logbookGrouped->map(function ($items) use ($targetPerHari) {
                             return min($items->where('status', 'sedang halangan')->count(), $targetPerHari);
                        })->sum();
                    } else {
                        $totalBerjamaah = $logbookSholatFiltered->where('status', 'sholat berjamaah')->whereIn('waktu', $validPrayers)->count();
                        $totalHalangan = $logbookSholatFiltered->where('status', 'sedang halangan')->whereIn('waktu', $validPrayers)->count();
                    }

                    $penyebut = $totalWajib - $totalHalangan;
                    if ($penyebut > 0) {
                        $persenSholat = round(($totalBerjamaah / $penyebut) * 100, 0);
                    }
                }
                $mhs->hitung_sholat = $persenSholat;
            }

            return view('tim monev.evaluasi.daftar-mahasiswa', [
                'unit' => $unit,
                'kriteriaList' => $kriteriaList,
                'mappedNilai' => $mappedNilai
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function bulkStorePenilaian(Request $request)
    {
        // Validasi input: Array harus ada
        $request->validate([
            'evaluasi' => 'required|array',
            'id_unit' => 'required',
            'catatan_monev' => 'nullable|string',
        ]);

        try {
            $dosen = Auth::user()->dosen;
            $monevData = $this->getActiveMonevAssignment($dosen);
            $monevAssignment = $monevData['active'];

            // Update catatan unit
            $unit = Unit::findOrFail($request->id_unit);
            // Cek apakah unit ini milik tim monev ini, jika iya update catatannya
            if ($unit->id_tim_monev == $monevAssignment->id) {
                $unit->update([
                    'catatan_monev' => $request->catatan_monev
                ]);
            }

            // Loop setiap mahasiswa yang dikirim dari form
            // Format: $request->evaluasi[ID_MAHASISWA][ID_KRITERIA] = NILAI
            foreach ($request->evaluasi as $idMahasiswa => $scores) {
                
                // Filter: Hanya proses jika ada setidaknya satu nilai yang diisi (tidak null/kosong)
                $filledScores = array_filter($scores, function($val) { 
                    return !is_null($val) && $val !== ''; 
                });

                if (empty($filledScores)) continue; 

                //Update/Create Header Evaluasi (Tabel: evaluasi_mahasiswa)
                $evalHeader = EvaluasiMahasiswa::updateOrCreate(
                    [
                        'id_tim_monev' => $monevAssignment->id,
                        'id_mahasiswa' => $idMahasiswa
                    ],
                    [
                        'updated_at' => now() // Trigger update timestamp
                    ]
                );

                //Simpan Detail Nilai (Tabel: evaluasi_mahasiswa_detail)
                foreach ($filledScores as $idKriteria => $nilai) {
                    
                    // Pastikan nilai dalam range 1-3
                    if ($nilai < 1 || $nilai > 3) continue;

                    EvaluasiMahasiswaDetail::updateOrCreate(
                        [
                            'id_evaluasi_mahasiswa' => $evalHeader->id,
                            'id_kriteria_monev'     => $idKriteria
                        ],
                        [
                            'nilai' => $nilai
                        ]
                    );
                }
            }

            return redirect()->back()->with('success', 'Data penilaian berhasil disimpan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function storePenilaian(Request $request, $id_mahasiswa)
    {
        $request->validate([
            'nilai' => 'required|array', 
            'nilai.*' => 'required|numeric|in:1,2,3', 
            'catatan_monev' => 'nullable|string|max:5000',
        ]);

        try {
            $dosen = Auth::user()->dosen;
            $monevAssignment = $this->getActiveMonevAssignment($dosen)['active'];
            $mahasiswa = Mahasiswa::findOrFail($id_mahasiswa);

            if ($mahasiswa->unit->id_tim_monev != $monevAssignment->id) {
                throw new \Exception('Anda tidak berhak menilai mahasiswa ini.');
            }

            // Header
            $evaluasiHeader = EvaluasiMahasiswa::updateOrCreate(
                ['id_tim_monev' => $monevAssignment->id, 'id_mahasiswa' => $id_mahasiswa],
                ['catatan_monev' => $request->catatan_monev]
            );

            // Detail
            foreach ($request->nilai as $idKriteria => $skor) {
                EvaluasiMahasiswaDetail::updateOrCreate(
                    ['id_evaluasi_mahasiswa' => $evaluasiHeader->id, 'id_kriteria_monev' => $idKriteria],
                    ['nilai' => $skor]
                );
            }

            return redirect()->route('monev.evaluasi.penilaian', $id_mahasiswa)
                             ->with('success', 'Penilaian berhasil disimpan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
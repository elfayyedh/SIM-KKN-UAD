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
            
            $activeData = $this->getActiveMonevAssignment($dosen);
            $monevAssignment = $activeData['active'];

            $units = Unit::with(['lokasi', 'dpl.dosen.user', 'prokers.kegiatan', 'kkn']) 
                        ->where('id_tim_monev', $monevAssignment->id)
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

            // Ambil Unit & Data Mahasiswa
            $unit = Unit::with([
                'kkn', 
                'mahasiswa.userRole.user', 
                'dpl.dosen.user',
                'mahasiswa.kegiatan.logbookKegiatan', 
                'mahasiswa.logbookSholat',
            ])
            ->where('id', $id_unit)
            ->where('id_tim_monev', $monevAssignment->id) 
            ->firstOrFail();

            // Data Pendukung (Kriteria & Nilai Existing)
            $kriteriaList = KriteriaMonev::where('id_kkn', $unit->id_kkn)->orderBy('urutan', 'asc')->get();
            
            $mhsIds = $unit->mahasiswa->pluck('id');
            $existingEvaluations = EvaluasiMahasiswa::with('details')
                ->where('id_tim_monev', $monevAssignment->id)
                ->whereIn('id_mahasiswa', $mhsIds)
                ->get();

            $mappedNilai = [];
            foreach ($existingEvaluations as $eval) {
                foreach ($eval->details as $detail) {
                    $mappedNilai[$eval->id_mahasiswa][$detail->id_kriteria_monev] = $detail->nilai;
                }
            }

            // HITUNG STATISTIK (LOGIC UTAMA)
            foreach ($unit->mahasiswa as $mhs) {
                $mhs->hitung_jkem = $mhs->kegiatan->pluck('logbookKegiatan')->flatten()->sum('total_jkem');

                $persenSholat = 0;
                if ($unit->tanggal_penerjunan && $unit->tanggal_penarikan) {
                    $tglMulai = Carbon::parse($unit->tanggal_penerjunan);
                    $tglSelesai = Carbon::parse($unit->tanggal_penarikan);
                    $totalHari = abs($tglSelesai->diffInDays($tglMulai)) + 1;
                    
                    $isAlternatif = stripos($unit->kkn->nama ?? '', 'alternatif') !== false;
                    $targetPerHari = $isAlternatif ? 3 : 5;
                    $validPrayers = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];
                    $totalWajib = $totalHari * $targetPerHari;
                    
                    if ($isAlternatif) {
                        $logbookGrouped = $mhs->logbookSholat
                            ->whereIn('waktu', $validPrayers)
                            ->groupBy('tanggal');

                        $totalBerjamaah = $logbookGrouped->map(function ($items) use ($targetPerHari) {
                             $countAsli = $items->where('status', 'sholat berjamaah')->count();
                             return min($countAsli, $targetPerHari);
                        })->sum();

                        $totalHalangan = $logbookGrouped->map(function ($items) use ($targetPerHari) {
                             $countAsli = $items->where('status', 'sedang halangan')->count();
                             return min($countAsli, $targetPerHari);
                        })->sum();
                    } else {
                        $totalBerjamaah = $mhs->logbookSholat->where('status', 'sholat berjamaah')->whereIn('waktu', $validPrayers)->count();
                        $totalHalangan = $mhs->logbookSholat->where('status', 'sedang halangan')->whereIn('waktu', $validPrayers)->count();
                    }

                    $penyebut = $totalWajib - $totalHalangan;
                    if ($penyebut > 0) $persenSholat = round(($totalBerjamaah / $penyebut) * 100, 0);
                }
                $mhs->hitung_sholat = $persenSholat;
            }

            return view('tim monev.evaluasi.daftar-mahasiswa', [
                'unit' => $unit,
                'kriteriaList' => $kriteriaList,
                'mappedNilai' => $mappedNilai
            ]);

        } catch (\Exception $e) {
            return redirect()->route('monev.evaluasi.index')->with('error', $e->getMessage());
        }
    }

    public function bulkStorePenilaian(Request $request)
    {
        // Validasi input: Array harus ada
        $request->validate([
            'evaluasi' => 'required|array',
        ]);

        try {
            $dosen = Auth::user()->dosen;
            $monevData = $this->getActiveMonevAssignment($dosen);
            $monevAssignment = $monevData['active'];

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

    public function showPenilaianPage($id_mahasiswa)
    {
        try {
            $dosen = Auth::user()->dosen;
            $monevData = $this->getActiveMonevAssignment($dosen);
            $monevAssignment = $monevData['active'];
            
            // Ambil Mahasiswa Target
            $mahasiswa = Mahasiswa::with([
                'userRole.user', 'unit.dpl', 'unit.kkn',
                'logbookSholat', 'kegiatan.logbookKegiatan'
            ])->findOrFail($id_mahasiswa);
            
            // Cek Hak Akses
            if ($mahasiswa->unit->id_tim_monev != $monevAssignment->id) {
                throw new \Exception('Anda tidak berhak menilai mahasiswa ini.');
            }

            // [SIDEBAR] Ambil Daftar Teman Satu Unit
            $daftarTeman = Mahasiswa::with(['userRole.user', 'evaluasiOlehMonev' => function($q) use ($monevAssignment) {
                    $q->where('id_tim_monev', $monevAssignment->id);
                }])
                ->where('id_unit', $mahasiswa->id_unit)
                ->orderBy('nim', 'asc')
                ->get();

            // Hitung Ulang Data Dinamis (Hanya untuk 1 mahasiswa ini agar tampil di Header Form)
            $totalJkem = $mahasiswa->kegiatan->pluck('logbookKegiatan')->flatten()->sum('total_jkem');
            
            $persenSholat = 0;
            if ($mahasiswa->unit && $mahasiswa->unit->tanggal_penerjunan && $mahasiswa->unit->tanggal_penarikan) {
                $tglMulai = Carbon::parse($mahasiswa->unit->tanggal_penerjunan);
                $tglSelesai = Carbon::parse($mahasiswa->unit->tanggal_penarikan);
                $totalHari = abs($tglSelesai->diffInDays($tglMulai)) + 1;

                $isAlternatif = stripos($mahasiswa->unit->kkn->nama ?? '', 'alternatif') !== false;
                $targetPerHari = $isAlternatif ? 3 : 5;
                $validPrayers = $isAlternatif ? ['dzuhur', 'ashar', 'maghrib'] : ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];
                $totalWajib = $totalHari * $targetPerHari;

                if ($isAlternatif) {
                    $totalBerjamaah = $mahasiswa->logbookSholat
                        ->where('status', 'sholat berjamaah')
                        ->whereIn('waktu', $validPrayers)
                        ->groupBy('tanggal')
                        ->map(function ($items) use ($targetPerHari) {
                             $c = $items->count(); return $c > $targetPerHari ? $targetPerHari : $c;
                        })->sum();
                } else {
                    $totalBerjamaah = $mahasiswa->logbookSholat
                        ->where('status', 'sholat berjamaah')
                        ->whereIn('waktu', $validPrayers)
                        ->count();
                }

                $totalHalangan = $mahasiswa->logbookSholat->where('status', 'sedang halangan')->whereIn('waktu', $validPrayers)->count();
                if ($isAlternatif && $totalHalangan > ($totalHari*3)) $totalHalangan = $totalHari*3;

                $penyebut = $totalWajib - $totalHalangan;
                if ($penyebut > 0) $persenSholat = round(($totalBerjamaah / $penyebut) * 100, 1);
            }

            // Data Pendukung View
            $kriteriaList = KriteriaMonev::where('id_kkn', $mahasiswa->unit->id_kkn)
                                ->orderBy('urutan', 'asc')
                                ->get();

            $dynamicData = [
                'total_jkem'    => $totalJkem . ' Menit',
                'persen_sholat' => $persenSholat . '%',
                'nama_mhs'      => $mahasiswa->userRole->user->nama
            ];

            $evaluasi = EvaluasiMahasiswa::with('details')
                            ->where('id_tim_monev', $monevAssignment->id)
                            ->where('id_mahasiswa', $id_mahasiswa)
                            ->first();

            $existingAnswers = $evaluasi ? $evaluasi->details->pluck('nilai', 'id_kriteria_monev')->toArray() : [];

            return view('tim monev.evaluasi.penilaian-mahasiswa', [
                'mahasiswa'       => $mahasiswa,
                'daftarTeman'     => $daftarTeman, // Kirim list teman buat sidebar
                'evaluasi'        => $evaluasi,        
                'kriteriaList'    => $kriteriaList,   
                'dynamicData'     => $dynamicData,     
                'existingAnswers' => $existingAnswers, 
            ]);

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage()); 
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
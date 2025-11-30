<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Unit;
use App\Models\Mahasiswa;
use App\Models\EvaluasiMahasiswa;
use Illuminate\Validation\Rule;

class MonevController extends Controller
{
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

            $allAssignments = $dosen->timMonevAssignments()->with('kkn')->get();

            if ($allAssignments->isEmpty()) {
                throw new \Exception('Anda tidak memiliki penugasan sebagai Tim Monev.');
            }

            if ($request->has('kkn_id')) {
                $activeAssignment = $allAssignments->firstWhere('id_kkn', $request->kkn_id);
            } else {
                $activeId = session('active_monev_assignment_id');
                $activeAssignment = $allAssignments->find($activeId) ?? $allAssignments->first();
            }

            if (!$activeAssignment) {
                $activeAssignment = $allAssignments->first();
            }

            session(['active_monev_assignment_id' => $activeAssignment->id]);

            $units = \App\Models\Unit::with(['lokasi', 'dpl.dosen.user'])
                                ->where('id_tim_monev', $activeAssignment->id)
                                ->get();

            return view('tim monev.evaluasi.evaluasi-unit', compact(
                'units', 
                'activeAssignment', 
                'allAssignments' 
            ));

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    public function showMahasiswaPage($id_unit)
    {
        try {
            $dosen = Auth::user()->dosen;
            $monevAssignment = $this->getActiveMonevAssignment($dosen)['active'];

            $unit = Unit::with(['mahasiswa.userRole.user', 'dpl.dosen.user'])
                        ->where('id', $id_unit)
                        ->where('id_tim_monev', $monevAssignment->id) 
                        ->firstOrFail();

            return view('tim monev.evaluasi.daftar-mahasiswa', [
                'unit' => $unit
            ]);

        } catch (\Exception $e) {
            return redirect()->route('monev.evaluasi.index')->with('error', 'Unit tidak ditemukan atau Anda tidak memiliki akses.');
        }
    }

    public function showPenilaianPage($id_mahasiswa)
    {
        try {
            $dosen = Auth::user()->dosen;
            $monevAssignment = $this->getActiveMonevAssignment($dosen)['active'];
            
            // Ambil Data Mahasiswa
            $mahasiswa = \App\Models\Mahasiswa::with([
                'userRole.user', 'unit.dpl', 'unit.kkn',
                'logbookSholat', 'kegiatan.logbookKegiatan'
            ])->findOrFail($id_mahasiswa);
            
            // Cek Hak Akses
            if ($mahasiswa->unit->id_tim_monev != $monevAssignment->id) {
                throw new \Exception('Anda tidak berhak menilai mahasiswa ini.');
            }

            //Hitung Variable Dinamis
            $totalJkem = $mahasiswa->kegiatan->pluck('logbookKegiatan')->flatten()->sum('total_jkem');
            
            $persenSholat = 0;
            if ($mahasiswa->unit && $mahasiswa->unit->tanggal_penerjunan && $mahasiswa->unit->tanggal_penarikan) {
                $tglMulai = \Carbon\Carbon::parse($mahasiswa->unit->tanggal_penerjunan);
                $tglSelesai = \Carbon\Carbon::parse($mahasiswa->unit->tanggal_penarikan);
                $totalHari = abs($tglSelesai->diffInDays($tglMulai)) + 1;

                // Untuk periode KKN alternatif, sholat hanya 3 kali per hari (dzuhur, ashar, maghrib)
                $isAlternatif = stripos($mahasiswa->unit->kkn->nama, 'alternatif') !== false;
                $prayersPerDay = $isAlternatif ? 3 : 5;
                $totalWajibSholat = $totalHari * $prayersPerDay;

                // Filter sholat yang dihitung berdasarkan jenis KKN
                $validPrayers = $isAlternatif ? ['dzuhur', 'ashar', 'maghrib'] : ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];

                $totalBerjamaah = $mahasiswa->logbookSholat
                    ->where('status', 'sholat berjamaah')
                    ->whereIn('waktu', $validPrayers)
                    ->count();
                $totalHalangan = $mahasiswa->logbookSholat
                    ->where('status', 'sedang halangan')
                    ->whereIn('waktu', $validPrayers)
                    ->count();
                $penyebut = $totalWajibSholat - $totalHalangan;

                if ($penyebut > 0) {
                    $persenSholat = round(($totalBerjamaah / $penyebut) * 100, 1);
                }
            }

            $kriteriaList = \App\Models\KriteriaMonev::where('id_kkn', $mahasiswa->unit->id_kkn)
                                ->orderBy('urutan', 'asc')
                                ->get();

            // Kamus Data 
            $dynamicData = [
                'total_jkem'    => $totalJkem . ' Menit',
                'persen_sholat' => $persenSholat . '%',
                'nama_mhs'      => $mahasiswa->userRole->user->nama
            ];

            // Ambil Jawaban Eksisting 
            $evaluasi = \App\Models\EvaluasiMahasiswa::with('details')
                            ->where('id_tim_monev', $monevAssignment->id)
                            ->where('id_mahasiswa', $id_mahasiswa)
                            ->first();

            $existingAnswers = [];
            if ($evaluasi) {
                $existingAnswers = $evaluasi->details->pluck('nilai', 'id_kriteria_monev')->toArray();
            }

            return view('tim monev.evaluasi.penilaian-mahasiswa', [
                'mahasiswa'       => $mahasiswa,
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
        // Validasi Array
        $request->validate([
            'nilai' => 'required|array', 
            'nilai.*' => 'required|numeric|in:1,2,3', 
            'catatan_monev' => 'nullable|string|max:5000',
        ]);

        try {
            $dosen = Auth::user()->dosen;
            $monevAssignment = $this->getActiveMonevAssignment($dosen)['active'];
            $mahasiswa = \App\Models\Mahasiswa::findOrFail($id_mahasiswa);

            if ($mahasiswa->unit->id_tim_monev != $monevAssignment->id) {
                throw new \Exception('Anda tidak berhak menilai mahasiswa ini.');
            }

            // Simpan Header Evaluasi 
            $evaluasiHeader = \App\Models\EvaluasiMahasiswa::updateOrCreate(
                [
                    'id_tim_monev' => $monevAssignment->id,
                    'id_mahasiswa' => $id_mahasiswa,
                ],
                [
                    'catatan_monev' => $request->catatan_monev
                ]
            );

            // Simpan Detail Jawaban 
            foreach ($request->nilai as $idKriteria => $skor) {
                \App\Models\EvaluasiMahasiswaDetail::updateOrCreate(
                    [
                        'id_evaluasi_mahasiswa' => $evaluasiHeader->id,
                        'id_kriteria_monev'     => $idKriteria
                    ],
                    [
                        'nilai' => $skor
                    ]
                );
            }

            return redirect()->route('monev.evaluasi.penilaian', $id_mahasiswa)
                             ->with('success', 'Penilaian berhasil disimpan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
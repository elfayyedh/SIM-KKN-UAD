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

    public function index()
    {
        try {
            $dosen = Auth::user()->dosen;
            if (!$dosen) throw new \Exception('Profil Dosen tidak ditemukan.');

            $assignments = $this->getActiveMonevAssignment($dosen);
            $monevAssignment = $assignments['active']; 
            $allMonevAssignments = $assignments['all']; 

            $units = Unit::with(['lokasi', 'dpl.dosen.user'])
                                ->where('id_tim_monev', $monevAssignment->id)
                                ->get();

            return view('tim monev.evaluasi.evaluasi-unit', compact(
                'units', 
                'monevAssignment', 
                'allMonevAssignments'
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
            
            $mahasiswa = Mahasiswa::with([
                                'userRole.user', 
                                'unit.dpl', 
                                'unit.kkn',
                                'logbookSholat', 
                                'kegiatan.logbookKegiatan'
                            ])->findOrFail($id_mahasiswa);
            
            if ($mahasiswa->unit->id_tim_monev != $monevAssignment->id) {
                throw new \Exception('Anda tidak berhak menilai mahasiswa ini.');
            }

            $totalJkem = $mahasiswa->kegiatan->pluck('logbookKegiatan')->flatten()->sum('total_jkem');
            
            $persenSholat = 0;
            if ($mahasiswa->unit && $mahasiswa->unit->tanggal_penerjunan && $mahasiswa->unit->tanggal_penarikan) {
                $tglMulai = \Carbon\Carbon::parse($mahasiswa->unit->tanggal_penerjunan);
                $tglSelesai = \Carbon\Carbon::parse($mahasiswa->unit->tanggal_penarikan);
                $totalHari = abs($tglSelesai->diffInDays($tglMulai)) + 1;
                $totalWajibSholat = $totalHari * 5;
                
                $totalBerjamaah = $mahasiswa->logbookSholat->where('status', 'sholat berjamaah')->count();
                $totalHalangan = $mahasiswa->logbookSholat->where('status', 'sedang halangan')->count();
                $penyebut = $totalWajibSholat - $totalHalangan;

                if ($penyebut > 0) {
                    $persenSholat = round(($totalBerjamaah / $penyebut) * 100, 1);
                }
            }
            
            $evaluasi = EvaluasiMahasiswa::where('id_tim_monev', $monevAssignment->id)
                                        ->where('id_mahasiswa', $id_mahasiswa)
                                        ->first();

            return view('tim monev.evaluasi.penilaian-mahasiswa', [
                'mahasiswa' => $mahasiswa,
                'evaluasi' => $evaluasi,
                'totalJkem' => $totalJkem,
                'persenSholat' => $persenSholat, 
            ]);

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage()); 
        }
    }

    public function storePenilaian(Request $request, $id_mahasiswa)
    {
        $request->validate([
            'eval_jkem' => 'required|numeric|in:1,2,3',
            'eval_form1' => 'required|numeric|in:1,2,3',
            'eval_form2' => 'required|numeric|in:1,2,3',
            'eval_form3' => 'required|numeric|in:1,2,3',
            'eval_form4' => 'required|numeric|in:1,2,3',
            'eval_sholat' => 'required|numeric|in:1,2,3',
            'catatan_monev' => 'nullable|string|max:5000',
        ]);

        try {
            $dosen = Auth::user()->dosen;
            $monevAssignment = $this->getActiveMonevAssignment($dosen)['active'];
            $mahasiswa = Mahasiswa::findOrFail($id_mahasiswa);

            if ($mahasiswa->unit->id_tim_monev != $monevAssignment->id) {
                throw new \Exception('Anda tidak berhak menilai mahasiswa ini.');
            }

            EvaluasiMahasiswa::updateOrCreate(
                [
                    'id_tim_monev' => $monevAssignment->id,
                    'id_mahasiswa' => $id_mahasiswa,
                ],
                [
                    'eval_jkem' => $request->eval_jkem,
                    'eval_form1' => $request->eval_form1,
                    'eval_form2' => $request->eval_form2,
                    'eval_form3' => $request->eval_form3,
                    'eval_form4' => $request->eval_form4,
                    'eval_sholat' => $request->eval_sholat,
                    'catatan_monev' => $request->catatan_monev,
                ]
            );

            return redirect()->route('monev.evaluasi.penilaian', $id_mahasiswa)
                             ->with('success', 'Penilaian berhasil disimpan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
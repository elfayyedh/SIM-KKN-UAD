<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dpl;
use App\Models\TimMonev;
use App\Models\Dosen;
use Illuminate\Validation\Rule;
use App\Models\Mahasiswa;

class MonevController extends Controller
{
    /**
     * Helper untuk mendapatkan penugasan Monev yang sedang AKTIF
     * dari session.
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

    /**
     * Menampilkan halaman utama Evaluasi Unit (2 kotak).
     * (Fungsi ini sudah benar)
     */
    public function index()
    {
        try {
            $dosen = Auth::user()->dosen;
            if (!$dosen) {
                throw new \Exception('Profil Dosen tidak ditemukan.');
            }

            // Gunakan helper
            $assignments = $this->getActiveMonevAssignment($dosen);
            $monevAssignment = $assignments['active']; 
            $allMonevAssignments = $assignments['all']; 
            
            $kkn_id = $monevAssignment->id_kkn;

            $selfDplIds = $dosen->dplAssignments()
                               ->where('id_kkn', $kkn_id)
                               ->pluck('id');

            $selectedDpls = $monevAssignment->dplYangDievaluasi()
                                ->with('dosen.user') 
                                ->get();
            
            $availableDpls = Dpl::where('id_kkn', $kkn_id) 
                                ->whereNotIn('id', $selectedDpls->pluck('id')) 
                                ->whereNotIn('id', $selfDplIds) 
                                ->with('dosen.user') 
                                ->get();

            return view('tim monev.evaluasi.evaluasi-index', compact(
                'availableDpls', 
                'selectedDpls', 
                'monevAssignment', 
                'allMonevAssignments' 
            ));

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
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


    public function assignDpl(Request $request)
    {
        $request->validate(['id_dpl' => 'required|uuid|exists:dpl,id']);

        try {
            $dosen = Auth::user()->dosen;
            $monevAssignment = $this->getActiveMonevAssignment($dosen)['active'];

            if ($monevAssignment->dplYangDievaluasi()->count() >= 3) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal: Anda hanya dapat memilih maksimal 3 DPL.'
                ], 403); 
            }

            $monevAssignment->dplYangDievaluasi()->attach($request->id_dpl);
            
            $newlySelectedDpl = Dpl::with('dosen.user')->find($request->id_dpl);

            return response()->json([
                'status' => 'success',
                'message' => 'DPL berhasil ditambahkan.',
                'data' => $newlySelectedDpl
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function removeDpl(Request $request)
    {
        $request->validate(['id_dpl' => 'required|uuid|exists:dpl,id']);

        try {
            $dosen = Auth::user()->dosen;
            $monevAssignment = $this->getActiveMonevAssignment($dosen)['active'];

            $monevAssignment->dplYangDievaluasi()->detach($request->id_dpl);

            return response()->json([
                'status' => 'success',
                'message' => 'DPL berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function showDplUnits($id_dpl)
    {
        try {
            $dosen = Auth::user()->dosen;
            $monevAssignment = $this->getActiveMonevAssignment($dosen)['active'];
            
            // Cek keamanan berdasarkan $monevAssignment yang AKTIF
            if (!$monevAssignment->dplYangDievaluasi()->where('dpl.id', $id_dpl)->exists()) {
                throw new \Exception('Anda tidak ditugaskan untuk mengevaluasi DPL ini.');
            }

            $dpl = Dpl::with([
                'dosen.user', 
                'units.lokasi', 
                'units.mahasiswa',
                'units.prokers.kegiatan' 
            ])->findOrFail($id_dpl);

            $dpl->units->each(function ($unit) use ($dpl) {
                $unit->kkn_nama = $dpl->kkn ? $dpl->kkn->nama : 'KKN Tanpa Nama';
                
                $total_jkem_unit = $unit->prokers->sum(function ($proker) {
                    return $proker->kegiatan ? $proker->kegiatan->sum('total_jkem') : 0;
                });
                $unit->total_jkem_all_prokers = $total_jkem_unit;
            });

            return view('tim monev.evaluasi.evaluasi-dpl-unit', [
                'dpl' => $dpl,
                'units' => $dpl->units
            ]);

        } catch (\Exception $e) {
            return redirect()->route('monev.evaluasi.index')->with('error', $e->getMessage());
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
                            ])
                            ->findOrFail($id_mahasiswa);
            
            $dplUnit = $mahasiswa->unit->dpl;
            if (!$monevAssignment->dplYangDievaluasi()->where('dpl.id', $dplUnit->id)->exists()) {
                throw new \Exception('Anda tidak ditugaskan untuk mengevaluasi mahasiswa di unit ini.');
            }

            // 1. Hitung Total JKEM (Sudah Aman)
            $totalJkem = $mahasiswa->kegiatan->pluck('logbookKegiatan')->flatten()->sum('total_jkem');
            
            // ==========================================================
            // 🔥 PERBAIKAN LOGIKA SHOLAT DENGAN abs()
            // ==========================================================
            $persenSholat = 0;
            $totalWajibSholat = 0;
            $totalBerjamaah = 0;
            $totalHalangan = 0;
            $penyebut = 0;
            $totalHari = 0;

            if ($mahasiswa->unit && $mahasiswa->unit->tanggal_penerjunan && $mahasiswa->unit->tanggal_penarikan) {
                
                $tglMulai = \Carbon\Carbon::parse($mahasiswa->unit->tanggal_penerjunan);
                $tglSelesai = \Carbon\Carbon::parse($mahasiswa->unit->tanggal_penarikan);
                
                // 🔥 PERBAIKAN DI SINI:
                // Kita pakai abs() untuk memastikan hasilnya selalu positif
                $totalHari = abs($tglSelesai->diffInDays($tglMulai)) + 1;
                $totalWajibSholat = $totalHari * 5;
                
                $totalBerjamaah = $mahasiswa->logbookSholat->where('status', 'sholat berjamaah')->count();
                $totalHalangan = $mahasiswa->logbookSholat->where('status', 'sedang halangan')->count();

                $penyebut = $totalWajibSholat - $totalHalangan;

                if ($penyebut > 0) {
                    $persenSholat = round(($totalBerjamaah / $penyebut) * 100, 1);
                }
            }
            
            // ==========================================================
            // 🔥 KITA HAPUS dd() AGAR HALAMAN TAMPIL
            // ==========================================================
            
            $evaluasi = \App\Models\EvaluasiMahasiswa::where('id_tim_monev', $monevAssignment->id)
                                                    ->where('id_mahasiswa', $id_mahasiswa)
                                                    ->first();

            return view('tim monev.evaluasi.penilaian-mahasiswa', [
                'mahasiswa' => $mahasiswa,
                'evaluasi' => $evaluasi,
                'totalJkem' => $totalJkem,
                'persenSholat' => $persenSholat, 
            ]);

        } catch (\Exception $e) {
            dd($e); // Biarkan dd() di sini untuk jaga-jaga
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

            $dplUnit = $mahasiswa->unit->dpl;
            if (!$monevAssignment->dplYangDievaluasi()->where('dpl.id', $dplUnit->id)->exists()) {
                throw new \Exception('Anda tidak ditugaskan untuk mengevaluasi mahasiswa di unit ini.');
            }

            \App\Models\EvaluasiMahasiswa::updateOrCreate(
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

            return redirect()->route('mahasiswa.show', $id_mahasiswa)
                             ->with('success', 'Penilaian berhasil disimpan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
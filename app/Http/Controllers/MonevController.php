<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dpl;
use App\Models\TimMonev;
use App\Models\Dosen;
use Illuminate\Validation\Rule;

class MonevController extends Controller
{
    /**
     * Helper untuk mendapatkan penugasan Monev yang sedang AKTIF
     * dari session.
     */
    private function getActiveMonevAssignment($dosen)
    {
        // 1. Ambil semua penugasan Monev
        $allMonevAssignments = $dosen->timMonevAssignments()->with('kkn')->get();

        if ($allMonevAssignments->isEmpty()) {
            throw new \Exception('Dosen ini tidak memiliki penugasan Tim Monev.');
        }

        // 2. Cek session
        $activeAssignmentId = session('active_monev_assignment_id');

        // 3. Cari yang aktif
        $activeMonevAssignment = $allMonevAssignments->find($activeAssignmentId);

        // 4. Jika session tidak ada, atau tidak valid
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

    /**
     * [BARU] Fungsi untuk mengganti KKN aktif di session.
     * (Fungsi ini sudah benar)
     */
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


    /**
     * [AJAX] Menambahkan DPL ke daftar evaluasi.
     */
    public function assignDpl(Request $request)
    {
        $request->validate(['id_dpl' => 'required|uuid|exists:dpl,id']);

        try {
            $dosen = Auth::user()->dosen;
            
            // ==========================================================
            // 🔥 PERBAIKAN DI SINI (Gunakan helper, BUKAN firstOrFail)
            // ==========================================================
            $monevAssignment = $this->getActiveMonevAssignment($dosen)['active'];

            // --- CONSTRAINT 1: Maksimal 3 DPL ---
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

    /**
     * [AJAX] Menghapus DPL dari daftar evaluasi.
     */
    public function removeDpl(Request $request)
    {
        $request->validate(['id_dpl' => 'required|uuid|exists:dpl,id']);

        try {
            $dosen = Auth::user()->dosen;
            
            // ==========================================================
            // 🔥 PERBAIKAN DI SINI (Gunakan helper, BUKAN firstOrFail)
            // ==========================================================
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

    /**
     * Menampilkan halaman daftar unit dari DPL yang dipilih.
     */
    public function showDplUnits($id_dpl)
    {
        try {
            $dosen = Auth::user()->dosen;
            
            // ==========================================================
            // 🔥 PERBAIKAN DI SINI (Gunakan helper, BUKAN firstOrFail)
            // ==========================================================
            $monevAssignment = $this->getActiveMonevAssignment($dosen)['active'];
            
            // Cek keamanan berdasarkan $monevAssignment yang AKTIF
            if (!$monevAssignment->dplYangDievaluasi()->where('dpl.id', $id_dpl)->exists()) {
                throw new \Exception('Anda tidak ditugaskan untuk mengevaluasi DPL ini.');
            }

            $dpl = Dpl::with([
                'dosen.user', 
                'units.lokasi', 
                'units.mahasiswa',
                'units.prokers.kegiatan' // Load ini untuk hitungan JKEM
            ])->findOrFail($id_dpl);

            // Tambahkan logika hitungan JKEM (dari UnitController)
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
}
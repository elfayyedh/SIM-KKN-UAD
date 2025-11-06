<?php

namespace App\Http\Controllers;

// 1. IMPORT SEMUA MODEL YANG KITA BUTUHKAN
use App\Models\Dpl;
use App\Models\Dosen;
use App\Models\TimMonev;
use App\Models\PenugasanEvaluasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TimMonevController extends Controller
{
    private function checkMonevAccess()
    {
        $activeUserRole = Auth::user()->userRoles()->find(session('selected_role'));
        if (!$activeUserRole || $activeUserRole->role->nama_role != 'Tim Monev') {
            abort(403, 'Akses Ditolak: Hanya untuk Tim Monev.');
        }
        return $activeUserRole;
    }

    public function index()
    {
        try {
            $activeUserRole = $this->checkMonevAccess();
            $user = Auth::user();
            $dosen = $user->dosen;
            $kkn_id = $activeUserRole->id_kkn;

            if (!$dosen) {
                throw new \Exception('Profil Dosen (Monev) tidak ditemukan.');
            }

            $monevAssignment = $dosen->timMonevAssignments()->where('id_kkn', $kkn_id)->first();
            
            if (!$monevAssignment) {
                throw new \Exception('Penugasan Tim Monev untuk KKN ini tidak ditemukan.');
            }

            $dpl_dipilih_ids = $monevAssignment->dplYangDievaluasi()->pluck('id_dpl');

            $dpl_tersedia = Dpl::where('id_kkn', $kkn_id)
                ->where('id_dosen', '!=', $dosen->id) 
                ->whereNotIn('id', $dpl_dipilih_ids)   
                ->with('dosen.user', 'units') 
                ->get();

            $dpl_dipilih = PenugasanEvaluasi::where('id_tim_monev', $monevAssignment->id)
                                ->with('dpl.dosen.user', 'dpl.units')
                                ->get();

            return view('tim monev.evaluasi', compact('dpl_tersedia', 'dpl_dipilih'));

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Gagal memuat halaman evaluasi: ' . $e->getMessage());
        }
    }

    public function pilih($id_dpl) 
    {
        try {
            $activeUserRole = $this->checkMonevAccess();
            $dosen = Auth::user()->dosen;
            $kkn_id = $activeUserRole->id_kkn;
            $monevAssignment = $dosen->timMonevAssignments()->where('id_kkn', $kkn_id)->first();

            if (!$monevAssignment) {
                throw new \Exception('Penugasan Monev tidak ditemukan.');
            }

            PenugasanEvaluasi::firstOrCreate(
                [
                    'id_tim_monev' => $monevAssignment->id,
                    'id_dpl' => $id_dpl
                ],
                ['status' => 'pending'] 
            );

            return redirect()->back()->with('success', 'DPL berhasil ditambahkan ke daftar evaluasi.');
        
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memilih DPL: ' . $e->getMessage());
        }
    }

    public function hapus($id_penugasan) 
    {
        try {
            $activeUserRole = $this->checkMonevAccess();
            $dosen = Auth::user()->dosen;
            $kkn_id = $activeUserRole->id_kkn;
            $monevAssignment = $dosen->timMonevAssignments()->where('id_kkn', $kkn_id)->first();

            $penugasan = PenugasanEvaluasi::where('id', $id_penugasan)
                            ->where('id_tim_monev', $monevAssignment->id) 
                            ->first();
            
            if ($penugasan) {
                $penugasan->delete();
                return redirect()->back()->with('success', 'DPL berhasil dihapus dari daftar.');
            }

            return redirect()->back()->with('error', 'Gagal menghapus DPL (data tidak ditemukan).');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus DPL: ' . $e->getMessage());
        }
    }

    public function showForm($id_penugasan) 
    {
        try {
            $activeUserRole = $this->checkMonevAccess();
            $dosen = Auth::user()->dosen;
            $kkn_id = $activeUserRole->id_kkn;
            $monevAssignment = $dosen->timMonevAssignments()->where('id_kkn', $kkn_id)->first();

            $penugasan = PenugasanEvaluasi::where('id', $id_penugasan)
                                        // (Ganti 'tim_monev_id' -> 'id_tim_monev')
                                        ->where('id_tim_monev', $monevAssignment->id) // Keamanan!
                                        ->with('dpl.dosen.user', 'dpl.units.mahasiswa')
                                        ->firstOrFail(); 
            
            return "Ini adalah halaman FORM evaluasi untuk: " . $penugasan->dpl->dosen->user->nama;
        
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuka form: ' . $e->getMessage());
        }
    }
}
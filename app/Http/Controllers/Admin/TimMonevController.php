<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimMonev;
use App\Models\Dosen;
use App\Models\KKN;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class TimMonevController extends Controller
{
    /**
     * Menampilkan Halaman Daftar Tim Monev
     */
    public function index()
    {
        // Ambil semua data Tim Monev beserta relasinya
        $timMonev = TimMonev::with(['dosen.user', 'kkn'])->get();
        return view('administrator.read.show-tim-monev', compact('timMonev'));
    }

    /**
     * Menampilkan Halaman Tambah (Create)
     */
    public function create()
    {
        $dosen = Dosen::with('user')->get();
        $kkn = KKN::where('status', 1)->get(); // Ambil KKN Aktif
        return view('administrator.create.create-tim-monev', compact('dosen', 'kkn'));
    }

    /**
     * Menyimpan Data Baru (Store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_dosen' => 'required|exists:dosen,id',
            'units'    => 'required|array', 
            'units.*'  => 'exists:unit,id',
        ]);

        DB::beginTransaction();
        try {
            $dosenId = $request->id_dosen;
            
            $selectedUnits = Unit::whereIn('id', $request->units)->get();

            $unitsGroupedByKkn = $selectedUnits->groupBy('id_kkn');

            foreach ($unitsGroupedByKkn as $kknId => $units) {
                $timMonev = TimMonev::firstOrCreate([
                    'id_dosen' => $dosenId,
                    'id_kkn'   => $kknId
                ]);

                $unitIdsInGroup = $units->pluck('id')->toArray();
                
                Unit::whereIn('id', $unitIdsInGroup)->update([
                    'id_tim_monev' => $timMonev->id
                ]);
            }

            DB::commit();
            return redirect()->route('tim-monev.index')->with('success', 'Penugasan Tim Monev berhasil disimpan (Multi-KKN Supported).');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan Halaman Edit
     */
    public function edit($id)
    {
        $timMonev = TimMonev::with('kkn')->findOrFail($id);
        $dosen = Dosen::with('user')->get();
        $kkn = KKN::where('status', 1)->get();

        // Ambil Unit untuk plotting di halaman edit
        $units = Unit::with(['lokasi', 'dpl.dosen.user', 'timMonev.dosen.user'])
                    ->where('id_kkn', $timMonev->id_kkn)
                    ->orderBy('nama', 'asc')
                    ->get();

        return view('administrator.update.edit-tim-monev', compact('timMonev', 'dosen', 'kkn', 'units'));
    }

    /**
     * Update Data (Simpan Perubahan)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_dosen' => 'required|exists:dosen,id',
            'id_kkn'   => 'required|exists:kkn,id',
            'units'    => 'nullable|array',
        ]);

        $timMonev = TimMonev::findOrFail($id);

        DB::beginTransaction();
        try {
            // Update Info Dasar
            $timMonev->update([
                'id_dosen' => $request->id_dosen,
                'id_kkn'   => $request->id_kkn,
            ]);

            // Reset Plotting Lama
            Unit::where('id_tim_monev', $timMonev->id)->update(['id_tim_monev' => null]);

            // Set Plotting Baru
            if ($request->has('units')) {
                Unit::whereIn('id', $request->units)->update(['id_tim_monev' => $timMonev->id]);
            }

            DB::commit();
            return redirect()->route('tim-monev.index')->with('success', 'Data berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Data (Destroy)
     */
    public function destroy($id)
    {
        try {
            $timMonev = TimMonev::findOrFail($id);
            $timMonev->delete();
            return redirect()->route('tim-monev.index')->with('success', 'Tim Monev berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * API AJAX Get Units
     */
    public function getUnitsByKkn(Request $request, $id_kkn)
    {
        try {
            $units = \App\Models\Unit::with([
                'kkn',
                'lokasi',
                'dpl.dosen.user',      
                'timMonev.dosen.user'  
            ])
            ->where('id_kkn', $id_kkn)
            ->orderBy('nama', 'asc')
            ->get();

            return response()->json($units);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllActiveUnits()
    {
        try {
            $activeKknIds = KKN::where('status', 1)->pluck('id');
            if ($activeKknIds->isEmpty()) {
                $activeKknIds = [KKN::latest()->value('id')];
            }
            $units = Unit::with(['kkn', 'lokasi', 'dpl.dosen.user', 'timMonev.dosen.user'])
                        ->whereIn('id_kkn', $activeKknIds)
                        ->get();

            return response()->json($units);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Dpl;
use App\Models\Dosen;
use App\Models\KKN;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DplController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        if ($role != "Admin") {
            return view('not-found');
        }

        $dpl = Dpl::with(['dosen.user', 'kkn'])->get();
        return view('administrator.read.show-dpl', compact('dpl'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        if ($role != "Admin") {
            return view('not-found');
        }

        $kkn = KKN::all();
        $dosen = Dosen::with('user')->get();
        return view('administrator.create.create-dpl', compact('kkn', 'dosen'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        if ($role != "Admin") {
            return view('not-found');
        }

        $validator = Validator::make($request->all(), [
            'id_dosen' => 'required|exists:dosen,id',
            'units'    => 'required|array',
            'units.*'  => 'exists:unit,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $dosenId = $request->id_dosen;
            $selectedUnits = Unit::whereIn('id', $request->units)->get();
            $unitsGroupedByKkn = $selectedUnits->groupBy('id_kkn');

            foreach ($unitsGroupedByKkn as $kknId => $units) {
                $dpl = Dpl::firstOrCreate([
                    'id_dosen' => $dosenId,
                    'id_kkn'   => $kknId
                ]);

                $unitIds = $units->pluck('id')->toArray();
                
                Unit::whereIn('id', $unitIds)->update([
                    'id_dpl' => $dpl->id
                ]);
            }

            DB::commit();
            return redirect()->route('dpl.index')->with('success', 'DPL berhasil ditambahkan dan Unit berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        if ($role != "Admin") {
            return view('not-found');
        }

        $dpl = Dpl::findOrFail($id);
        $kkn = KKN::all();
        $dosen = Dosen::with('user')->get();
        return view('administrator.update.edit-dpl', compact('dpl', 'kkn', 'dosen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        if ($role != "Admin") {
            return view('not-found');
        }

        $validator = Validator::make($request->all(), [
            'id_dosen' => 'required|exists:dosen,id',
            'id_kkn'   => 'required|exists:kkn,id',
            'units'    => 'nullable|array', // Array unit yang dicentang
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $dpl = Dpl::findOrFail($id);
            $existing = Dpl::where('id_dosen', $request->id_dosen)
                           ->where('id_kkn', $request->id_kkn)
                           ->where('id', '!=', $id)
                           ->first();

            if ($existing) {
                return redirect()->back()->with('error', 'Dosen tersebut sudah menjadi DPL di KKN ini (Duplicate).');
            }

            $dpl->update([
                'id_dosen' => $request->id_dosen,
                'id_kkn'   => $request->id_kkn
            ]);
            Unit::where('id_dpl', $dpl->id)->update(['id_dpl' => null]);

            if ($request->has('units')) {
                Unit::whereIn('id', $request->units)->update([
                    'id_dpl' => $dpl->id
                ]);
            }

            DB::commit();
            return redirect()->route('dpl.index')->with('success', 'Data DPL dan Unit Bimbingan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        if ($role != "Admin") {
            return view('not-found');
        }

        try {
            DB::beginTransaction();
            $dpl = Dpl::findOrFail($id);
            $affectedUnits = Unit::where('id_dpl', $dpl->id)->get();
            $unitCount = $affectedUnits->count();
            $unitNames = $affectedUnits->pluck('nama')->implode(', ');

            Unit::where('id_dpl', $dpl->id)->update(['id_dpl' => null]);
            $dpl->delete();

            DB::commit();
            if ($unitCount > 0) {
                return redirect()->route('dpl.index')->with('success', 
                    "DPL berhasil dihapus. PERHATIAN: DPL ini memiliki {$unitCount} unit bimbingan ({$unitNames}) yang kini statusnya telah di-RESET (Tanpa Pembimbing). Harap segera tetapkan DPL baru."
                );
            }
            return redirect()->route('dpl.index')->with('success', 'DPL berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus DPL: ' . $e->getMessage());
        }
    }
}

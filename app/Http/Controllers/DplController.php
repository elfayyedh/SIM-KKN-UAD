<?php

namespace App\Http\Controllers;

use App\Models\Dpl;
use App\Models\Dosen;
use App\Models\KKN;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            'id_kkn' => 'required|exists:kkn,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if already assigned
        $existing = Dpl::where('id_dosen', $request->id_dosen)
                       ->where('id_kkn', $request->id_kkn)
                       ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Dosen sudah ditugaskan sebagai DPL untuk KKN ini.');
        }

        Dpl::create($request->only(['id_dosen', 'id_kkn']));

        return redirect()->route('dpl.index')->with('success', 'DPL berhasil ditambahkan.');
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
            'id_kkn' => 'required|exists:kkn,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dpl = Dpl::findOrFail($id);

        // Check if already assigned (excluding current)
        $existing = Dpl::where('id_dosen', $request->id_dosen)
                       ->where('id_kkn', $request->id_kkn)
                       ->where('id', '!=', $id)
                       ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Dosen sudah ditugaskan sebagai DPL untuk KKN ini.');
        }

        $dpl->update($request->only(['id_dosen', 'id_kkn']));

        return redirect()->route('dpl.index')->with('success', 'DPL berhasil diperbarui.');
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

        $dpl = Dpl::with(['units', 'dosen.user'])->findOrFail($id);
        
        // Cek apakah DPL masih membimbing unit
        $unitCount = $dpl->units()->count();
        
        if ($unitCount > 0) {
            // Ambil nama-nama unit yang masih dibimbing
            $unitNames = $dpl->units()->pluck('nama')->toArray();
            $unitList = implode(', ', $unitNames);
            
            $dosenName = $dpl->dosen->user->nama ?? 'DPL';
            
            return redirect()->route('dpl.index')->with('error', 
                "DPL {$dosenName} tidak dapat dihapus karena masih membimbing {$unitCount} unit: {$unitList}. " .
                "Silakan hapus atau pindahkan unit tersebut terlebih dahulu."
            );
        }
        
        // Jika tidak ada unit yang terkait, baru bisa dihapus
        $dpl->delete();

        return redirect()->route('dpl.index')->with('success', 'DPL berhasil dihapus.');
    }
}

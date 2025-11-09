<?php

namespace App\Http\Controllers;

use App\Models\TimMonev;
use App\Models\Dosen;
use App\Models\KKN;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TimMonevController extends Controller
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

        $timMonev = TimMonev::with(['dosen.user', 'kkn'])->get();
        return view('administrator.read.show-tim-monev', compact('timMonev'));
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
        return view('administrator.create.create-tim-monev', compact('kkn', 'dosen'));
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
        $existing = TimMonev::where('id_dosen', $request->id_dosen)
                            ->where('id_kkn', $request->id_kkn)
                            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Dosen sudah ditugaskan sebagai Tim Monev untuk KKN ini.');
        }

        TimMonev::create($request->only(['id_dosen', 'id_kkn']));

        return redirect()->route('tim-monev.index')->with('success', 'Tim Monev berhasil ditambahkan.');
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

        $timMonev = TimMonev::findOrFail($id);
        $kkn = KKN::all();
        $dosen = Dosen::with('user')->get();
        return view('administrator.update.edit-tim-monev', compact('timMonev', 'kkn', 'dosen'));
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

        $timMonev = TimMonev::findOrFail($id);

        // Check if already assigned (excluding current)
        $existing = TimMonev::where('id_dosen', $request->id_dosen)
                            ->where('id_kkn', $request->id_kkn)
                            ->where('id', '!=', $id)
                            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Dosen sudah ditugaskan sebagai Tim Monev untuk KKN ini.');
        }

        $timMonev->update($request->only(['id_dosen', 'id_kkn']));

        return redirect()->route('tim-monev.index')->with('success', 'Tim Monev berhasil diperbarui.');
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

        $timMonev = TimMonev::findOrFail($id);
        $timMonev->delete();

        return redirect()->route('tim-monev.index')->with('success', 'Tim Monev berhasil dihapus.');
    }
}

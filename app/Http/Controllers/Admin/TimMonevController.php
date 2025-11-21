<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimMonev;
use App\Models\Unit;

class TimMonevController extends Controller
{
    /**
     * Menyimpan data Tim Monev baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_dosen' => 'required|uuid|exists:dosen,id',
            'id_kkn' => 'required|uuid|exists:kkn,id',
        ]);

        $exists = TimMonev::where('id_dosen', $request->id_dosen)
                          ->where('id_kkn', $request->id_kkn)
                          ->exists();
        
        if ($exists) {
            return redirect()->back()->with('error', 'Dosen ini sudah terdaftar sebagai Tim Monev di KKN ini.');
        }

        TimMonev::create([
            'id_dosen' => $request->id_dosen,
            'id_kkn' => $request->id_kkn,
        ]);

        return redirect()->back()->with('success', 'Dosen berhasil ditambahkan sebagai Tim Monev.');
    }

    public function plotting($id)
    {
        $timMonev = TimMonev::with(['dosen', 'kkn'])->findOrFail($id);
        $idDosenMonev = $timMonev->id_dosen;

        $units = Unit::with(['lokasi', 'dpl.dosen'])
                    ->where('id_kkn', $timMonev->id_kkn)
                    ->whereDoesntHave('dpl', function($query) use ($idDosenMonev) {
                        $query->where('id_dosen', $idDosenMonev);
                    })
                    ->orderBy('nama', 'asc')
                    ->get();

        return view('administrator.update.edit-plotting-unit', compact('timMonev', 'units'));
    }

    public function updatePlotting(Request $request, $id)
    {
        $timMonev = TimMonev::findOrFail($id);

        Unit::where('id_tim_monev', $timMonev->id)->update(['id_tim_monev' => null]);

        if ($request->has('units')) {
            $request->validate(['units' => 'array']);
            Unit::whereIn('id', $request->units)->update(['id_tim_monev' => $timMonev->id]);
        }

        return redirect()->route('tim-monev.index')->with('success', 'Plotting unit berhasil disimpan.');
    }
}
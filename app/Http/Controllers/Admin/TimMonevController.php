<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimMonev;

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
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BidangProker;
use Illuminate\Http\Request;

class BidangProkerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                "id_kkn" => 'required|exists:kkn,id',
                "nama" => 'required|string',
                "tipe" => 'required|string',
                "syarat_jkem" => 'required|numeric',
            ],
            [
                'id_kkn.required' => 'KKN harus dipilih',
                'id_kkn.exists' => 'KKN tidak ditemukan',
                'syarat_jkem.required' => 'Minimal JKEM harus diisi',
                'syarat_jkem.numeric' => 'JKEM harus angka',
                'nama.required' => 'Nama Bidang Proker harus diisi',
                'tipe.required' => 'Tipe harus diisi',
            ]
        );

        try {
            $bidangProker = BidangProker::create($validated);
            return redirect()->back()->with('success_bidang', 'Bidang Proker berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_bidang', $e->getMessage());
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, string $id)
{
    $validated = $request->validate(
        [
            "nama" => 'required|string',
            "tipe" => 'required|string',
            "syarat_jkem" => 'required|numeric',
        ],
        [
            'syarat_jkem.required' => 'Minimal JKEM harus diisi',
            'syarat_jkem.numeric' => 'JKEM harus angka',
            'nama.required' => 'Nama Bidang harus diisi',
            'tipe.required' => 'Tipe harus diisi',
        ]
    );

    try {
        $bidangProker = BidangProker::find($id);
        $bidangProker->update($validated);
        return redirect()->back()->with('success_bidang', 'Bidang Proker berhasil diperbaharui');
    } catch (\Exception $e) {
        return redirect()->back()->with('error_bidang', $e->getMessage());
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $bidangProker = BidangProker::find($id);
            $bidangProker->delete();
            return response()->json(['status' => 'success', 'message' => 'Bidang Proker berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Bidang Proker gagal dihapus']);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KriteriaMonev;

class KriteriaMonevController extends Controller
{
    /**
     * Menyimpan Kriteria Baru (Dari Modal Tambah di Halaman Edit KKN)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kkn' => 'required|exists:kkn,id',
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'variable_key' => 'nullable|string',
            'link_url' => 'nullable|string',
            'link_text' => 'nullable|string',
        ]);

        try {
            $lastUrutan = KriteriaMonev::where('id_kkn', $request->id_kkn)->max('urutan');

            KriteriaMonev::create([
                'id_kkn'       => $request->id_kkn,
                'judul'        => $request->judul,
                'keterangan'   => $request->keterangan,
                'variable_key' => $request->variable_key,
                'link_url'     => $request->link_url,
                'link_text'    => $request->link_text,
                'urutan'       => $lastUrutan + 1,
            ]);

            return redirect()->back()->with('success_kriteria', 'Kriteria berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error_kriteria', 'Gagal menambah kriteria: ' . $e->getMessage());
        }
    }

    /**
     * Update Kriteria (Dari Modal Edit)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'variable_key' => 'nullable|string',
            'link_url' => 'nullable|string',
            'link_text' => 'nullable|string',
        ]);

        try {
            $kriteria = KriteriaMonev::findOrFail($id);
            
            $kriteria->update([
                'judul'        => $request->judul,
                'keterangan'   => $request->keterangan,
                'variable_key' => $request->variable_key,
                'link_url'     => $request->link_url,
                'link_text'    => $request->link_text,
            ]);

            return redirect()->back()->with('success_kriteria', 'Kriteria berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error_kriteria', 'Gagal update kriteria: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Kriteria (Dari Modal Hapus)
     */
    public function destroy($id)
    {
        try {
            $kriteria = KriteriaMonev::findOrFail($id);
            $kriteria->delete();

            return redirect()->back()->with('success_kriteria', 'Kriteria berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error_kriteria', 'Gagal menghapus kriteria: ' . $e->getMessage());
        }
    }
}
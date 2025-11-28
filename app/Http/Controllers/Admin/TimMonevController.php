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
     * 1. Menampilkan Halaman Daftar Tim Monev
     */
    public function index()
    {
        // Ambil semua data Tim Monev beserta relasinya
        $timMonev = TimMonev::with(['dosen.user', 'kkn'])->get();

        // Sesuaikan nama view ini dengan lokasi file index kamu
        // Contoh: 'admin.tim_monev.index' atau 'administrator.manajemen_tim_monev.index'
        return view('administrator.read.show-tim-monev', compact('timMonev'));
    }

    /**
     * 2. Menampilkan Halaman Tambah (Create)
     */
    public function create()
    {
        $dosen = Dosen::with('user')->get();
        $kkn = KKN::where('status', 1)->get(); // Ambil KKN Aktif

        // Sesuaikan nama view ini dengan lokasi file create kamu
        return view('administrator.create.create-tim-monev', compact('dosen', 'kkn'));
    }

    /**
     * 3. Menyimpan Data Baru (Store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_dosen' => 'required|exists:dosen,id',
            'id_kkn'   => 'required|exists:kkn,id',
            'units'    => 'nullable|array',
        ]);

        // Cek duplikasi
        $exists = TimMonev::where('id_dosen', $request->id_dosen)
                          ->where('id_kkn', $request->id_kkn)
                          ->exists();
        
        if ($exists) {
            return redirect()->back()->with('error', 'Dosen ini sudah terdaftar sebagai Tim Monev di KKN ini.');
        }

        DB::beginTransaction();
        try {
            // Buat Tim Monev
            $timMonev = TimMonev::create([
                'id_dosen' => $request->id_dosen,
                'id_kkn'   => $request->id_kkn,
            ]);

            // Plotting Unit
            if ($request->has('units')) {
                Unit::whereIn('id', $request->units)->update(['id_tim_monev' => $timMonev->id]);
            }

            DB::commit();
            return redirect()->route('tim-monev.index')->with('success', 'Tim Monev berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * 4. Menampilkan Halaman Edit
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

        // Sesuaikan path view edit kamu
        return view('administrator.update.edit-tim-monev', compact('timMonev', 'dosen', 'kkn', 'units'));
    }

    /**
     * 5. Update Data (Simpan Perubahan)
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
     * 6. Hapus Data (Destroy)
     */
    public function destroy($id)
    {
        try {
            $timMonev = TimMonev::findOrFail($id);
            
            // Unit yang dipegang otomatis jadi NULL karena di migration kita set onDelete('set null')
            // Tapi kalau mau manual di sini juga bisa:
            // Unit::where('id_tim_monev', $timMonev->id)->update(['id_tim_monev' => null]);

            $timMonev->delete();

            return redirect()->route('tim-monev.index')->with('success', 'Tim Monev berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * 7. API AJAX Get Units
     */
    public function getUnitsByKkn(Request $request, $id_kkn)
    {
        $id_dosen_monev = $request->query('id_dosen');

        $units = Unit::with(['lokasi', 'dpl.dosen.user', 'timMonev.dosen.user'])
                    ->where('id_kkn', $id_kkn)
                    ->when($id_dosen_monev, function ($query) use ($id_dosen_monev) {
                        return $query->whereDoesntHave('dpl', function($q) use ($id_dosen_monev) {
                            $q->where('id_dosen', $id_dosen_monev);
                        });
                    })
                    ->orderBy('nama', 'asc')
                    ->get();

        return response()->json($units);
    }
}
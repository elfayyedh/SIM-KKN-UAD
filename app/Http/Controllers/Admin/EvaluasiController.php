<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KKN;
use App\Models\EvaluasiMahasiswa;
use App\Models\EvaluasiMahasiswaDetail;
use App\Models\KriteriaMonev;
use App\Models\Mahasiswa;
use App\Models\Unit;
use App\Models\TimMonev;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EvaluasiExport;

class EvaluasiController extends Controller
{
    public function index(Request $request)
    {
        $kkns = KKN::all();

        $selectedKkn = $request->get('kkn_id');
        $evaluations = collect();

        if ($selectedKkn) {
            $evaluations = EvaluasiMahasiswa::with([
                'mahasiswa.userRole.user',
                'mahasiswa.unit.lokasi.kecamatan.kabupaten',
                'mahasiswa.unit.dpl.dosen.user',
                'timMonev.dosen.user',
                'details.kriteriaMonev'
            ])
            ->whereHas('mahasiswa.unit', function($query) use ($selectedKkn) {
                $query->where('id_kkn', $selectedKkn);
            })
            ->get();
        }

        return view('administrator.read.evaluasi-monev', compact('kkns', 'evaluations', 'selectedKkn'));
    }

    public function export(Request $request)
    {
        $kknId = $request->get('kkn_id');

        if (!$kknId) {
            return redirect()->back()->with('error', 'Pilih KKN terlebih dahulu');
        }

        $kkn = KKN::findOrFail($kknId);
        $filename = 'evaluasi_monev_' . $kkn->nama . '_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new EvaluasiExport($kknId), $filename);
    }
}

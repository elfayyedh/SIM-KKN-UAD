<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluasiMahasiswa;
use App\Models\KKN;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EvaluasiMahasiswaExport;

class EvaluasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id_kkn)
    {
        $kkn = KKN::findOrFail($id_kkn);
        $evaluasi = EvaluasiMahasiswa::with(['mahasiswa.userRole.user', 'timMonev.dosen.user'])
            ->whereHas('mahasiswa', function ($query) use ($id_kkn) {
                $query->where('id_kkn', $id_kkn);
            })
            ->get();

        return view('administrator.read.evaluasi-mahasiswa', compact('kkn', 'evaluasi'));
    }

    /**
     * Export evaluations to Excel.
     */
    public function export($id_kkn)
    {
        $kkn = KKN::findOrFail($id_kkn);
        return Excel::download(new EvaluasiMahasiswaExport($id_kkn), 'evaluasi_mahasiswa_' . $kkn->nama . '.xlsx');
    }
}

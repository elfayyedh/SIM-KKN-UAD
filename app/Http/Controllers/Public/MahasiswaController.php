<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BidangProker;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function showByIdUnit($id) {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Mahasiswa::select('id_unit')->find($id);
        if (Auth::user()->userRoles->find(session('selected_role'))->role->nama_role == "Mahasiswa" && Auth::user()->userRoles->find(session('selected_role'))->mahasiswa->id_unit != $data->id_unit) {
            return view('not-found');
        }
        try {
            $user = Mahasiswa::with([
                'prodi',
                'userRole.user',
                'kkn',
                'unit',
                'logbookKegiatan',
                'logbookSholat'
            ])->findOrFail($id);

            $prokerData = BidangProker::with(['proker' => function ($query) use ($user) {
                if ($query->getModel()->type == 'individu') {
                    $query->where('id_mahasiswa', $user->id);
                }
            }])
                ->where('id_kkn', $user->kkn->id)
                ->get();

            $prokerData->each(function ($item) {
                $totalJKEM = $item->proker->sum(function ($proker) {
                    return $proker->total_jkem;
                });
                $item->total_jkem_bidang = $totalJKEM;
            });


            return view('mahasiswa.profil-user', compact('user', 'prokerData'));
        } catch (\Exception $e) {
            return view('not-found');
        }
    }

    public function getProkerMahasiswa(string $id, string $id_kkn, string $id_unit)
    {
        try {
            $prokerData = BidangProker::where('id_kkn', $id_kkn)
                ->where('tipe', 'individu') // Ambil hanya tipe individu
                ->with([
                    'proker' => function ($query) use ($id_unit, $id) {
                        $query->where('id_unit', $id_unit)
                            ->whereHas('kegiatan', function ($query) use ($id) {
                                $query->where('id_mahasiswa', $id);
                            });
                    },
                    'proker.kegiatan' => function ($query) use ($id) {
                        $query->where('id_mahasiswa', $id);
                    },
                    'proker.kegiatan.mahasiswa.userRole.user',
                    'proker.kegiatan.tanggalRencanaProker',
                    'proker.kegiatan.logbookKegiatan.logbookHarian',
                    'proker.organizer',
                    'proker.tempatDanSasaran',
                ])
                ->get();

            // Ambil bidang yang bertipe unit
            $unitProkerData = BidangProker::where('id_kkn', $id_kkn)
                ->where('tipe', 'unit') // Ambil hanya tipe unit
                ->with([
                    'proker' => function ($query) use ($id_unit) {
                        $query->where('id_unit', $id_unit);
                    },
                    'proker.kegiatan',
                    'proker.kegiatan.mahasiswa.userRole.user',
                    'proker.kegiatan.tanggalRencanaProker',
                    'proker.kegiatan.logbookKegiatan.logbookHarian',
                    'proker.organizer',
                    'proker.tempatDanSasaran',
                ])
                ->get();

            // Gabungkan hasil
            $prokerData = $prokerData->merge($unitProkerData);

            foreach ($prokerData as $bidangProker) {
                foreach ($bidangProker->proker as $proker) {
                    // Hitung total_jkem untuk setiap Proker
                    $proker->total_jkem = $proker->kegiatan->sum('total_jkem');
                }
            }

            return response()->json($prokerData);
        } catch (\Exception $e) {
            return response()->json(['error' => "Failed to load data"], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BidangProker;
use App\Models\Kegiatan;
use App\Models\KKN;
use App\Models\Mahasiswa;
use App\Models\Proker;
use App\Models\Unit;
use App\Models\Dosen;
use App\Models\Dpl;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function showUnits()
    {
        try {
            $activeUserRole = Auth::user()->userRoles()->find(session('selected_role'));
            $kkn_id = $activeUserRole->id_kkn; 
            $dosen = Auth::user()->dosen;
            if (!$dosen) {
                throw new \Exception('Profil Dosen tidak ditemukan.');
            }
            $dplAssignment = Dpl::where('id_dosen', $dosen->id)
                                ->where('id_kkn', $kkn_id)
                                ->first();
            if (!$dplAssignment) {
                throw new \Exception('Penugasan DPL untuk KKN ini tidak ditemukan.');
            }
            $units = $dplAssignment->units() 
                                   ->with(['lokasi.kecamatan.kabupaten', 'prokers.kegiatan']) 
                                   ->withCount('mahasiswa')
                                   ->get(); 

            $units->each(function ($unit) {
                $total_jkem_unit = $unit->prokers->sum(function ($proker) {
                    return $proker->kegiatan->sum('total_jkem');
                });
                $unit->total_jkem_all_prokers = $total_jkem_unit;
            });
            return view('dpl.manajemen unit.unit', compact('dplAssignment', 'units'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Gagal memuat unit: ' . $e->getMessage());
        }
    }

    private function idUserRole()
    {
        return Auth::user()->userRoles->find(session('selected_role'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id = null)
    {
        $userRole = $this->idUserRole();
        $roleName = $userRole->role->nama_role;
        if ($roleName == 'Mahasiswa') {
            $mahasiswaUnitId = $userRole->mahasiswa->id_unit;
            
            if ($id == null) {
                $id = $mahasiswaUnitId;
            } else if ($id != $mahasiswaUnitId) {
                return view('not-found');
            }
        } else if ($roleName == 'DPL') {
            if ($id == null) {
                return view('not-found');
            }
        } else {
            return view('not-found');
        }
        try {
            $unit = Unit::with([
                'kkn', 
                'dpl.dosen.user', 
                'lokasi.kecamatan.kabupaten', 
                'mahasiswa.prodi' 
            ])->findOrFail($id);
        } catch (\Exception $e) {
            return view('not-found');
        }
        if ($roleName == 'Mahasiswa') {
            return view('mahasiswa.manajemen unit.profil-unit', compact('unit'));
        } else if ($roleName == 'DPL') {
            return view('dpl.manajemen unit.profil-unit', compact('unit'));
        }
        return view('not-found');
    }

    public function getAnggota(string $id)
    {
        try {
            $anggota = Mahasiswa::with(['userRole.user', 'prodi'])->where('id_unit', $id)->get();
            $anggota = $anggota->map(function ($anggota) {
                return [
                    'id' => $anggota->id,
                    'nama' => $anggota->userRole->user->nama,
                    'prodi' => $anggota->prodi->nama_prodi,
                    'jenis_kelamin' => $anggota->userRole->user->jenis_kelamin,
                    'jabatan' => $anggota->jabatan,
                    'nim' => $anggota->nim,
                    'no_telp' => $anggota->userRole->user->no_telp,
                ];
            });
            return response()->json($anggota);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getProkerUnit(string $id, string $id_kkn)
    {
        try {
            $prokerData = BidangProker::with([
                'proker' => function ($query) use ($id) {
                    $query->where('id_unit', $id);
                },
                'proker.kegiatan',
                'proker.kegiatan.mahasiswa.userRole.user', 
                'proker.kegiatan.tanggalRencanaProker',
                'proker.kegiatan.logbookKegiatan.logbookHarian',
                'proker.organizer',
                'proker.tempatDanSasaran',
            ])
                ->where('id_kkn', $id_kkn)
                ->get();

            foreach ($prokerData as $bidangProker) {
                foreach ($bidangProker->proker as $proker) {
                    $proker->total_jkem = $proker->kegiatan->sum('total_jkem');
                }
            }
            return response()->json($prokerData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500); 
        }
    }

    public function getMatriks(string $id, string $id_kkn)
    {
        $proker =  BidangProker::with(['proker' => function ($query) use ($id) {
            $query->where('id_unit', $id);
        }, 'proker.kegiatan.tanggalRencanaProker', 'proker.kegiatan.mahasiswa.userRole.user', 'proker.kegiatan.logbookKegiatan.logbookHarian'])->where('id_kkn', $id_kkn)->get();
        return response()->json($proker);
    }


    public function kalender()
    {
        try {
            $unit = $this->idUserRole()->mahasiswa->id_unit;
            return view('mahasiswa.kalender', compact('unit'));
        } catch (\Exception $e) {
            return view('not-found');
        }
    }

    public function getKegiatanByUnit($id)
    {
        // Ambil data proker berdasarkan id_unit
        $proker = Proker::where('id_unit', $id)
            ->with(['kegiatan.tanggalRencanaProker', 'kegiatan.logbookKegiatan.logbookHarian'])
            ->get();

        $data = [];

        foreach ($proker as $p) {
            foreach ($p->kegiatan as $k) {
                // Menangani tanggal rencana proker
                foreach ($k->tanggalRencanaProker as $t) {
                    $data[] = [
                        'tanggal' => $t->tanggal,
                        'nama_kegiatan' => $k->nama,
                        'className' => 'bg-warning',
                        'id' => $k->id
                    ];
                }

                // Menangani logbook kegiatan
                foreach ($k->logbookKegiatan as $l) {
                    if ($l->logbookHarian) { // Pastikan logbookHarian ada
                        $data[] = [
                            'tanggal' => $l->logbookHarian->tanggal, // Akses tanggal langsung dari logbookHarian
                            'nama_kegiatan' => $k->nama,
                            'className' => 'bg-primary',
                            'id' => $k->id
                        ];
                    }
                }
            }
        }

        return response()->json(['status' => 'success', 'data' => $data]);
    }


    // Untuk mendapatkan info kegiatan
    public function getKegiatanInfo(string $id)
    {
        try {
            $kegiatan = Kegiatan::with(['proker.bidang', 'tanggalRencanaProker'])->where('id', $id)->first();

            $data = [
                'nama_kegiatan' => $kegiatan->nama,
                'nama_proker' => $kegiatan->proker->nama,
                'bidang_proker' => $kegiatan->proker->bidang->nama,
            ];

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function generateProkerUnitPdf(string $id_unit, string $id_kkn)
    {
        // Ambil data proker dari database
        $dataProker = Unit::with([
            'kkn',
            'proker.bidang',
            'proker.kegiatan',
            'proker.kegiatan.tanggalRencanaProker',
            'proker.organizer',
        ])->where('id', $id_unit)->first();

        // Transform the data
        $data = [
            'data' => [
                'nama_unit' => $dataProker->nama, // Adjust as needed if dynamic
                'kkn' => $dataProker->kkn->nama,
                'proker' => $dataProker->proker->groupBy(function ($proker) {
                    return $proker->bidang->nama; // Group by bidang name
                })->map(function ($prokers, $bidangNama) {
                    return [
                        'bidang' => $bidangNama,
                        'proker' => $prokers->map(function ($proker) {
                            return [
                                'nama' => $proker->nama,
                                'kegiatan' => $proker->kegiatan->map(function ($kegiatan) {
                                    return [
                                        'nama' => $kegiatan->nama,
                                        'frekuensi' => $kegiatan->frekuensi,
                                        'jkem' => $kegiatan->jkem,
                                        'total_jkem' => $kegiatan->total_jkem,
                                        'tanggal' => $kegiatan->tanggalRencanaProker->map(function ($tanggal) {
                                            return [
                                                'tanggal' => $tanggal->tanggal,
                                            ];
                                        }),
                                    ];
                                }),
                            ];
                        }),
                    ];
                }),
            ],
        ];


        if (!$dataProker) {
            return response()->json(['message' => 'Data not found.']);
        }


        $pdf = PDF::loadView('mahasiswa.manajemen unit.proker_pdf', $data);

        return $pdf->download('proker_unit_' . str_replace('.', '_', $dataProker->nama) . '.pdf');
    }

    public function edit($id)
    {
        try {
            $role = $this->idUserRole()->role->nama_role;
            if ($role == "DPL" || $role == "Mahasiswa") {
                if ($role == "Mahasiswa" && $this->idUserRole()->mahasiswa->id_unit != $id) {
                    return view('not-found');
                }
                $unit = Unit::with(['kkn', 'dpl', 'lokasi', 'mahasiswa'])->findOrFail($id);
                if ($role == "Mahasiswa") {
                    return view('mahasiswa.manajemen unit.edit-unit', compact('unit'));
                } else {
                    return view('dpl.manajemen unit.edit-unit', compact('unit'));
                }

            } else {
                throw new \Exception('Anda tidak mempunyai akses untuk unit ini');
            }
        } catch (\Exception $e) {
            return view('not-found');
        }
    }

    public function updateJabatanAnggota(Request $request)
    {
        $role = $this->idUserRole()->role->nama_role;
        if ($role == "DPL" || $role == "Mahasiswa") {
            if ($role == "Mahasiswa" && $this->idUserRole()->mahasiswa->id_unit != $request->id_unit) {
                return redirect()->back()->with('error', 'Anda tidak mempunyai akses untuk unit ini');
            }
            $request->validate([
                'id_mahasiswa' => 'required|array',
                'jabatan' => 'required|array',
                'id_mahasiswa.*' => 'exists:mahasiswa,id', // Validasi setiap id_mahasiswa ada di tabel mahasiswa
                'jabatan.*' => 'nullable|string|max:255', // Validasi setiap jabatan adalah string yang maksimal 255 karakter
            ]);

            try {

                $idMahasiswa = $request->input('id_mahasiswa');
                $jabatan = $request->input('jabatan');

                // Loop melalui data input dan update jabatan mahasiswa
                foreach ($idMahasiswa as $index => $id) {
                    $mahasiswa = Mahasiswa::find($id);
                    if ($mahasiswa) {
                        $mahasiswa->jabatan = $jabatan[$index] === '-' ? null : $jabatan[$index];
                        $mahasiswa->save();
                    }
                }

                // Redirect atau kembalikan response sesuai kebutuhan
                return redirect()->back()->with('success', 'Data jabatan mahasiswa berhasil disimpan.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Data jabatan mahasiswa gagal disimpan.');
            }
        } else {
            return view('not-found');
        }
    }

    public function updateProfilUnit(Request $request)
    {
        $role = $this->idUserRole()->role->nama_role;
        if ($role != "DPL") {
            return redirect()->back()->with('error', 'Anda tidak mempunyai akses untuk unit ini');
        }
        try {
            $request->validate([
                'id' => 'string|required|exists:unit,id',
                'tanggal_penerjunan' => 'date|required',
                'tanggal_penarikan' => 'date|required',
            ]);

            $unit = Unit::find($request->id);
            $unit->update([
                'tanggal_penerjunan' => $request->tanggal_penerjunan,
                'tanggal_penarikan' => $request->tanggal_penarikan,
            ]);
            return redirect()->back()->with('success', 'Data profil unit berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data profil unit gagal disimpan.' . $e->getMessage());
        }
    }

    public function getRekapKegiatan(Request $request)
    {
        try {
            $id = $request->input('id');
            $id_kkn = $request->input('id_kkn');
            $kegiatan = BidangProker::with(['proker' => function ($query) use ($id) {
                $query->where('id_unit', $id);
            }, 'proker.kegiatan.mahasiswa', 'proker.kegiatan.logbookKegiatan.logbookHarian', 'proker.tempatDanSasaran', 'proker.kegiatan.logbookKegiatan.dana'])->where('id_kkn', $id_kkn)->get();
            return response()->json($kegiatan, 200);
        } catch (\Exception) {
            return response()->json(['message' => 'Data not found.'], 404);
        }
    }
    public function getUnitTable()
    {
        try {
            $activeUserRole = $this->idUserRole();
            $kkn_id = $activeUserRole->id_kkn; 

            $dosen = Auth::user()->dosen; 
            if (!$dosen) {
                throw new \Exception('Profil Dosen tidak ditemukan.');
            }

            $dplAssignment = Dpl::where('id_dosen', $dosen->id)
                                ->where('id_kkn', $kkn_id)
                                ->first();

            if (!$dplAssignment) {
                throw new \Exception('Penugasan DPL untuk KKN ini tidak ditemukan.');
            }

            $units = $dplAssignment->units() 
                                   ->with(['lokasi.kecamatan.kabupaten']) 
                                   ->withCount('mahasiswa')
                                   ->get(); 
            return view('components.unit-table', compact('units'));

        } catch (\Exception $e) {
            return '<p class="text-danger">Error memuat tabel: ' . $e->getMessage() . '</p>';
        }
    }
}
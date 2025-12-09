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
    public function adminShowUnits(Request $request)
    {
        // Get all units from all KKN
        $units = Unit::with([
            'kkn',
            'lokasi.kecamatan.kabupaten', 
            'prokers.kegiatan',
            'dpl.dosen.user'
        ])
        ->withCount('mahasiswa')
        ->get();

        // Calculate total JKEM for each unit
        $units->each(function ($unit) {
            $total_jkem_unit = $unit->prokers->sum(function ($proker) {
                return $proker->kegiatan->sum('total_jkem');
            });
            $unit->total_jkem_all_prokers = $total_jkem_unit;
        });

        return view('administrator.read.show-unit', compact('units'));
    }

    public function showUnits(Request $request)
    {
        try {
            $role = session('active_role');
            $dosen = Auth::user()->dosen;
            if (!$dosen) {
                throw new \Exception('Profil Dosen tidak ditemukan.');
            }

            if ($role == 'dpl') {
                $dplAssignments = Dpl::where('id_dosen', $dosen->id)->with('kkn')->get();

                if ($dplAssignments->isEmpty()) {
                    throw new \Exception('Penugasan DPL tidak ditemukan.');
                }

                $units = collect();
                foreach ($dplAssignments as $assignment) {
                    // Query Unit
                    $unitsFromThisAssignment = $assignment->units()
                        ->with(['lokasi.kecamatan.kabupaten', 'prokers.kegiatan'])
                        ->withCount('mahasiswa')
                        ->get();

                    // Inject Nama KKN 
                    $unitsFromThisAssignment->each(function ($unit) use ($assignment) {
                        $unit->setAttribute('kkn_nama', $assignment->kkn ? $assignment->kkn->nama : 'KKN Tanpa Nama');
                    });

                    $units = $units->merge($unitsFromThisAssignment);
                }

                return view('dpl.manajemen unit.unit', compact('units'));
            }

            elseif ($role == 'monev') {
                $allAssignments = \App\Models\TimMonev::where('id_dosen', $dosen->id)
                                        ->with('kkn')
                                        ->get();

                if ($allAssignments->isEmpty()) {
                    throw new \Exception('Anda tidak memiliki penugasan sebagai Tim Monev.');
                }

                // Filter Dropdown
                if ($request->has('kkn_id')) {
                    $activeAssignment = $allAssignments->firstWhere('id_kkn', $request->kkn_id);
                } else {
                    $activeId = session('active_monev_assignment_id');
                    $activeAssignment = $allAssignments->find($activeId);
                }

                if (!$activeAssignment) $activeAssignment = $allAssignments->first();
                session(['active_monev_assignment_id' => $activeAssignment->id]);

                $units = Unit::with(['lokasi.kecamatan.kabupaten', 'prokers.kegiatan', 'dpl.dosen.user'])
                            ->where('id_tim_monev', $activeAssignment->id)
                            ->withCount('mahasiswa')
                            ->get();

                return view('tim monev.evaluasi.evaluasi-unit', compact(
                    'units', 
                    'activeAssignment', 
                    'allAssignments'
                ));
            }

            else {
                return redirect()->route('dashboard')->with('error', 'Hanya DPL dan Tim Monev yang bisa mengakses halaman ini.');
            }

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Gagal memuat unit: ' . $e->getMessage());
        }
    }

    private function getActiveRoleInfo()
    {
        $roleName = 'Guest';
        $userRole = null;

        if (!Auth::check()) {
            return compact('roleName', 'userRole');
        }

        if (session('user_is_dosen', false)) {
            $roleName = session('active_role'); 
        } else {
            $userRole = Auth::user()->userRoles->find(session('selected_role'));
            if ($userRole && $userRole->role) {
                $roleName = $userRole->role->nama_role;
            }
        }
        return compact('roleName', 'userRole');
    }

    private function idUserRole()
    {
        return Auth::user()->userRoles->find(session('selected_role'));
    }

    public function show(?string $id = null)
    {
        ['roleName' => $roleName, 'userRole' => $userRole] = $this->getActiveRoleInfo();

        if ($roleName == 'Mahasiswa') {
            $mahasiswaUnitId = $userRole->mahasiswa->id_unit;
            
            if ($id == null) {
                $id = $mahasiswaUnitId;
            } else if ($id != $mahasiswaUnitId) {
                return view('not-found');
            }
        } else if ($roleName == 'dpl') { 
            if ($id == null) {
                return view('not-found');
            }
        } else if ($roleName == 'monev') {
            if ($id == null) {
                return view('not-found'); 
            }
        } else if ($roleName == 'Admin') {
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
        } else if ($roleName == 'dpl') {
            return view('dpl.manajemen unit.profil-unit', compact('unit'));
        } else if ($roleName == 'monev') {
            return view('tim monev.evaluasi.profil-unit', compact('unit'));
        } else if ($roleName == 'Admin') {
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
            ['roleName' => $roleName, 'userRole' => $userRole] = $this->getActiveRoleInfo();

            if ($roleName == 'Mahasiswa') {
                if (!$userRole || !$userRole->mahasiswa) {
                    throw new \Exception('Data Mahasiswa tidak ditemukan.');
                }
                $unit = $userRole->mahasiswa->id_unit;
                return view('mahasiswa.kalender', compact('unit'));
            } elseif ($roleName == 'dpl') {
                // For DPL, get units under their supervision
                $dosen = Auth::user()->dosen;
                if (!$dosen) {
                    throw new \Exception('Profil Dosen tidak ditemukan.');
                }
                $dplAssignments = Dpl::where('id_dosen', $dosen->id)->get();
                if ($dplAssignments->isEmpty()) {
                    throw new \Exception('Penugasan DPL tidak ditemukan.');
                }
                $dplAssignment = $dplAssignments->first();
                $units = $dplAssignment->units()->pluck('id')->toArray();
                // Pass the first unit or handle multiple units as needed
                $unit = $units[0] ?? null;
                return view('dpl.kalender', compact('unit'));
            } else {
                return view('not-found');
            }
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
            ['roleName' => $roleName, 'userRole' => $userRole] = $this->getActiveRoleInfo();
            
            if ($roleName == "dpl" || $roleName == "Mahasiswa" || $roleName == "Admin") {
                if ($roleName == "Mahasiswa" && $userRole->mahasiswa->id_unit != $id) {
                    return view('not-found');
                }
                $unit = Unit::with(['kkn', 'dpl', 'lokasi', 'mahasiswa'])->findOrFail($id);
                if ($roleName == "Mahasiswa") {
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
        ['roleName' => $roleName, 'userRole' => $userRole] = $this->getActiveRoleInfo();

        if ($roleName == "dpl" || $roleName == "Mahasiswa" || $roleName == "Admin") {
            if ($roleName == "Mahasiswa" && $userRole->mahasiswa->id_unit != $request->id_unit) {
                return redirect()->back()->with('error', 'Anda tidak mempunyai akses untuk unit ini');
            }
            $request->validate([
                'id_mahasiswa' => 'required|array',
                'jabatan' => 'required|array',
                'id_mahasiswa.*' => 'exists:mahasiswa,id', 
                'jabatan.*' => 'nullable|string|max:255', 
            ]);

            try {
                $idMahasiswa = $request->input('id_mahasiswa');
                $jabatan = $request->input('jabatan');

                foreach ($idMahasiswa as $index => $id) {
                    $mahasiswa = Mahasiswa::find($id);
                    if ($mahasiswa) {
                        $mahasiswa->jabatan = $jabatan[$index] === '-' ? null : $jabatan[$index];
                        $mahasiswa->save();
                    }
                }
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
        ['roleName' => $roleName, 'userRole' => $userRole] = $this->getActiveRoleInfo();

        if ($roleName != "dpl" && $roleName != "Admin") {
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

    public function updateLinkLokasi(Request $request)
    {
        ['roleName' => $roleName, 'userRole' => $userRole] = $this->getActiveRoleInfo();

        if ($roleName == "dpl" || $roleName == "Mahasiswa" || $roleName == "Admin") {
            if ($roleName == "Mahasiswa" && $userRole->mahasiswa->id_unit != $request->id_unit) {
                return redirect()->back()->with('error', 'Anda tidak mempunyai akses untuk mengubah lokasi unit ini');
            }

            $request->validate([
                'id_unit'     => 'required|exists:unit,id',
                'link_lokasi' => 'required|url|active_url',
            ], [
                'link_lokasi.url' => 'Format link harus berupa URL (awalan http:// atau https://)',
                'link_lokasi.active_url' => 'Link tidak valid.'
            ]);

            try {
                $unit = Unit::with('lokasi')->findOrFail($request->id_unit);
                if ($unit->lokasi) {
                    $unit->lokasi->update([
                        'link_lokasi' => $request->link_lokasi
                    ]);
                } else {
                    return redirect()->back()->with('error', 'Data Lokasi (Kecamatan/Desa) belum diatur oleh Admin. Tidak bisa simpan link.');
                }

                return redirect()->back()->with('success', 'Link Google Maps berhasil diperbarui.');

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal menyimpan lokasi: ' . $e->getMessage());
            }

        } else {
            return view('not-found');
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
            ['roleName' => $roleName, 'userRole' => $userRole] = $this->getActiveRoleInfo();

            if ($roleName != 'dpl') {
                throw new \Exception('Hanya DPL yang bisa memuat tabel ini.');
            }

            $dosen = Auth::user()->dosen; 
            if (!$dosen) {
                throw new \Exception('Profil Dosen tidak ditemukan.');
            }

            $dplAssignments = $dosen->dplAssignments()->get();
            if ($dplAssignments->isEmpty()) {
                throw new \Exception('Penugasan DPL tidak ditemukan.');
            }

            $units = collect();
            foreach($dplAssignments as $assignment) {
                $unitsFromThis = $assignment->units() 
                    ->with(['lokasi.kecamatan.kabupaten']) 
                    ->withCount('mahasiswa')
                    ->get();
                $units = $units->merge($unitsFromThis);
            }
            
            return view('components.unit-table', compact('units'));

        } catch (\Exception $e) {
            return '<p class="text-danger">Error memuat tabel: ' . $e->getMessage() . '</p>';
        }
    }

    public function exportAnggotaPDF($id)
    {
        set_time_limit(300); // Set timeout 5 menit
        ini_set('memory_limit', '512M'); // Increase memory limit
        
        $unit = Unit::with(['lokasi.kecamatan.kabupaten', 'dpl.dosen.user', 'kkn'])->findOrFail($id);
        $anggota = Mahasiswa::with(['userRole.user', 'prodi'])
            ->where('id_unit', $id)
            ->get();

        $pdf = PDF::loadView('mahasiswa.manajemen unit.export-anggota-pdf', compact('unit', 'anggota'))
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);
        return $pdf->download('Daftar Anggota Unit ' . $unit->nama . '.pdf');
    }

    public function exportRekapKegiatanPDF($id)
    {
        set_time_limit(300); // Set timeout 5 menit
        ini_set('memory_limit', '512M'); // Increase memory limit
        
        $unit = Unit::with(['lokasi.kecamatan.kabupaten', 'dpl.dosen.user', 'kkn'])->findOrFail($id);
        
        $kegiatan = BidangProker::with([
            'proker' => function ($query) use ($id) {
                $query->where('id_unit', $id)
                    ->with([
                        'tempatDanSasaran',
                        'kegiatan' => function ($q) {
                            $q->with([
                                // Load LogbookKegiatan beserta Dananya
                                'logbookKegiatan' => function($lk) {
                                    $lk->with(['dana', 'logbookHarian']);
                                }
                            ]);
                        }
                    ]);
            }
        ])->where('id_kkn', $unit->id_kkn)->get();

        $pdf = PDF::loadView('mahasiswa.manajemen unit.export-rekap-kegiatan-pdf', compact('unit', 'kegiatan'))
            ->setPaper('a4', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);
        return $pdf->download('Rekap Kegiatan Unit ' . $unit->nama . '.pdf');
    }
}
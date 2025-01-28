<?php

namespace App\Http\Controllers;

use App\Exports\ProkerExport;
use App\Models\BidangProker;
use App\Models\Kegiatan;
use App\Models\Mahasiswa;
use App\Models\Organizer;
use App\Models\Proker;
use App\Models\TanggalRencanaProker;
use App\Models\TempatSasaran;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ProkerController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private function roleUser()
    {
        return Auth::user()->userRoles->find(session('selected_role'));
    }
    public function indexProkerUnit()
    {
        $id_unit = $this->roleUser()->mahasiswa->id_unit; // TODO: get this from the authenticated user
        try {
            $unit = Unit::where('id', $id_unit)->first();
            $bidang_proker = BidangProker::where('id_kkn', $this->roleUser()->id_kkn)->where('tipe', 'unit')->get();
            if ($unit) {
                return view('mahasiswa.manajemen unit.proker-bersama-read', compact('unit', 'bidang_proker'));
            } else {
                return view('not-found');
            }
        } catch (\Exception $e) {
            return view('not-found');
        }
    }
    public function indexProkerIndividu()
    {
        try {
            $id_mahasiswa = $this->roleUser()->mahasiswa->id;
            $id_kkn = $this->roleUser()->mahasiswa->id_kkn;
            $mahasiswa = Mahasiswa::where('id', $id_mahasiswa)->first();
            $bidang_proker = BidangProker::where('id_kkn', $this->roleUser()->id_kkn)->where('tipe', 'individu')->get();

            if ($mahasiswa) {
                return view('mahasiswa.manajemen proker.read-proker', compact('mahasiswa', 'bidang_proker'));
            } else {
                return view('not-found');
            }
        } catch (\Exception $e) {
            return view('not-found');
        }
    }

    // dipakai pada ajax readprokerbersama
    public function getProkerUnit()
    {
        $id_unit = $this->roleUser()->mahasiswa->id_unit; // TODO: get this from the authenticated user
        $id_kkn = $this->roleUser()->mahasiswa->id_kkn;
        $prokerData = BidangProker::with([
            'proker' => function ($query) use ($id_unit) {
                $query->where('id_unit', $id_unit);
            },
            'proker.kegiatan',
            'proker.kegiatan.tanggalRencanaProker',
            'proker.organizer',
            'proker.tempatDanSasaran',
        ])
            ->where('id_kkn', $id_kkn)
            ->where('tipe', 'unit')
            ->get();

        foreach ($prokerData as $bidangProker) {

            foreach ($bidangProker->proker as $proker) {
                // Hitung total_jkem untuk setiap Proker
                $proker->total_jkem = $proker->kegiatan->sum('total_jkem');
            }
        }

        return response()->json($prokerData);
    }

    // dipakai pada ajax read proker individu
    public function getProkerIndividu()
    {
        $id_unit = $this->roleUser()->mahasiswa->id_unit;
        $id_kkn = $this->roleUser()->mahasiswa->id_kkn;
        $id_mahasiswa = $this->roleUser()->mahasiswa->id;

        $prokerData = BidangProker::where('id_kkn', $id_kkn)
            ->where('tipe', 'individu')
            ->with([
                'proker' => function ($query) use ($id_unit, $id_mahasiswa) {
                    $query->where('id_unit', $id_unit)
                        ->whereHas('kegiatan', function ($query) use ($id_mahasiswa) {
                            $query->where('id_mahasiswa', $id_mahasiswa);
                        });
                },
                'proker.kegiatan' => function ($query) use ($id_mahasiswa) {
                    $query->where('id_mahasiswa', $id_mahasiswa);
                },
                'proker.kegiatan.tanggalRencanaProker',
                'proker.tempatDanSasaran',
            ])
            ->get();

        foreach ($prokerData as $bidangProker) {

            foreach ($bidangProker->proker as $proker) {
                // Hitung total_jkem untuk setiap Proker
                $proker->total_jkem = $proker->kegiatan->sum('total_jkem');
            }
        }

        return response()->json($prokerData);
    }




    /**
     * Show the form for creating a new resource.
     */
    public function createUnit()
    {
        $unitData = $this->roleUser()->mahasiswa->unit;
        $id_unit = $unitData->id;
        $id_kkn = $unitData->id_kkn;
        try {
            $data_proker = Proker::where('id_unit', $id_unit)
                ->whereHas('bidang', function ($query) {
                    $query->where('tipe', 'unit');
                })
                ->get();
            $mahasiswa = Mahasiswa::where('id_unit', $id_unit)->get();
            $bidang_proker = BidangProker::where('id_kkn', $id_kkn)->where('tipe', 'unit')->get();
            $unit = Unit::where('id', $id_unit)->first();
            if ($unit && $data_proker && $mahasiswa && $bidang_proker) {
                return view('mahasiswa.manajemen unit.create-proker', compact('mahasiswa', 'bidang_proker', 'unit', 'data_proker'));
            } else {
                return view('not-found');
            }
        } catch (\Exception $e) {
            return view('not-found');
        }
    }

    public function getDataProkerByIdBidang(string $id, string $id_bidang)
    {
        try {
            $data = Proker::select(['id', 'nama'])->where('id_unit', $id)
                ->whereHas('bidang', function ($query) use ($id_bidang) {
                    $query->where('id', $id_bidang);
                })
                ->get();

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


    public function createProkerIndividu()
    {
        try {

            $id_unit = $this->roleUser()->mahasiswa->id_unit; // TODO: get this from the authenticated user / ambil dari blade
            $id_kkn = $this->roleUser()->mahasiswa->id_kkn;
            $id_mahasiswa = $this->roleUser()->mahasiswa->id;

            // Mengambil data proker individu dari semua mahasiswa dengan unit yang sama
            $data_proker = Proker::where('id_unit', $id_unit)
                ->whereHas('bidang', function ($query) {
                    $query->where('tipe', 'individu');
                })
                ->get();
            $unit = Unit::where('id', $id_unit)->first();
            $mahasiswa = Mahasiswa::where('id', $id_mahasiswa)->first();
            $bidang_proker = BidangProker::where('id_kkn', $id_kkn)->where('tipe', 'individu')->get();
            if ($mahasiswa && $unit && $mahasiswa->id_unit == $unit->id) {
                return view('mahasiswa.manajemen proker.create-proker', compact('mahasiswa', 'bidang_proker', 'data_proker', 'unit', 'id_kkn'));
            } else {
                return view('not-found');
            }
        } catch (\Exception $e) {
            return dd($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'program' => 'required|string|max:255',
            'id_bidang' => 'required|string|exists:bidang_proker,id',
            'id_unit' => 'required|string|exists:unit,id',
            'id_kkn' => 'required|string|exists:kkn,id',
            'tempat' => 'required|string|max:255',
            'sasaran' => 'required|string|max:255',
            'organizer' => 'required|array',
            'organizer.*.nama' => 'required|string|max:255',
            'organizer.*.peran' => 'nullable|string|max:255',
            'kegiatan' => 'required|array',
            'kegiatan.*.nama' => 'required|string|max:255',
            'kegiatan.*.frekuensi' => 'required|integer',
            'kegiatan.*.jkem' => 'required|integer',
            'kegiatan.*.totalJkem' => 'required|integer',
            'id_mahasiswa' => 'required|string|exists:mahasiswa,id',
            'kegiatan.*.tanggal' => 'required|array',
            'kegiatan.*.tanggal.*.tanggal' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        if ($request->input('id_mahasiswa') != $this->roleUser()->mahasiswa->id) {
            return response()->json(['errors' => 'Unauthorized'], 401);
        }

        // Simpan proker atau ambil id jika sudah ada
        $proker = Proker::firstOrCreate(
            ['nama' => $request->input('program'), 'id_unit' => $request->input('id_unit'), 'id_bidang' => $request->input('id_bidang')],
        );

        TempatSasaran::firstOrCreate(['tempat' => $request->input('tempat'), 'sasaran' => $request->input('sasaran'), 'id_proker' => $proker->id]);

        // Simpan organizer atau ambil id jika sudah ada
        foreach ($request->input('organizer') as $organizerData) {
            $organizer = Organizer::firstOrCreate(
                ['id_proker' => $proker->id, 'nama' => $organizerData['nama']],
                ['peran' => $organizerData['peran'] ?? ''] // gunakan '' jika peran tidak ada
            );

            // Jika entri sudah ada, tambahkan peran baru
            if (!$organizer->wasRecentlyCreated && !empty($organizerData['peran'])) {
                if ($organizer->peran === '') {
                    $organizer->peran = $organizerData['peran'];
                } else {
                    $organizer->peran .= ', ' . $organizerData['peran'];
                }
                $organizer->save();
            }
        }

        // Simpan kegiatan dengan memperhatikan id_mahasiswa yang berbeda untuk setiap kegiatan dalam proker yang sama
        foreach ($request->input('kegiatan') as $kegiatanData) {
            // Pengecekan apakah kegiatan dengan nama yang sama sudah ada dalam proker yang sama
            $existingKegiatan = Kegiatan::where('id_proker', $proker->id)
                ->where('nama', $kegiatanData['nama'])
                ->first();

            if ($existingKegiatan) {
                // Jika ada kegiatan yang sudah ada, tambahkan hanya tanggal baru jika tidak ada tanggal yang sama
                foreach ($kegiatanData['tanggal'] as $tanggalData) {
                    $existingTanggal = TanggalRencanaProker::where('id_kegiatan', $existingKegiatan->id)
                        ->where('tanggal', $tanggalData['tanggal'])
                        ->first();

                    if (!$existingTanggal) {
                        TanggalRencanaProker::create([
                            'id_kegiatan' => $existingKegiatan->id,
                            'id_kkn' => $request->input('id_kkn'), // Pastikan id_kkn ada di request
                            'tanggal' => $tanggalData['tanggal'],
                        ]);
                    }
                }
            } else {
                // Jika kegiatan belum ada, buat baru
                $kegiatan = Kegiatan::create([
                    'id_proker' => $proker->id,
                    'id_mahasiswa' => $request->input('id_mahasiswa'),
                    'nama' => $kegiatanData['nama'],
                    'frekuensi' => $kegiatanData['frekuensi'],
                    'jkem' => $kegiatanData['jkem'],
                    'total_jkem' => $kegiatanData['totalJkem'],
                ]);

                // Simpan tanggal rencana proker untuk kegiatan baru
                foreach ($kegiatanData['tanggal'] as $tanggalData) {
                    TanggalRencanaProker::create([
                        'id_kegiatan' => $kegiatan->id,
                        'id_kkn' => $request->input('id_kkn'), // Pastikan id_kkn ada di request
                        'tanggal' => $tanggalData['tanggal'],
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Data saved successfully', 'proker' => $proker], 201);
    }

    public function storeProkerIndividu(Request $request)
    {
        // Validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'program' => 'required|string|max:255',
            'id_bidang' => 'required|string|exists:bidang_proker,id',
            'id_unit' => 'required|string|exists:unit,id',
            'id_kkn' => 'required|string|exists:kkn,id',
            'tempat' => 'required|string|max:255',
            'sasaran' => 'required|string|max:255',
            'kegiatan' => 'required|array',
            'kegiatan.*.nama' => 'required|string|max:255',
            'kegiatan.*.frekuensi' => 'required|integer',
            'kegiatan.*.jkem' => 'required|integer',
            'kegiatan.*.totalJkem' => 'required|integer',
            'id_mahasiswa' => 'required|string|exists:mahasiswa,id',
            'kegiatan.*.tanggal' => 'required|array',
            'kegiatan.*.tanggal.*.tanggal' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        if ($request->input('id_mahasiswa') != $this->roleUser()->mahasiswa->id) {
            return response()->json(['message' => 'Tidak memiliki akses tambah data proker'], 404);
        }

        // Simpan proker atau ambil id jika sudah ada
        $proker = Proker::firstOrCreate(
            ['nama' => $request->input('program'), 'id_unit' => $request->input('id_unit'), 'id_bidang' => $request->input('id_bidang')],
        );

        TempatSasaran::firstOrCreate(['tempat' => $request->input('tempat'), 'sasaran' => $request->input('sasaran'), 'id_proker' => $proker->id], ['id_mahasiswa' => $request->input('id_mahasiswa')]);

        // Simpan kegiatan dengan memperhatikan id_mahasiswa yang berbeda untuk setiap kegiatan dalam proker yang sama
        foreach ($request->input('kegiatan') as $kegiatanData) {
            // Pengecekan apakah kegiatan dengan nama yang sama sudah ada dalam proker yang sama
            $existingKegiatan = Kegiatan::where('id_proker', $proker->id)
                ->where('nama', $kegiatanData['nama'])
                ->first();

            if ($existingKegiatan) {
                // Jika ada kegiatan yang sudah ada, tambahkan hanya tanggal baru jika tidak ada tanggal yang sama
                foreach ($kegiatanData['tanggal'] as $tanggalData) {
                    $existingTanggal = TanggalRencanaProker::where('id_kegiatan', $existingKegiatan->id)
                        ->where('tanggal', $tanggalData['tanggal'])
                        ->first();

                    if (!$existingTanggal) {
                        TanggalRencanaProker::create([
                            'id_kegiatan' => $existingKegiatan->id,
                            'id_kkn' => $request->input('id_kkn'), // Pastikan id_kkn ada di request
                            'tanggal' => $tanggalData['tanggal'],
                        ]);
                    }
                }
            } else {
                // Jika kegiatan belum ada, buat baru
                $kegiatan = Kegiatan::create([
                    'id_proker' => $proker->id,
                    'id_mahasiswa' => $request->input('id_mahasiswa'),
                    'nama' => $kegiatanData['nama'],
                    'frekuensi' => $kegiatanData['frekuensi'],
                    'jkem' => $kegiatanData['jkem'],
                    'total_jkem' => $kegiatanData['totalJkem'],
                ]);

                // Simpan tanggal rencana proker untuk kegiatan baru
                foreach ($kegiatanData['tanggal'] as $tanggalData) {
                    TanggalRencanaProker::create([
                        'id_kegiatan' => $kegiatan->id,
                        'id_kkn' => $request->input('id_kkn'), // Pastikan id_kkn ada di request
                        'tanggal' => $tanggalData['tanggal'],
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Data saved successfully', 'proker' => $proker], 201);
    }


    /**
     * Display the specified resource.
     */
    public function showProkerUnit(string $id, string $id_mahasiswa = null)
    {
        try {

            // Ambil proker dengan tipe bidangnya
            $prokers = Proker::with(['bidang'])
                ->where('id', $id)
                ->first();

            if (!$prokers) {
                return view('not-found');
            }

            // Ambil kegiatan berdasarkan tipe proker
            $proker = Proker::with(['kegiatan' => function ($query) use ($id_mahasiswa, $prokers) {
                if ($prokers->bidang->type !== 'unit' && $id_mahasiswa) {
                    $query->where('id_mahasiswa', $id_mahasiswa);
                }
            }, 'kegiatan.tanggalRencanaProker', 'unit', 'organizer', 'bidang', 'tempatDanSasaran' => function ($query) use ($id_mahasiswa, $prokers) {
                if ($prokers->bidang->type !== 'unit' && $id_mahasiswa) {
                    $query->where('id_mahasiswa', $id_mahasiswa);
                }
            }])
                ->where('id', $id)
                ->first();

            // Hitung total_jkem
            $total_jkem = 0;
            foreach ($proker->kegiatan as $kegiatan) {
                $total_jkem += $kegiatan->total_jkem;
            }

            // Set total_jkem pada objek proker
            $proker->total_jkem = $total_jkem;
            if ($id_mahasiswa == null) {
                $id_mahasiswa = $this->roleUser()->mahasiswa->id;
            }

            return view('mahasiswa.manajemen unit.detail-proker', compact('proker', 'id_mahasiswa'));
        } catch (\Exception $e) {
            return view('not-found');
        }
    }

    public function showProkerIndividu(string $id, string $id_mahasiswa)
    {

        // Ambil proker dengan tipe bidangnya
        $prokers = Proker::with(['bidang'])
            ->where('id', $id)
            ->first();


        if (!$prokers) {
            return view('not-found');
        }

        // Ambil kegiatan berdasarkan tipe proker
        $proker = Proker::with(['kegiatan' => function ($query) use ($id_mahasiswa, $prokers) {
            if ($prokers->bidang->type !== 'unit' && $id_mahasiswa) {
                $query->where('id_mahasiswa', $id_mahasiswa);
            }
        }, 'kegiatan.tanggalRencanaProker', 'unit', 'organizer', 'bidang', 'tempatDanSasaran' => function ($query) use ($id_mahasiswa, $prokers) {
            if ($prokers->bidang->type !== 'unit' && $id_mahasiswa) {
                $query->where('id_mahasiswa', $id_mahasiswa);
            }
        }])
            ->where('id', $id)
            ->first();

        // Hitung total_jkem
        $total_jkem = 0;
        foreach ($proker->kegiatan as $kegiatan) {
            $total_jkem += $kegiatan->total_jkem;
        }

        // Set total_jkem pada objek proker
        $proker->total_jkem = $total_jkem;

        return view('mahasiswa.manajemen proker.detail-proker', compact('proker', 'id_mahasiswa'));
    }

    public function storeKegiatan(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_mahasiswa' => 'required|exists:mahasiswa,id',
                'id_kkn' => 'required|exists:kkn,id',
                'id_proker' => 'required|exists:proker,id',
                'nama' => 'required|string',
                'frekuensi' => 'required|integer|min:1',
                'jkem' => 'required|integer|min:50',
                'totalJKEM' => 'required|integer|min:50',
                'tanggal_kegiatan' => 'required|string',
            ]);

            $tanggalArray = explode(',', $validatedData['tanggal_kegiatan']);
            array_walk($tanggalArray, function ($tanggal) {
                \Carbon\Carbon::parse($tanggal);
            });

            $kegiatan = Kegiatan::create([
                'id_mahasiswa' => $validatedData['id_mahasiswa'],
                'id_proker' => $validatedData['id_proker'],
                'nama' => $validatedData['nama'],
                'frekuensi' => $validatedData['frekuensi'],
                'jkem' => $validatedData['jkem'],
                'total_jkem' => $validatedData['totalJKEM'],
            ]);

            $kegiatan->tanggalRencanaProker()->createMany(
                array_map(function ($tanggal) use ($validatedData) {
                    return ['tanggal' => $tanggal, 'id_kkn' => $validatedData['id_kkn']];
                }, $tanggalArray)
            );

            return redirect()->back()->with('success', 'Berhasil menambah kegiatan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambah kegiatan, pastikan semua input terisi! ' . $e->getMessage());
        }
    }

    public function editKegiatan(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_kegiatan' => 'required|exists:kegiatan,id',
                'id_kkn' => 'required|exists:kkn,id',
                'nama' => 'required|string',
                'frekuensi' => 'required|integer|min:1',
                'jkem' => 'required|integer|min:50',
                'totalJKEM' => 'required|integer|min:50',
                'tanggal_kegiatan' => 'required|string',
            ]);

            $tanggalArray = explode(',', $validatedData['tanggal_kegiatan']);
            array_walk($tanggalArray, function ($tanggal) {
                \Carbon\Carbon::parse($tanggal);
            });

            $kegiatan = Kegiatan::findOrFail($validatedData['id_kegiatan']);
            $kegiatan->update([
                'nama' => $validatedData['nama'],
                'frekuensi' => $validatedData['frekuensi'],
                'jkem' => $validatedData['jkem'],
                'total_jkem' => $validatedData['totalJKEM'],
            ]);

            // Menghapus tanggal yang sudah ada
            $kegiatan->tanggalRencanaProker()->delete();

            // Menyimpan tanggal baru
            $kegiatan->tanggalRencanaProker()->createMany(
                array_map(function ($tanggal) use ($validatedData) {
                    return ['tanggal' => $tanggal, 'id_kkn' => $validatedData['id_kkn']];
                }, $tanggalArray)
            );

            return redirect()->back()->with('success', 'Berhasil mengedit kegiatan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengedit kegiatan, pastikan semua input terisi! ');
        }
    }



    /**
     * Show the form for editing the specified resource.
     */

    public function editOrganizer(Request $request)
    {
        // Validasi data
        $validatedData = $request->validate([
            'organizers' => 'required|array',
            'organizers.*.id' => 'required|exists:organizer,id',
            'organizers.*.peran' => 'required|string',
        ]);

        // Proses setiap organizer
        foreach ($validatedData['organizers'] as $organizerData) {
            $organizer = Organizer::findOrFail($organizerData['id']);
            $organizer->peran = $organizerData['peran'];
            $organizer->save();
        }

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Berhasil mengedit data organizer');
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateProker(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|string|exists:proker,id',
            'nama' => 'required|string',
        ]);

        // Begin a transaction
        DB::beginTransaction();

        try {
            // Update Proker
            $proker = Proker::findOrFail($validatedData['id']);
            $proker->nama = $validatedData['nama'];
            $proker->save();

            // Commit the transaction
            DB::commit();

            return redirect()->back()->with('success', 'Proker berhasil di edit');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Rollback the transaction if there's a model not found exception
            DB::rollBack();
            return redirect()->back()->with('error', 'Proker gagal di edit');
        } catch (\Exception $e) {
            // Rollback the transaction if there's any other error
            DB::rollBack();
            return redirect()->back()->with('error', 'Proker gagal di edit');
        }
    }


    public function updateProkerIndividu(Request $request)
    {
        $validatedData = $request->validate([
            'program' => 'required|string',
            'id_proker' => 'required|string|exists:proker,id',
        ]);

        $idProker = $request->input('id_proker');
        $idMahasiswa = $request->input('id_mahasiswa');

        if ($idMahasiswa != $this->roleUser()->mahasiswa->id) {
            return response()->json(['message' => 'failed'], 404);
        }

        // Begin a transaction
        DB::beginTransaction();

        try {
            $proker = Proker::findOrFail($idProker);
            $proker->nama = $validatedData['nama'];
            $proker->save();

            // Commit the transactionte
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Rollback the transaction if there's an error
            DB::rollBack();
            Log::error('Error updating Proker: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyKegiatan(Request $request)
    {
        try {
            $validateData = $request->validate([
                'id' => 'required|string|exists:kegiatan,id',
                'id_proker' => 'required|string|exists:proker,id',
            ]);

            $proker = Proker::find($validateData['id_proker']);
            $kegiatan = Kegiatan::find($validateData['id']);
            if ($proker->id != $kegiatan->id_proker || $this->roleUser()->mahasiswa->id_unit != $proker->id_unit) {
                return redirect()->back()->with('error', 'Tidak memiliki akses untuk kegiatan ini');
            }
            $kegiatan->delete();

            if (!$kegiatan) {
                return redirect()->back()->with('error', 'Kegiatan gagal dihapus');
            }
            return redirect()->back()->with('success', 'Kegiatan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus kegiatan, ' . $e->getMessage());
        }
    }

    public function checkProkerStatus(Request $request)
    {
        $idProker = $request->id;

        // Query untuk mengecek apakah ada kegiatan dari proker tersebut yang sudah dijalankan
        $logbookDetails = DB::table('logbook_harian')
            ->join('logbook_kegiatan', 'logbook_kegiatan.id_logbook_harian', '=', 'logbook_harian.id')
            ->join('kegiatan', 'logbook_kegiatan.id_kegiatan', '=', 'kegiatan.id')
            ->join('mahasiswa', 'logbook_harian.id_mahasiswa', '=', 'mahasiswa.id')
            ->where('kegiatan.id_proker', $idProker)
            ->select('logbook_harian.tanggal as tanggal_kegiatan')
            ->get();

        if ($logbookDetails->isNotEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Proker sudah pernah terealisasi pada logbook harian dan tidak dapat dihapus.',
                'data' => $logbookDetails // Mengirim data mahasiswa dan tanggal kegiatan
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Proker belum pernah dijalankan, dapat dihapus.']);
    }
    public function checkStatusKegiatan(Request $request)
    {
        $id = $request->id;

        // Query untuk mengecek apakah ada kegiatan dari proker tersebut yang sudah dijalankan
        $logbookDetails = DB::table('logbook_harian')
            ->join('logbook_kegiatan', 'logbook_kegiatan.id_logbook_harian', '=', 'logbook_harian.id')
            ->join('kegiatan', 'logbook_kegiatan.id_kegiatan', '=', 'kegiatan.id')
            ->join('mahasiswa', 'logbook_harian.id_mahasiswa', '=', 'mahasiswa.id')
            ->where('kegiatan.id', $id)
            ->select('logbook_harian.tanggal as tanggal_kegiatan')
            ->get();

        if ($logbookDetails->isNotEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kegiatan sudah pernah terealisasi pada logbook harian dan tidak dapat dihapus.',
                'data' => $logbookDetails // Mengirim data mahasiswa dan tanggal kegiatan
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Kegiatan belum pernah dijalankan, dapat dihapus.']);
    }




    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {

        if ($this->roleUser()->role->nama_role != 'Mahasiswa') {
            return view('not-found');
        }
        try {
            $proker = Proker::findOrFail($id);

            if ($this->roleUser()->mahasiswa->id_unit != $proker->id_unit) {
                return view('not-found');
            }

            // Cek tipe bidang
            if ($proker->bidang->tipe === 'unit') {
                // Hapus semua data yang berkaitan
                $proker->tempatDanSasaran()->delete();

                // Hapus setiap kegiatan satu per satu untuk memicu observer
                foreach ($proker->kegiatan as $kegiatan) {
                    $kegiatan->delete();
                }

                $proker->delete();

                return response()->json(['success' => true, 'message' => 'Proker unit berhasil dihapus.']);
            } elseif ($proker->bidang->tipe === 'individu') {
                // Hapus data berdasarkan syarat individu
                $loggedInMahasiswaId = $this->roleUser()->mahasiswa->id;

                // Hapus kegiatan dengan id_mahasiswa yang sesuai
                $proker->kegiatan()->where('id_mahasiswa', $loggedInMahasiswaId)->where('id_proker', $id)->get()->each->delete();

                // Hapus tempatDanSasaran yang memiliki id_proker dan id_mahasiswa yang sesuai
                TempatSasaran::where('id_proker', $id)->where('id_mahasiswa', $loggedInMahasiswaId)->delete();

                // Cek apakah masih ada kegiatan yang tersisa untuk proker tersebut
                if ($proker->kegiatan()->count() == 0) {
                    // Jika tidak ada kegiatan yang tersisa, hapus proker
                    $proker->delete();

                    return response()->json(['success' => true, 'message' => 'Proker individu dan semua datanya berhasil dihapus.']);
                }

                return response()->json(['success' => true, 'message' => 'Kegiatan dan tempatDanSasaran individu berhasil dihapus.']);
            }

            return response()->json(['success' => false, 'message' => 'Tipe bidang tidak valid.'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'failed'], 500);
        }
    }

    public function exportProker($id)
    {
        $unit = Unit::where('id', $id)->first();
        return Excel::download(new ProkerExport($id), 'Proker Unit ' . $unit->nama . '.xlsx');
    }
    public function exportProkerPDF($id)
    {
        $unit = Unit::where('id', $id)->first();
        $prokers = BidangProker::with([
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
            ->where('id_kkn', $unit->id_kkn)
            ->get();

        foreach ($prokers as $bidangProker) {

            foreach ($bidangProker->proker as $proker) {
                // Hitung total_jkem untuk setiap Proker
                $proker->total_jkem = $proker->kegiatan->sum('total_jkem');
            }
        }
        return view('mahasiswa.manajemen proker.export-pdf-proker', compact('unit', 'prokers'));
    }
}

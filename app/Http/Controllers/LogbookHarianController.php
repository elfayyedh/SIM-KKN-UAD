<?php

namespace App\Http\Controllers;

use App\Models\DanaKegiatan;
use App\Models\Kegiatan;
use App\Models\LogbookHarian;
use App\Models\LogbookKegiatan;
use App\Models\LogbookSholat;
use App\Models\Mahasiswa;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogbookHarianController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private function roleUser()
    {
        return Auth::user()->userRoles->find(session('selected_role'));
    }

    public function index()
    {
        try {
            $mahasiswa = $this->roleUser()->mahasiswa;
            $tanggal_penarikan = $mahasiswa->unit->tanggal_penarikan;
            if ($tanggal_penarikan == null) {
                $tanggal_penarikan = $mahasiswa->kkn->tanggal_selesai;
            }
            $data = [
                'tanggal_penerjunan' => $mahasiswa->unit->tanggal_penerjunan,
                'tanggal_penarikan' => $tanggal_penarikan,
                'mahasiswa' => $mahasiswa,
            ];
            return view('mahasiswa.manajemen-logbook.read-logbook-kegiatan', compact('data'));
        } catch (\Exception $e) {
            return view('not-found');
        }
    }

    public function checkLogbookKegiatan(Request $request)
    {
        $id_mahasiswa = $request->input('id_mahasiswa');
        $tanggal_penerjunan = Carbon::parse($request->input('tanggal_penerjunan'))->format('Y-m-d');
        $tanggal_penarikan = Carbon::parse($request->input('tanggal_penarikan'))->format('Y-m-d');

        $logbookKegiatan = LogbookHarian::where('id_mahasiswa', $id_mahasiswa)
            ->whereBetween('tanggal', [$tanggal_penerjunan, $tanggal_penarikan])
            ->get();


        return response()->json(['status' => 'success', 'data' => $logbookKegiatan]);
    }

    public function getLogbookKegiatan(Request $request)
    {
        $id_mahasiswa = $request->input('id_mahasiswa');
        $tanggal_penerjunan = Carbon::parse($request->input('tanggal_penerjunan'))->format('Y-m-d');
        $tanggal_penarikan = Carbon::parse($request->input('tanggal_penarikan'))->format('Y-m-d');

        // Retrieve the id_unit associated with the given id_mahasiswa
        $id_unit = Mahasiswa::where('id', $id_mahasiswa)->value('id_unit');

        $logbookHarian = LogbookHarian::with(['logbookKegiatan.kegiatan.proker.bidang', 'logbookKegiatan.dana'])
            ->where('id_unit', $id_unit)
            ->whereBetween('tanggal', [$tanggal_penerjunan, $tanggal_penarikan])
            ->whereHas('logbookKegiatan', function ($query) use ($id_mahasiswa) {
                $query->where(function ($query) use ($id_mahasiswa) {
                    $query->where('jenis', 'bersama')
                        ->orWhere(function ($query) use ($id_mahasiswa) {
                            $query->where('jenis', 'individu')
                                ->where('id_mahasiswa', $id_mahasiswa);
                        })
                        ->orWhere(function ($query) use ($id_mahasiswa) {
                            $query->where('jenis', 'bantu')
                                ->where('id_mahasiswa', $id_mahasiswa);
                        });
                });
            })
            ->get();



        // Return the results as JSON
        return response()->json($logbookHarian);
    }


    public function addLogbookKegiatan(string $id_mahasiswa, string $tanggal)
    {
        try {
            // Verifikasi bahwa mahasiswa yang login sama dengan mahasiswa yang ingin diakses
            if ($this->roleUser()->mahasiswa->id != $id_mahasiswa) {
                return view('not-found');
            }

            // Ambil atau buat Logbook Harian
            $logbook = LogbookHarian::firstOrCreate([
                'id_mahasiswa' => $id_mahasiswa,
                'tanggal' => $tanggal,
                'id_unit' => $this->roleUser()->mahasiswa->id_unit
            ], [
                'total_jkem' => 0,
                'status' => 'belum diisi'
            ]);

            // Ambil mahasiswa untuk mendapatkan ID unit
            $mahasiswa = Mahasiswa::where('id', $id_mahasiswa)->first();
            $id_unit = $mahasiswa->id_unit;
            $kegiatan = LogbookKegiatan::with(['kegiatan.proker.bidang'])
                ->where('id_unit', $id_unit)
                ->whereHas('logbookHarian', function ($query) use ($logbook) {
                    $query->where('tanggal', $logbook->tanggal);
                })->get();

            // Muat relasi yang belum dimuat jika diperlukan
            $kegiatan->loadMissing(['kegiatan', 'logbookHarian']);

            $data_kegiatan = $kegiatan->map(function ($k) use ($id_mahasiswa) {
                if ($k->jenis == 'bersama' || $k->id_mahasiswa == $id_mahasiswa) {
                    return (object)[
                        'id' => $k->id,
                        'id_mahasiswa' => $k->id_mahasiswa,
                        'id_logbook_harian' => $k->id_logbook_harian,
                        'id_kegiatan' => $k->id_kegiatan,
                        'jam_mulai' => $k->jam_mulai,
                        'jam_selesai' => $k->jam_selesai,
                        'total_jkem' => $k->total_jkem,
                        'jenis' => $k->jenis,
                        'deskripsi' => $k->deskripsi,
                        'kegiatan' => $k->kegiatan,  // Menambahkan objek kegiatan beserta relasinya
                        'logbook_harian' => $k->logbookHarian // Menambahkan objek logbook_harian beserta relasinya
                    ];
                }
            })->filter()->values()->all();


            $data = [
                'mahasiswa' => $mahasiswa,
                'tanggal' => $tanggal,
                'logbook' => $logbook,
                'kegiatan' => $data_kegiatan
            ];


            return view('mahasiswa.manajemen-logbook.add-logbook-kegiatan', compact('data'));
        } catch (\Exception $e) {
            return dd($e->getMessage());
        }
    }


    public function saveKegiatan(Request $request)
    {
        try {
            $mahasiswa = Auth::user()->userRoles->find(session('selected_role'))->mahasiswa->id;
            $id_unit = Auth::user()->userRoles->find(session('selected_role'))->mahasiswa->id_unit;

            $data = $request->validate([
                'id' => 'nullable|string',
                'id_mahasiswa' => 'required|string|exists:mahasiswa,id',
                'id_logbook_harian' => 'required|string|exists:logbook_harian,id',
                'id_kegiatan' => 'required|string|exists:kegiatan,id',
                'jam_mulai' => 'required|string',
                'jam_selesai' => 'required|string',
                'total_jkem' => 'required|integer|min:20',
                'deskripsi' => 'nullable|string',
                'status' => 'required|string|in:tambah,ubah',
            ], [
                'total_jkem.required' => 'Total jkem harus diisi',
                'jam_mulai.required' => 'Jam mulai harus diisi',
                'jam_selesai.required' => 'Jam selesai harus diisi',
                'id_kegiatan.exists' => 'Kegiatan tidak ditemukan',
                'id_logbook_harian.exists' => 'Logbook Harian tidak ditemukan',
                'id_mahasiswa.exists' => 'Mahasiswa tidak ditemukan',
                'total_jkem.numeric' => 'Total jkem harus angka',
            ]);

            if ($mahasiswa != $data['id_mahasiswa']) {
                return response()->json('failed');
            }

            $jenis = '';
            $kegiatan = Kegiatan::with(['proker.bidang'])->where('id', $data['id_kegiatan'])->first();
            if ($kegiatan && $kegiatan->proker && $kegiatan->proker->bidang) {
                $jenis = $kegiatan->proker->bidang->tipe == 'unit'
                    ? 'bersama'
                    : ($kegiatan->id_mahasiswa == $mahasiswa
                        ? 'individu'
                        : 'bantu');
            }

            if ($data['status'] == 'tambah') {
                $logbookKegiatan = LogbookKegiatan::create([
                    'id_mahasiswa' => $data['id_mahasiswa'],
                    'id_logbook_harian' => $data['id_logbook_harian'],
                    'id_kegiatan' => $data['id_kegiatan'],
                    'jam_mulai' => $data['jam_mulai'],
                    'jam_selesai' => $data['jam_selesai'],
                    'total_jkem' => $data['total_jkem'],
                    'deskripsi' => $data['deskripsi'],
                    'jenis' => $jenis,
                    'id_unit' => $id_unit
                ]);

                return redirect()->back()->with('success', 'Kegiatan berhasil ditambahkan');
            } else if ($data['status'] == 'ubah' && $data['id'] != null) {
                $logbookKegiatan = LogbookKegiatan::where('id', $data['id'])->first();
                if ($logbookKegiatan) {
                    $logbookKegiatan->update([
                        'id_mahasiswa' => $data['id_mahasiswa'],
                        'id_logbook_harian' => $data['id_logbook_harian'],
                        'id_kegiatan' => $data['id_kegiatan'],
                        'jam_mulai' => $data['jam_mulai'],
                        'jam_selesai' => $data['jam_selesai'],
                        'total_jkem' => $data['total_jkem'],
                        'deskripsi' => $data['deskripsi'],
                        'jenis' => $jenis,
                        'id_unit' => $id_unit
                    ]);

                    return redirect()->back()->with('success', 'Kegiatan berhasil diubah');
                } else {
                    return redirect()->back()->with('error', 'Kegiatan tidak ditemukan');
                }
            } else {
                return redirect()->back()->with('error', 'Gagal, status tidak diketahui');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal, ' . $e->getMessage());
        }
    }

    public function sholat()
    {
        try {
            $mahasiswa = $this->roleUser()->mahasiswa;
            $tanggal_penarikan = $mahasiswa->unit->tanggal_penarikan;
            if ($tanggal_penarikan == null) {
                $tanggal_penarikan = $mahasiswa->kkn->tanggal_selesai;
            }
            $data = [
                'tanggal_penerjunan' => $mahasiswa->unit->tanggal_penerjunan,
                'tanggal_penarikan' => $tanggal_penarikan,
                'mahasiswa' => $mahasiswa = Mahasiswa::where('id', $mahasiswa->id)->first(),
            ];
            return view('mahasiswa.manajemen-logbook.read-logbook-sholat', compact('data'));
        } catch (\Exception $e) {
            return view('not-found');
        }
    }

    public function checkLogbookSholat(Request $request)
    {
        $id_mahasiswa = $request->input('id_mahasiswa');
        $tanggal_penerjunan = Carbon::parse($request->input('tanggal_penerjunan'))->format('Y-m-d');
        $tanggal_penarikan = Carbon::parse($request->input('tanggal_penarikan'))->format('Y-m-d');

        $logbookSholat = LogbookSholat::where('id_mahasiswa', $id_mahasiswa)
            ->whereBetween('tanggal', [$tanggal_penerjunan, $tanggal_penarikan])
            ->get();
        return response()->json($logbookSholat);
    }

    public function addLogbookSholat(string $id_mahasiswa, string $tanggal)
    {
        try {
            if ($this->roleUser()->mahasiswa->id != $id_mahasiswa) {
                return view('not-found');
            } else {
                $data = [
                    'mahasiswa' => Mahasiswa::where('id', $id_mahasiswa)->first(),
                    'tanggal' => $tanggal
                ];
                return view('mahasiswa.manajemen-logbook.add-logbook-sholat', compact('data'));
            }
        } catch (\Exception $e) {
            return view('not-found');
        }
    }

    public function deleteKegiatan(Request $request)
    {
        $id = $request->input('id');
        $kegiatan = LogbookKegiatan::where('id', $id)->first();

        if ($kegiatan) {
            // Hapus kegiatan
            $kegiatan->delete();

            return redirect()->back()->with('success', 'Kegiatan berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Kegiatan gagal dihapus');
        }
    }




    public function getLogbookByDate($tanggal)
    {
        try {
            $id_mahasiswa = $this->roleUser()->mahasiswa->id;

            $logbook = LogbookSholat::where('id_mahasiswa', $id_mahasiswa)
                ->whereDate('tanggal', $tanggal)
                ->get();

            return response()->json($logbook);
        } catch (\Exception $e) {
            return view('not-found');
        }
    }

    public function saveSholat(Request $request)
    {
        if (Auth::user()->jenis_kelamin == "L" && $request->input('status') == 'sedang halangan') {
            return response()->json(['status' => 'failed', 'message' => 'Anda bukan perempuan']);
        }
        $data = $request->validate([
            'id_mahasiswa' => 'required|string|exists:mahasiswa,id',
            'tanggal' => 'required|date',
            'waktu' => 'required|string',
            'status' => 'required|string',
            'nama_imam' => 'nullable|string',
            'jumlah_jamaah' => 'nullable|string',
        ]);

        try {
            if ($this->roleUser()->role->nama_role != "Mahasiswa") {
                throw new \Exception();
            }
            $store_data = LogbookSholat::where(
                [
                    'id_mahasiswa' => $data['id_mahasiswa'],
                    'tanggal' => $data['tanggal'],
                    'waktu' => $data['waktu'],
                ]
            )->first();

            if ($store_data != null) {
                $store_data->status = $data['status'];
                $store_data->imam = $data['nama_imam'];
                $store_data->jumlah_jamaah = $data['jumlah_jamaah'];
                $store_data->update();
            } else {
                $update_data = new LogbookSholat();
                $update_data->id_mahasiswa = $data['id_mahasiswa'];
                $update_data->tanggal = $data['tanggal'];
                $update_data->waktu = $data['waktu'];
                $update_data->status = $data['status'];
                $update_data->imam = $data['nama_imam'];
                $update_data->jumlah_jamaah = $data['jumlah_jamaah'];
                $update_data->save();
            }
            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Failed to save data' . $e->getMessage()]);
        }
    }

    // Get logbook sholat PDF
    public function exportLogbookHarianPDF($id_mahasiswa, $tanggal_penerjunan, $tanggal_penarikan)
    {
        set_time_limit(300); // Set timeout 5 menit
        ini_set('memory_limit', '512M'); // Increase memory limit

        $tanggal_penerjunan = Carbon::parse($tanggal_penerjunan)->format('Y-m-d');
        $tanggal_penarikan = Carbon::parse($tanggal_penarikan)->format('Y-m-d');

        $mahasiswa = Mahasiswa::with(['userRole.user', 'unit.lokasi.kecamatan.kabupaten', 'prodi'])->findOrFail($id_mahasiswa);

        // Ambil data logbook harian dengan relasi
        $logbookHarian = LogbookHarian::with([
            'logbookKegiatan.kegiatan.proker.bidang',
            'logbookKegiatan.dana'
        ])
        ->where('id_mahasiswa', $id_mahasiswa)
        ->whereBetween('tanggal', [$tanggal_penerjunan, $tanggal_penarikan])
        ->orderBy('tanggal')
        ->get();

        $pdf = PDF::loadView('mahasiswa.manajemen-logbook.export-pdf-logbook-harian-mahasiswa', [
            'mahasiswa' => $mahasiswa,
            'logbookData' => $logbookHarian,
            'tanggal_penerjunan' => $tanggal_penerjunan,
            'tanggal_penarikan' => $tanggal_penarikan
        ])
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);
        return $pdf->download('Logbook Harian ' . $mahasiswa->userRole->user->nama . '.pdf');
    }

    public function exportPDF(string $id, string $tanggal_penerjunan, string $tanggal_penarikan)
    {
        return $this->getPDF($id, $tanggal_penerjunan, $tanggal_penarikan);
    }

    public function getPDF(string $id, string $tanggal_penerjunan, string $tanggal_penarikan)
    {
        set_time_limit(300); // Set timeout 5 menit
        ini_set('memory_limit', '512M'); // Increase memory limit

        $tanggal_penerjunan = Carbon::parse($tanggal_penerjunan)->format('Y-m-d');
        $tanggal_penarikan = Carbon::parse($tanggal_penarikan)->format('Y-m-d');

        $data = LogbookSholat::where('id_mahasiswa', $id)
            ->whereBetween('tanggal', [$tanggal_penerjunan, $tanggal_penarikan])
            ->get();
        $mahasiswa = Mahasiswa::with(['unit.dpl.dosen.user', 'unit.lokasi.kecamatan.kabupaten', 'prodi'])->where('id', $id)->first();

        $pdf = PDF::loadView('mahasiswa.pdf_sholat', compact('data', 'mahasiswa', 'tanggal_penerjunan', 'tanggal_penarikan'))
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);
        return $pdf->download('Logbook Sholat ' . $mahasiswa->userRole->user->nama . '.pdf');
    }

    public function halanganFullDay(Request $request)
    {
        // Pastikan hanya pengguna perempuan yang dapat mengakses
        if (Auth::user()->jenis_kelamin != "P") {
            return response()->json(['status' => 'failed', 'message' => 'Anda bukan perempuan']);
        }

        // Validasi data
        $data = $request->validate([
            'id_mahasiswa' => 'required|string|exists:mahasiswa,id',
            'tanggal' => 'required|date',
        ]);

        Log::info('Data received:', $data);

        $waktu = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];

        foreach ($waktu as $w) {
            // Debugging info
            $data_sholat = LogbookSholat::where([
                'id_mahasiswa' => $data['id_mahasiswa'],
                'tanggal' => $data['tanggal'],
                'waktu' => $w
            ])->first();

            if ($data_sholat != null) {
                // Update status
                $data_sholat->status = 'sedang halangan';
                $data_sholat->save();
                Log::info('Updated status to sedang halangan', $data_sholat->toArray());
            } else {
                $logbook = new LogbookSholat();
                $logbook->id_mahasiswa = $data['id_mahasiswa'];
                $logbook->tanggal = $data['tanggal'];
                $logbook->waktu = $w;
                $logbook->status = 'sedang halangan';
                $logbook->save();
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan'], 200);
    }


    public function savePendanaan(Request $request)
    {
        try {

            $validateData = $request->validate([
                'id_logbook_kegiatan' => 'required|string|exists:logbook_kegiatan,id',
                'jumlah' => 'required|numeric|min:500',
                'sumber' => 'string|required',
                'id_unit' => 'string|required|exists:unit,id',
            ]);

            $dana = DanaKegiatan::create([
                'id_logbook_kegiatan' => $validateData['id_logbook_kegiatan'],
                'sumber' => $validateData['sumber'],
                'jumlah' => $validateData['jumlah'],
                'id_unit' => $validateData['id_unit'],
            ]);

            return redirect()->back()->with('success', 'Pendanaan berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Pendanaan gagal ditambahkan' . $e->getMessage());
        }
    }

    public function getPendanaan(Request $request)
    {
        try {
            $data = $request->validate([
                'id' => 'required|string|exists:logbook_kegiatan,id',
            ]);

            $pendanaan = DanaKegiatan::where('id_logbook_kegiatan', $data['id'])->get();

            return response()->json(['status' => 'success', 'data' => $pendanaan]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil data']);
        }
    }


    public function getKegiatan(Request $request)
    {
        try {
            $data = $request->validate([
                'id_unit' => 'required|string|exists:unit,id',
            ]);

            $kegiatan = Kegiatan::select('id', 'nama', 'id_proker', 'id_mahasiswa')
                ->whereHas('proker', function ($query) use ($data) {
                    $query->where('id_unit', $data['id_unit']);
                })
                ->with([
                    'mahasiswa.userRole.user' => function ($query) {
                        $query->select('id', 'nama');
                    },
                    'proker' => function ($query) use ($data) {
                        $query->select('id', 'id_unit', 'nama', 'id_bidang')
                            ->where('id_unit', $data['id_unit'])
                            ->with([
                                'bidang' => function ($query) {
                                    $query->select('id', 'nama');
                                }
                            ]);
                    },

                ])
                ->get();



            return response()->json(['status' => 'success', 'data' => $kegiatan]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Gagal memuat data']);
        }
    }

    public function saveLogbookKegiatan(Request $request)
    {
        $data = $request->validate([
            'id_mahasiswa' => 'required|string|exists:mahasiswa,id',
            'id_kegiatan' => 'required|string|exists:kegiatan,id',
            'jam_mulai' => 'required|string',
            'jam_selesai' => 'required|string',
            'tanggal' => 'required|date',
        ]);

        try {
            // Get mahasiswa to get id_unit
            $mahasiswa = Mahasiswa::find($data['id_mahasiswa']);
            $id_unit = $mahasiswa->id_unit;

            // Create or get LogbookHarian
            $logbookHarian = LogbookHarian::firstOrCreate([
                'id_mahasiswa' => $data['id_mahasiswa'],
                'tanggal' => $data['tanggal'],
                'id_unit' => $id_unit
            ], [
                'total_jkem' => 0,
                'status' => 'belum diisi'
            ]);

            // Calculate total_jkem
            $jamMulai = Carbon::parse($data['jam_mulai']);
            $jamSelesai = Carbon::parse($data['jam_selesai']);
            $totalJkem = $jamSelesai->diffInMinutes($jamMulai);

            // Determine jenis
            $kegiatan = Kegiatan::with('proker.bidang')->find($data['id_kegiatan']);
            $jenis = 'individu'; // default
            if ($kegiatan && $kegiatan->proker && $kegiatan->proker->bidang) {
                if ($kegiatan->proker->bidang->tipe == 'unit') {
                    $jenis = 'bersama';
                } elseif ($kegiatan->id_mahasiswa != $data['id_mahasiswa']) {
                    $jenis = 'bantu';
                }
            }

            // Create LogbookKegiatan
            LogbookKegiatan::create([
                'id_mahasiswa' => $data['id_mahasiswa'],
                'id_logbook_harian' => $logbookHarian->id,
                'id_kegiatan' => $data['id_kegiatan'],
                'jam_mulai' => $data['jam_mulai'],
                'jam_selesai' => $data['jam_selesai'],
                'total_jkem' => $totalJkem,
                'jenis' => $jenis,
                'id_unit' => $id_unit
            ]);

            return response()->json(['status' => 'success', 'message' => 'Logbook kegiatan berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }
}

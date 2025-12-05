<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KKN;
use App\Models\EvaluasiMahasiswa;
use App\Models\Mahasiswa;
use App\Models\Unit;
use App\Models\TimMonev;
use Illuminate\Http\Request;
use App\Exports\EvaluasiExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class EvaluasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate user is authenticated and has Admin role
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login.index')->with('error', 'Silakan login terlebih dahulu.');
        }

        $selectedRoleId = session('selected_role');
        $userRole = $user->userRoles()->with('role')->find($selectedRoleId);
        if (!$userRole || $userRole->role->nama_role !== 'Admin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $kknId = $request->get('kkn_id');

        $kkns = KKN::orderBy('created_at', 'desc')->get();

        if ($kknId) {
            $kkn = KKN::find($kknId);

            // Get all mahasiswa for this KKN with relations (include unit)
            $mahasiswa = Mahasiswa::with([
                'userRole.user',
                'unit'
            ])
            ->where('id_kkn', $kknId)
            ->get();

            // Get kriteria monev for this KKN (used on student page)
            $kriteriaList = \App\Models\KriteriaMonev::where('id_kkn', $kknId)->orderBy('urutan', 'asc')->get();

            // Get existing evaluations for students in this KKN
            $mhsIds = $mahasiswa->pluck('id');
            $existingEvaluations = EvaluasiMahasiswa::with(['evaluasiMahasiswaDetail','timMonev'])
                ->whereIn('id_mahasiswa', $mhsIds)
                ->orderBy('created_at','desc')
                ->get();

            // Map latest nilai per mahasiswa per kriteria and tim monev info
            $mappedNilai = [];
            $timMonevInfo = [];
            foreach ($existingEvaluations as $eval) {
                // set tim monev name if not set yet
                if (!isset($timMonevInfo[$eval->id_mahasiswa]) && $eval->timMonev) {
                    $timMonevInfo[$eval->id_mahasiswa] = $eval->timMonev->dosen->user->nama ?? null;
                }
                foreach ($eval->evaluasiMahasiswaDetail as $detail) {
                    // only set if not already set (we ordered desc so first value is latest)
                    if (!isset($mappedNilai[$eval->id_mahasiswa][$detail->id_kriteria_monev])) {
                        $mappedNilai[$eval->id_mahasiswa][$detail->id_kriteria_monev] = $detail->nilai;
                    }
                }
            }
        } else {
            $kkn = null;
            $mahasiswa = collect();
            $kriteriaList = collect();
            $mappedNilai = [];
            $timMonevInfo = [];
        }

        // Get card values
        $cardValues = $this->getCardValues($kknId);

        // Get unit data for locations
        $unitData = $this->getUnitData($kknId);

        // Get belum dinilai data
        $belumDinilaiData = $this->getBelumDinilaiData($kknId);

        return view('administrator.read.evaluasi-mahasiswa', compact('kkns', 'kkn', 'mahasiswa', 'kriteriaList', 'mappedNilai', 'timMonevInfo', 'cardValues', 'unitData', 'belumDinilaiData'));
    }

    /**
     * Show evaluation details for a single mahasiswa (Admin view)
     */
    public function show($kknId, $id)
    {
        // auth + role check
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login.index')->with('error', 'Silakan login terlebih dahulu.');
        }

        $selectedRoleId = session('selected_role');
        $userRole = $user->userRoles()->with('role')->find($selectedRoleId);
        if (!$userRole || $userRole->role->nama_role !== 'Admin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $kkn = KKN::find($kknId);
        if (!$kkn) {
            return redirect()->back()->with('error', 'KKN tidak ditemukan.');
        }

        $mahasiswa = Mahasiswa::with(['userRole.user','unit'])->where('id_kkn', $kknId)->where('id', $id)->firstOrFail();

        // Load all evaluations for this mahasiswa (by tim monev)
        $evaluations = EvaluasiMahasiswa::with('evaluasiMahasiswaDetail','timMonev')
            ->where('id_mahasiswa', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get kriteria for display
        $kriteriaList = \App\Models\KriteriaMonev::where('id_kkn', $kknId)->orderBy('urutan','asc')->get();

        return view('administrator.read.evaluasi-mahasiswa-show', compact('kkn','mahasiswa','evaluations','kriteriaList'));
    }
    public function store(Request $request)
    {
        // Validate user is authenticated and has Admin role
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login.index')->with('error', 'Silakan login terlebih dahulu.');
        }

        $selectedRoleId = session('selected_role');
        $userRole = $user->userRoles()->with('role')->find($selectedRoleId);
        if (!$userRole || $userRole->role->nama_role !== 'Admin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses untuk menyimpan evaluasi.');
        }

        $request->validate([
            'kkn_id' => 'required|uuid|exists:kkn,id',
            'evaluasi' => 'required|array',
        ]);

        try {
            // Loop setiap mahasiswa yang dikirim dari form
            foreach ($request->evaluasi as $idMahasiswa => $scores) {
                // Filter: Hanya proses jika ada setidaknya satu nilai yang diisi
                $filledScores = array_filter($scores, function($val) {
                    return !is_null($val) && $val !== '';
                });

                if (empty($filledScores)) continue;

                // Create or update header evaluasi (admin evaluation)
                $evalHeader = EvaluasiMahasiswa::updateOrCreate(
                    [
                        'id_mahasiswa' => $idMahasiswa
                    ],
                    [
                        'id_tim_monev' => null, // Admin evaluation, no specific tim monev
                        'updated_at' => now()
                    ]
                );

                // Simpan Detail Nilai
                foreach ($filledScores as $idKriteria => $nilai) {
                    // Pastikan nilai dalam range 1-3
                    if ($nilai < 1 || $nilai > 3) continue;

                    \App\Models\EvaluasiMahasiswaDetail::updateOrCreate(
                        [
                            'id_evaluasi_mahasiswa' => $evalHeader->id,
                            'id_kriteria_monev' => $idKriteria
                        ],
                        [
                            'nilai' => $nilai
                        ]
                    );
                }
            }

            return redirect()->back()->with('success', 'Data penilaian berhasil disimpan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * Export evaluasi to Excel
     */
    public function export(Request $request, $kknId)
    {
        // Validate user is authenticated and has Admin role
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login.index')->with('error', 'Silakan login terlebih dahulu.');
        }

        $selectedRoleId = session('selected_role');
        $userRole = $user->userRoles()->with('role')->find($selectedRoleId);
        if (!$userRole || $userRole->role->nama_role !== 'Admin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses untuk export evaluasi.');
        }

        if (!$kknId) {
            return redirect()->back()->with('error', 'Pilih KKN terlebih dahulu');
        }

        // Handle export all
        if ($kknId === 'all') {
            $filename = 'evaluasi_mahasiswa_all_' . date('Y-m-d-His') . '.xlsx';
            return Excel::download(new EvaluasiExport($kknId), $filename);
        }

        $kkn = KKN::find($kknId);
        if (!$kkn) {
            return redirect()->back()->with('error', 'KKN tidak ditemukan.');
        }

        $filename = 'evaluasi_mahasiswa_' . str_replace(' ', '_', $kkn->nama) . '.xlsx';
        return Excel::download(new EvaluasiExport($kknId), $filename);
    }

    private function getCardValues($kknId = null)
    {
        if (!$kknId) {
            return [
                'total_mahasiswa' => 0,
                'total_unit' => 0,
                'total_dinilai' => 0,
                'total_belum_dinilai' => 0
            ];
        }

        $totalMahasiswa = Mahasiswa::where('id_kkn', $kknId)->count();
        $totalUnit = Unit::where('id_kkn', $kknId)->count();

        $mahasiswaIds = Mahasiswa::where('id_kkn', $kknId)->pluck('id');
        $totalDinilai = EvaluasiMahasiswa::whereIn('id_mahasiswa', $mahasiswaIds)->distinct('id_mahasiswa')->count('id_mahasiswa');
        $totalBelumDinilai = $totalMahasiswa - $totalDinilai;

        return [
            'total_mahasiswa' => $totalMahasiswa,
            'total_unit' => $totalUnit,
            'total_dinilai' => $totalDinilai,
            'total_belum_dinilai' => $totalBelumDinilai
        ];
    }

    private function getUnitData($kknId = null)
    {
        if (!$kknId) {
            return collect();
        }

        return Unit::with(['lokasi.kecamatan.kabupaten'])
            ->where('id_kkn', $kknId)
            ->get()
            ->groupBy(function ($unit) {
                return $unit->lokasi->kecamatan->id ?? null;
            })
            ->map(function ($units, $kecamatanId) {
                $firstUnit = $units->first();
                return [
                    'total_unit' => $units->count(),
                    'kecamatan' => $firstUnit->lokasi->kecamatan->nama ?? '-',
                    'kabupaten' => $firstUnit->lokasi->kecamatan->kabupaten->nama ?? '-'
                ];
            })
            ->values();
    }

    private function getBelumDinilaiData($kknId = null)
    {
        if (!$kknId) {
            return collect();
        }

        $mahasiswaIds = Mahasiswa::where('id_kkn', $kknId)->pluck('id');
        $dinilaiIds = EvaluasiMahasiswa::whereIn('id_mahasiswa', $mahasiswaIds)->pluck('id_mahasiswa')->unique();

        return Mahasiswa::with(['userRole.user', 'unit'])
            ->where('id_kkn', $kknId)
            ->whereNotIn('id', $dinilaiIds)
            ->get()
            ->map(function ($mahasiswa) {
                return [
                    'nama_mahasiswa' => $mahasiswa->userRole->user->nama ?? '-',
                    'nama_unit' => $mahasiswa->unit->nama ?? '-'
                ];
            });
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\EntriDataKKN;
use App\Models\BidangProker;
use App\Models\KKN;
use App\Models\QueueProgress;
use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Models\TimMonev;
use App\Models\UserRole;
use App\Models\Role;

class KKNController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kkn = KKN::all();
        return view('administrator.read.read-kkn', compact('kkn'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator.create.create-kkn');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "nama" => 'required|string',
            "thn_ajaran" => 'required|string',
            "tanggal_mulai" => 'required|date',
            "tanggal_selesai" => 'required|date',
            "file_excel" => 'required',
            "fields" => 'required|array'
        ]);


        $kkn = KKN::firstOrCreate([
            'nama' => $validated['nama'],
        ], [
            'thn_ajaran' => $validated['thn_ajaran'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai']
        ]);


        foreach ($validated['fields'] as $field) {
            $bidang = BidangProker::firstOrCreate([
                'id_kkn' => $kkn->id,
                'nama' => $field['bidang'],
            ], [
                'tipe' => $field['tipe_bidang'],
                'syarat_jkem' => $field['syarat_jkem'],
            ]);
        }

        $progress = QueueProgress::create(['progress' => 0, 'total' => 0, 'step' => 0, 'status' => 'in_progress', 'message' => 'In Progress']);

        EntriDataKKN::dispatch($validated['file_excel'], $kkn->id, $progress->id);

        return response()->json(['id_progress' => $progress->id]);
    }

    public function getProgress($id)
    {
        // Ambil data QueueProgress berdasarkan ID
        $queueProgress = QueueProgress::find($id);

        return response()->json($queueProgress);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kkn = KKN::with([
            'mahasiswa.prodi',
            'mahasiswa.userRole.user',
            'dpl.dosen.user',
            'dpl.units',
            'timMonev.dosen.user',
            'units.lokasi.kecamatan.kabupaten',
            'units.mahasiswa' 
        ])->findOrFail($id);
        foreach ($kkn->units as $unit) { 
            $unit->total_jkem = $unit->mahasiswa->sum('total_jkem');
        }
        $assignedDosenIds = $kkn->timMonev->pluck('dosen.id');
        $available_dosens = Dosen::whereNotIn('id', $assignedDosenIds)->with('user')->get();
        return view('administrator.read.detail-kkn', compact('kkn', 'available_dosens'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $kkn = KKN::with(['bidangProker' => function ($query) {
            $query->withCount('proker');
        }])->findOrFail($id);

        return view('administrator.update.edit-kkn', compact('kkn'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate(
            [
                "nama" => 'required|string',
                "thn_ajaran" => 'required|string',
                "tanggal_mulai" => 'required|date',
                "tanggal_selesai" => 'required|date',
            ],
            [
                'nama.required' => 'Nama harus diisi',
                'thn_ajaran.required' => 'Tahun Ajaran harus diisi',
                'tanggal_mulai.required' => 'Tanggal Mulai harus diisi',
                'tanggal_selesai.required' => 'Tanggal Selesai harus diisi',
            ]
        );

        try {
            $kkn = KKN::find($id);
            $kkn->update($validated);
            return redirect()->back()->with(['success_kkn' => "Data KKN berhasil diperbarui"]);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error_kkn' => "Data KKN gagal diperbarui"]);
        }
    }
    /**
     * Menambahkan Dosen sebagai Tim Monev di KKN ini
     */
    public function addMonev(Request $request, $id_kkn)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
        ]);

        try {
            $dosen = Dosen::findOrFail($request->dosen_id);
            $roleMonev = Role::where('nama_role', 'Tim Monev')->first();

            TimMonev::firstOrCreate(
                [
                    'id_dosen' => $dosen->id,
                    'id_kkn' => $id_kkn,
                ]
            );

            UserRole::firstOrCreate(
                [
                    'id_user' => $dosen->user_id, 
                    'id_role' => $roleMonev->id,
                    'id_kkn' => $id_kkn
                ]
            );

            return redirect()->back()->with('success', 'Tim Monev berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan Tim Monev: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus penugasan Tim Monev
     */
    public function removeMonev($id_penugasan_monev)
    {
        try {
            $monevAssignment = TimMonev::findOrFail($id_penugasan_monev);

            $kkn_id = $monevAssignment->id_kkn;
            $dosen_id = $monevAssignment->id_dosen; 
            $user_id = $monevAssignment->dosen->user_id; 
            $roleMonevId = Role::where('nama_role', 'Tim Monev')->value('id');
            $monevAssignment->delete(); 
            $sisaTugasMonev = TimMonev::where('id_dosen', $dosen_id)->exists();

            if ($sisaTugasMonev) {
                UserRole::where('id_user', $user_id)
                    ->where('id_role', $roleMonevId)
                    ->where('id_kkn', $kkn_id)
                    ->delete();
            } else {
                UserRole::where('id_user', $user_id)
                        ->where('id_role', $roleMonevId)
                        ->delete(); 
            }

            return redirect()->back()->with('success', 'Tim Monev berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus Tim Monev: ' . $e->getMessage());
        }
    }
}

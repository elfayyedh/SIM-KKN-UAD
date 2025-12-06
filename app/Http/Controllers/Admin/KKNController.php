<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\EntriDataKKN;
use App\Models\BidangProker;
use App\Models\KKN;
use App\Models\QueueProgress;
use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Models\KriteriaMonev;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class KKNController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kkn = KKN::orderBy('created_at', 'desc')->get();

        $kkn->transform(function ($item) {
            $today = Carbon::now();
            if (! $item->status) { 
                $item->status_text = 'Non-Aktif';
                $item->status_color = 'danger';
            } 
            elseif ($today->lt($item->tanggal_mulai)) {
                $item->status_text = 'Belum Mulai';
                $item->status_color = 'warning';
            } 
            elseif ($today->gt($item->tanggal_selesai)) {
                $item->status_text = 'Selesai';
                $item->status_color = 'secondary';
            } 
            else {
                $item->status_text = 'Sedang Berjalan';
                $item->status_color = 'success';
            }
            return $item;
        });
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
            "tanggal_cutoff_penilaian" => 'required|date|after_or_equal:tanggal_mulai|before_or_equal:tanggal_selesai',
            "file_excel" => 'required',
            "fields" => 'required|array',
            "kriteria" => 'nullable|array',
            "kriteria.*.judul" => 'required_with:kriteria|string',
        ]);

        DB::beginTransaction();

        try {
            $kkn = \App\Models\KKN::firstOrCreate([
                'nama' => $validated['nama'],
            ], [
                'thn_ajaran' => $validated['thn_ajaran'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'tanggal_cutoff_penilaian' => $validated['tanggal_cutoff_penilaian']
            ]);

            foreach ($validated['fields'] as $field) {
                \App\Models\BidangProker::firstOrCreate([
                    'id_kkn' => $kkn->id,
                    'nama' => $field['bidang'],
                ], [
                    'tipe' => $field['tipe_bidang'],
                    'syarat_jkem' => $field['syarat_jkem'],
                ]);
            }

            if ($request->has('kriteria') && is_array($request->kriteria)) {
                foreach ($request->kriteria as $index => $item) {
                    if (empty($item['judul'])) continue;

                    KriteriaMonev::create([
                        'id_kkn'       => $kkn->id, 
                        'judul'        => $item['judul'],
                        'keterangan'   => $item['keterangan'] ?? null,
                        'variable_key' => $item['variable_key'] ?? null,
                        'link_url'     => $item['link_url'] ?? null,
                        'link_text'    => $item['link_text'] ?? null,
                        'urutan'       => $index + 1 
                    ]);
                }
            }
            $progress = \App\Models\QueueProgress::create([
                'progress' => 0, 
                'total' => 0, 
                'step' => 0, 
                'status' => 'in_progress', 
                'message' => 'In Progress'
            ]);

            DB::commit();

            \App\Jobs\EntriDataKKN::dispatch($validated['file_excel'], $kkn->id, $progress->id);

            return response()->json(['id_progress' => $progress->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
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
        ])->find($id);
        foreach ($kkn->units as $unit) { 
            $unit->total_jkem = $unit->mahasiswa->sum('total_jkem');
        }

        $dosens = Dosen::with('user')->get();

        return view('administrator.read.detail-kkn', compact('kkn', 'dosens'));
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
                "tanggal_cutoff_penilaian" => 'required|date|after_or_equal:tanggal_mulai|before_or_equal:tanggal_selesai',
            ],
            [
                'nama.required' => 'Nama harus diisi',
                'thn_ajaran.required' => 'Tahun Ajaran harus diisi',
                'tanggal_mulai.required' => 'Tanggal Mulai harus diisi',
                'tanggal_selesai.required' => 'Tanggal Selesai harus diisi',
                "tanggal_cutoff_penilaian" => 'Tanggal Cut Off harus diisi',
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
}
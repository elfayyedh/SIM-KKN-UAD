<?php

namespace App\Http\Controllers;

use App\Models\BidangProker;
use App\Models\Dpl;
use App\Models\KKN;
use App\Models\Mahasiswa;
use App\Models\TimMonev;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    private function getActiveRoleName()
    {
        // 2. Ambil user_role yang aktif dari session
        $userRole = Auth::user()->userRoles()->find(session('selected_role'));

        if ($userRole && $userRole->role) {
            return $userRole->role->nama_role;
        }

        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (session('user_is_dosen', false)) {
            
            $activeRole = session('active_role'); 

            if ($activeRole == 'dpl') {
                $dosen = Auth::user()->dosen; 
                $dplAssignment = $dosen ? $dosen->dplAssignments()->first() : null;
                $email = Auth::user()->email;
                $id_kkn = $dplAssignment ? $dplAssignment->id_kkn : null; 
                return view('dpl.dashboard', compact('email', 'id_kkn')); 

            } elseif ($activeRole == 'monev') {
                $dosen = Auth::user()->dosen;
                $monevAssignment = $dosen ? $dosen->timMonevAssignments()->first() : null;
                $email = Auth::user()->email;
                $id_kkn = $monevAssignment ? $monevAssignment->id_kkn : null; 
                return view('tim monev.dashboard', compact('email', 'id_kkn')); 

            } else {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                return redirect()->route('login.index')->with('error', 'Peran Dosen Anda tidak aktif.');
            }
        }
        $activeUserRole = Auth::user()->userRoles()->find(session('selected_role'));
        if (!$activeUserRole || !$activeUserRole->role) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login.index')->with('error', 'Peran Anda tidak valid atau tidak dikenali.');
        }
        $roleName = $activeUserRole->role->nama_role;
        
        if ($roleName == "Mahasiswa") {
            $id_unit = $activeUserRole->mahasiswa->id_unit;
            $id_kkn = $activeUserRole->mahasiswa->id_kkn;
            return view('mahasiswa.dasbboard', compact('id_unit', 'id_kkn')); 
        
        } else if ($roleName == "Admin") {
            $user = Auth::user(); 
            $kkn = KKN::all();
            return view('administrator.dasbboard', compact('user', 'kkn')); 
        
        } else {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login.index')->with('error', 'Role Anda (non-dosen) tidak dikenali.');
        }
    }

    public function getCardValue(Request $request)
    {
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        
        // Handle Admin
        if ($role == "Admin") {
            $periode = $request->input('periode');
            if ($periode == 'semua') {
                $total_mahasiswa = Mahasiswa::all()->count();
                $total_unit = Unit::all()->count();
                $total_dpl = Dpl::all()->count();
                $total_tim_monev = TimMonev::all()->count();
            } else {
                $total_mahasiswa = Mahasiswa::whereHas('kkn', function ($query) use ($periode) {
                    $query->where('id', $periode);
                })->count();
                $total_unit = Unit::whereHas('kkn', function ($query) use ($periode) {
                    $query->where('id', $periode);
                })->count();
                $total_dpl = Dpl::whereHas('kkn', function ($query) use ($periode) {
                    $query->where('id', $periode);
                })->count();
                $total_tim_monev = TimMonev::whereHas('kkn', function ($query) use ($periode) {
                    $query->where('id', $periode);
                })->count();
            }

            return response()->json([
                'status' => 'success',
                'total_mahasiswa' => $total_mahasiswa,
                'total_unit' => $total_unit,
                'total_dpl' => $total_dpl,
                'total_tim_monev' => $total_tim_monev,
            ]);
        }
        
        // Handle DPL
        if ($role == "DPL") {
            $dosen = Auth::user()->dosen;
            $periode = $request->input('periode');
            
            // Ambil dpl assignment berdasarkan periode yang dipilih
            $dpl = $dosen ? $dosen->dplAssignments()->where('id_kkn', $periode)->first() : null;
            
            if (!$dpl) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'DPL assignment not found for this period'
                ], 404);
            }
            
            // Filter berdasarkan periode tertentu
            $units = Unit::where('id_dpl', $dpl->id)
                ->where('id_kkn', $periode)
                ->get();
            $unit_ids = $units->pluck('id');
            
            $total_mahasiswa = Mahasiswa::whereIn('id_unit', $unit_ids)
                ->where('id_kkn', $periode)
                ->count();
            $total_unit = $units->count();

            return response()->json([
                'status' => 'success',
                'total_mahasiswa' => $total_mahasiswa,
                'total_unit' => $total_unit,
            ]);
        }
        
        // Role lain tidak punya akses
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized'
        ], 403);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function getChartData()
    {
        $startYear = date('Y') - 4; // Mulai dari 5 tahun yang lalu
        $endYear = date('Y'); // Tahun sekarang

        // Query untuk menghitung total mahasiswa per tahun
        $totalMahasiswa = DB::table('mahasiswa')
            ->join('kkn', 'mahasiswa.id_kkn', '=', 'kkn.id')
            ->select(DB::raw('YEAR(kkn.created_at) as year'))
            ->selectRaw('COUNT(DISTINCT mahasiswa.id) as total_mahasiswa')
            ->whereYear('kkn.created_at', '>=', $startYear)
            ->whereYear('kkn.created_at', '<=', $endYear)
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // Query untuk menghitung total DPL per tahun
        $totalDpl = DB::table('dpl')
            ->join('kkn', 'dpl.id_kkn', '=', 'kkn.id')
            ->select(DB::raw('YEAR(kkn.created_at) as year'))
            ->selectRaw('COUNT(DISTINCT dpl.id) as total_dpl')
            ->whereYear('kkn.created_at', '>=', $startYear)
            ->whereYear('kkn.created_at', '<=', $endYear)
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // Query untuk menghitung total Tim Monev per tahun
        $totalTimMonev = DB::table('tim_monev')
            ->join('kkn', 'tim_monev.id_kkn', '=', 'kkn.id')
            ->select(DB::raw('YEAR(kkn.created_at) as year'))
            ->selectRaw('COUNT(DISTINCT tim_monev.id) as total_tim_monev')
            ->whereYear('kkn.created_at', '>=', $startYear)
            ->whereYear('kkn.created_at', '<=', $endYear)
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // Menggabungkan data menjadi format yang diinginkan
        $years = range($startYear, $endYear);
        $result = [];
        foreach ($years as $year) {
            $result[$year] = [
                'total_mahasiswa' => $totalMahasiswa->where('year', $year)->first()->total_mahasiswa ?? 0,
                'total_dpl' => $totalDpl->where('year', $year)->first()->total_dpl ?? 0,
                'total_tim_monev' => $totalTimMonev->where('year', $year)->first()->total_tim_monev ?? 0,
            ];
        }

        return response()->json($result);
    }

    public function getDonutChart(Request $request)
    {
        $periode = $request->input('periode');
        if ($periode == 'semua') {
            $bidangProkerData = BidangProker::withCount('proker')->get();
        } else {
            $bidangProkerData = BidangProker::where('id_kkn', $periode)->withCount('proker')->get();
        }

        return response()->json($bidangProkerData);
    }

    public function getProdiData(Request $request)
    {
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        
        // Handle Admin
        if ($role == "Admin") {
            $id_kkn = $request->input('periode');

            // Jika periode adalah "semua", ambil semua data
            if ($id_kkn === 'semua') {
                $data = DB::table('mahasiswa')
                    ->join('prodi', 'mahasiswa.id_prodi', '=', 'prodi.id')
                    ->join('unit', 'mahasiswa.id_unit', '=', 'unit.id')
                    ->select(
                        'prodi.nama_prodi',
                        DB::raw('COUNT(DISTINCT mahasiswa.id_unit) as total_unit'),
                        DB::raw('COUNT(mahasiswa.id) as total_mahasiswa')
                    )
                    ->groupBy('prodi.id', 'prodi.nama_prodi')
                    ->get();
            } else {
                // Jika periode adalah id_kkn tertentu, ambil data berdasarkan id_kkn
                $data = DB::table('mahasiswa')
                    ->join('prodi', 'mahasiswa.id_prodi', '=', 'prodi.id')
                    ->join('unit', 'mahasiswa.id_unit', '=', 'unit.id')
                    ->where('mahasiswa.id_kkn', $id_kkn)
                    ->select(
                        'prodi.nama_prodi',
                        DB::raw('COUNT(DISTINCT mahasiswa.id_unit) as total_unit'),
                        DB::raw('COUNT(mahasiswa.id) as total_mahasiswa')
                    )
                    ->groupBy('prodi.id', 'prodi.nama_prodi')
                    ->get();
            }
            
            return response()->json($data);
        }
        
        // Handle DPL
        if ($role == "DPL") {
            $dosen = Auth::user()->dosen;
            $periode = $request->input('periode');
            
            // Ambil dpl assignment berdasarkan periode yang dipilih
            $dpl = $dosen ? $dosen->dplAssignments()->where('id_kkn', $periode)->first() : null;
            
            if (!$dpl) {
                return response()->json([]);
            }
            
            // Get units untuk DPL ini berdasarkan periode
            $units = Unit::where('id_dpl', $dpl->id)
                ->where('id_kkn', $periode)
                ->get();
            $unit_ids = $units->pluck('id');
            
            $data = DB::table('mahasiswa')
                ->join('prodi', 'mahasiswa.id_prodi', '=', 'prodi.id')
                ->whereIn('mahasiswa.id_unit', $unit_ids)
                ->where('mahasiswa.id_kkn', $periode)
                ->select(
                    'prodi.nama_prodi',
                    DB::raw('COUNT(DISTINCT mahasiswa.id_unit) as total_unit'),
                    DB::raw('COUNT(mahasiswa.id) as total_mahasiswa')
                )
                ->groupBy('prodi.id', 'prodi.nama_prodi')
                ->get();
                
            return response()->json($data);
        }

        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
    }

    public function getUnitData(Request $request)
    {
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        
        // Handle Admin
        if ($role == "Admin") {
            $periode = $request->periode;

            $query = DB::table('unit')
                ->join('lokasi', 'unit.id_lokasi', '=', 'lokasi.id')
                ->join('kecamatan', 'lokasi.id_kecamatan', '=', 'kecamatan.id')
                ->join('kabupaten', 'kecamatan.id_kabupaten', '=', 'kabupaten.id')
                ->select(
                    DB::raw('COUNT(unit.id) AS total_unit'),
                    'kecamatan.nama AS kecamatan',
                    'kabupaten.nama AS kabupaten'
                )
                ->groupBy('kecamatan.id', 'kabupaten.id', 'kecamatan.nama', 'kabupaten.nama');

            if ($periode !== 'semua') {
                $query->where('unit.id_kkn', $periode);
            }

            $data = $query->get();
            return response()->json($data);
        }
        
        // Handle DPL
        if ($role == "DPL") {
            $dosen = Auth::user()->dosen;
            $periode = $request->periode;
            
            // Ambil dpl assignment berdasarkan periode yang dipilih
            $dpl = $dosen ? $dosen->dplAssignments()->where('id_kkn', $periode)->first() : null;
            
            if (!$dpl) {
                return response()->json([]);
            }
            
            $data = DB::table('unit')
                ->join('lokasi', 'unit.id_lokasi', '=', 'lokasi.id')
                ->join('kecamatan', 'lokasi.id_kecamatan', '=', 'kecamatan.id')
                ->join('kabupaten', 'kecamatan.id_kabupaten', '=', 'kabupaten.id')
                ->select(
                    DB::raw('COUNT(unit.id) AS total_unit'),
                    'kecamatan.nama AS kecamatan',
                    'kabupaten.nama AS kabupaten'
                )
                ->where('unit.id_dpl', $dpl->id)
                ->where('unit.id_kkn', $periode)
                ->groupBy('kecamatan.id', 'kabupaten.id', 'kecamatan.nama', 'kabupaten.nama')
                ->get();
                
            return response()->json($data);
        }

        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
    }
}
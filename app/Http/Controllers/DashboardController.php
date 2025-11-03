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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->userRoles->find(session('selected_role'))->role->nama_role == "Mahasiswa") {
            $id_unit = Auth::user()->userRoles->find(session('selected_role'))->mahasiswa->id_unit;
            $id_kkn = Auth::user()->userRoles->find(session('selected_role'))->mahasiswa->id_kkn;
            return view('mahasiswa.dasbboard', compact('id_unit', 'id_kkn'));
        } else if (Auth::user()->userRoles->find(session('selected_role'))->role->nama_role == "Admin") {
            $user = User::where('id', Auth::user()->id)->first();
            $kkn = KKN::all();
            return view('administrator.dasbboard', compact('user', 'kkn'));
        } elseif (Auth::user()->userRoles->find(session('selected_role'))->role->nama_role == "DPL") {
            $dpl = Auth::user()->userRoles->find(session('selected_role'))->dpl;
            $id_kkn = $dpl->id_kkn;
            
            // Get units untuk DPL ini
            $units = Unit::where('id_dpl', $dpl->id)->get();
            $unit_ids = $units->pluck('id');
            
            // Count total mahasiswa dari unit yang di-handle DPL ini
            $total_mahasiswa = Mahasiswa::whereIn('id_unit', $unit_ids)
                ->where('id_kkn', $id_kkn)
                ->count();
            
            // Count total unit
            $total_unit = $units->count();
            
            // Get data lokasi untuk DPL
            $data_lokasi = DB::table('unit')
                ->join('lokasi', 'unit.id_lokasi', '=', 'lokasi.id')
                ->join('kecamatan', 'lokasi.id_kecamatan', '=', 'kecamatan.id')
                ->join('kabupaten', 'kecamatan.id_kabupaten', '=', 'kabupaten.id')
                ->select(
                    DB::raw('COUNT(unit.id) AS total_unit'),
                    'kecamatan.nama AS kecamatan',
                    'kabupaten.nama AS kabupaten'
                )
                ->where('unit.id_dpl', $dpl->id)
                ->where('unit.id_kkn', $id_kkn)
                ->groupBy('kecamatan.id', 'kabupaten.id', 'kecamatan.nama', 'kabupaten.nama')
                ->get();
            
            // Get data prodi untuk DPL
            $data_prodi = DB::table('mahasiswa')
                ->join('prodi', 'mahasiswa.id_prodi', '=', 'prodi.id')
                ->whereIn('mahasiswa.id_unit', $unit_ids)
                ->where('mahasiswa.id_kkn', $id_kkn)
                ->select(
                    'prodi.nama_prodi',
                    DB::raw('COUNT(DISTINCT mahasiswa.id_unit) as total_unit'),
                    DB::raw('COUNT(mahasiswa.id) as total_mahasiswa')
                )
                ->groupBy('prodi.id', 'prodi.nama_prodi')
                ->get();
            
            return view('dpl.dashboard', compact('dpl', 'id_kkn', 'total_mahasiswa', 'total_unit', 'data_lokasi', 'data_prodi')); 
        } else {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login.index')->with('error', 'Role tidak valid atau tidak dikenali.');
        }
            
    }

    public function getCardValue(Request $request)
    {
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        if ($role != "Admin") {
            return response()->json([
                'data' => null
            ]);
        }

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
                ->groupBy('prodi.id', 'prodi.nama_prodi') // Tambahkan 'prodi.nama_prodi' ke group by
                ->get();
        } else {
            // Jika periode adalah id_kkn tertentu, ambil data berdasarkan id_kkn
            $data = DB::table('mahasiswa')
                ->join('prodi', 'mahasiswa.id_prodi', '=', 'prodi.id')
                ->join('unit', 'mahasiswa.id_unit', '=', 'unit.id')
                ->where('mahasiswa.id_kkn', $id_kkn) // Filter berdasarkan id_kkn
                ->select(
                    'prodi.nama_prodi',
                    DB::raw('COUNT(DISTINCT mahasiswa.id_unit) as total_unit'),
                    DB::raw('COUNT(mahasiswa.id) as total_mahasiswa')
                )
                ->groupBy('prodi.id', 'prodi.nama_prodi')
                ->get();
        }

        return response()->json($data); // Kembalikan data dalam format JSON
    }

    public function getUnitData(Request $request)
    {
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
}
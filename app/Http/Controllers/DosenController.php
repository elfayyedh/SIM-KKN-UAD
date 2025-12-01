<?php

namespace App\Http\Controllers;

use App\Jobs\EntriDataDosen;
use App\Models\Dosen;
use App\Models\User;
use App\Models\QueueProgress;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DosenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if user is admin
        $user = Auth::user();
        $isAdmin = $user->userRoles->contains(function ($userRole) {
            return $userRole->role->nama_role === 'Admin';
        });

        if (!$isAdmin) {
            abort(403, 'Unauthorized action.');
        }

        $dosen = Dosen::with(['user'])->get();
        return view('administrator.read.show-dosen', compact('dosen'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if user is admin
        $user = Auth::user();
        $isAdmin = $user->userRoles->contains(function ($userRole) {
            return $userRole->role->nama_role === 'Admin';
        });

        if (!$isAdmin) {
            abort(403, 'Unauthorized action.');
        }

        return view('administrator.create.create-dosen');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "file_excel" => 'required|array',
            "file_excel.*.nama" => 'required|string',
            "file_excel.*.nidn" => 'required|string',
            "file_excel.*.email" => 'required|email',
            "file_excel.*.nomorHP" => 'nullable|string',
            "file_excel.*.jenisKelamin" => 'nullable|in:L,P',
        ]);

        DB::beginTransaction();

        try {
            $progress = QueueProgress::create([
                'progress' => 0,
                'total' => count($validated['file_excel']),
                'step' => 0,
                'status' => 'pending',
                'message' => 'Menunggu proses...'
            ]);

            DB::commit();

            EntriDataDosen::dispatch($validated['file_excel'], $progress->id);

            return response()->json(['id_progress' => $progress->id]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nidn' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'no_telp' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:L,P',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $dosen = Dosen::findOrFail($id);
            $user = $dosen->user;

            // Check if email is being changed and if it's already taken by another user
            if ($user->email !== $request->email) {
                $emailExists = User::where('email', $request->email)
                    ->where('id', '!=', $user->id)
                    ->exists();

                if ($emailExists) {
                    return response()->json(['errors' => ['email' => ['Email sudah digunakan oleh user lain.']]], 422);
                }
            }

            // Update user data
            $user->update([
                'nama' => $request->nama,
                'email' => $request->email,
                'no_telp' => $request->no_telp,
                'jenis_kelamin' => $request->jenis_kelamin ?? 'L',
            ]);

            // Update dosen data (only nip field)
            $dosen->update([
                'nip' => $request->nidn,
            ]);

            DB::commit();

            return response()->json(['success' => 'Data dosen berhasil diupdate.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $dosen = Dosen::findOrFail($id);
            $user = $dosen->user;

            // Check if dosen is assigned as DPL
            if ($dosen->dplAssignments()->count() > 0) {
                return response()->json(['error' => 'Tidak dapat menghapus dosen yang masih menjadi DPL.'], 400);
            }

            // Check if dosen is assigned as Tim Monev
            if ($dosen->timMonevAssignments()->count() > 0) {
                return response()->json(['error' => 'Tidak dapat menghapus dosen yang masih menjadi Tim Monev.'], 400);
            }

            // Delete user roles
            UserRole::where('id_user', $user->id)->delete();

            // Delete dosen
            $dosen->delete();

            // Delete user
            $user->delete();

            DB::commit();

            return response()->json(['success' => 'Data dosen berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

}


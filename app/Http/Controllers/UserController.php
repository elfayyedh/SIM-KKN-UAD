<?php

namespace App\Http\Controllers;

use App\Models\BidangProker;
use App\Models\KKN;
use App\Models\Mahasiswa;
use App\Models\Role;
use App\Models\User;
use App\Models\Dpl;
use App\Models\Dosen;
use App\Models\TimMonev;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Helper aman untuk mendapatkan nama role aktif.
     * Mengembalikan string seperti 'Admin', 'Mahasiswa', 'dpl', 'monev', atau 'Guest'.
     */
    private function getActiveRoleName()
    {
        if (!Auth::check()) {
            return 'Guest';
        }

        if (session('user_is_dosen', false)) {
            return session('active_role'); 
        } else {
            $activeUserRole = Auth::user()->userRoles->find(session('selected_role'));
            if ($activeUserRole && $activeUserRole->role) {
                return $activeUserRole->role->nama_role; 
            }
        }
        
        $roles = Auth::user()->userRoles;
        if($roles->count() >= 1) {
            $role = $roles->first();
            session(['selected_role' => $role->id]);
            return $role->role->nama_role;
        }

        return 'Guest'; 
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kkn = KKN::all();
        if (count($kkn) > 0) {
            return view('administrator.create.create-user', compact('kkn'));
        } else {
            return redirect('/kkn/create')->with('error', 'Silahkan tambahkan data KKN terlebih dahulu');
        }
    }
    
    public function createAdmin()
    {
        if ($this->getActiveRoleName() != "Admin") {
            return view('not-found');
        }
        return view('administrator.create.create-admin');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($this->getActiveRoleName() != "Admin") {
            return view('not-found');
        }
        
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 
                'regex:/[0-9]/', 'regex:/[@$!%*?&-_]/'
            ],
            'jenis_kelamin' => 'required|in:L,P',
            'no_telp' => 'required|numeric|digits_between:10,13',
        ], [
            'email.unique' => 'Email sudah terdaftar',
            'password.regex' => 'Password harus mengandung minimal 1 huruf kapital, 1 huruf kecil, 1 angka, dan 1 karakter khusus',
            'no_telp.numeric' => 'No Telpon harus berupa angka',
            'no_telp.digits_between' => 'No Telpon harus memiliki minimal 10 sampai 13 digit',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = new User();
        $user->nama = $request->input('nama');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->jenis_kelamin = $request->input('jenis_kelamin');
        $user->no_telp = $request->input('no_telp');
        $user->save();
        
        $role = Role::where('nama_role', 'Admin')->first();
        $user->userRoles()->create([
            'id_role' => $role->id,
        ]);

        return redirect()->route('user.admin')
            ->with('success', 'Data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $role = $this->getActiveRoleName();
        $user = Auth::user();

        if ($role == "Mahasiswa") {
            try {
                $activeUserRole = Auth::user()->userRoles()->with('mahasiswa')->find(session('selected_role'));
                if (!$activeUserRole || !$activeUserRole->mahasiswa) {
                    throw new \Exception('Data Mahasiswa tidak ditemukan.');
                }
                
                $id = $activeUserRole->mahasiswa->id;
                $mahasiswa_data = Mahasiswa::with([
                    'prodi', 'userRole.user', 'kkn', 'unit',
                    'logbookKegiatan', 'logbookSholat'
                ])->findOrFail($id);

                $prokerData = BidangProker::with(['proker' => function ($query) use ($mahasiswa_data) {
                    if ($query->getModel()->type == 'individu') {
                        $query->where('id_mahasiswa', $mahasiswa_data->id);
                    }
                }])
                    ->where('id_kkn', $mahasiswa_data->kkn->id)
                    ->get();

                $prokerData->each(function ($item) {
                    $totalJKEM = $item->proker->sum(function ($proker) {
                        return $proker->total_jkem;
                    });
                    $item->total_jkem_bidang = $totalJKEM;
                });

                return view('mahasiswa.profil-user', ['user' => $mahasiswa_data, 'prokerData' => $prokerData]);
            
            } catch (\Exception $e) {
                return view('not-found');
            }
        } else if ($role == "Admin") {
            echo "Under maintenance";

        } else if ($role == "dpl") { 
            try {
                $dosen = $user->dosen; 
                if (!$dosen) {
                    throw new \Exception('Data profil Dosen (NIP, dll) tidak ditemukan.');
                }
                
                $dplAssignments = $dosen->dplAssignments()
                    ->with([
                        'kkn', 
                        'units.lokasi.kecamatan.kabupaten', 
                        'units.prokers.kegiatan'
                    ]) 
                    ->get(); 
                
                if ($dplAssignments->isEmpty()) {
                    throw new \Exception('Data penugasan DPL tidak ditemukan.');
                }
                
                $units = collect();
                foreach ($dplAssignments as $dplAssignment) {
                    $units = $units->merge($dplAssignment->units);
                }

                $units->each(function ($unit) {
                    $total_jkem_unit = $unit->prokers->sum(function ($proker) {
                        return $proker->kegiatan->sum('total_jkem');
                    });
                    $unit->total_jkem_all_prokers = $total_jkem_unit;
                });
                
                return view('dpl.profil-user', compact('user', 'dosen', 'dplAssignments', 'units'));
            } catch (\Exception $e) {
                return redirect()->route('dashboard')->with('error', 'Gagal memuat profil: ' . $e->getMessage());
            }

        } else if ($role == "monev") { 
            try {
                $dosen = $user->dosen; 
                if (!$dosen) {
                    throw new \Exception('Data profil Dosen tidak ditemukan.');
                }
                return view('tim monev.profil-user', compact('user', 'dosen'));
            
            } catch (\Exception $e) {
                return redirect()->route('dashboard')->with('error', $e->getMessage());
            }
        } else {
            return view('not-found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = $this->getActiveRoleName();
        $user = User::findOrFail($id);
        $loggedInUser = Auth::user();

        if ($loggedInUser->id != $user->id && $role != "Admin") {
            return view('not-found');
        }
        return view('user-edit', compact('user'));
    }

    public function adminShow()
    {
        $role = $this->getActiveRoleName();
        if ($role == "Admin") {
            $admin = User::whereHas('userRoles', function ($query) {
                $query->whereHas('role', function ($query) {
                    $query->where('nama_role', 'Admin');
                });
            })->get();

            return view('administrator.read.show-admin', compact('admin'));
        } else {
            return view('not-found');
        }
    }

    public function mahasiswaIndex()
    {
        if ($this->getActiveRoleName() != "Admin") {
            return view('not-found');
        }
        $mahasiswa = Mahasiswa::with(['userRole.user', 'prodi', 'kkn', 'unit'])->get();
        return view('administrator.read.manajemen-mahasiswa', compact('mahasiswa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = $this->getActiveRoleName();
        $user = User::findOrFail($id);
        $loggedInUser = Auth::user();

        if ($loggedInUser->id != $user->id && $role != "Admin") {
            return view('not-found');
        }

        $request->validate([
            'nama' => 'required',
            'no_telp' => 'required|numeric|digits_between:10,13',
            'jenis_kelamin' => 'required',
        ], [
            'no_telp.required' => 'Nomor Telepon harus diisi',
            'no_telp.numeric' => 'Nomor Telepon harus angka',
            'no_telp.digits_between' => 'Nomor Telepon minimal 10 sampai 13 angka',
            'nama.required' => 'Nama harus diisi',
            'jenis_kelamin.required' => 'Jenis Kelamin harus diisi',
        ]);
        
        $user->update($request->all());
        return redirect()->back()->with('success_user', 'Data user berhasil diubah');
    }

    public function updatePassword(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $loggedInUser = Auth::user();
        $loggedInRole = $this->getActiveRoleName();

        if ($loggedInUser->id != $user->id && $loggedInRole != "Admin") {
            return view('not-found');
        }

        $validator = Validator::make($request->all(), [
            'old-password' => 'required',
            'new_password' => [
                'required', 'min:8',
                'regex:/[a-z]/', 
                'regex:/[A-Z]/', 
                'regex:/[0-9]/', 
                'regex:/[\W_]/', 
            ],
            'confirm_password' => 'required|same:new_password',
        ], [
            'old-password.required' => 'Password sebelumnya harus diisi.',
            'new_password.required' => 'Password baru harus diisi.',
            'new_password.min' => 'Password baru harus minimal 8 karakter.',
            'new_password.regex' => 'Password baru harus mengandung huruf kecil, huruf besar, angka, dan karakter khusus.',
            'confirm_password.required' => 'Konfirmasi password harus diisi.',
            'confirm_password.same' => 'Konfirmasi password tidak cocok dengan password baru.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if ($loggedInRole != 'Admin' && !Hash::check($request->input('old-password'), $user->password)) {
            return redirect()->back()->withErrors(['old-password' => 'Password lama salah.']);
        }
        if (!Hash::check($request->input('old-password'), $user->password)) {
             return redirect()->back()->withErrors(['old-password' => 'Password lama salah.']);
        }


        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return redirect()->back()->with('success_pw', 'Password berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
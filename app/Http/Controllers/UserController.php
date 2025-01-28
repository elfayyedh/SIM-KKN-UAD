<?php

namespace App\Http\Controllers;

use App\Models\BidangProker;
use App\Models\KKN;
use App\Models\Mahasiswa;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
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
        if (Auth::user()->userRoles->find(session('selected_role'))->role->nama_role != "Admin") {
            return view('not-found');
        }
        return view('administrator.create.create-admin');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->userRoles->find(session('selected_role'))->role->nama_role != "Admin") {
            return view('not-found');
        }
        // Validasi data input
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8', // Minimal 8 karakter
                'regex:/[a-z]/', // Harus ada huruf kecil
                'regex:/[A-Z]/', // Harus ada huruf kapital
                'regex:/[0-9]/', // Harus ada angka
                'regex:/[@$!%*?&-_]/', // Harus ada karakter khusus
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

        // Proses penyimpanan data
        $user = new User();
        $user->nama = $request->input('nama');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->jenis_kelamin = $request->input('jenis_kelamin');
        $user->no_telp = $request->input('no_telp');
        $user->save();
        // Simpan role user
        $role = Role::where('nama_role', 'Admin')->first();
        $user->userRoles()->create([
            'id_role' => $role->id,
        ]);

        return redirect()->route('user.admin') // Ganti dengan route tujuan setelah sukses
            ->with('success', 'Data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        if ($role == "Mahasiswa") {
            $id = Auth::user()->userRoles->find(session('selected_role'))->mahasiswa->id;
            try {
                $user = Mahasiswa::with([
                    'prodi',
                    'userRole.user',
                    'kkn',
                    'unit',
                    'logbookKegiatan',
                    'logbookSholat'
                ])->findOrFail($id);

                $prokerData = BidangProker::with(['proker' => function ($query) use ($user) {
                    if ($query->getModel()->type == 'individu') {
                        $query->where('id_mahasiswa', $user->id);
                    }
                }])
                    ->where('id_kkn', $user->kkn->id)
                    ->get();

                $prokerData->each(function ($item) {
                    $totalJKEM = $item->proker->sum(function ($proker) {
                        return $proker->total_jkem;
                    });
                    $item->total_jkem_bidang = $totalJKEM;
                });


                return view('mahasiswa.profil-user', compact('user', 'prokerData'));
            } catch (\Exception $e) {
                return view('not-found');
            }
        } else if ($role == "Admin") {
            echo "Under maintenance";
        } else if ($role == "DPL") {
            echo "Under maintenance";
        } else if ($role == "Tim Monev") {
            echo "Under maintenance";
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        if ($role == "Mahasiswa") {
            if (Auth::user()->id != $id) {
                return view('not-found');
            }
            $user = User::where('id', $id)->first();
            return view('user-edit', compact('user'));
        } else if ($role == "Admin") {
            $user = User::where('id', $id)->first();
            return view('user-edit', compact('user'));
        } else {
            return view('not-found');
        }
    }

    public function adminShow()
    {
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        if ($role == "Admin") {
            // Ambil data user yang memiliki role admin dan jalankan query dengan get()
            $admin = User::whereHas('userRoles', function ($query) {
                $query->whereHas('role', function ($query) {
                    $query->where('nama_role', 'Admin');
                });
            })->get(); // tambahkan get() di sini untuk mengeksekusi query

            // Debugging jika masih diperlukan
            // dd($admin);

            return view('administrator.read.show-admin', compact('admin'));
        } else {
            return view('not-found');
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate request
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
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        if ($role == "Admin") {
            $user = User::where('id', $id)->first();
            $user->update($request->all());
            return redirect()->back()->with('success_user', 'Data user berhasil diubah');
        } else if ($role == "Mahasiswa") {
            if (Auth::user()->id != $id) {
                return view('not-found');
            }
            $user = User::where('id', $id)->first();
            $user->update($request->all());
            return redirect()->back()->with('success_user', 'Data user berhasil diubah');
        }
    }

    public function updatePassword(Request $request, string $id)
    {
        $role = Auth::user()->userRoles->find(session('selected_role'))->role->nama_role;
        if ($role == "Mahasiswa") {
            if (Auth::user()->id != $id) {
                return view('not-found');
            }
        }
        // Validasi input dari form
        $validator = Validator::make($request->all(), [
            'old-password' => 'required',
            'new_password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',      // Harus memiliki minimal 1 huruf kecil
                'regex:/[A-Z]/',      // Harus memiliki minimal 1 huruf besar
                'regex:/[0-9]/',      // Harus memiliki minimal 1 angka
                'regex:/[\W_]/',      // Harus memiliki minimal 1 karakter khusus
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

        // Jika validasi gagal
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('id', $id)->first();

        // Memastikan password lama benar
        if (!Hash::check($request->input('old-password'), $user->password)) {
            return redirect()->back()->withErrors(['old-password' => 'Password lama salah.']);
        }

        // Update password baru
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        // Redirect dengan pesan sukses
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

@php
    $activeRoleName = ''; 
    if (Auth::check()) {
        if (session('user_is_dosen', false)) {
            $activeRoleName = session('active_role');
        } else {
            $activeUserRole = Auth::user()->userRoles->find(session('selected_role'));
            if ($activeUserRole && $activeUserRole->role) {
                $activeRoleName = $activeUserRole->role->nama_role;
            }
        }
    }
@endphp

<div>
    <table class="datatable-buttons table table-striped table-bordered dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Lokasi</th>
                <th>Kecamatan</th>
                <th>Kabupaten</th>
                <th>Total JKEM</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($units as $item)
                <tr>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->lokasi->nama }}</td>
                    <td>{{ $item->lokasi->kecamatan->nama }}</td>
                    <td>{{ $item->lokasi->kecamatan->kabupaten->nama }}</td>
                    <td>{{ $item->total_jkem_all_prokers }}</td>
                    <td>
                        @if ($activeRoleName == 'monev')
                            <a href="{{ route('monev.evaluasi.daftar-mahasiswa', $item->id) }}" class="btn btn-info btn-sm">
                                <i class="bx bx-show-alt me-1"></i> Lihat Anggota
                            </a>
                        @else
                            <a href="{{ route('unit.show', $item->id) }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-show-alt me-1"></i> Detail Unit
                            </a>
                        @endif

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
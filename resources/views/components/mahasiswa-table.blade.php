@props([
    'mahasiswa', // Data mahasiswa yang dikirim
    'mode' => 'default' // Penanda baru kita: 'default' (tombol Edit) atau 'monev' (tombol Penilaian)
])

<div>
    <table class="datatable-buttons table table-striped table-bordered dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIM</th>
                <th>Prodi</th>
                <th>Jenis Kelamin</th>
                <th>Nomor Telpon</th>
                <th>Unit</th>
                <th>Total JKEM</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mahasiswa as $item)
                <tr>
                    <td>{{ $item->userRole->user->nama }}</td>
                    <td>{{ $item->nim }}</td>
                    <td>{{ $item->prodi->nama_prodi }}</td>
                    <td>{{ $item->userRole->user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td><a
                            href="https://wa.me/{{ $item->userRole->user->no_telp }}">{{ $item->userRole->user->no_telp }}</a>
                    </td>
                    <td><a href="{{ route('unit.show', ['id' => $item->unit->id]) }}">{{ $item->unit->nama }}</a></td>
                    <td>{{ $item->total_jkem }}</td>
                    <td>
                        @if ($mode == 'monev')
                            <a href="{{ route('monev.evaluasi.penilaian', $item->id) }}" class="btn btn-success btn-sm">
                                <i class="bx bx-edit-alt me-1"></i> Beri Penilaian
                            </a>
                        @else
                            <a class="btn btn-secondary btn-sm" href="{{ route('user.edit', $item->userRole->user->id) }}"><i
                                    class="bx bx-edit me-1">Edit</i></a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
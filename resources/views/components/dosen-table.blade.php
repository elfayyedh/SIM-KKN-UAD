@props(['dosen'])

<div>
    <table class="datatable-buttons table table-striped table-bordered dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIP</th>
                <th>Email</th>
                <th>Nomor Telpon</th>
                <th>Jenis Kelamin</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dosen as $item)
                <tr>
                    <td>{{ $item->user->nama }}</td>
                    <td>{{ $item->nip }}</td>
                    <td>{{ $item->user->email }}</td>
                    <td><a href="https://wa.me/{{ $item->user->no_telp }}">{{ $item->user->no_telp }}</a></td>
                    <td>{{ $item->user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td>
                        <a class="btn btn-secondary btn-sm" href="{{ route('user.edit', $item->user->id) }}">
                            <i class="bx bx-edit me-1"></i>Edit
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

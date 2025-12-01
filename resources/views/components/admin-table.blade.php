@props(['admin'])

<div>
    <table class="datatable-buttons table table-striped table-bordered dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Nomor Telpon</th>
                <th>Jenis Kelamin</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($admin as $item)
                <tr>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->email }}</td>
                    <td><a href="https://wa.me/{{ $item->no_telp }}">{{ $item->no_telp }}</a></td>
                    <td>{{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td>
                        <a class="btn btn-secondary btn-sm" href="{{ route('user.edit', $item->id) }}">
                            <i class="bx bx-edit me-1"></i>Edit
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

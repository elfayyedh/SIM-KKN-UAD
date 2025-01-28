<div>
    <div>
        <table class="datatable-buttons table table-striped table-bordered dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Jenis Kelamin</th>
                    <th>Nomor Telpon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($timMonev as $item)
                    <tr>
                        <td>{{ $item->userRole->user->nama }}</td>
                        <td>{{ $item->nip }}</td>
                        <td>{{ $item->userRole->user->jenis_kelamin }}</td>
                        <td>{{ $item->userRole->user->no_telp }}</td>
                        <td>
                            <a href="{{ route('user.edit', $item->userRole->user->id) }}"><i
                                    class="bx bx-edit me-1">Edit</i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

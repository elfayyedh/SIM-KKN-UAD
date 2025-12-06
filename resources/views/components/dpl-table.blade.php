<div>
    <div>
        <table class="datatable-buttons table table-striped table-bordered dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Jenis Kelamin</th>
                    <th>Nomor Telpon</th>
                    <th>Unit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($dpl as $item)
                <tr>
                    <td>{{ $item->dosen->user->nama ?? 'N/A' }}</td>
                    <td>{{ $item->dosen->nip ?? 'N/A' }}</td>
                    <td>
                        @if($item->dosen->user->jenis_kelamin == 'L')
                            Laki-laki
                        @elseif($item->dosen->user->jenis_kelamin == 'P')
                            Perempuan
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $item->dosen->user->no_telp ?? 'N/A' }}</td>
                    <td>{{ $item->units->count() }} Unit</td>
                    <td>
                        <a href="{{ route('user.edit', $item->dosen->user->id) }}" class="btn btn-secondary btn-sm"><i class="bx bx-edit me-1"></i>Edit</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

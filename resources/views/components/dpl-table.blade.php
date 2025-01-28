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
                        <td>{{ $item->userRole->user->nama }}</td>
                        <td>{{ $item->nip }}</td>
                        <td>{{ $item->userRole->user->jenis_kelamin }}</td>
                        <td>{{ $item->userRole->user->no_telp }}</td>
                        <td>
                            <div class="dropdown">
                                <a class="btn-link text-muted font-size-16 shadow-none dropdown-toggle" href="#"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-dots-horizontal-rounded"></i>
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    @foreach ($item->units as $item)
                                        <li><a class="dropdown-item"
                                                href="/unit/detail/{{ $item->id }}">{{ $item->nama }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </td>
                        <td>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

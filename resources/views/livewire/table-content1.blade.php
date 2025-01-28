<div class="card">
    <div class="card-header">
        <div class="card-title">Rekap JKEM anggota</div>
    </div>
    <div class="card-body table-responsive" style="overflow-x: auto">
        <table class="table nowrap w-100 table-striped table-hover">
            <thead>
                <tr class="text-nowrap">
                    <th>Nama</th>
                    <th>Tercapai</th>
                    <th>Belum tercapai</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($content as $item)
                    <tr>
                        <td><a
                                href="{{ route('mahasiswa.show', ['id' => $item->id]) }}">{{ $item->userRole->user->nama }}</a>
                        </td>
                        <td>
                            {{ $item->jkem_tercapai }}
                        </td>
                        <td>
                            {{ $item->jkem_belum_tercapai }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div>
    <table class="datatable-buttons table table-striped table-bordered dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>Nama kontol</th>
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
                        <a href="{{ route('unit.show', $item->id) }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-show-alt me-1"></i> Detail Unit
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
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
            @foreach ($unit as $item)
                <tr>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->lokasi->nama }}</td>
                    <td>{{ $item->lokasi->kecamatan->nama }}</td>
                    <td>{{ $item->lokasi->kecamatan->kabupaten->nama }}</td>
                    <td>{{ $item->total_jkem }}</td>
                    <td>
                        Edit
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@props(['timMonev']) {{-- 1. Asumsi kamu nerima 'timMonev' (sesuai Blade KKN) --}}
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
                        <td>
                            <a href="{{ route('user.edit', $item->dosen->user->id) }}"
                            class="btn btn-sm btn-info">Edit</a>
                            
                            <form action="{{ route('kkn.removeMonev', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type-="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Yakin hapus {{ $item->dosen->user->nama }} dari Tim Monev?')">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
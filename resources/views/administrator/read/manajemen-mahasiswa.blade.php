@extends('layouts.index')

@section('title', 'MANAJEMEN MAHASISWA')
@section('styles')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">MANAJEMEN MAHASISWA</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Pengguna</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Mahasiswa</a></li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12 table-responsive">
                    <x-alert-component />
                    <table class="datatable-buttons table table-striped table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>NIM</th>
                                <th>Prodi</th>
                                <th>Jenis Kelamin</th>
                                <th>Nomor Telpon</th>
                                <th>Unit</th>
                                <th>KKN</th>
                                <th>Total JKEM</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mahasiswa as $item)
                            <tr>
                                <td>{{ $item->userRole->user->nama ?? 'N/A' }}</td>
                                <td>{{ $item->nim ?? 'N/A' }}</td>
                                <td>{{ $item->prodi->nama_prodi ?? 'N/A' }}</td>
                                <td>{{ $item->userRole->user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                <td>{{ $item->userRole->user->no_telp ?? 'N/A' }}</td>
                                <td>{{ $item->unit->nama ?? 'N/A' }}</td>
                                <td>{{ $item->kkn->nama ?? 'N/A' }}</td>
                                <td>{{ $item->total_jkem ?? 0 }}</td>
                                <td class="text-center">
                                    @php
                                        $today = \Carbon\Carbon::now();
                                        $isKknSelesai = false;
                                        if (isset($item->kkn)) {
                                            $isKknSelesai = !$item->kkn->status || $today->gt($item->kkn->tanggal_selesai);
                                        }
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-center">
                                        <small class="status-label-{{ $item->id }} me-2 d-flex align-items-center">
                                            @if (!$item->status)
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            @elseif ($isKknSelesai)
                                                <span class="badge bg-secondary">Selesai</span>
                                            @else
                                                <span class="badge bg-success">Aktif</span>
                                            @endif
                                        </small>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" 
                                                type="checkbox" 
                                                data-id="{{ $item->id }}"
                                                {{ $item->status ? 'checked' : '' }}
                                                {{ $isKknSelesai ? 'disabled' : '' }}
                                                title="{{ $isKknSelesai ? 'Periode KKN sudah selesai' : '' }}">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('user.edit', $item->userRole->user->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('pageScript')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".datatable-buttons").DataTable();

            $(document).on('change', '.status-toggle', function() {
                const checkbox = $(this);
                const mahasiswaId = checkbox.data('id');
                const isActive = checkbox.is(':checked');

                $.ajax({
                    url: `/mahasiswa/${mahasiswaId}/toggle-status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            const badgeClass = response.status ? 'bg-success' : 'bg-danger';
                            const badgeText = response.status ? 'Aktif' : 'Tidak Aktif';
                            $(`.status-label-${mahasiswaId}`).html(
                                `<span class="badge ${badgeClass}">${badgeText}</span>`
                            );
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        checkbox.prop('checked', !isActive);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat mengubah status'
                        });
                    }
                });
            });
        });
    </script>
@endsection

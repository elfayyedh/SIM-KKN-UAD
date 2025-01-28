@extends('layouts.index')
@section('title', 'Edit Unit | ' . $unit->nama)

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Page title --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">EDIT UNIT</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Unit</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('unit.show') }}">Unit {{ $unit->nama }}</a>
                                </li>
                                <li class="breadcrumb-item active">Edit Unit</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End Page title --}}

            <x-alert-component />
            @if (auth()->user()->userRoles->find(session('selected_role'))->role->nama_role == 'DPL' &&
                    auth()->user()->userRoles->find(session('selected_role'))->dpl->id == $unit->id_dpl)
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-primary fw-bold mb-3">#Profil unit</div>
                                <form action="{{ route('unit.updateProfilUnit') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group mb-3">
                                        <input type="hidden" name="id" value="{{ $unit->id }}">
                                        <label for="tanggal_penerjunan" class="form-label">Tanggal penerjunan</label>
                                        <input type="date" class="form-control datepicker-basic"
                                            name="tanggal_penerjunan" value="{{ $unit->tanggal_penerjunan }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="tanggal_penarikan" class="form-label">Tanggal penarikan</label>
                                        <input type="date" class="form-control datepicker-basic" name="tanggal_penarikan"
                                            value="{{ $unit->tanggal_penarikan != null ? $unit->tanggal_penarikan : '-' }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-primary fw-bold mb-3">#Jabatan anggota</div>
                            <form action="{{ route('unit.updateJabatanAnggota') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <input type="hidden" name="id_unit" value="{{ $unit->id }}">
                                    @foreach ($unit->mahasiswa as $index => $anggota)
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label class="form-label">{{ $anggota->nim }} -
                                                    {{ $anggota->userRole->user->nama }}</label>
                                                <input type="hidden" name="id_mahasiswa[]" value="{{ $anggota->id }}">
                                                <input type="text" class="form-control" name="jabatan[]"
                                                    value="{{ $anggota->jabatan == null ? '-' : $anggota->jabatan }}">
                                            </div>
                                        </div>
                                        @if (($index + 1) % 3 == 0)
                                </div>
                                <div class="row">
                                    @endif
                                    @endforeach
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="kkn_mulai" class="form-control" value="{{ $unit->kkn->tanggal_mulai }}">
    <input type="hidden" id="kkn_selesai" class="form-control" value="{{ $unit->kkn->tanggal_selesai }}">
@endsection
@section('pageScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <!-- datepicker js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        $(document).ready(function() {
            const minFormatted = moment($('#kkn_mulai').val(), "DD-MM-YYYY").format("YYYY-MM-DD");
            const maxFormatted = moment($('#kkn_selesai').val(), "DD-MM-YYYY").format("YYYY-MM-DD");

            flatpickr(".datepicker-basic", {
                locale: "id",
                altInput: true,
                minDate: minFormatted,
                maxDate: maxFormatted,
                altFormat: "l, j F Y",
                dateFormat: "Y-m-d",
            });
        });
    </script>
@endsection

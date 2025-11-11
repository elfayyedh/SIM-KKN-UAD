@extends('layouts.index')

@section('title', 'Evaluasi Unit KKN')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Evaluasi Unit KKN</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active"><a href="{{ route('monev.evaluasi.index') }}">Evaluasi Unit</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tampilkan pesan error jika ada --}}
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if ($allMonevAssignments->count() > 1)
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('monev.evaluasi.set-kkn') }}" method="POST" class="row d-flex align-items-center">
                        @csrf
                        <div class="col-md-3">
                            <label class="form-label mb-0">
                                <strong>Anda terdaftar di {{ $allMonevAssignments->count() }} KKN:</strong>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <select name="assignment_id" class="form-select" onchange="this.form.submit()">
                                @foreach ($allMonevAssignments as $assignment)
                                    <option value="{{ $assignment->id }}" 
                                        {{-- Tandai KKN yang sedang aktif --}}
                                        @if($assignment->id == $monevAssignment->id) selected @endif>
                                        {{ $assignment->kkn->nama ?? 'KKN Tanpa Nama' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <noscript>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Ganti KKN</button>
                            </div>
                        </noscript>
                    </form>
                </div>
            </div>
        @endif

        <div class="alert alert-info">
            Menampilkan DPL untuk KKN: <strong>{{ $monevAssignment->kkn->nama ?? '?' }}</strong>.
            <br>
            Pilih DPL untuk dievaluasi unitnya (Maksimal 3). Anda tidak dapat memilih diri sendiri.
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Daftar DPL Tersedia</h4>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Nama Dosen</th>
                                        <th>NIP</th>
                                        <th style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="available-dpl-list">
                                    @forelse ($availableDpls as $dpl)
                                        <tr id="available-row-{{ $dpl->id }}">
                                            <td>{{ $dpl->dosen->user->nama ?? '?' }}</td>
                                            <td>{{ $dpl->dosen->nip ?? '?' }}</td>
                                            <td>
                                                <button 
                                                    class="btn btn-sm btn-primary btn-assign" 
                                                    data-id="{{ $dpl->id }}">
                                                    Pilih
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada DPL yang tersedia.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            DPL Dipilih 
                            (<span id="selected-count">{{ $selectedDpls->count() }}</span>/3)
                        </h4>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Nama Dosen</th>
                                        <th colspan="2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="selected-dpl-list">
                                    @foreach ($selectedDpls as $dpl)
                                        <tr id="selected-row-{{ $dpl->id }}">
                                            <td>
                                                {{ $dpl->dosen->user->nama ?? '?' }}
                                                <br>
                                                <small class="text-muted">{{ $dpl->dosen->nip ?? '?' }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('monev.evaluasi.dpl-units', $dpl->id) }}" class="btn btn-sm btn-outline-primary">
                                                    Lihat Unit
                                                </a>
                                            </td>
                                            <td>
                                                <button 
                                                    class="btn btn-sm btn-danger btn-remove" 
                                                    data-id="{{ $dpl->id }}">
                                                    Batal
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageScript')
{{-- (JavaScript AJAX tidak berubah, jadi saya potong) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const assignUrl = '{{ route('monev.evaluasi.assign') }}';
        const removeUrl = '{{ route('monev.evaluasi.remove') }}';
        
        const availableList = document.getElementById('available-dpl-list');
        const selectedList = document.getElementById('selected-dpl-list');
        const selectedCountEl = document.getElementById('selected-count');

        availableList.addEventListener('click', function(e) {
            if (!e.target.classList.contains('btn-assign')) return;

            e.preventDefault();
            const button = e.target;
            const dplId = button.dataset.id;
            
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            const formData = new FormData();
            formData.append('id_dpl', dplId);

            fetch(assignUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    document.getElementById(`available-row-${dplId}`).remove();
                    const newRow = createSelectedRow(result.data);
                    selectedList.insertAdjacentHTML('beforeend', newRow);
                    updateCount(1);
                } else {
                    alert(result.message);
                    button.disabled = false;
                    button.innerHTML = 'Pilih';
                }
            })
            .catch(() => {
                alert('Terjadi kesalahan. Silakan coba lagi.');
                button.disabled = false;
                button.innerHTML = 'Pilih';
            });
        });

        selectedList.addEventListener('click', function(e) {
            if (!e.target.classList.contains('btn-remove')) return;

            e.preventDefault();
            const button = e.target;
            const dplId = button.dataset.id;
            
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            const formData = new FormData();
            formData.append('id_dpl', dplId);

            fetch(removeUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    document.getElementById(`selected-row-${dplId}`).remove();
                    updateCount(-1);
                } else {
                    alert(result.message);
                    button.disabled = false;
                    button.innerHTML = 'Batal';
                }
            })
            .catch(() => {
                alert('Terjadi kesalahan. Silakan coba lagi.');
                button.disabled = false;
                button.innerHTML = 'Batal';
            });
        });

        function createSelectedRow(dpl) {
            const dplUnitUrl = `{{ url('monev/evaluasi/dpl') }}/${dpl.id}/units`;
            return `
                <tr id="selected-row-${dpl.id}">
                    <td>
                        ${dpl.dosen.user.nama ?? '?'}
                        <br>
                        <small class="text-muted">${dpl.dosen.nip ?? '?'}</small>
                    </td>
                    <td>
                        <a href="${dplUnitUrl}" class="btn btn-sm btn-outline-primary">
                            Lihat Unit
                        </a>
                    </td>
                    <td>
                        <button 
                            class="btn btn-sm btn-danger btn-remove" 
                            data-id="${dpl.id}">
                            Batal
                        </button>
                    </td>
                </tr>
            `;
        }

        function updateCount(change) {
            let currentCount = parseInt(selectedCountEl.textContent);
            selectedCountEl.textContent = currentCount + change;
        }
    });
</script>
@endsection
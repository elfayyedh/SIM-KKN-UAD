<form action="{{ route('bidang.update', $bidangProker->id) }}" method="POST" id="bidang_proker_form">
    @csrf
    @method('PUT')
    <div class="row border mb-3">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Bidang</label>
                <input type="text" class="form-control" id="nama" name="nama" required
                    value="{{ $bidangProker->nama }}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="tipe" class="form-label">Tipe</label>
                <select id="tipe" class="form-select" name="tipe">
                    <option value="individu" @if ($bidangProker->tipe == 'individu') selected @endif>Individu</option>
                    <option value="unit" @if ($bidangProker->tipe == 'unit') selected @endif>Bersama</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="syarat_jkem" class="form-label">Minimal JKEM</label>
                <input type="number" class="form-control" id="syarat_jkem" name="syarat_jkem" required
                    value="{{ $bidangProker->syarat_jkem }}">
            </div>
        </div>
        <div class="col-md-2 align-self-end d-flex gap-3">
            <div class="mb-3">
                <button type="submit" class="btn btn-soft-success"><i class="bx bx-save"></i><span class="d-md-none">
                        Simpan</span></button>
            </div>
            <div class="mb-3">
                <a class="btn btn-soft-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                    data-id="{{ $bidangProker->id }}" data-count="{{ $bidangProker->proker_count }}"><i
                        class="bx bx-trash"></i><span class="d-md-none">
                        Hapus</span></a>
            </div>
        </div>
    </div>
</form>

<div class="alert alert-info">
    <b>Pilih "Tipe Kriteria" untuk mengisi otomatis.</b>
</div>

<div id="container-kriteria">
    {{-- ITEM PERTAMA (DEFAULT) --}}
    <div class="row border mb-3 row-kriteria">

        {{-- KOLOM 1: TIPE KRITERIA --}}
        <div class="col-lg-4">
            <div class="mb-3">
                <label class="form-label">Tipe Kriteria</label>
                <select class="form-select select-template">
                    <option value="" selected disabled>-- Pilih Tipe --</option>
                    <option value="jkem">Penilaian JKEM</option>
                    <option value="sholat">Penilaian Sholat</option>
                    <option value="form1">Penilaian Form 1</option>
                    <option value="form2">Penilaian Form 2</option>
                    <option value="form3">Penilaian Form 3</option>
                    <option value="form4">Penilaian Form 4</option>
                    <option value="custom" class="fw-bold text-primary">-- Custom / Manual --</option>
                </select>
            </div>
        </div>

        {{-- KOLOM 2: JUDUL --}}
        <div class="col-lg-4">
            <div class="mb-3">
                <label class="form-label">Judul Kriteria <span class="text-danger">*</span></label>
                <input type="text" class="form-control input-judul" name="kriteria[0][judul]" placeholder="Masukkan Judul" required>
                
                {{-- INPUT HIDDEN (DATA DISIMPAN DISINI SECARA OTOMATIS BY JS) --}}
                <input type="hidden" class="input-var" name="kriteria[0][variable_key]">
                <input type="hidden" class="input-url" name="kriteria[0][link_url]">
                <input type="hidden" class="input-text" name="kriteria[0][link_text]">
            </div>
        </div>

        {{-- KOLOM 3: KETERANGAN --}}
        <div class="col-lg-3">
            <div class="mb-3">
                <label class="form-label">Keterangan (Skala) <span class="text-danger">*</span></label>
                <input type="text" class="form-control input-ket" name="kriteria[0][keterangan]" placeholder="1:..., 2:..., 3:..." required>
            </div>
        </div>

        {{-- KOLOM 4: TOMBOL HAPUS --}}
        <div class="col-lg-1 d-flex align-items-center justify-content-center">
            <button type="button" class="btn btn-soft-danger btn-hapus-kriteria mt-3" disabled>
                <i class="bx bx-trash font-size-18"></i>
            </button>
        </div>
    </div>
</div>

<button type="button" class="btn btn-soft-primary" id="btn-tambah-kriteria">
    <i class="bx bx-plus"></i> Tambah Kriteria
</button>
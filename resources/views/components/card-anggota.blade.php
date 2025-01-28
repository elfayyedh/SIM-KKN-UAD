<a href="{{ route('mahasiswa.show', $anggota->id) }}">
    <div class="card mb-3 card-anggota">
        <div class="card-body">
            <div class="d-flex flex-column">
                <p class="fw-bold">{{ $anggota->userRole->user->nama }}</p>
                <p class="text-muted mb-0">{{ $anggota->nim }}</p>
                <p class="text-muted mb-0">{{ $anggota->prodi->nama_prodi }}</p>
            </div>
        </div>
    </div>
</a>

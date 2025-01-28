<div class="accordion" id="accordionFlushExample">
    @php
        $index = 1;
    @endphp
    @foreach ($proker as $bidangProker)
        <div class="accordion-item">
            <h2 class="accordion-header" id="flush-headingOne">
                <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse"
                    data-bs-target="#flush-{{ $index++ }}" aria-expanded="true" aria-controls="flush-collapseOne">
                    {{ $bidangProker->nama }}
                </button>
            </h2>
            <div id="flush-{{ $index - 1 }}" class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
                data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
                    <table class="table table-striped table-bordered table-responsive nowrap w-100 mb-5">
                        <thead>
                            <tr>
                                <th rowspan="2">Nama proker</th>
                                <th>Tempat</th>
                                <th>Sasaran</th>
                                <th>Total JKEM</th>
                                <th>Tanggal rencana</th>
                                <th>Aksi</th>
                            </tr>
                            <tr>
                                <th>

                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($bidangProker->proker->count() == 0)
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada proker</td>
                                </tr>
                            @else
                                @foreach ($bidangProker->proker as $proker)
                                    <tr>
                                        <td>{{ $proker->nama }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th>Total</th>
                                    <th>{{ $bidangProker->total_jkem_bidang }}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>

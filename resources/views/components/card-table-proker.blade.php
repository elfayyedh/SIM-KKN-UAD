@foreach ($proker as $bidangData)
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ $bidangData->nama }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach ($bidangData->proker as $item)
                    <table class="col-6">
                        <thead>
                            <tr>
                                <th>Program</th>
                                <th>:</th>
                                <th>{{ $item->nama }}</th>
                            </tr>
                        </thead>
                    </table>
                    h5
                    @foreach ($item->kegiatan as $kegiatan)
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
@endforeach

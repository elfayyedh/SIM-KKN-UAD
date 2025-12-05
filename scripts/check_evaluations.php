<?php
header('Content-Type: application/json');

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EvaluasiMahasiswa;
use App\Models\EvaluasiMahasiswaDetail;
use App\Models\KriteriaMonev;
use App\Models\Mahasiswa;

try {
    $evalCount = EvaluasiMahasiswa::count();
    $detailCount = EvaluasiMahasiswaDetail::count();
    $kriteriaCount = KriteriaMonev::count();

    $mahasiswa = Mahasiswa::with(['userRole.user','unit'])->take(10)->get()->map(function($m) {
        return [
            'id' => $m->id,
            'nama' => optional(optional($m->userRole)->user)->nama ?? null,
            'nim' => $m->nim ?? null,
            'unit' => optional($m->unit)->nama ?? null,
        ];
    });

    echo json_encode([
        'success' => true,
        'evaluasi_mahasiswa_count' => $evalCount,
        'evaluasi_mahasiswa_detail_count' => $detailCount,
        'kriteria_monev_count' => $kriteriaCount,
        'sample_mahasiswa' => $mahasiswa,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

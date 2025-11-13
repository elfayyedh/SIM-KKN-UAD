<?php

use App\Exports\MatrikExport;
use App\Http\Controllers\Admin\BidangProkerController;
use App\Http\Controllers\Admin\KKNController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\LogbookHarianController;
use App\Http\Controllers\ProkerController;
use App\Http\Controllers\Public\DownloaderController;
use App\Http\Controllers\Public\MahasiswaController;
use App\Http\Controllers\RoleSelectionController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\AuthenticatedUser;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\DosenRoleSwitchController;
use App\Http\Controllers\Admin\TimMonevController;
use App\Http\Controllers\MonevController;

Route::get('/', [DashboardController::class, 'index'])->middleware([Authenticate::class])->name('dashboard');
Route::get('/chart-data', [DashboardController::class, 'getChartData'])->middleware([Authenticate::class, AdminMiddleware::class])->name('chart-data');
Route::get('/get-donut-chart', [DashboardController::class, 'getDonutChart'])->middleware([Authenticate::class, AdminMiddleware::class])->name('donut-chart');
Route::get('/get-prodi-data', [DashboardController::class, 'getProdiData'])->middleware([Authenticate::class])->name('prodi-data');
Route::get('/get-unit-data', [DashboardController::class, 'getUnitData'])->middleware([Authenticate::class])->name('unit-data');
Route::get('/login', [AuthController::class, 'index'])->middleware([AuthenticatedUser::class])->name('login.index');
Route::post('/login/request', [AuthController::class, 'login'])->middleware([AuthenticatedUser::class])->name('login');
Route::get('/choose-role', [RoleSelectionController::class, 'chooseRole'])->middleware([Authenticate::class])->name('choose.role');
Route::middleware([Authenticate::class])->get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/set-role/{role_id}', [RoleSelectionController::class, 'setRole'])
       ->middleware([Authenticate::class]) 
       ->name('set.role');

Route::post('/dosen/switch-role', [DosenRoleSwitchController::class, 'switchRole'])
        ->middleware([Authenticate::class])
        ->name('dosen.role.switch');

// ! Admin
//? Manajemen KKN
Route::prefix('/kkn')->middleware([Authenticate::class, AdminMiddleware::class])->group(function () {
    Route::get('/create', [KKNController::class, 'create'])->name('kkn.create'); // Done
    Route::post('/store', [KKNController::class, 'store'])->name('kkn.store'); // Done
    Route::get('/', [KKNController::class, 'index'])->name('kkn.index'); // Done
    Route::get('/detail/{id}', [KKNController::class, 'show'])->name('kkn.show'); // Done
    Route::get('/edit/{id}', [KKNController::class, 'edit'])->name('kkn.edit'); // Done
    Route::put('/update/{id}', [KKNController::class, 'update'])->name('kkn.update'); // Done
});

Route::post('/tim-monev/store', [TimMonevController::class, 'store'])
       ->name('admin.tim-monev.store')
       ->middleware([Authenticate::class, AdminMiddleware::class]);

// Manajemen informasi
Route::prefix('/informasi')->middleware([Authenticate::class, AdminMiddleware::class])->group(function () {
    Route::get('/faq', [InformasiController::class, 'faqIndex'])->name('informasi.faq');
    Route::post('/faq/store', [InformasiController::class, 'saveInformasi'])->name('informasi.faq.store');
    Route::post('/faq/update/status', [InformasiController::class, 'setFaqStatus'])->name('informasi.faq.setFaqStatus');
    Route::delete('/faq/deleteFaq', [InformasiController::class, 'deleteInformasi'])->name('informasi.faq.deleteFaq');
    Route::get('/pengumuman', [InformasiController::class, 'pengumumanIndex'])->name('informasi.pengumuman');
});

// Informasi
Route::prefix('/pages')->middleware([Authenticate::class])->group(function () {
    Route::get('/faq', [InformasiController::class, 'faqView'])->name('informasi.faq.view');
    Route::get('/timeline', [InformasiController::class, 'timelineView'])->name('informasi.timeline.view');
    Route::get('/pengumuman', [InformasiController::class, 'pengumumanView'])->name('informasi.pengumuman.view');
});



//Mendapatkan progress entry data KKN
Route::get('/queue-progress/{jobId}', [KKNController::class, 'getProgress'])->name('kkn.progress'); // Done

//? Manajemen bidang Proker
Route::prefix('/bidang')->middleware([Authenticate::class, AdminMiddleware::class])->group(function () {
    Route::put('/update/{id}', [BidangProkerController::class, 'update'])->name('bidang.update');
    Route::post('/store', [BidangProkerController::class, 'store'])->name('bidang.store');
    Route::delete('/destroy/{id}', [BidangProkerController::class, 'destroy'])->name('bidang.destroy');
});

Route::get('/card-value', [DashboardController::class, 'getCardValue'])->middleware([Authenticate::class])->name('card.value');




//? Manajemen Pengguna
Route::middleware(Authenticate::class)->prefix('/user')->group(function () {
    Route::get('/create', [UserController::class, 'create'])->name('user.create'); // TODO
    Route::middleware([AdminMiddleware::class])->get('/create-admin', [UserController::class, 'createAdmin'])->name('user.create-admin'); // TODO
    Route::middleware([AdminMiddleware::class])->post('/store', [UserController::class, 'store'])->name('user.store'); // TODO
    Route::get('/', [UserController::class, 'index'])->name('user.index'); // TODO
    Route::get('/detail', [UserController::class, 'show'])->name('user.show'); // TODO
    Route::middleware([AdminMiddleware::class])->get('/admin', [UserController::class, 'adminShow'])->name('user.admin'); // TODO
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit'); // TODO
    Route::put('/update/{id}', [UserController::class, 'update'])->name('user.update'); // TODO
    Route::put('/update-password/{id}', [UserController::class, 'updatePassword'])->name('user.update.password'); // TODO
});
// Manajemen DPL
Route::prefix('/dpl')->middleware([Authenticate::class, AdminMiddleware::class])->group(function () {
    Route::get('/', [App\Http\Controllers\DplController::class, 'index'])->name('dpl.index');
    Route::get('/create', [App\Http\Controllers\DplController::class, 'create'])->name('dpl.create');
    Route::post('/store', [App\Http\Controllers\DplController::class, 'store'])->name('dpl.store');
    Route::get('/edit/{id}', [App\Http\Controllers\DplController::class, 'edit'])->name('dpl.edit');
    Route::put('/update/{id}', [App\Http\Controllers\DplController::class, 'update'])->name('dpl.update');
    Route::delete('/destroy/{id}', [App\Http\Controllers\DplController::class, 'destroy'])->name('dpl.destroy');
});

// Manajemen Tim Monev
Route::prefix('/tim-monev')->middleware([Authenticate::class, AdminMiddleware::class])->group(function () {
    Route::get('/', [App\Http\Controllers\TimMonevController::class, 'index'])->name('tim-monev.index');
    Route::get('/create', [App\Http\Controllers\TimMonevController::class, 'create'])->name('tim-monev.create');
    Route::post('/store', [App\Http\Controllers\TimMonevController::class, 'store'])->name('tim-monev.store');
    Route::get('/edit/{id}', [App\Http\Controllers\TimMonevController::class, 'edit'])->name('tim-monev.edit');
    Route::put('/update/{id}', [App\Http\Controllers\TimMonevController::class, 'update'])->name('tim-monev.update');
    Route::delete('/destroy/{id}', [App\Http\Controllers\TimMonevController::class, 'destroy'])->name('tim-monev.destroy');
});

// ! End Admin

// ! DPL
Route::middleware([Authenticate::class, 'role.dosen:dpl'])->prefix('dpl')->name('dpl.')->group(function () {
    Route::get('/dashboard', [UnitController::class, 'showUnits'])->name('dashboard');
    Route::get('/unit', [UnitController::class, 'showUnits'])->name('unit.index');
});

// ! TIM MONEV
Route::middleware([Authenticate::class, 'role.dosen:monev'])->prefix('monev')->name('monev.')->group(function () {
    
    Route::get('/dashboard', [MonevController::class, 'index'])->name('dashboard');
    Route::get('/evaluasi', [MonevController::class, 'index'])->name('evaluasi.index');
    Route::post('/evaluasi/set-kkn', [MonevController::class, 'setActiveKkn'])->name('evaluasi.set-kkn');
    Route::post('/evaluasi/assign-dpl', [MonevController::class, 'assignDpl'])->name('evaluasi.assign');
    Route::post('/evaluasi/remove-dpl', [MonevController::class, 'removeDpl'])->name('evaluasi.remove');
    Route::get('/evaluasi/dpl/{id_dpl}/units', [MonevController::class, 'showDplUnits'])->name('evaluasi.dpl-units');
    Route::get('/evaluasi/mahasiswa/{id_mahasiswa}/penilaian', [MonevController::class, 'showPenilaianPage'])
         ->name('evaluasi.penilaian');
    Route::post('/evaluasi/mahasiswa/{id_mahasiswa}/penilaian/store', [MonevController::class, 'storePenilaian'])
         ->name('evaluasi.penilaian.store');
    Route::get('/evaluasi/unit/{id_unit}/mahasiswa', [MonevController::class, 'showMahasiswaPage'])->name('evaluasi.daftar-mahasiswa');
});

//! Unit
Route::middleware([Authenticate::class])->prefix('/unit')->group(function () {
    Route::get('/edit/{id}', [UnitController::class, 'edit'])->name('unit.edit');
    Route::get('/get-table', [UnitController::class, 'getUnitTable'])->name('unit.getTable');
    Route::get('/', [UnitController::class, 'showUnits'])->name('unit.index');
    Route::get('/detail/{id?}', [UnitController::class, 'show'])->name('unit.show');
    Route::get('/getProker/{id}/{id_kkn}', [UnitController::class, 'getProkerUnit'])->name('unit.getProker');
    Route::get('/getMatriks/{id}/{id_kkn}', [UnitController::class, 'getMatriks'])->name('unit.getMatriks');
    Route::get('/getAnggota/{id}', [UnitController::class, 'getAnggota'])->name('unit.getAnggota');
    Route::get('/getKegiatanByUnit/{id}', [UnitController::class, 'getKegiatanByUnit'])->name('unit.getKegiatanByUnit');
    Route::get('/getKegiatanInfo/{id}', [UnitController::class, 'getKegiatanInfo'])->name('unit.getKegiatanInfo');
    Route::get('/getRekapKegiatan', [UnitController::class, 'getRekapKegiatan'])->name('unit.getRekapKegiatan');
    Route::get('/generateProkerUnitPdf/{id_unit}/{id_kkn}', [UnitController::class, 'generateProkerUnitPdf'])->name('unit.generateProkerUnitPdf');
    Route::put('/updateJabatanAnggota', [UnitController::class, 'updateJabatanAnggota'])->name('unit.updateJabatanAnggota');
    Route::put('/updateProfilUnit', [UnitController::class, 'updateProfilUnit'])->name('unit.updateProfilUnit');
    Route::get('/export-matriks/{id_unit}/{id_kkn}', function ($idUnit, $idKkn) {
        return Excel::download(new MatrikExport($idUnit, $idKkn), 'matriks kegiatan.xlsx');
    })->name('unit.export-matriks');
});

//! End Unit

// ! Manajemen Logbook
Route::middleware([Authenticate::class])->prefix('/logbook')->group(function () {
    Route::get('/', [LogbookHarianController::class, 'index'])->name('logbook.index');
    Route::get('/check', [LogbookHarianController::class, 'checkLogbookKegiatan'])->name('logbook.checkLogbookKegiatan');
    Route::get('/getLogbookKegiatan', [LogbookHarianController::class, 'getLogbookKegiatan'])->name('logbook.getLogbookKegiatan');
    Route::get('/getKegiatan', [LogbookHarianController::class, 'getKegiatan'])->name('logbook.getKegiatan');
    Route::post('/saveKegiatan', [LogbookHarianController::class, 'saveKegiatan'])->name('logbook.saveKegiatan');
    Route::delete('/deleteKegiatan', [LogbookHarianController::class, 'deleteKegiatan'])->name('logbook.deleteKegiatan');
    Route::get('/getPendanaan', [LogbookHarianController::class, 'getPendanaan'])->name('logbook.getPendanaan');
    Route::post('/savePendanaan', [LogbookHarianController::class, 'savePendanaan'])->name('logbook.savePendanaan');
    Route::get('/kegiatan/add/{id_mahasiswa}/{tanggal}', [LogbookHarianController::class, 'addLogbookKegiatan'])->name('logbook.kegiatan.add');
    Route::get('/sholat', [LogbookHarianController::class, 'sholat'])->name('logbook.sholat');
    Route::get('/sholat/check', [LogbookHarianController::class, 'checkLogbookSholat'])->name('logbook.sholat.check');
    Route::get('/sholat/getPDF/{id}/{tanggal_penerjunan}/{tanggal_penarikan}', [LogbookHarianController::class, 'getPDF'])->name('logbook.sholat.getPDF');
    Route::get('/sholat/add/{id_mahasiswa}/{tanggal}', [LogbookHarianController::class, 'addLogbookSholat'])->name('logbook.sholat.add');
    Route::get('/sholat/getLogbookByDate/{tanggal}', [LogbookHarianController::class, 'getLogbookByDate'])->name('logbook.sholat.getLogbookByDate');
    Route::post('/sholat/saveSholat/', [LogbookHarianController::class, 'saveSholat'])->name('logbook.sholat.saveSholat');
    Route::post('/sholat/halanganFullDay/', [LogbookHarianController::class, 'halanganFullDay'])->name('logbook.sholat.halanganFullDay');
});
// ! End Manajemen Logbook

//! Proker
Route::middleware([Authenticate::class])->prefix('/proker')->group(function () {
    Route::get('/unit', [ProkerController::class, 'indexProkerUnit'])->name('proker.unit');
    Route::get('/individu', [ProkerController::class, 'indexProkerIndividu'])->name('proker.individu');
    Route::get('/getProkerUnit', [ProkerController::class, 'getProkerUnit'])->name('get.proker.unit');
    Route::get('/getProkerIndividu', [ProkerController::class, 'getProkerIndividu'])->name('get.proker.individu');
    Route::get('/unit/create', [ProkerController::class, 'createUnit'])->name('proker.create.unit');
    Route::get('/individu/create', [ProkerController::class, 'createProkerIndividu'])->name('proker.create.individu');
    Route::post('/unit/store', [ProkerController::class, 'store'])->name('proker.store');
    Route::post('/individu/store', [ProkerController::class, 'storeProkerIndividu'])->name('proker.individu.store');
    Route::get('/unit/detail/{id}/{id_mahasiswa?}', [ProkerController::class, 'showProkerUnit'])->name('proker.unit.show');
    Route::get('/individu/detail/{id}/{id_mahasiswa}', [ProkerController::class, 'showProkerIndividu'])->name('proker.individu.show');
    Route::delete('/delete/{id?}', [ProkerController::class, 'destroy'])->name('proker.delete');
    Route::delete('/destroy/kegiatan', [ProkerController::class, 'destroyKegiatan'])->name('proker.delete.kegiatan');
    Route::get('/edit/{id?}', [ProkerController::class, 'editProkerUnit'])->name('proker.unit.edit');
    Route::put('/kegiatan/edit', [ProkerController::class, 'editKegiatan'])->name('kegiatan.edit');
    Route::post('/kegiatan/store', [ProkerController::class, 'storeKegiatan'])->name('kegiatan.add');
    Route::get('/individu/edit/{id}', [ProkerController::class, 'editProkerIndividu'])->name('proker.individu.edit');
    Route::put('/unit/update/', [ProkerController::class, 'updateProker'])->name('proker.update');
    Route::put('/individu/update/{id?}', [ProkerController::class, 'updateProkerIndividu'])->name('proker.individu.update');
    Route::put('/editOrganizer', [ProkerController::class, 'editOrganizer'])->name('proker.organizer.update');
    Route::get('/checkProkerStatus', [ProkerController::class, 'checkProkerStatus'])->name('proker.checkProkerStatus');
    Route::get('/exportProker/{id}', [ProkerController::class, 'exportProker'])->name('proker.exportProker');
    Route::get('/exportProkerPDF/{id}', [ProkerController::class, 'exportProkerPDF'])->name('proker.exportProkerPDF');
    Route::get('/checkStatusKegiatan', [ProkerController::class, 'checkStatusKegiatan'])->name('proker.checkStatusKegiatan');
    Route::get('/unit/getDataProkerByIdBidang/{id}/{id_bidang}', [ProkerController::class, 'getDataProkerByIdBidang'])->name('proker.getDataProkerByIdBidang');
});
//! End Proker

Route::middleware([Authenticate::class])->get('/kalender', [UnitController::class, 'kalender'])->name('kalender');

//! Mahasiswa
Route::middleware([Authenticate::class])->get('/mahasiswa/detail/{id}', [MahasiswaController::class, 'show'])->name('mahasiswa.show');
Route::middleware([Authenticate::class])->get('/mahasiswa/proker/{id}/{id_kkn}/{id_unit}', [MahasiswaController::class, 'getProkerMahasiswa'])->name('mahasiswa.getProkerMahasiswa');

//! End Mahasiswa

//! Comment
Route::middleware([Authenticate::class])->prefix('/comment')->group(function () {
    Route::post('/store', [CommentController::class, 'store'])->name('comment.store');
    Route::get('/get/{id_bidang_proker}', [CommentController::class, 'getComments'])->name('comment.get');
    Route::put('/update/{id}', [CommentController::class, 'update'])->name('comment.update');
    Route::delete('/delete/{id}', [CommentController::class, 'destroy'])->name('comment.delete');
});
//! End Comment

Route::middleware([Authenticate::class])->post('/upload-image', [DownloaderController::class, 'upload'])->name('upload.image');
//! Public
//Download file
Route::middleware([Authenticate::class])->get('/download/{filename}', [DownloaderController::class, 'download'])->name('file.download');

//! End Public

//Not found
Route::fallback(function () {
    return view('not-found');
});
Route::get('/not-found', function () {
    return view('not-found');
})->name('not-found');
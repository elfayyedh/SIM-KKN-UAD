@extends('layouts.index')
@section('title', 'Tambah Data Dosen')
@section('styles')
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Tambah Data Dosen</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Pengguna</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Daftar Dosen</a></li>
                                <li class="breadcrumb-item active">Tambah Data Dosen</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h5>Import Excel Data Dosen</h5>
                                <p class="card-title-desc">Upload file Excel yang berisi data dosen</p>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="card bg-info-subtle">
                                        <div class="card-body">
                                            <h6>Silahkan upload file excel data dosen sesuai format. Untuk
                                                melihat format file excel data dosen <a
                                                     href="{{ asset('example_dosen.xlsx') }}" download>klik
                                                    disini</a></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="file_excel" class="form-label">Import file (.xlsx) <span
                                                class="text-danger" id="text-file">*</span></label>
                                        <input type="file" class="form-control" id="file_excel" accept=".xlsx"
                                            placeholder="Masukkan file">
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="preview-section" style="display: none;">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Preview Data</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Nama</th>
                                                            <th>NIDN</th>
                                                            <th>Email</th>
                                                            <th>Nomor HP</th>
                                                            <th>Jenis Kelamin</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="preview-tbody">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" id="submit-btn" disabled>
                                        <i class="bx bx-save me-1"></i>Simpan Data
                                    </button>
                                    <a href="{{ route('dosen.index') }}" class="btn btn-secondary">
                                        <i class="bx bx-x me-1"></i>Batal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pageScript')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        console.log('=== SCRIPT DOSEN DIMUAT ===');
        let excelData = [];

        // Check if library loaded
        $(document).ready(function() {
            console.log('=== DOCUMENT READY ===');
            if (typeof XLSX === 'undefined') {
                console.error('❌ Library XLSX tidak dimuat!');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Library Excel tidak dapat dimuat. Refresh halaman atau cek koneksi internet.'
                });
            } else {
                console.log('✅ Library XLSX berhasil dimuat:', XLSX.version);
            }
            
            console.log('Tombol submit:', $('#submit-btn').length);
            console.log('File input:', $('#file_excel').length);
        });

        $('#file_excel').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) {
                console.log('Tidak ada file dipilih');
                return;
            }

            console.log('File dipilih:', file.name);

            if (typeof XLSX === 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Library Excel belum dimuat. Silakan refresh halaman.'
                });
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                    const rows = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
                    
                    console.log('Rows dari Excel:', rows);
                    processExcelData(rows);
                } catch (error) {
                    console.error('Error reading file:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal membaca file Excel: ' + error.message
                    });
                }
            };
            reader.readAsArrayBuffer(file);
        });

        function processExcelData(rows) {
            if (!rows || rows.length < 2) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'File Excel kosong atau hanya berisi header.'
                });
                return;
            }

            // Assume first row is header
            const headers = rows[0];
            const dataRows = rows.slice(1);

            excelData = [];
            let previewHtml = '';
            let errorCount = 0;

            dataRows.forEach((row, index) => {
                // Skip empty rows
                if (!row[0] && !row[1] && !row[2]) {
                    return;
                }

                const dosenData = {
                    nama: row[0] ? String(row[0]).trim() : '',
                    nidn: row[1] ? String(row[1]).trim() : '',
                    email: row[2] ? String(row[2]).trim() : '',
                    nomorHP: row[3] ? String(row[3]).trim() : '',
                    jenisKelamin: row[4] ? String(row[4]).trim().toUpperCase() : 'L'
                };

                // Validate required fields
                if (!dosenData.nama || !dosenData.nidn || !dosenData.email) {
                    errorCount++;
                    let missingFields = [];
                    if (!dosenData.nama) missingFields.push('Nama');
                    if (!dosenData.nidn) missingFields.push('NIP');
                    if (!dosenData.email) missingFields.push('Email');
                    console.warn(`Baris ${index + 2}: ${missingFields.join(', ')} kosong`, dosenData);
                    return;
                }

                excelData.push(dosenData);

                previewHtml += `
                    <tr>
                        <td>${excelData.length}</td>
                        <td>${dosenData.nama}</td>
                        <td>${dosenData.nidn}</td>
                        <td>${dosenData.email}</td>
                        <td>${dosenData.nomorHP || '-'}</td>
                        <td>${dosenData.jenisKelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</td>
                    </tr>
                `;
            });

            console.log('Data yang akan diupload:', excelData);
            console.log(`✅ ${excelData.length} data valid, ❌ ${errorCount} data diabaikan`);

            if (excelData.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tidak Ada Data Valid',
                    html: `Tidak ada data yang dapat diproses.<br><br>
                           <strong>${errorCount} baris</strong> memiliki data tidak lengkap.<br>
                           Pastikan kolom <strong>Nama, NIP, dan Email</strong> terisi di setiap baris.<br><br>
                           <small>Buka Console (F12) untuk melihat detail baris yang bermasalah.</small>`
                });
                return;
            }

            if (errorCount > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    html: `Ditemukan <strong>${excelData.length} data valid</strong> dari ${excelData.length + errorCount} baris.<br><br>
                           <strong>${errorCount} baris diabaikan</strong> karena data tidak lengkap (Nama/NIP/Email kosong).<br><br>
                           <small>Periksa Console (F12) untuk detail baris yang bermasalah.</small>`,
                    showCancelButton: true,
                    confirmButtonText: 'Lanjutkan Upload',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        $('#submit-btn').prop('disabled', true);
                        $('#preview-section').hide();
                        excelData = [];
                        return;
                    }
                });
            }

            $('#preview-tbody').html(previewHtml);
            $('#preview-section').show();
            
            // Enable button
            const submitBtn = $('#submit-btn');
            submitBtn.prop('disabled', false);
            submitBtn.removeClass('btn-secondary').addClass('btn-primary');
            console.log('Tombol Simpan Data aktif:', !submitBtn.prop('disabled'));
            console.log('Total data siap upload:', excelData.length);

            let message = `Ditemukan ${excelData.length} data dosen`;
            if (errorCount > 0) {
                message += ` (${errorCount} baris diabaikan karena data tidak lengkap)`;
            }

            Swal.fire({
                icon: 'success',
                title: 'File berhasil dibaca',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        }

        $('#submit-btn').on('click', function() {
            if (excelData.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Tidak ada data yang akan diupload'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Anda akan mengupload ${excelData.length} data dosen. Lanjutkan?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Upload',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    uploadData();
                }
            });
        });

        function uploadData() {
            const button = $('#submit-btn');
            button.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Uploading...');

            $.ajax({
                url: '{{ route('dosen.store') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    file_excel: excelData
                },
                success: function(response) {
                    if (response.id_progress) {
                        showProgressModal(response.id_progress);
                    }
                },
                error: function(xhr) {
                    button.prop('disabled', false).html(originalText);
                    console.error('Upload error:', xhr);
                    console.error('Response:', xhr.responseJSON);
                    
                    let errorMsg = 'Terjadi kesalahan saat upload data';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        } else if (xhr.responseJSON.errors) {
                            // Validation errors
                            let errors = xhr.responseJSON.errors;
                            errorMsg = 'Validasi gagal:\n';
                            for (let field in errors) {
                                errorMsg += '- ' + errors[field].join(', ') + '\n';
                            }
                        } else if (xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Upload',
                        text: errorMsg,
                        footer: '<small>Periksa Console (F12) untuk detail error</small>'
                    });
                }
            });
        }

        function showProgressModal(progressId) {
            Swal.fire({
                title: 'Progress input data',
                html: '<div id="progress-text">Data inserted: 0/0 (0%)</div>',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Kembali',
                allowOutsideClick: false,
                didOpen: () => {
                    checkProgress(progressId);
                }
            });
        }

        function checkProgress(progressId) {
            const interval = setInterval(() => {
                $.ajax({
                    url: `/progress/${progressId}`,
                    method: 'GET',
                    success: function(response) {
                        const percentage = Math.round(response.progress || 0);
                        $('#progress-text').html(`Data inserted: ${response.step}/${response.total} (${percentage}%)`);

                        if (response.status === 'completed') {
                            clearInterval(interval);
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Data dosen berhasil diupload',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location.href = '{{ route('dosen.index') }}';
                            });
                        } else if (response.status === 'failed') {
                            clearInterval(interval);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Upload gagal'
                            });
                            $('#submit-btn').prop('disabled', false).html(
                                '<i class="bx bx-save me-1"></i>Simpan Data');
                        }
                    },
                    error: function() {
                        clearInterval(interval);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memeriksa progress'
                        });
                        $('#submit-btn').prop('disabled', false).html(
                            '<i class="bx bx-save me-1"></i>Simpan Data');
                    }
                });
            }, 1000);
        }
    </script>
@endsection

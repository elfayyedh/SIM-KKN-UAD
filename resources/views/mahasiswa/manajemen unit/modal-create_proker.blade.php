  {{-- Modal addProkerModal xl --}}
  <div id="addProkerModal" class="modal modal-xl fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header border-bottom">
                  <h5 class="modal-title" id="staticBackdropLabel">Tambah Proker <a
                          href="{{ asset('assets/video/0715.mp4') }}" class="text-secondary image-popup-video-map"
                          data-title="Video Tutorial" data-description="Tutorial menambahkan proker"><i
                              class="mdi mdi-video"></i></a></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-12">
                          <div class="card border-0">
                              <div class="card-body">
                                  <div id="basic-pills-wizard" class="twitter-bs-wizard">
                                      <ul class="twitter-bs-wizard-nav">
                                          <li class="nav-item">
                                              <a href="#proker" class="nav-link" data-toggle="tab">
                                                  <div class="step-icon" data-bs-toggle="tooltip"
                                                      data-bs-placement="top" title="Program Kerja">
                                                      <i class="mdi mdi-folder-outline"></i>
                                                  </div>
                                              </a>
                                          </li>
                                          <li class="nav-item">
                                              <a href="#kegiatan" class="nav-link" data-toggle="tab">
                                                  <div class="step-icon" data-bs-toggle="tooltip"
                                                      data-bs-placement="top" title="Kegiatan">
                                                      <i class="mdi mdi-view-list-outline"></i>
                                                  </div>
                                              </a>
                                          </li>

                                          <li class="nav-item">
                                              <a href="#peran" class="nav-link" data-toggle="tab">
                                                  <div class="step-icon" data-bs-toggle="tooltip"
                                                      data-bs-placement="top" title="Peran mahasiswa">
                                                      <i class="mdi mdi-account-multiple-check-outline"></i>
                                                  </div>
                                              </a>
                                          </li>
                                          <li class="nav-item">
                                              <a href="#review" class="nav-link" data-toggle="tab">
                                                  <div class="step-icon" data-bs-toggle="tooltip"
                                                      data-bs-placement="top" title="review">
                                                      <i class="mdi mdi-clipboard-list-outline"></i>
                                                  </div>
                                              </a>
                                          </li>
                                      </ul>
                                      <!-- wizard-nav -->

                                      <input type="hidden" id="tanggal_penerjunan-unit"
                                          value="{{ $unit->tanggal_penerjunan }}">

                                      <div class="tab-content twitter-bs-wizard-tab-content">
                                          <div class="tab-pane" id="proker">
                                              <div class="text-center mb-4">
                                                  <h5>Pilih program atau tambahkan program baru</h5>
                                              </div>
                                              <div class="row">
                                                  <div class="col-lg-6">
                                                      <div class="mb-3">
                                                          <label for="bidang_program" class="form-label">Bidang
                                                              Proker<span class="text-danger">*</span></label>
                                                          <select id="bidang_program" class="form-select w-100">
                                                              @foreach ($bidang_proker as $bidang)
                                                                  <option value="{{ $bidang->id }}">
                                                                      {{ $bidang->nama }}</option>
                                                              @endforeach
                                                          </select>
                                                      </div>
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <div class="mb-3">
                                                          <label for="program" class="form-label">Program<span
                                                                  class="text-danger">*</span></label>
                                                          {{-- Select2 --}}
                                                          <select id="program" class="selectize w-100">
                                                              @foreach ($prokerData as $proker)
                                                                  <option value="{{ $proker->id }}">
                                                                      {{ $proker->nama }}</option>
                                                              @endforeach
                                                          </select>
                                                      </div>
                                                  </div>
                                              </div>
                                              <div class="row">

                                                  <div class="col-lg-6">
                                                      <div class="mb-3">
                                                          <label for="program" class="form-label">Tempat<span
                                                                  class="text-danger">*</span></label>
                                                          <input type="text" id="tempat" required
                                                              class="form-control"
                                                              placeholder="contoh: Rumah pak dukuh">
                                                      </div>
                                                  </div>
                                                  <div class="col-lg-6">
                                                      <div class="mb-3">
                                                          <label for="bidang_program" class="form-label">Sasaran<span
                                                                  class="text-danger">*</span></label>
                                                          <input type="text" class="form-control" name="sasaran"
                                                              id="sasaran" placeholder="contoh: Ibu rumah tangga ">
                                                      </div>
                                                  </div>
                                              </div>
                                              <ul class="pager wizard twitter-bs-wizard-pager-link" id="proker-pager">
                                                  <li class="next"><a href="javascript: void(0);"
                                                          class="btn btn-primary">Berikutnya
                                                          <i class="bx bx-chevron-right ms-1"></i></a></li>
                                              </ul>
                                          </div>
                                          <!-- tab pane -->
                                          <div class="tab-pane" id="kegiatan">
                                              <div>
                                                  <div class="text-center mb-4">
                                                      <h5>Tambah kegiatan</h5>
                                                  </div>
                                                  <div id="listKegiatan">
                                                      <div class="row border kegiatan-row pt-3">
                                                          <div class="col-lg-4">
                                                              <div class="mb-3">
                                                                  <label for="kegiatan" class="form-label">Kegiatan
                                                                      ke-1
                                                                      <span class="text-danger">*</span></label>
                                                                  <input type="text" required class="form-control"
                                                                      name="kegiatan" id="kegiatan"
                                                                      placeholder="Nama kegiatan">
                                                              </div>
                                                          </div>
                                                          <div class="col-lg-3">
                                                              <div class="mb-3">
                                                                  <label for="frekuensi" class="form-label">Frekuensi
                                                                      <span class="text-danger">*</span></label>
                                                                  <input type="number" min="1"
                                                                      class="form-control frekuensi" name="frekuensi"
                                                                      required id="frekuensi">
                                                              </div>
                                                          </div>
                                                          <div class="col-lg-3">
                                                              <div class="mb-3">
                                                                  <label for="jkem" class="form-label">JKEM
                                                                      <span class="text-danger">*</span></label>
                                                                  <select name="jkem" required id="jkem"
                                                                      class="form-select jkem">
                                                                      <option value="50">50</option>
                                                                      <option value="100">100</option>
                                                                      <option value="150">150</option>
                                                                      <option value="200">200</option>
                                                                      <option value="250">250</option>
                                                                  </select>
                                                              </div>
                                                          </div>
                                                          <div class="col-lg-2">
                                                              <div class="mb-3">
                                                                  <label for="totalJKEM" class="form-label">Total
                                                                      JKEM</label>
                                                                  <input type="text" min="1" readonly
                                                                      class="form-control totalJKEM" name="totalJKEM"
                                                                      id="totalJKEM">
                                                              </div>
                                                          </div>
                                                          <div class="col-12">
                                                              <div class="mb-3">
                                                                  <label for="tanggal_kegiatan"
                                                                      class="form-label">Tanggal Kegiatan <span
                                                                          class="text-danger">*</span> <span
                                                                          class="text-muted small">(Pilih tanggal
                                                                          tanggal sesuai jumlah
                                                                          frekuensi)</span></label>
                                                                  <input type="text" required data-flatpickr
                                                                      class="form-control tanggal_kegiatan"
                                                                      name="tanggal_kegiatan" id="tanggal_kegiatan">
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>

                                                  <div class="row mt-3 mb-3">
                                                      <div class="col">
                                                          <button class="btn btn-soft-primary" id="addKegiatan"><i
                                                                  class="bx bx-plus me-1"></i>Tambah
                                                              Kegiatan</button>
                                                      </div>
                                                  </div>

                                                  <ul class="pager wizard twitter-bs-wizard-pager-link">
                                                      <li class="previous"><a href="javascript: void(0);"
                                                              class="btn btn-primary"><i
                                                                  class="bx bx-chevron-left me-1"></i>
                                                              Sebelumnya</a></li>
                                                      <li class="next" id="kegiatanNextButton"><a
                                                              href="javascript: void(0);"
                                                              class="btn btn-primary">Berikutnya <i
                                                                  class="bx bx-chevron-right ms-1"></i></a></li>
                                                  </ul>
                                              </div>
                                          </div>
                                          <!-- tab pane -->
                                          <div class="tab-pane" id="peran">
                                              <div>
                                                  <div class="text-center mb-4">
                                                      <h5>Peran Mahasiswa</h5>
                                                  </div>
                                                  <div class="peran_Mahasiswa">
                                                      <div class="row">
                                                          @foreach ($mahasiswa as $item)
                                                              <div class="col-lg-4">
                                                                  <div class="mb-3">
                                                                      <input type="hidden" class="anggota_peran"
                                                                          value="{{ $item->userRole->user->nama }}">
                                                                      <label for="peran_{{ $item->id }}"
                                                                          class="form-label">
                                                                          Peran <span
                                                                              class="text-muted">{{ $item->userRole->user->nama }}</span>
                                                                      </label>
                                                                      <select id="peran_{{ $item->id }}"
                                                                          class="selectPeran w-100">
                                                                          <!-- Options will be populated by selectize -->
                                                                      </select>
                                                                  </div>
                                                              </div>
                                                              @if (($loop->index + 1) % 3 == 0 && !$loop->last)
                                                      </div>
                                                      <div class="row">
                                                          @endif
                                                          @endforeach
                                                      </div>
                                                  </div>

                                                  <ul class="pager wizard twitter-bs-wizard-pager-link">
                                                      <li class="previous"><a href="javascript: void(0);"
                                                              class="btn btn-primary"><i
                                                                  class="bx bx-chevron-left me-1"></i>
                                                              Sebelumnya</a></li>
                                                      <li class="next"><a href="javascript: void(0);"
                                                              class="btn btn-primary" id="peranNextButton">Berikutnya
                                                              <i class="bx bx-chevron-right ms-1"></i></a></li>
                                                  </ul>
                                              </div>
                                          </div>
                                          <!-- tab pane -->
                                          <div class="tab-pane" id="review">
                                              <div>
                                                  <div class="text-center mb-4">
                                                      <h5>Review</h5>
                                                  </div>
                                                  <div id="reviewsContainer">
                                                      <div class="row">
                                                          <div class="col-12">
                                                              <table class="table-detail-info">
                                                                  <tbody>
                                                                      <tr>
                                                                          <td class="p-1">Bidang</td>
                                                                          <td class="p-1">:</td>
                                                                          <td class="p-1" id="data-review_bidang">
                                                                          </td>
                                                                      </tr>
                                                                      <tr>
                                                                          <td class="p-1">Program</td>
                                                                          <td class="p-1">:</td>
                                                                          <td class="p-1" id="data-review_program">
                                                                          </td>
                                                                      </tr>
                                                                      <tr>
                                                                          <td class="p-1">Total JKEM</td>
                                                                          <td class="p-1">:</td>
                                                                          <td class="p-1"
                                                                              id="data-review_totalJKEM"></td>
                                                                      </tr>
                                                                      <tr>
                                                                          <td class="p-1">Tempat</td>
                                                                          <td class="p-1">:</td>
                                                                          <td class="p-1" id="data-review_tempat">
                                                                          </td>
                                                                      </tr>
                                                                      <tr>
                                                                          <td class="p-1">Sasaran</td>
                                                                          <td class="p-1">:</td>
                                                                          <td class="p-1" id="data-review_sasaran">
                                                                          </td>
                                                                      </tr>
                                                                  </tbody>
                                                              </table>

                                                              <h6 class="_info mt-4 fw-bold mb-3 text-primary">
                                                                  # Daftar
                                                                  Kegiatan</h6>
                                                              <div class="review_daftar-kegiatain w-100"
                                                                  style="overflow-x: auto;">
                                                                  <table
                                                                      class="table table-bordered table-responsive text-nowrap nowrap w-100 mt-1">
                                                                      <thead>
                                                                          <tr>
                                                                              <th class="p-1 text-center align-middle"
                                                                                  rowspan="2">
                                                                                  Kegiatan</th>
                                                                              <th class="p-1 text-center align-middle"
                                                                                  colspan="3">Ekuivalensi JKEM
                                                                                  (menit)
                                                                              </th>
                                                                              <th rowspan="2"
                                                                                  class="p-1 text-center align-middle">
                                                                                  Tanggal Rencana</th>
                                                                              <th rowspan="2"
                                                                                  class="p-1 text-center align-middle">
                                                                                  Peran mahasiswa</th>

                                                                          </tr>
                                                                          <tr>
                                                                              <th class="p-1 text-center align-middle">
                                                                                  Frekuensi
                                                                              </th>
                                                                              <th class="p-1 text-center align-middle">
                                                                                  JKEM</th>
                                                                              <th class="p-1 text-center align-middle">
                                                                                  Total JKEM
                                                                              </th>
                                                                          </tr>
                                                                      </thead>
                                                                      <tbody></tbody>
                                                                  </table>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                              <ul class="pager wizard twitter-bs-wizard-pager-link">
                                                  <li class="previous"><a href="javascript: void(0);"
                                                          class="btn btn-primary"><i
                                                              class="bx bx-chevron-left me-1"></i>
                                                          Sebelumnya</a></li>
                                                  <li class="float-end btnConfirm"><a href="javascript: void(0);"
                                                          class="btn btn-primary" id="save-change"
                                                          data-bs-toggle="modal" data-bs-target=".confirmModal">Simpan
                                                          Data</a></li>
                                              </ul>
                                          </div>
                                      </div>
                                      <!-- tab pane -->
                                  </div>
                                  <!-- end tab content -->
                              </div>
                          </div>
                          <!-- end card body -->
                      </div>
                      <!-- end card -->
                  </div>
                  <!-- end col -->
              </div>
          </div>
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->

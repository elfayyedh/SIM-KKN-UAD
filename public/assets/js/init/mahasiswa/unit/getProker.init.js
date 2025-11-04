$(document).ready(async function () {
    let id_unit = $("#id_unit").val();
    let id_kkn = $("#id_kkn").val();
    const table_container = $("#program_kerja_unit");
    table_container.html("Loading proker...");

    try {
        const response = await fetch(`/unit/getProker/${id_unit}/${id_kkn}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        let prokerHtml = "";

        data.forEach((bidangData, index) => {
            prokerHtml += `
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">${bidangData.nama}</h5>
                    </div>
                    <div class="card-body" style="overflow-x: auto;">
                        <table class="table-proker">
                            <thead class="align-middle">
                                <tr>
                                    <th rowspan="2" class="p-1 text-center text-nowrap">No</th>
                                    <th rowspan="2" class="p-1 text-center text-nowrap">Program & Kegiatan</th>
                                    <th colspan="3" class="p-1 text-center text-nowrap">Ekuivalensi JKEM (Menit)</th>
                                    <th rowspan="2" class="p-1 text-center text-nowrap">Tanggal Rencana</th>
                                    <th rowspan="2" class="p-1 text-center text-nowrap">Tanggal Realisasi</th>
                                    ${
                                        bidangData.tipe === "unit"
                                            ? '<th rowspan="2" class="p-1 text-center text-nowrap">Peran Mahasiswa</th>'
                                            : '<th rowspan="2" class="p-1 text-center text-nowrap">Penanggung jawab</th>'
                                    }
                                </tr>
                                <tr>
                                    <th class="p-1 text-center text-nowrap">Frekuensi</th>
                                    <th class="p-1 text-center text-nowrap">JKEM</th>
                                    <th class="p-1 text-center text-nowrap">Total JKEM</th>
                                </tr>
                            </thead>
                            <tbody>`;

            bidangData.proker.forEach((program, programIndex) => {
                prokerHtml += `
                    <tr class="program-row bg-light">
                        <td class="p-1 text-center fw-bold text-nowrap">${
                            programIndex + 1
                        }</td>
                        <td colspan="${
                            bidangData.tipe === "unit" ? 6 : 7
                        }" class="p-1 fw-bold text-nowrap">${program.nama}</td>
                        ${
                            bidangData.tipe === "unit"
                                ? `<td rowspan="${
                                      program.kegiatan.length + 1
                                  }" class="p-1 align-top">
                                      <ul>
                                      ${program.organizer
                                          .map((organizer) => {
                                              return `<li><span class="fw-bold text-nowrap">${
                                                  organizer.nama
                                              }: </span> ${
                                                  organizer.peran == null
                                                      ? "-"
                                                      : organizer.peran
                                              }</li>`;
                                          })
                                          .join("")}
                                      </ul>
                                  </td>`
                                : ""
                        }
                    </tr>`;

                program.kegiatan.forEach((kegiatan, index) => {
                    prokerHtml += `
                        <tr>
                        ${
                            index === 0
                                ? "<td rowspan='" +
                                  program.kegiatan.length +
                                  "' class='p-1 text-center fw-bold text-nowrap vertical-text'>Kegiatan</td>"
                                : ""
                        }
                            <td class="p-1 align-top text-nowrap">${
                                kegiatan.nama
                            }</td>
                            <td class="p-1 align-top text-nowrap">${
                                kegiatan.frekuensi
                            }</td>
                            <td class="p-1 align-top text-nowrap">${
                                kegiatan.jkem
                            }</td>
                            <td class="p-1 align-top text-nowrap">${
                                kegiatan.total_jkem
                            }</td>
                            <td class="p-1 align-top text-nowrap">
                                <ul>
                                ${kegiatan.tanggal_rencana_proker
                                    .map((date) => {
                                        return `<li>${moment(
                                            date.tanggal
                                        ).format("dddd, D MMMM YYYY")}</li>`;
                                    })
                                    .join("")}
                                </ul>
                            </td>
                            <td class="p-1 align-top text-nowrap">
                                <ul>
                                ${kegiatan.logbook_kegiatan
                                    .map((logbook) => {
                                        return `<li>${moment(
                                            logbook.logbook_harian.tanggal
                                        ).format("dddd, D MMMM YYYY")}</li>`;
                                    })
                                    .join("")}
                                </ul>
                            </td>`;
                    if (bidangData.tipe === "individu") {
                        const namaMahasiswa =
                            kegiatan.mahasiswa.user_role.user.nama;
                        prokerHtml += `<td class="p-1 align-top"><span class="fw-bold text-nowrap">${namaMahasiswa}</span>`;
                    }
                    prokerHtml += `</tr>`;
                });
            });

            var total_jkem = 0;
            bidangData.proker.forEach((program) => {
                total_jkem += program.total_jkem;
            });

            prokerHtml += `</tbody></table></div><div class="card-footer">
            <h5>Total JKEM : ${total_jkem}</h5>
            `;
            if (bidangData.tipe === "unit") {
                prokerHtml += `<h5>Minimal JKEM : ${bidangData.syarat_jkem}</h5>`;
            }
            const userRole = $('meta[name="user-role"]').attr('content') || 'mahasiswa';

            if (userRole === 'DPL' || userRole === 'dpl') {
                prokerHtml += `
                <div class="mt-3">
                    <h6>Komentar DPL:</h6>
                    <div id="comments-${bidangData.id}" class="comments-section">
                        <div class="comment-form mb-3">
                            <textarea class="form-control" id="comment-text-${bidangData.id}" rows="3" placeholder="Tulis komentar..."></textarea>
                            <button class="btn btn-primary mt-2" onclick="submitComment('${bidangData.id}')">Kirim Komentar</button>
                        </div>
                        <div id="comment-list-${bidangData.id}" class="comment-list">
                            <!-- Comments will be loaded here -->
                        </div>
                    </div>
                </div>
                `;
            } else {
                prokerHtml += `
                <div class="mt-3">
                    <h6>Komentar DPL:</h6>
                    <div id="comments-${bidangData.id}" class="comments-section">
                        <div id="comment-list-${bidangData.id}" class="comment-list">
                            <!-- Comments will be loaded here -->
                        </div>
                    </div>
                </div>
                `;
            }
            prokerHtml += `</div></div>`;
        });

        table_container.html(prokerHtml);

        // Load comments for each bidang
        data.forEach((bidangData) => {
            loadComments(bidangData.id);
        });

    } catch (error) {
        console.error("Error fetching data:", error);
        table_container.html("Failed to load data.");
    }
});

// Function to submit comment
async function submitComment(bidangId) {
    const commentText = $(`#comment-text-${bidangId}`).val().trim();
    if (!commentText) {
        alert('Komentar tidak boleh kosong');
        return;
    }

    try {
        const response = await fetch('/comment/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({
                id_bidang_proker: bidangId,
                komentar: commentText
            })
        });

        const result = await response.json();

        if (result.status === 'success') {
            $(`#comment-text-${bidangId}`).val('');
            loadComments(bidangId);
        } else {
            alert(result.message || 'Gagal menambahkan komentar');
        }
    } catch (error) {
        console.error('Error submitting comment:', error);
        alert('Terjadi kesalahan saat mengirim komentar');
    }
}

// Function to load comments
async function loadComments(bidangId) {
    try {
        const response = await fetch(`/comment/get/${bidangId}`);
        const result = await response.json();

        if (result.status === 'success') {
            const commentList = $(`#comment-list-${bidangId}`);
            let commentsHtml = '';

            const userRole = $('meta[name="user-role"]').attr('content') || 'mahasiswa';

            if (result.data.length === 0) {
                if (userRole === 'DPL' || userRole === 'dpl') {
                    commentsHtml = '<p class="text-muted">Belum ada komentar.</p>';
                } else {
                    commentsHtml = '<p class="text-muted">Belum ada komentar.</p>';
                }
            } else {
                result.data.forEach(comment => {
                    const createdAt = moment(comment.created_at).format('D MMMM YYYY, HH:mm');
                    let actionButtons = '';

                    if (userRole === 'DPL' || userRole === 'dpl') {
                        actionButtons = `
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editComment('${comment.id}', '${comment.komentar.replace(/'/g, "\\'")}')">Edit</button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteComment('${comment.id}', '${bidangId}')">Hapus</button>
                            </div>
                        `;
                    }

                    commentsHtml += `
                        <div class="comment-item border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>${comment.dpl.user_role.user.nama}</strong>
                                    <small class="text-muted ms-2">${createdAt}</small>
                                </div>
                                ${actionButtons}
                            </div>
                            <p class="mt-1 mb-0">${comment.komentar}</p>
                        </div>
                    `;
                });
            }

            commentList.html(commentsHtml);
        }
    } catch (error) {
        console.error('Error loading comments:', error);
    }
}

// Function to edit comment
function editComment(commentId, currentComment) {
    Swal.fire({
        title: 'Edit Komentar',
        input: 'textarea',
        inputValue: currentComment,
        inputAttributes: {
            'aria-label': 'Type your message here'
        },
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value || value.trim() === '') {
                return 'Komentar tidak boleh kosong!';
            }
        }
    }).then((result) => {
        if (result.isConfirmed && result.value !== currentComment) {
            updateComment(commentId, result.value.trim());
        }
    });
}

// Function to update comment
async function updateComment(commentId, newComment) {
    try {
        const response = await fetch(`/comment/update/${commentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({
                komentar: newComment
            })
        });

        const result = await response.json();

        if (result.status === 'success') {
            // Reload comments for the bidang
            const bidangId = result.data.id_bidang_proker;
            loadComments(bidangId);
        } else {
            alert(result.message || 'Gagal mengupdate komentar');
        }
    } catch (error) {
        console.error('Error updating comment:', error);
        alert('Terjadi kesalahan saat mengupdate komentar');
    }
}



// Function to delete comment
async function deleteComment(commentId, bidangId) {
    Swal.fire({
        title: 'Hapus Komentar',
        text: 'Apakah Anda yakin ingin menghapus komentar ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`/comment/delete/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Komentar berhasil dihapus.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    loadComments(bidangId);
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message || 'Gagal menghapus komentar',
                        icon: 'error'
                    });
                }
            } catch (error) {
                console.error('Error deleting comment:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menghapus komentar',
                    icon: 'error'
                });
            }
        }
    });
}

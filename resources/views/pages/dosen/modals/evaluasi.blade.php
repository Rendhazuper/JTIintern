<div class="modal fade" id="evaluasiModal" tabindex="-1" aria-labelledby="evaluasiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="evaluasiModalLabel"
                    style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Evaluasi Mahasiswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="evaluasiForm">
                    <div class="mb-3">
                        <label class="form-label" style="font-family: 'Open Sans', sans-serif; font-weight: 600;">
                            Nilai Akhir
                        </label>
                        <input type="number" class="form-control" id="nilai_akhir" name="nilai_akhir" min="0"
                            max="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-family: 'Open Sans', sans-serif; font-weight: 600;">
                            Catatan Evaluasi
                        </label>
                        <textarea class="form-control" id="catatan_evaluasi" name="catatan_evaluasi" rows="4" required></textarea>
                    </div>
                    <input type="hidden" id="id_mahasiswa" name="id_mahasiswa">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitEvaluasi()">
                    Simpan Evaluasi
                </button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        function submitEvaluasi() {
            const form = document.getElementById('evaluasiForm');
            const formData = new FormData(form);

            Swal.fire({
                title: 'Loading...',
                text: 'Menyimpan evaluasi',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            api.post(`/mahasiswa/${formData.get('id_mahasiswa')}/evaluasi`, {
                    nilai_akhir: formData.get('nilai_akhir'),
                    catatan_evaluasi: formData.get('catatan_evaluasi')
                })
                .then(function(response) {
                    Swal.close();
                    if (response.data.success) {
                        Swal.fire('Berhasil', 'Evaluasi berhasil disimpan', 'success')
                            .then(() => {
                                $('#evaluasiModal').modal('hide');
                                loadMahasiswaData(filterState); // Reload data
                            });
                    } else {
                        Swal.fire('Gagal', 'Gagal menyimpan evaluasi', 'error');
                    }
                })
                .catch(function(error) {
                    Swal.close();
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan evaluasi', 'error');
                });
        }

        // Function to populate evaluasi form
        function generateEvaluasiHTML(data) {
            document.getElementById('nilai_akhir').value = data.nilai_akhir || '';
            document.getElementById('catatan_evaluasi').value = data.catatan_evaluasi || '';
            document.getElementById('id_mahasiswa').value = data.id_mahasiswa;
        }

        // Function to generate log aktivitas HTML
        function generateLogAktivitasHTML(logs) {
            if (!logs || logs.length === 0) {
                return `
                <div class="text-center py-4">
                    <i class="fas fa-clipboard-list text-muted" style="font-size: 48px;"></i>
                    <p class="mt-3 text-muted">Belum ada log aktivitas</p>
                </div>
            `;
            }

            return logs.map(log => `
            <div class="activity-item mb-3 p-3 border rounded">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0" style="font-family: 'Open Sans', sans-serif; font-weight: 600;">
                        ${log.judul}
                    </h6>
                    <span class="text-muted" style="font-size: 12px;">
                        ${new Date(log.tanggal).toLocaleDateString('id-ID')}
                    </span>
                </div>
                <p class="mb-0" style="font-family: 'Open Sans', sans-serif;">
                    ${log.deskripsi}
                </p>
            </div>
        `).join('');
        }
    </script>
@endpush

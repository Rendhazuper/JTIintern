{{-- filepath: c:\laragon\www\JTIintern\resources\views\pages\dosen\modals\evaluasi.blade.php --}}

<div class="modal fade" id="evaluasiModal" tabindex="-1" aria-labelledby="evaluasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="evaluasiModalLabel">
                    <i class="fas fa-star me-2"></i>Evaluasi Mahasiswa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body" id="evaluasiBody">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
    function submitEvaluasi() {
        const form = document.getElementById('evaluasiForm');
        const formData = new FormData(form);
        const id_mahasiswa = formData.get('id_mahasiswa');
        const magang_id = formData.get('magang_id');

        if (!id_mahasiswa || !magang_id) {
            Swal.fire('Error', 'Data tidak lengkap', 'error');
            return;
        }

        Swal.fire({
            title: 'Loading...',
            text: 'Menyimpan evaluasi',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fix: Make sure the field names match what the controller expects
        api.post(`/mahasiswa/${id_mahasiswa}/evaluasi`, {
                nilai_dosen: formData.get('nilai_dosen'), // Match the validation name
                catatan_dosen: formData.get('catatan_dosen'), // Match the validation name
                magang_id: magang_id
            })
            .then(function(response) {
                Swal.close();
                if (response.data.success) {
                    Swal.fire('Berhasil', response.data.message, 'success')
                        .then(() => {
                            bootstrap.Modal.getInstance(document.getElementById('evaluasiModal')).hide();
                            loadMahasiswaData(filterState);
                        });
                } else {
                    Swal.fire('Gagal', response.data.message || 'Gagal menyimpan evaluasi', 'error');
                }
            })
            .catch(function(error) {
                Swal.close();
                console.error('Error:', error);
                const errorMessage = error.response?.data?.message || 'Terjadi kesalahan saat menyimpan evaluasi';
                Swal.fire('Error', errorMessage, 'error');
            });
    }

    function generateEvaluasiHTML(data) {
        return `
        <form id="evaluasiForm">
            <div class="mb-3">
                <label class="form-label">Nilai Dosen</label>
                <input type="number" class="form-control" id="nilai_dosen" name="nilai_dosen" 
                       min="0" max="100" value="${data.nilai_dosen || ''}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Catatan Evaluasi</label>
                <textarea class="form-control" id="catatan_dosen" name="catatan_dosen" 
                          rows="4" required>${data.catatan_dosen || ''}</textarea>
            </div>
            <input type="hidden" id="id_mahasiswa" name="id_mahasiswa" value="${data.id_mahasiswa}">
            <input type="hidden" id="magang_id" name="magang_id" value="${data.id_magang}">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitEvaluasi()">
                    ${data.is_existing ? 'Update Evaluasi' : 'Simpan Evaluasi'}
                </button>
            </div>
        </form>
    `;
    }

    // Helper function to format date
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    // Add event listener for nilai calculation
    document.addEventListener('change', function(e) {
        if (e.target.id === 'nilai_dosen') {
            const nilaiDosen = parseFloat(e.target.value);
            const nilaiPerusahaan = parseFloat(document.querySelector('.nilai-display').textContent);

            updatePreview(nilaiDosen, nilaiPerusahaan);
            updateGradePreview(nilaiDosen);
        }
    });

    function updatePreview(nilaiDosen, nilaiPerusahaan) {
        const previewNilaiDosen = document.getElementById('preview-nilai-dosen');
        const previewNilaiAkhir = document.getElementById('preview-nilai-akhir');
        const previewGrade = document.getElementById('preview-grade');

        if (previewNilaiDosen) {
            previewNilaiDosen.textContent = isNaN(nilaiDosen) ? '-' : nilaiDosen;
        }

        if (!isNaN(nilaiDosen) && !isNaN(nilaiPerusahaan)) {
            const nilaiAkhir = ((nilaiDosen + nilaiPerusahaan) / 2).toFixed(2);
            const grade = getGradeFromScore(nilaiAkhir);

            if (previewNilaiAkhir) previewNilaiAkhir.textContent = nilaiAkhir;
            if (previewGrade) previewGrade.textContent = grade;
        } else {
            if (previewNilaiAkhir) previewNilaiAkhir.textContent = '-';
            if (previewGrade) previewGrade.textContent = '-';
        }
    }

    function updateGradePreview(nilai) {
        const gradePreview = document.getElementById('dosen-grade-preview');
        if (!gradePreview) return;

        const gradeValue = gradePreview.querySelector('.grade-value');
        const gradeDesc = gradePreview.querySelector('.grade-desc');

        if (isNaN(nilai) || nilai === '') {
            gradeValue.textContent = '-';
            gradeDesc.textContent = 'Masukkan nilai';
            gradeValue.style.color = '#495057';
            return;
        }

        const grade = getGradeFromScore(nilai);
        const description = getGradeDescription(grade);
        const color = getGradeColor(grade);

        gradeValue.textContent = grade;
        gradeDesc.textContent = description;
        gradeValue.style.color = color;
    }

    function getGradeFromScore(score) {
        if (score >= 81) return 'A';
        if (score >= 74) return 'B+';
        if (score >= 66) return 'B';
        if (score >= 61) return 'C+';
        if (score >= 51) return 'C';
        if (score >= 40) return 'D';
        return 'E';
    }

    function getGradeDescription(grade) {
        const descriptions = {
            'A': 'Sangat Baik',
            'B+': 'Lebih dari Baik',
            'B': 'Baik',
            'C+': 'Lebih dari Cukup',
            'C': 'Cukup',
            'D': 'Kurang',
            'E': 'Sangat Kurang'
        };
        return descriptions[grade] || '';
    }

    function getGradeColor(grade) {
        const colors = {
            'A': '#28a745',
            'B+': '#17a2b8',
            'B': '#007bff',
            'C+': '#ffc107',
            'C': '#fd7e14',
            'D': '#dc3545',
            'E': '#6c757d'
        };
        return colors[grade] || '#6c757d';
    }
</script>

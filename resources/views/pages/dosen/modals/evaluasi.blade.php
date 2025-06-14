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

        api.post(`/mahasiswa/${id_mahasiswa}/evaluasi`, {
                nilai_dosen: formData.get('nilai_dosen'),
                catatan_dosen: formData.get('catatan_dosen'),
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
        const mahasiswa = data.mahasiswa;
        const fileUrl = data.file_url;
        const isExisting = data.is_existing;

        return `
        <div class="row mb-4">
            <!-- Informasi Mahasiswa -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-user-graduate me-2"></i>Data Mahasiswa
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="student-avatar text-center mb-3">
                            <div class="avatar-circle">
                                ${mahasiswa.nama_mahasiswa.charAt(0).toUpperCase()}
                            </div>
                            <h6 class="mt-2 mb-0">${mahasiswa.nama_mahasiswa}</h6>
                            <small class="text-muted">${mahasiswa.nim}</small>
                        </div>
                        
                        <div class="student-details">
                            <div class="detail-item">
                                <i class="fas fa-building text-primary me-2"></i>
                                <span class="detail-label">Perusahaan:</span>
                                <span class="detail-value">${mahasiswa.nama_perusahaan || '-'}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-star text-warning me-2"></i>
                                <span class="detail-label">Nilai Perusahaan:</span>
                                <span class="detail-value">
                                    <span class="badge bg-primary fs-6">${mahasiswa.nilai_perusahaan}</span>
                                </span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-calendar text-success me-2"></i>
                                <span class="detail-label">Tanggal Submit:</span>
                                <span class="detail-value">${formatDate(mahasiswa.tanggal_submit_perusahaan)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- File Penilaian -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-file-pdf me-2"></i>Form Penilaian Perusahaan
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        ${fileUrl ? `
                            <div class="file-preview">
                                <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                                <p class="mb-3">Form penilaian dari perusahaan</p>
                                <a href="${fileUrl}" target="_blank" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-external-link-alt me-1"></i>Buka File PDF
                                </a>
                                <div class="mt-2">
                                    <small class="text-muted">Klik untuk melihat form penilaian yang diisi perusahaan</small>
                                </div>
                            </div>
                        ` : `
                            <div class="no-file">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <p class="text-muted">Tidak ada file yang diupload</p>
                            </div>
                        `}
                    </div>
                </div>
            </div>
            
            <!-- Preview Nilai -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-calculator me-2"></i>Preview Nilai Akhir
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="nilai-preview">
                            <div class="nilai-item">
                                <small class="text-muted">Perusahaan</small>
                                <div class="nilai-display">${mahasiswa.nilai_perusahaan}</div>
                            </div>
                            <div class="plus-sign">+</div>
                            <div class="nilai-item">
                                <small class="text-muted">Dosen</small>
                                <div class="nilai-display" id="preview-nilai-dosen">-</div>
                            </div>
                            <div class="equals-sign">÷ 2 =</div>
                            <div class="hasil-akhir">
                                <small class="text-muted">Nilai Akhir</small>
                                <div class="nilai-akhir-display" id="preview-nilai-akhir">-</div>
                                <div class="grade-display" id="preview-grade">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <hr>
        
        <!-- Form Evaluasi Dosen -->
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Form Evaluasi Dosen
                </h6>
            </div>
            <div class="card-body">
                <form id="evaluasiForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nilai_dosen" class="form-label">
                                    <i class="fas fa-star text-warning me-1"></i>Nilai Dosen (0-100)
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-lg" 
                                           id="nilai_dosen" name="nilai_dosen" 
                                           min="0" max="100" step="0.01" 
                                           value="${mahasiswa.nilai_dosen || ''}" 
                                           required>
                                    <span class="input-group-text">/ 100</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Berikan nilai berdasarkan performa mahasiswa selama bimbingan
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="grade-preview-box">
                                <label class="form-label">Preview Grade:</label>
                                <div class="grade-preview" id="dosen-grade-preview">
                                    <span class="grade-value">-</span>
                                    <small class="grade-desc">Masukkan nilai</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="catatan_dosen" class="form-label">
                            <i class="fas fa-comment-alt text-info me-1"></i>Catatan/Komentar Evaluasi
                        </label>
                        <textarea class="form-control" id="catatan_dosen" name="catatan_dosen" 
                                  rows="4" placeholder="Berikan catatan evaluasi untuk mahasiswa...">${mahasiswa.catatan_dosen || ''}</textarea>
                        <div class="form-text">
                            <i class="fas fa-lightbulb me-1"></i>
                            Berikan feedback konstruktif untuk membantu pengembangan mahasiswa
                        </div>
                    </div>
                    
                    ${mahasiswa.nilai_akhir ? `
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Evaluasi Saat Ini:</h6>
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <strong>Perusahaan:</strong><br>
                                    <span class="badge bg-primary fs-6">${mahasiswa.nilai_perusahaan}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Dosen:</strong><br>
                                    <span class="badge bg-info fs-6">${mahasiswa.nilai_dosen}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Nilai Akhir:</strong><br>
                                    <span class="badge bg-success fs-6">${mahasiswa.nilai_akhir}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Grade:</strong><br>
                                    <span class="badge bg-warning text-dark fs-6">${mahasiswa.grade}</span>
                                </div>
                            </div>
                        </div>
                    ` : ''}
                    
                    <input type="hidden" id="id_mahasiswa" name="id_mahasiswa" value="${mahasiswa.id_mahasiswa}">
                    <input type="hidden" id="magang_id" name="magang_id" value="${mahasiswa.id_magang}">
                    
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-primary btn-lg" onclick="submitEvaluasi()">
                            <i class="fas fa-save me-2"></i>${isExisting ? 'Update Evaluasi' : 'Simpan Evaluasi'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <style>
            .avatar-circle {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 24px;
                font-weight: bold;
                margin: 0 auto;
            }
            
            .detail-item {
                display: flex;
                align-items: center;
                margin-bottom: 10px;
                padding: 8px;
                background: #f8f9fa;
                border-radius: 5px;
            }
            
            .detail-label {
                font-weight: 600;
                margin-left: 5px;
                margin-right: 10px;
                min-width: 80px;
            }
            
            .detail-value {
                flex: 1;
            }
            
            .nilai-preview {
                display: flex;
                align-items: center;
                justify-content: center;
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .nilai-item, .hasil-akhir {
                text-align: center;
            }
            
            .nilai-display, .nilai-akhir-display {
                font-size: 24px;
                font-weight: bold;
                color: #495057;
                background: #f8f9fa;
                padding: 10px;
                border-radius: 50%;
                width: 60px;
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 5px auto;
            }
            
            .plus-sign, .equals-sign {
                font-size: 20px;
                font-weight: bold;
                color: #6c757d;
            }
            
            .grade-preview-box {
                background: linear-gradient(135deg, #f8f9fa, #e9ecef);
                border-radius: 10px;
                padding: 15px;
                text-align: center;
                border: 2px dashed #dee2e6;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            
            .grade-preview {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            
            .grade-value {
                font-size: 2rem;
                font-weight: bold;
                color: #495057;
                margin-bottom: 5px;
            }
            
            .grade-desc {
                color: #6c757d;
            }
            
            .grade-display {
                font-size: 14px;
                font-weight: bold;
                color: #28a745;
                margin-top: 5px;
            }
        </style>
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

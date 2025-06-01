@extends('layouts.app', ['class' => 'g-sidenav-show'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Data Dosen'])
    <div class="container-fluid py-4">
        <!-- Stats Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="icon-dosen icon-warning">
                            <i class="bi bi-exclamation-triangle fs-1"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h1 class="mb-1 fw-bold" id="jumlah-dosen">0</h1>
                        <p class="text-muted mb-0">Dosen yang tersedia menjadi pembimbing</p>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" onclick="mulaiPlotting()">Mulai Plotting</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Dosen Card -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Daftar Dosen</h5>
                    <div class="d-flex gap-2">
                        <button type="button" style="color: white; background: #02A232;" class="btn"
                            onclick="tambahDosen()">
                            <i class="fas fa-plus me-2"></i>Tambah Dosen
                        </button>
                        <button type="button" class="btn btn-primary" onclick="importCSV()">
                            <i class="fas fa-file-import me-2"></i>Import CSV
                        </button>
                        <button type="button" class="btn btn-primary" onclick="exportPDF()">
                            <i class="fas fa-file-pdf me-2"></i>Export PDF
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Wilayah
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">NIP
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody id="dosen-table-body" class="border-top-0">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <p class="text-muted mb-0">Menampilkan 1-4 dari 100 Dosen</p>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            <li class="page-item"><a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">...</a></li>
                            <li class="page-item"><a class="page-link" href="#">50</a></li>
                            <li class="page-item"><a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal Tambah Dosen -->
    <div class="modal fade" id="tambahDosenModal" tabindex="-1" aria-labelledby="tambahDosenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="tambahDosenForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahDosenModalLabel">Tambah Dosen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_dosen" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama_dosen" name="nama_dosen" required>
                        </div>
                        <div class="mb-3">
                            <label for="wilayah_id" class="form-label">Wilayah</label>
                            <select class="form-select" id="wilayah_id" name="wilayah_id" required>
                                <option value="">Pilih Wilayah</option>
                                <!-- Data wilayah akan dimuat via JS -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Edit Dosen -->
    <div class="modal fade" id="editDosenModal" tabindex="-1" aria-labelledby="editDosenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editDosenForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDosenModalLabel">Edit Dosen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editDosenId">
                        <div class="mb-3">
                            <label for="edit_nama_dosen" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="edit_nama_dosen" name="nama_dosen" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_wilayah_id" class="form-label">Wilayah</label>
                            <select class="form-select" id="edit_wilayah_id" name="wilayah_id" required>
                                <option value="">Pilih Wilayah</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="edit_nip" name="nip" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/dosen.css') }}">
@push('js')
    <script src="{{ asset('assets/js/dosen.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        function loadWilayahOptions() {
            axios.get('/api/wilayah')
                .then(function (response) {
                    if (response.data.success) {
                        const wilayahSelect = document.getElementById('wilayah_id');
                        wilayahSelect.innerHTML = '<option value="">Pilih Wilayah</option>';
                        response.data.data.forEach(function (wilayah) {
                            wilayahSelect.innerHTML += `<option value="${wilayah.wilayah_id}">${wilayah.nama_kota}</option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading wilayah:', error);
                });
        }

        // Panggil fungsi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            loadDosenData();
        });

        function loadDosenData() {
            // Tampilkan loading state
            const tableBody = document.getElementById('dosen-table-body');
            tableBody.innerHTML = `
                                        <tr>
                        <td colspan="4" class="text-center">
                            <div class="py-5">
                                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                                <div class="mt-3">
                                    <h6 class="text-primary mb-1">Memuat data dosen</h6>
                                    <p class="text-xs text-secondary">Mohon tunggu sebentar...</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;

            // Fetch data dari API
            axios.get('/api/dosen')
                .then(function (response) {
                    // Clear loading state
                    tableBody.innerHTML = '';

                    if (response.data.success && Array.isArray(response.data.data)) {
                        // Update jumlah dosen
                        document.getElementById('jumlah-dosen').innerText = response.data.data.length;

                        if (response.data.data.length > 0) {
                            response.data.data.forEach(function (dosen, index) {
                                // Create row with animation delay
                                const row = document.createElement('tr');
                                row.style.opacity = '0';
                                row.style.animation = `fadeIn 0.3s ease-out ${index * 0.05}s forwards`;

                                row.innerHTML = `
                                                               <td>
                            <div class="d-flex">
                                <div class="avatar avatar-sm bg-gradient-primary rounded-circle text-white me-3 d-flex align-items-center justify-content-center">
                                    ${dosen.nama_dosen.charAt(0).toUpperCase() || '?'}
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">${dosen.nama_dosen || '-'}</h6>
                                    <p class="text-xs text-secondary mb-0">${dosen.email || '-'}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-sm font-weight-normal">${dosen.wilayah || '-'}</span>
                        </td>
                        <td>
                            <span class="text-sm font-weight-normal">${dosen.nip || '-'}</span>
                        </td>
                        <td class="text-end">
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-primary me-1" onclick="editDosen('${dosen.id_dosen}')" title="Edit Dosen">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="hapusDosen('${dosen.id_dosen}')" title="Hapus Dosen">
                                    <i class="fas fa-trash me-1"></i>Hapus
                                </button>
                            </div>
                        </td>
                    `;
                                tableBody.appendChild(row);
                            });

                            // Add animation keyframes if not already added
                            if (!document.getElementById('fade-in-animation')) {
                                const style = document.createElement('style');
                                style.id = 'fade-in-animation';
                                style.textContent = `
                                                            @keyframes fadeIn {
                                                                from { opacity: 0; transform: translateY(10px); }
                                                                to { opacity: 1; transform: translateY(0); }
                                                            }
                                                        `;
                                document.head.appendChild(style);
                            }

                        } else {
                            showEmptyState(tableBody);
                        }
                    } else {
                        document.getElementById('jumlah-dosen').innerText = '0';
                        showEmptyState(tableBody);
                    }
                })
                .catch(function (error) {
                    document.getElementById('jumlah-dosen').innerText = '0';
                    console.error('Error loading dosen:', error);
                    showErrorState(tableBody);
                });
        }

        // Helper function untuk menampilkan empty state
        function showEmptyState(tableBody) {
            tableBody.innerHTML = `
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-user-graduate" style="font-size: 3.5rem;"></i>
                                </div>
                                <h6 class="text-muted">Tidak ada data dosen</h6>
                                <p class="text-sm text-secondary mb-3">Belum ada dosen yang tersedia. Silakan tambahkan dosen baru.</p>
                                <button class="btn btn-sm btn-success" onclick="tambahDosen()">
                                    <i class="fas fa-plus me-1"></i>Tambah Dosen
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
        }

        // Helper function untuk menampilkan error state
        function showErrorState(tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4">
                        <div class="error-state">
                            <div class="error-state-icon">
                                <i class="fas fa-exclamation-circle" style="font-size: 3.5rem;"></i>
                            </div>
                            <h6 class="text-danger">Gagal memuat data</h6>
                            <p class="text-sm mb-3">Terjadi kesalahan saat memuat data dosen. Silakan coba lagi nanti.</p>
                            <button class="btn btn-sm btn-primary" onclick="loadDosenData()">
                                <i class="fas fa-sync-alt me-1"></i>Coba Lagi
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        function tambahDosen() {
            loadWilayahOptions();
            document.getElementById('tambahDosenForm').reset();
            const modal = new bootstrap.Modal(document.getElementById('tambahDosenModal'));
            modal.show();
        }

        document.getElementById('tambahDosenForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = {
                nama_dosen: document.getElementById('nama_dosen').value,
                wilayah_id: parseInt(document.getElementById('wilayah_id').value, 10), // pastikan integer
                nip: document.getElementById('nip').value
            };

            if (isNaN(formData.wilayah_id) || !formData.wilayah_id) {
                Swal.fire('Peringatan', 'Silakan pilih wilayah!', 'warning');
                return;
            }

            axios.post('/api/dosen', formData)
                .then(function (response) {
                    if (response.data.success) {
                        Swal.fire('Berhasil', 'Dosen berhasil ditambahkan!', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('tambahDosenModal')).hide();
                        loadDosenData();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal menambah dosen.', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat menambah dosen.', 'error');
                });
        });

        function loadEditWilayahOptions(selectedId = null) {
            axios.get('/api/wilayah')
                .then(function (response) {
                    if (response.data.success) {
                        const wilayahSelect = document.getElementById('edit_wilayah_id');
                        wilayahSelect.innerHTML = '<option value="">Pilih Wilayah</option>';
                        response.data.data.forEach(function (wilayah) {
                            const selected = selectedId == wilayah.wilayah_id ? 'selected' : '';
                            wilayahSelect.innerHTML += `<option value="${wilayah.wilayah_id}" ${selected}>${wilayah.nama_kota}</option>`;
                        });
                    }
                });
        }

        function editDosen(id) {
            axios.get(`/api/dosen/${id}`)
                .then(function (response) {
                    if (response.data.success) {
                        const dosen = response.data.data;
                        document.getElementById('editDosenId').value = dosen.id_dosen;
                        document.getElementById('edit_nama_dosen').value = dosen.nama_dosen;
                        document.getElementById('edit_nip').value = dosen.nip;
                        loadEditWilayahOptions(dosen.wilayah_id);

                        const modal = new bootstrap.Modal(document.getElementById('editDosenModal'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', 'Data dosen tidak ditemukan.', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengambil data dosen.', 'error');
                });
        }

        document.getElementById('editDosenForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const id = document.getElementById('editDosenId').value;
            const formData = {
                nama_dosen: document.getElementById('edit_nama_dosen').value,
                wilayah_id: parseInt(document.getElementById('edit_wilayah_id').value, 10),
                nip: document.getElementById('edit_nip').value
            };

            if (isNaN(formData.wilayah_id) || !formData.wilayah_id) {
                Swal.fire('Peringatan', 'Silakan pilih wilayah!', 'warning');
                return;
            }

            axios.put(`/api/dosen/${id}`, formData)
                .then(function (response) {
                    if (response.data.success) {
                        Swal.fire('Berhasil', 'Dosen berhasil diperbarui!', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('editDosenModal')).hide();
                        loadDosenData();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal memperbarui dosen.', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat memperbarui dosen.', 'error');
                });
        });

        function hapusDosen(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus dosen ini?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/api/dosen/${id}`)
                        .then(function (response) {
                            if (response.data.success) {
                                Swal.fire('Berhasil', 'Dosen berhasil dihapus!', 'success');
                                loadDosenData();
                            } else {
                                Swal.fire('Gagal', response.data.message || 'Gagal menghapus dosen.', 'error');
                            }
                        })
                        .catch(function (error) {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus dosen.', 'error');
                        });
                }
            });
        }

        function mulaiPlotting() {
            // Redirect ke halaman plotting
            window.location.href = '/plotting';
        }

        function viewDosen(id) {
            // Tampilkan loading
            Swal.fire({
                title: 'Loading...',
                text: 'Mengambil data dosen',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            axios.get(`/api/dosen/${id}`)
                .then(function (response) {
                    Swal.close();

                    if (response.data.success) {
                        const dosen = response.data.data;

                        // Tampilkan detail dosen dalam modal atau sweetalert
                        Swal.fire({
                            title: 'Detail Dosen',
                            html: `
                                                    <div class="text-start">
                                                        <div class="mb-3">
                                                            <label class="fw-bold d-block">Nama:</label>
                                                            <span>${dosen.nama_dosen || '-'}</span>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="fw-bold d-block">NIP:</label>
                                                            <span>${dosen.nip || '-'}</span>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="fw-bold d-block">Email:</label>
                                                            <span>${dosen.email || '-'}</span>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="fw-bold d-block">Wilayah:</label>
                                                            <span>${dosen.wilayah || '-'}</span>
                                                        </div>
                                                    </div>
                                                `,
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#5e72e4',
                        });
                    } else {
                        Swal.fire('Gagal', 'Data dosen tidak ditemukan.', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.close();
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengambil data dosen.', 'error');
                });
        }

        // Add this function after your existing JavaScript code

        function importCSV() {
            // Create and show modal
            Swal.fire({
                title: 'Import Data Dosen',
                html: `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        File CSV harus memiliki kolom: nama, nip, wilayah
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm mb-3" onclick="downloadTemplate()">
                            <i class="fas fa-download me-1"></i>Download Template
                        </button>
                        <div class="custom-file">
                            <input type="file" class="form-control" id="csvFile" accept=".csv">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Import',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const fileInput = document.getElementById('csvFile');
                    const formData = new FormData();
                    
                    if (!fileInput.files[0]) {
                        Swal.showValidationMessage('Silakan pilih file CSV terlebih dahulu');
                        return false;
                    }
                    
                    formData.append('csv_file', fileInput.files[0]);
                    
                    return axios.post('/api/dosen/import', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then(response => {
                        if (!response.data.success) {
                            throw new Error(response.data.message);
                        }
                        return response.data;
                    })
                    .catch(error => {
                        throw new Error(error.response?.data?.message || 'Terjadi kesalahan saat mengimpor data');
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value.errors && result.value.errors.length > 0) {
                        // Show warning if there are errors but some data was imported
                        Swal.fire({
                            title: 'Import Sebagian Berhasil',
                            html: `
                                ${result.value.message}<br><br>
                                <div class="alert alert-warning">
                                    <strong>Beberapa data tidak dapat diimpor:</strong>
                                    <ul class="mb-0 mt-1">
                                        ${result.value.errors.map(err => `<li class="text-start small">${err}</li>`).join('')}
                                    </ul>
                                </div>
                            `,
                            icon: 'warning'
                        });
                    } else {
                        // All data imported successfully
                        Swal.fire('Berhasil!', result.value.message, 'success');
                    }
                    loadDosenData(); // Refresh the data table
                }
            });
        }

        function downloadTemplate() {
            // Fetch wilayah for template
            axios.get('/api/wilayah')
                .then(function (response) {
                    if (response.data.success) {
                        const wilayah = response.data.data;

                        // Create CSV header
                        let csvContent = "nama,nip,wilayah\n";

                        // Add example data
                        csvContent += `Nama Dosen,198601012019031001,${wilayah[0]?.nama_kota || 'Jember'}\n`;

                        // Create file and trigger download
                        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                        const link = document.createElement("a");
                        const url = URL.createObjectURL(blob);
                        link.setAttribute("href", url);
                        link.setAttribute("download", "template_dosen.csv");
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else {
                        Swal.fire('Gagal', 'Tidak dapat membuat template, gagal memuat data wilayah', 'error');
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan saat membuat template CSV', 'error');
                });
        }

        // Add this function to your JavaScript code
        function exportPDF() {
            // Show loading state
            Swal.fire({
                title: 'Generating PDF...',
                text: 'Please wait while we generate your PDF',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make request to export endpoint
            fetch('/api/dosen/export/pdf')
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.blob();
                })
                .then(blob => {
                    // Create download link
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `data_dosen_${new Date().getTime()}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    Swal.close();
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Export Failed',
                        text: error.message || 'Failed to generate PDF'
                    });
                });
        }
    </script>
@endpush
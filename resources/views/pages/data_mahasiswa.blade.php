@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Data Mahasiswa'])
    <div class="container-fluid py-4">
        <div class="card pt-0">
            <!-- Card Header with Title & Controls -->
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3 py-3">
                <h6 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-user-graduate me-2 text-primary"></i>
                    Data Mahasiswa
                </h6>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control ps-0 border-start-0"
                            placeholder="Cari Mahasiswa...">
                        <button type="button" id="clearSearch" class="btn btn-outline-secondary border-start-0"
                            style="display:none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <select id="kelasFilter" class="form-select form-select-sm" style="min-width: 150px;">
                        <option value="">Semua Kelas</option>
                    </select>
                    <button type="button" class="btn btn-success btn-sm" onclick="tambahMahasiswa()">
                        <i class="fas fa-plus me-1"></i>Tambah Mahasiswa
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="importCSV()">
                        <i class="fas fa-file-import me-1"></i>Import CSV
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="exportPDF()">
                        <i class="fas fa-file-pdf me-1"></i>Export PDF
                    </button>
                </div>
            </div>

            <!-- Card Body with Table -->
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary">Mahasiswa</th>
                                <th class="text-uppercase text-secondary">NIM</th>
                                <th class="text-uppercase text-secondary">Kelas</th> <!-- New column -->
                                <th class="text-center text-uppercase text-secondary">Status</th>
                                <th class="text-uppercase text-secondary">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="mahasiswa-table-body">
                            <!-- Data akan diisi melalui JavaScript -->
                        </tbody>
                    </table>
                    <!-- Pagination Container -->
                    <div id="pagination-container" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Mahasiswa -->
    <div class="modal fade" id="modalTambahMahasiswa" tabindex="-1" aria-labelledby="modalTambahMahasiswaLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="formTambahMahasiswa" onsubmit="submitTambahMahasiswa(event)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahMahasiswaLabel">Tambah Mahasiswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Mahasiswa</label>
                            <input type="text" id="nama" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_kelas" class="form-label">Pilih Kelas</label>
                            <select id="id_kelas" name="id_kelas" class="form-select form-select-sm" required>
                                <option value="">Pilih Kelas</option>
                                <!-- Option kelas akan diisi via JS -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="minat" class="form-label">Minat</label>
                            <select id="minat" name="minat[]" class="form-select" multiple size="4" required>
                                <!-- Options will be loaded via JS -->
                            </select>
                            <small class="form-text text-muted">Tekan Ctrl (Windows) atau Command (Mac) untuk memilih lebih
                                dari satu</small>
                        </div>
                        <div class="mb-3">
                            <label for="skills" class="form-label">Skills</label>
                            <select id="skills" name="skills[]" class="form-select" multiple size="4">
                                <!-- Options will be loaded via JS -->
                            </select>
                            <small class="form-text text-muted">Tekan Ctrl (Windows) atau Command (Mac) untuk memilih lebih
                                dari satu</small>
                        </div>
                        <div class="mb-3">
                            <label for="lama_skill" class="form-label">Lama Skill (Bulan)</label>
                            <input type="number" id="lama_skill" name="lama_skill" class="form-control" min="0" value="6">
                            <small class="form-text text-muted">Durasi menguasai skill dalam bulan</small>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input type="text" id="alamat" name="alamat" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nim" class="form-label">NIM</label>
                            <input type="text" id="nim" name="nim" class="form-control" maxlength="15" required>
                        </div>
                        <div class="mb-3">
                            <label for="ipk" class="form-label">IPK</label>
                            <input type="number" step="0.01" min="0" max="4" id="ipk" name="ipk" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Mahasiswa - Struktur Lengkap -->
    <div class="modal fade" id="detailMahasiswaModal" tabindex="-1" aria-labelledby="detailMahasiswaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailMahasiswaModalLabel">Detail Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailMahasiswaBody">
                    <!-- Content akan diisi melalui JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Mahasiswa -->
    <div class="modal fade" id="modalEditMahasiswa" tabindex="-1" aria-labelledby="modalEditMahasiswaLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEditMahasiswa" onsubmit="submitEditMahasiswa(event)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditMahasiswaLabel">Edit Mahasiswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_mahasiswa" name="id_mahasiswa">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama Mahasiswa</label>
                            <input type="text" id="edit_name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_id_kelas" class="form-label">Pilih Kelas</label>
                            <select id="edit_id_kelas" name="id_kelas" class="form-select form-select-sm" required>
                                <option value="">Pilih Kelas</option>
                                <!-- Option kelas akan diisi via JS -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_skills" class="form-label">Skills</label>
                            <select id="edit_skills" name="skills[]" class="form-select" multiple size="4">
                                <!-- Options will be loaded via JS -->
                            </select>
                            <small class="form-text text-muted">Tekan Ctrl (Windows) atau Command (Mac) untuk memilih lebih
                                dari satu</small>
                        </div>
                        <div class="mb-3">
                            <label for="edit_lama_skill" class="form-label">Lama Skill (Bulan)</label>
                            <input type="number" id="edit_lama_skill" name="lama_skill" class="form-control" min="0"
                                value="6">
                            <small class="form-text text-muted">Durasi menguasai skill dalam bulan</small>
                        </div>
                        <div class="mb-3">
                            <label for="edit_minat" class="form-label">Minat</label>
                            <select id="edit_minat" name="minat[]" class="form-select" multiple size="4">
                                <!-- Options will be loaded via JS -->
                            </select>
                            <small class="form-text text-muted">Tekan Ctrl (Windows) atau Command (Mac) untuk memilih lebih
                                dari satu</small>
                        </div>
                        <div class="mb-3">
                            <label for="edit_alamat" class="form-label">Alamat</label>
                            <input type="text" id="edit_alamat" name="alamat" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nim" class="form-label">NIM</label>
                            <input type="text" id="edit_nim" name="nim" class="form-control" maxlength="15" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_ipk" class="form-label">IPK</label>
                            <input type="number" step="0.01" min="0" max="4" id="edit_ipk" name="ipk" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Import CSV -->
    <div class="modal fade" id="modalImportCSV" tabindex="-1" aria-labelledby="modalImportCSVLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formImportCSV" enctype="multipart/form-data" onsubmit="submitImportCSV(event)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalImportCSVLabel">Import Data Mahasiswa CSV</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            File CSV harus memiliki kolom: nama, nim, alamat, ipk, nama_kelas
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm mb-3"
                                onclick="downloadTemplate()">
                                <i class="fas fa-download me-1"></i>Download Template
                            </button>
                            <label for="csvFile" class="form-label">Pilih File CSV</label>
                            <input type="file" id="csvFile" name="csv_file" class="form-control" accept=".csv" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="headerRow" name="headerRow" checked>
                            <label class="form-check-label" for="headerRow">
                                File memiliki baris header
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>Import
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('css')
    <link href="{{ asset('assets/css/data-mahasiswa.css') }}" rel="stylesheet" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        function debounce(func, wait) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        let filterState = { prodi: '', kelas: '', search: '' }; // Tambahkan let

        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi
            loadKelasFilterOptions();
            loadMahasiswaData(filterState);

            // Setup event filter kelas
            const kelasFilter = document.getElementById('kelasFilter');
            if (kelasFilter) {
                kelasFilter.addEventListener('change', function (e) {
                    filterState.kelas = e.target.value;
                    loadMahasiswaData(filterState);
                });
            }

            // Setup event pencarian dengan debounce
            const searchInput = document.getElementById('searchInput');
            const clearSearch = document.getElementById('clearSearch');

            if (searchInput) {
                // Tambahkan kedua fungsi pada satu event listener
                searchInput.addEventListener('input', debounce(function (e) {
                    // Update tampilan tombol clear
                    if (clearSearch) {
                        clearSearch.style.display = this.value.length > 0 ? 'block' : 'none';
                    }

                    // Update filter state dan reload data
                    filterState.search = this.value.trim();
                    console.log("Search filter changed:", filterState); // Debug log
                    loadMahasiswaData(filterState);
                }, 500));
            }

            // Setup tombol clear search
            if (clearSearch) {
                clearSearch.addEventListener('click', function () {
                    if (searchInput) {
                        searchInput.value = '';
                        filterState.search = '';
                        this.style.display = 'none';
                        console.log("Search cleared:", filterState); // Debug log
                        loadMahasiswaData(filterState);
                    }
                });
            }
        });

        const api = axios.create({
            baseURL: '/api',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            withCredentials: true
        });

        function loadKelasFilterOptions() {
            api.get('/kelas')
                .then(function (response) {
                    if (response.data.success) {
                        const kelasFilter = document.getElementById('kelasFilter');
                        kelasFilter.innerHTML = '<option value="">Semua Kelas</option>';
                        response.data.data.forEach(function (kelas) {
                            kelasFilter.innerHTML += `<option value="${kelas.id_kelas}">${kelas.nama_kelas}</option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Gagal memuat data kelas:', error);
                });
        }

        function loadKelasOptions() {
            api.get('/kelas')
                .then(function (response) {
                    if (response.data.success) {
                        const select = document.getElementById('id_kelas');
                        select.innerHTML = '<option value="">Pilih Kelas</option>';
                        response.data.data.forEach(function (kelas) {
                            select.innerHTML += `<option value="${kelas.id_kelas}">${kelas.nama_kelas}</option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Gagal memuat data kelas:', error);
                });
        }

        function loadEditKelasOptions(selectedIdKelas = '') {
            api.get('/kelas')
                .then(function (response) {
                    if (response.data.success) {
                        const select = document.getElementById('edit_id_kelas');
                        select.innerHTML = '<option value="">Pilih Kelas</option>';
                        response.data.data.forEach(function (kelas) {
                            select.innerHTML += `<option value="${kelas.id_kelas}" ${kelas.id_kelas == selectedIdKelas ? 'selected' : ''}>
                                                                                                                                                                                                                                                        ${kelas.nama_kelas}
                                                                                                                                                                                                                                                    </option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Gagal memuat data kelas:', error);
                });
        }

        function loadMahasiswaData(filters = {}) {
            // Show loading state
            const tableBody = document.getElementById('mahasiswa-table-body');
            tableBody.innerHTML = `
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" class="text-center p-5">
                                                                                                                                        <div class="d-flex flex-column align-items-center">
                                                                                                                                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                                                                                                                                            <div class="text-primary fw-semibold">Memuat data mahasiswa...</div>
                                                                                                                                            <div class="text-muted small mt-2">Mohon tunggu sebentar</div>
                                                                                                                                        </div>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                            `;

            // Add slight delay for better UX when loading is very fast
            setTimeout(() => {
                api.get('/mahasiswa', { params: filters })
                    .then(function (response) {
                        // Handle response data
                        if (response.data && (response.data.success === true || Array.isArray(response.data.data))) {
                            // Clear loading state
                            tableBody.innerHTML = '';

                            // Parse mahasiswa data safely
                            const mahasiswaData = (response.data.data || []);

                            // Show empty state if no data
                            if (mahasiswaData.length === 0) {
                                showEmptyState(filters);
                                return;
                            }

                            // Render data with error handling
                            try {
                                mahasiswaData.forEach((mahasiswa, index) => {
                                    // Create table row with animation delay
                                    const tr = document.createElement('tr');
                                    tr.style.opacity = '0';
                                    tr.style.animation = `fadeIn 0.3s ease-out ${index * 0.05}s forwards`;
                                    tr.innerHTML = createMahasiswaRow(mahasiswa);
                                    tableBody.appendChild(tr);
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

                                // Add hover effect to rows
                                addRowHoverEffects();

                                // Check if we need pagination
                                addPaginationIfNeeded(response.data);

                            } catch (err) {
                                console.error('Error rendering mahasiswa data:', err);
                                showErrorState('Terjadi kesalahan saat menampilkan data.', true);
                            }
                        } else {
                            // Handle response error
                            console.error('Error response:', response.data);
                            showErrorState('Gagal memuat data mahasiswa.');
                        }
                    })
                    .catch(function (error) {
                        // Handle API error
                        console.error('API Error:', error);
                        let errorMessage = 'Gagal memuat data mahasiswa';

                        if (error.response) {
                            if (error.response.status === 401) {
                                errorMessage = 'Sesi Anda telah berakhir. Silakan login kembali.';
                                // Redirect to login after short delay
                                setTimeout(() => {
                                    window.location.href = '/login';
                                }, 2000);
                            } else if (error.response.status === 403) {
                                errorMessage = 'Anda tidak memiliki izin untuk mengakses data ini.';
                            } else if (error.response.data && error.response.data.message) {
                                errorMessage = error.response.data.message;
                            }
                        } else if (error.request) {
                            errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                        }

                        showErrorState(errorMessage);
                    });
            }, 300); // Short delay for better visual feedback

            // Helper function to create a mahasiswa row
            function createMahasiswaRow(mahasiswa) {
                return `
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-sm bg-gradient-primary rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm">
                                                                    <span class="text-white" style="font-weight: 600;">${mahasiswa.name.charAt(0) || '?'}</span>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0 text-sm">${mahasiswa.name || '-'}</h6>
                                                                    <p class="text-xs text-muted mb-0">${mahasiswa.email || '-'}</p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="fw-semibold">${mahasiswa.nim || '-'}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-dark">${mahasiswa.nama_kelas || '-'}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="status-badge ${getStatusClass(mahasiswa.status_magang)}">
                                                                ${mahasiswa.status_magang || 'Belum Magang'}
                                                            </span>
                                                        </td>
                                                        <td>
                                                           <div class="action-buttons">
                                                                <button class="btn btn-sm btn-info me-1" onclick="detailMahasiswa(${mahasiswa.id_mahasiswa})">
                                                                    <i class="fas fa-eye me-1"></i>Detail
                                                                </button>
                                                                <button class="btn btn-sm btn-primary me-1" onclick="editMahasiswa(${mahasiswa.id_mahasiswa})">
                                                                    <i class="fas fa-edit me-1"></i>Edit
                                                                </button>
                                                                <button class="btn btn-sm btn-danger" onclick="deleteMahasiswa(${mahasiswa.id_mahasiswa})">
                                                                    <i class="fas fa-trash-alt me-1"></i>Hapus
                                                                </button>
                                                            </div>
                                                        </td>
                                                    `;
            }

            // Helper function to determine status badge class
            function getStatusClass(status) {
                if (!status) return 'belum';

                switch (status) {
                    case 'Sedang Magang':
                        return 'magang';
                    case 'Selesai Magang':
                        return 'selesai';
                    case 'Menunggu Konfirmasi':
                        return 'menunggu';
                    default:
                        return 'belum';
                }
            }

            // Helper function to show empty state
            function showEmptyState(filters) {
                let filterMessage = '';
                if (filters && (filters.kelas || filters.prodi || filters.search)) {
                    filterMessage = 'dengan filter yang dipilih';
                }

                tableBody.innerHTML = `
                                                                                                                                    <tr>
                                                                                                                                        <td colspan="4">
                                                                                                                                            <div class="empty-state py-5">
                                                                                                                                                <div class="empty-state-icon mb-3">
                                                                                                                                                    <i class="fas fa-user-graduate text-muted opacity-25" style="font-size: 70px;"></i>
                                                                                                                                                </div>
                                                                                                                                                <h5 class="fw-semibold">Tidak ada data mahasiswa ${filterMessage}</h5>
                                                                                                                                                <p class="text-muted mb-3">Silakan tambahkan data mahasiswa baru atau ubah filter pencarian</p>
                                                                                                                                                <div class="d-flex justify-content-center gap-2">
                                                                                                                                                    <button class="btn btn-sm btn-primary" onclick="tambahMahasiswa()">
                                                                                                                                                        <i class="fas fa-plus me-1"></i>Tambah Mahasiswa
                                                                                                                                                    </button>
                                                                                                                                                    ${filters && (filters.kelas || filters.prodi) ? `
                                                                                                                                                        <button class="btn btn-sm btn-outline-secondary" onclick="resetFilters()">
                                                                                                                                                            <i class="fas fa-filter-circle-xmark me-1"></i>Reset Filter
                                                                                                                                                        </button>
                                                                                                                                                    ` : ''}
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        </td>
                                                                                                                                    </tr>
                                                                                                                                `;
            }

            // Helper function to show error state
            function showErrorState(message, isSystemError = false) {
                tableBody.innerHTML = `
                                                                                                                                    <tr>
                                                                                                                                        <td colspan="4">
                                                                                                                                            <div class="text-center py-4">
                                                                                                                                                <div class="mb-3">
                                                                                                                                                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 40px;"></i>
                                                                                                                                                </div>
                                                                                                                                                <h5 class="text-danger">${message}</h5>
                                                                                                                                                ${isSystemError ? `
                                                                                                                                                    <p class="text-muted mt-2 mb-3">Coba muat ulang halaman atau hubungi administrator</p>
                                                                                                                                                ` : ''}
                                                                                                                                                <button class="btn btn-sm btn-primary mt-2" onclick="loadMahasiswaData(filterState)">
                                                                                                                                                    <i class="fas fa-sync-alt me-1"></i>Coba Lagi
                                                                                                                                                </button>
                                                                                                                                            </div>
                                                                                                                                        </td>
                                                                                                                                    </tr>
                                                                                                                                `;
            }

            // Helper function to add hover effects
            function addRowHoverEffects() {
                const rows = document.querySelectorAll('#mahasiswa-table-body tr');
                rows.forEach(row => {
                    row.addEventListener('mouseenter', () => {
                        const buttons = row.querySelectorAll('.action-buttons .btn');
                        buttons.forEach(btn => {
                            btn.classList.add('shadow-sm');
                        });
                    });

                    row.addEventListener('mouseleave', () => {
                        const buttons = row.querySelectorAll('.action-buttons .btn');
                        buttons.forEach(btn => {
                            btn.classList.remove('shadow-sm');
                        });
                    });
                });
            }

            // Helper function to add pagination if needed
            function addPaginationIfNeeded(responseData) {
                const paginationContainer = document.getElementById('pagination-container');
                if (!paginationContainer) return;

                // Clear existing pagination
                paginationContainer.innerHTML = '';

                // Check if we have pagination data
                if (responseData.meta && responseData.meta.last_page > 1) {
                    const currentPage = responseData.meta.current_page;
                    const lastPage = responseData.meta.last_page;

                    let paginationHtml = `
                                                                                                                                        <nav aria-label="Page navigation">
                                                                                                                                            <ul class="pagination pagination-sm justify-content-center my-3">
                                                                                                                                                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                                                                                                                                    <a class="page-link" href="#" onclick="changePage(1)">
                                                                                                                                                        <i class="fas fa-angle-double-left"></i>
                                                                                                                                                    </a>
                                                                                                                                                </li>
                                                                                                                                                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                                                                                                                                    <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">
                                                                                                                                                        <i class="fas fa-angle-left"></i>
                                                                                                                                                    </a>
                                                                                                                                                </li>
                                                                                                                                    `;

                    // Generate page numbers
                    for (let i = Math.max(1, currentPage - 2); i <= Math.min(lastPage, currentPage + 2); i++) {
                        paginationHtml += `
                                                                                                                                            <li class="page-item ${i === currentPage ? 'active' : ''}">
                                                                                                                                                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                                                                                                                                            </li>
                                                                                                                                        `;
                    }

                    paginationHtml += `
                                                                                                                                                <li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
                                                                                                                                                    <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">
                                                                                                                                                        <i class="fas fa-angle-right"></i>
                                                                                                                                                    </a>
                                                                                                                                                </li>
                                                                                                                                                <li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
                                                                                                                                                    <a class="page-link" href="#" onclick="changePage(${lastPage})">
                                                                                                                                                        <i class="fas fa-angle-double-right"></i>
                                                                                                                                                    </a>
                                                                                                                                                </li>
                                                                                                                                            </ul>
                                                                                                                                        </nav>
                                                                                                                                    `;

                    paginationContainer.innerHTML = paginationHtml;
                }
            }
        }

        // Load minat options
        function loadMinatOptions() {
            api.get('/minat')
                .then(function (response) {
                    if (response.data) {
                        const select = document.getElementById('minat');
                        select.innerHTML = '';
                        response.data.forEach(function (minat) {
                            select.innerHTML += `<option value="${minat.minat_id}">${minat.nama_minat}</option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Gagal memuat data minat:', error);
                });
        }

        // Load minat options for edit form
        function loadEditMinatOptions(selectedMinatIds = []) {
            api.get('/minat')
                .then(function (response) {
                    if (response.data) {
                        const select = document.getElementById('edit_minat');
                        select.innerHTML = '';
                        response.data.forEach(function (minat) {
                            const isSelected = selectedMinatIds.includes(minat.minat_id);
                            select.innerHTML += `
                                                                <option value="${minat.minat_id}" ${isSelected ? 'selected' : ''}>
                                                                    ${minat.nama_minat}
                                                                </option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Gagal memuat data minat:', error);
                });
        }

        function resetFilters() {

            // Reset filter state
            filterState = { prodi: '', kelas: '', search: '' };

            // Reset UI selects dan input
            const kelasFilter = document.getElementById('kelasFilter');
            if (kelasFilter) kelasFilter.value = '';

            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.value = '';

            const clearSearch = document.getElementById('clearSearch');
            if (clearSearch) clearSearch.style.display = 'none';

            // Reload data dengan filter yang sudah direset
            loadMahasiswaData(filterState);
        }

        // Function to handle page navigation
        function changePage(page) {
            // Clone current filter state and add page
            const paginatedFilter = { ...filterState, page };

            // Load data with pagination
            loadMahasiswaData(paginatedFilter);

            // Scroll to top of table
            document.querySelector('.card').scrollIntoView({ behavior: 'smooth', block: 'start' });

            // Prevent default action
            return false;
        }



        function tambahMahasiswa() {
            loadKelasOptions();
            loadMinatOptions();
            loadSkillsOptions();
            var modal = new bootstrap.Modal(document.getElementById('modalTambahMahasiswa'));
            modal.show();
        }

        // Load skills options
        function loadSkillsOptions() {
            api.get('/skills')
                .then(function (response) {
                    if (response.data.success) {
                        const select = document.getElementById('skills');
                        select.innerHTML = '';
                        response.data.data.forEach(function (skill) {
                            select.innerHTML += `<option value="${skill.skill_id}">${skill.nama}</option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Gagal memuat data skills:', error);
                });
        }

        // Load skills options for edit form
        function loadEditSkillsOptions(selectedSkillIds = []) {
            api.get('/skills')
                .then(function (response) {
                    if (response.data.success) {
                        const select = document.getElementById('edit_skills');
                        select.innerHTML = '';
                        response.data.data.forEach(function (skill) {
                            const isSelected = selectedSkillIds.includes(skill.skill_id);
                            select.innerHTML += `
                                        <option value="${skill.skill_id}" ${isSelected ? 'selected' : ''}>
                                            ${skill.nama}
                                        </option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Gagal memuat data skills:', error);
                });
        }

        function detailMahasiswa(id) {
            // Tampilkan loading
            Swal.fire({
                title: 'Loading...',
                text: 'Mengambil data mahasiswa',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            api.get(`/mahasiswa/${id}`)
                .then(function (response) {
                    Swal.close();

                    if (response.data.success) {
                        const mahasiswa = response.data.data;

                        // Logging untuk debugging
                        console.log('Data mahasiswa:', mahasiswa);
                        console.log('Data skills:', mahasiswa.skills);
                        console.log('Data minat:', mahasiswa.minat);

                        // Render skills badges dengan penanganan data yang fleksibel
                        const skills = Array.isArray(mahasiswa.skills) && mahasiswa.skills.length > 0
                            ? mahasiswa.skills.map(skill => `
                                                            <span class="badge bg-primary me-1">
                                                                ${skill.nama || 'Tidak Diketahui'} 
                                                                (${skill.lama_skill || 'Tidak Diketahui'})
                                                            </span>
                                                          `).join('')
                            : '<span class="text-muted">Tidak ada skill</span>';

                        // Render minat badges
                        const minat = Array.isArray(mahasiswa.minat) && mahasiswa.minat.length > 0
                            ? mahasiswa.minat.map(m => `
                                                            <span class="badge bg-info me-1">
                                                                ${m.nama_minat || 'Tidak Diketahui'}
                                                            </span>
                                                          `).join('')
                            : '<span class="text-muted">Tidak ada minat</span>';

                        // Render dokumen dengan pengecekan array
                        const dokumen = Array.isArray(mahasiswa.dokumen) && mahasiswa.dokumen.length > 0
                            ? mahasiswa.dokumen.map(doc => `
                                                                                                                                                                            <li class="list-group-item">
                                                                                                                                                                                <strong>${doc.file_type || 'Dokumen'}:</strong> 
                                                                                                                                                                                <a href="${doc.file_url}" target="_blank">${doc.file_name || 'Unduh'}</a>
                                                                                                                                                                                <br><small>${doc.description || 'Tidak ada deskripsi'}</small>
                                                                                                                                                                            </li>
                                                                                                                                                                          `).join('')
                            : '<li class="list-group-item">Tidak ada dokumen</li>';

                        // Isi modal dengan data mahasiswa
                        document.getElementById('detailMahasiswaModalLabel').innerText =
                            `Detail Mahasiswa - ${mahasiswa.name || 'Tidak Diketahui'}`;

                        document.getElementById('detailMahasiswaBody').innerHTML = `
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>Nama:</strong> ${mahasiswa.name || '-'}</p>
                                                        <p><strong>Email:</strong> ${mahasiswa.email || '-'}</p>
                                                        <p><strong>NIM:</strong> ${mahasiswa.nim || '-'}</p>
                                                        <p><strong>Kelas:</strong> ${mahasiswa.nama_kelas || '-'}</p>
                                                        <p><strong>Status:</strong> ${mahasiswa.status_magang || 'Belum Magang'}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Alamat:</strong> ${mahasiswa.alamat || '-'}</p>
                                                        <p><strong>IPK:</strong> ${mahasiswa.ipk || '-'}</p>
                                                        <p><strong>Skills:</strong></p>
                                                        <div>${skills}</div>
                                                        <p class="mt-2"><strong>Minat:</strong></p>
                                                        <div>${minat}</div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <p><strong>Dokumen:</strong></p>
                                                <ul class="list-group">${dokumen}</ul>
                                            `;

                        // Tampilkan modal
                        const modal = new bootstrap.Modal(document.getElementById('detailMahasiswaModal'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal memuat detail mahasiswa', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.close();
                    console.error('Error:', error);

                    // Tampilkan pesan error yang lebih informatif
                    let errorMessage = 'Terjadi kesalahan saat memuat detail mahasiswa';
                    if (error.response) {
                        if (error.response.status === 404) {
                            errorMessage = 'Mahasiswa tidak ditemukan';
                        } else if (error.response.data && error.response.data.message) {
                            errorMessage = error.response.data.message;
                        }
                    }

                    Swal.fire('Error', errorMessage, 'error');
                });
        }

        function editMahasiswa(id) {
            api.get(`/mahasiswa/${id}`)
                .then(function (response) {
                    if (response.data.success) {
                        const mahasiswa = response.data.data;
                        document.getElementById('edit_id_mahasiswa').value = mahasiswa.id_mahasiswa;
                        document.getElementById('edit_name').value = mahasiswa.name;
                        document.getElementById('edit_alamat').value = mahasiswa.alamat;
                        document.getElementById('edit_nim').value = mahasiswa.nim;
                        document.getElementById('edit_ipk').value = mahasiswa.ipk;

                        // If there are skills with lama_skill property, use it
                        if (Array.isArray(mahasiswa.skills) && mahasiswa.skills.length > 0 &&
                            mahasiswa.skills[0].lama_skill) {
                            document.getElementById('edit_lama_skill').value = mahasiswa.skills[0].lama_skill;
                        }

                        loadEditKelasOptions(mahasiswa.id_kelas);

                        // Extract minat IDs
                        const minatIds = Array.isArray(mahasiswa.minat)
                            ? mahasiswa.minat.map(m => m.minat_id)
                            : [];

                        // Extract skill IDs 
                        const skillIds = Array.isArray(mahasiswa.skills)
                            ? mahasiswa.skills.map(s => s.skill_id)
                            : [];

                        // Load minat and skills options with selected values
                        loadEditMinatOptions(minatIds);
                        loadEditSkillsOptions(skillIds);

                        const modal = new bootstrap.Modal(document.getElementById('modalEditMahasiswa'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal memuat data mahasiswa', 'error');
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat data mahasiswa', 'error');
                });
        }

        function submitEditMahasiswa(event) {
            event.preventDefault();
            const form = event.target;
            const id = document.getElementById('edit_id_mahasiswa').value;

            // Get selected minat values
            const minatSelect = document.getElementById('edit_minat');
            const selectedMinat = Array.from(minatSelect.selectedOptions).map(option => option.value);

            // Get selected skills values
            const skillsSelect = document.getElementById('edit_skills');
            const selectedSkills = Array.from(skillsSelect.selectedOptions).map(option => option.value);
            const lamaSkill = document.getElementById('edit_lama_skill').value;

            const data = {
                name: form.name.value,
                id_kelas: form.id_kelas.value,
                alamat: form.alamat.value,
                nim: form.nim.value,
                ipk: form.ipk.value,
                minat: selectedMinat,
                skills: selectedSkills,
                lama_skill: lamaSkill
            };

            api.put(`/mahasiswa/${id}`, data)
                .then(res => {
                    if (res.data.success) {
                        Swal.fire('Berhasil!', 'Data mahasiswa berhasil diperbarui!', 'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditMahasiswa'));
                        modal.hide();
                        form.reset();
                        loadMahasiswaData(); // Refresh data mahasiswa
                    } else {
                        Swal.fire('Gagal', res.data.message || 'Gagal memperbarui data mahasiswa', 'error');
                    }
                })
                .catch(err => {
                    let msg = 'Terjadi kesalahan saat memperbarui data mahasiswa.';
                    if (err.response && err.response.data && err.response.data.message) {
                        msg = err.response.data.message;
                    }
                    Swal.fire('Error', msg, 'error');
                });
        }

        function deleteMahasiswa(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data mahasiswa ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim permintaan DELETE ke backend
                    api.delete(`/mahasiswa/${id}`)
                        .then(res => {
                            if (res.data.success) {
                                Swal.fire(
                                    'Terhapus!',
                                    'Data mahasiswa berhasil dihapus.',
                                    'success'
                                );
                                loadMahasiswaData(); // Refresh data mahasiswa
                            } else {
                                Swal.fire('Gagal', res.data.message || 'Gagal menghapus data mahasiswa', 'error');
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            Swal.fire('Error', 'Terjadi kesalahan saat menghapus data mahasiswa.', 'error');
                        });
                }
            });
        }

        function submitTambahMahasiswa(event) {
            event.preventDefault();
            const form = event.target;
            const nim = form.nim.value;

            // Get selected minat values
            const minatSelect = document.getElementById('minat');
            const selectedMinat = Array.from(minatSelect.selectedOptions).map(option => option.value);

            // Get selected skills values
            const skillsSelect = document.getElementById('skills');
            const selectedSkills = Array.from(skillsSelect.selectedOptions).map(option => option.value);
            const lamaSkill = document.getElementById('lama_skill').value;

            const data = {
                name: form.name.value,
                email: nim + '@student.com',
                password: nim,
                nim: nim,
                id_kelas: form.id_kelas.value,
                alamat: form.alamat.value,
                ipk: form.ipk.value,
                minat: selectedMinat,
                skills: selectedSkills,
                lama_skill: lamaSkill
            };

            api.post('/mahasiswa', data)
                .then(res => {
                    if (res.data.success) {
                        Swal.fire('Berhasil!', 'Mahasiswa berhasil ditambahkan!', 'success');
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modalTambahMahasiswa'));
                        modal.hide();
                        form.reset();
                        loadMahasiswaData();
                    } else {
                        Swal.fire('Gagal', res.data.message || 'Gagal menambahkan mahasiswa', 'error');
                    }
                })
                .catch(err => {
                    let msg = 'Terjadi kesalahan saat menambahkan mahasiswa.';
                    if (err.response && err.response.data && err.response.data.message) {
                        msg = err.response.data.message;
                    }
                    Swal.fire('Error', msg, 'error');
                });
        }

        function loadLatestApplications() {
            document.getElementById('latest-applications').innerHTML = `
                                                                                        <tr>
                                                                                            <td colspan="4" class="text-center py-3">
                                                                                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                                                                                <span class="text-sm">Memuat data...</span>
                                                                                            </td>
                                                                                        </tr>
                                                                                    `;

            api.get('/dashboard/latest-applications')
                .then(function (response) {
                    if (response.data.success) {
                        const applications = response.data.data;
                        const tableBody = document.getElementById('latest-applications');

                        tableBody.innerHTML = '';

                        if (applications.length === 0) {
                            tableBody.innerHTML = `
                                                                                                        <tr>
                                                                                                            <td colspan="4" class="text-center py-4">
                                                                                                                <img src="/assets/img/empty-data.svg" height="60" class="mb-3 opacity-60">
                                                                                                                <p class="mb-0 text-muted">Tidak ada permintaan magang terbaru</p>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    `;
                            return;
                        }

                        applications.forEach(app => {
                            let statusBadge;

                            // Penanganan status sesuai field auth
                            switch (app.status?.toLowerCase()) {
                                case 'diterima':
                                    statusBadge = '<span class="badge bg-gradient-success">Diterima</span>';
                                    break;
                                case 'ditolak':
                                    statusBadge = '<span class="badge bg-gradient-danger">Ditolak</span>';
                                    break;
                                case 'menunggu':
                                case 'pending':
                                    statusBadge = '<span class="badge bg-gradient-warning">Menunggu</span>';
                                    break;
                                default:
                                    statusBadge = `<span class="badge bg-gradient-secondary">${app.status || 'Menunggu'}</span>`;
                            }

                            const row = `
                                                                                                        <tr>
                                                                                                            <td class="ps-3">
                                                                                                                <div class="d-flex align-items-center">
                                                                                                                    <div class="avatar avatar-xs me-2 bg-gradient-primary rounded-circle">
                                                                                                                        <span class="text-white font-weight-bold">${app.nama_mahasiswa.charAt(0)}</span>
                                                                                                                    </div>
                                                                                                                    <div>
                                                                                                                        <h6 class="mb-0 text-sm">${app.nama_mahasiswa}</h6>
                                                                                                                        <p class="text-xs text-muted mb-0">${app.nim}</p>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </td>
                                                                                                            <td class="text-sm">${app.perusahaan}</td>
                                                                                                            <td class="text-sm">${statusBadge}</td>
                                                                                                            <td class="text-center">
                                                                                                                <a href="/magang/detail/${app.id_lamaran}" class="btn btn-link btn-sm m-0 p-0" data-toggle="tooltip" title="Lihat Detail">
                                                                                                                    <i class="fas fa-eye"></i>
                                                                                                                </a>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    `;
                            tableBody.innerHTML += row;
                        });

                        // Initialize tooltips
                        const tooltips = document.querySelectorAll('[data-toggle="tooltip"]');
                        tooltips.forEach(tooltip => {
                            new bootstrap.Tooltip(tooltip);
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading latest applications:', error);
                    if (error.response) {
                        console.error('Response status:', error.response.status);
                        console.error('Response data:', error.response.data);
                    }
                    document.getElementById('latest-applications').innerHTML = `
                                                                                                <tr>
                                                                                                    <td colspan="4" class="text-center text-danger py-3">
                                                                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                                                                        Gagal memuat data. Coba lagi nanti.
                                                                                                    </td>
                                                                                                </tr>
                                                                                            `;
                });
        }



        function importCSV() {
            // Tampilkan modal import CSV
            const modal = new bootstrap.Modal(document.getElementById('modalImportCSV'));
            modal.show();

            // Reset form
            document.getElementById('formImportCSV').reset();
        }

        function submitImportCSV(event) {
            event.preventDefault();

            const formData = new FormData();
            const fileInput = document.getElementById('csvFile');

            if (!fileInput.files[0]) {
                Swal.fire('Error', 'Silakan pilih file CSV terlebih dahulu', 'error');
                return;
            }

            // Ambil nama file yang benar dari input
            formData.append('csv_file', fileInput.files[0]);

            // Ambil info header row jika diperlukan
            const headerRow = document.getElementById('headerRow').checked;
            formData.append('header_row', headerRow ? '1' : '0');

            // Tampilkan loading
            Swal.fire({
                title: 'Mengimpor Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Kirim request ke endpoint yang sesuai
            axios.post('/api/mahasiswa/import', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => {
                    if (response.data.success) {
                        // Success handling
                        let message = response.data.message;

                        // Jika ada error pada beberapa data, tampilkan warning
                        if (response.data.errors && response.data.errors.length > 0) {
                            Swal.fire({
                                title: 'Import Sebagian Berhasil',
                                html: `
                                                                                ${message}<br><br>
                                                                                <div class="alert alert-warning">
                                                                                    <strong>Beberapa data tidak dapat diimpor:</strong>
                                                                                    <ul class="mb-0 mt-1">
                                                                                        ${response.data.errors.map(err => `<li class="text-start small">${err}</li>`).join('')}
                                                                                    </ul>
                                                                                </div>
                                                                            `,
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            // Semua data berhasil diimpor
                            Swal.fire('Berhasil', message, 'success');
                        }

                        // Tutup modal dan refresh data
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalImportCSV'));
                        modal.hide();
                        document.getElementById('formImportCSV').reset();

                        // Refresh data mahasiswa
                        loadMahasiswaData();
                    } else {
                        Swal.fire('Gagal', response.data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    let message = 'Terjadi kesalahan saat mengimpor data';

                    if (error.response) {
                        if (error.response.data && error.response.data.message) {
                            message = error.response.data.message;
                        }
                    }

                    Swal.fire('Error', message, 'error');
                });
        }

        function downloadTemplate() {
            // Fetch kelas untuk membuat template
            api.get('/kelas')
                .then(function (response) {
                    if (response.data.success) {
                        const kelas = response.data.data;

                        // Buat header CSV
                        let csvContent = "nama,nim,alamat,ipk,nama_kelas\n";

                        // Tambahkan contoh data
                        csvContent += `Nama Mahasiswa,E41230001,Jl. Contoh No.123,3.50,${kelas[0]?.nama_kelas || 'TIF-3A'}\n`;

                        // Buat file dan trigger download
                        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                        const link = document.createElement("a");
                        const url = URL.createObjectURL(blob);
                        link.setAttribute("href", url);
                        link.setAttribute("download", "template_mahasiswa.csv");
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else {
                        Swal.fire('Gagal', 'Tidak dapat membuat template, gagal memuat data kelas', 'error');
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan saat membuat template CSV', 'error');
                });
        }

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

            // Get current filters
            const params = new URLSearchParams();
            if (filterState.kelas) params.append('kelas', filterState.kelas);
            if (filterState.search) params.append('search', filterState.search);

            // Make request to export endpoint
            window.location.href = `/api/mahasiswa/export/pdf?${params.toString()}`;

            // Close loading after a short delay
            setTimeout(() => {
                Swal.close();
            }, 2000);
        }
    </script>
@endpush
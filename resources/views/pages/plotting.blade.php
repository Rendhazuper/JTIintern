@extends('layouts.app',  ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Plotting Dosen'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h6>Plotting Manual Dosen</h6>
                            <button type="button" class="btn btn-sm btn-info" id="showMatrixBtn">
                                <i class="bi bi-graph-up me-2"></i>Lihat Matrix Keputusan
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Replace the existing filter row with this improved version -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label d-flex align-items-center">
                                    <i class="fas fa-search text-primary me-2"></i>
                                    <span>Cari Dosen</span>
                                </label>
                                <div class="input-group input-group-dynamic">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchDosen"
                                        placeholder="Nama dosen atau NIP">
                                    <button class="btn btn-sm btn-outline-secondary border-0" type="button" id="clearSearch"
                                        style="display:none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <span>Filter Wilayah</span>
                                </label>
                                <select class="form-select" id="wilayahFilter">
                                    <option value="">Semua Wilayah</option>
                                    <!-- Will be populated dynamically -->
                                </select>
                            </div>
                        </div>

                        <!-- Update table class and structure -->
                        <div class="table-responsive">
                            <table class="table plotting-table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                            </div>
                                        </th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">Dosen</th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">Mahasiswa
                                        </th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">Wilayah</th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">Skills</th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-end">Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="plotting-table-body">
                                    <!-- Data will be populated dynamically -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <span class="text-sm text-secondary">
                                    Menampilkan <span id="showingCount">0-0</span> dari <span id="totalCount">0</span> dosen
                                </span>
                            </div>
                            <ul class="pagination mb-0" id="pagination">
                                <!-- Pagination will be generated dynamically -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto Plot Section -->
        <div class="card mt-4">
            <div class="card-header pb-0">
                <h6>Plotting Otomatis dengan SAW</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="text-sm">
                            Plotting otomatis akan menggunakan metode SAW (Simple Additive Weighting) untuk menemukan dosen
                            pembimbing terbaik untuk setiap mahasiswa berdasarkan kriteria wilayah dan kecocokan skill.
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-primary" id="autoPlotBtn">
                            <i class="fas fa-magic me-2"></i>Auto-Plot Dosen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Assign Mahasiswa -->
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalLabel">Assign Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="selectedDosenId">
                    <div class="mb-3">
                        <label for="mahasiswaSelect" class="form-label">Pilih Mahasiswa</label>
                        <!-- In your modal -->
                        <select class="form-select" id="mahasiswaSelect" multiple size="5">
                            <!-- Increased size for better visibility -->
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveAssignBtn">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Matrix Keputusan -->
    <div class="modal fade" id="matrixModal" tabindex="-1" aria-labelledby="matrixModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="matrixModalLabel">Matrix Keputusan SAW</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="active-matrix">
                            <div id="activeMatrixContainer">
                                <!-- Matrix untuk magang aktif akan dimuat di sini -->
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pending-matrix">
                            <div id="pendingMatrixContainer">
                                <!-- Matrix untuk magang pending akan dimuat di sini -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/plotting.css') }}">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Variabel global
        let allDosen = [];
        let filteredDosen = [];
        let allWilayah = [];
        let currentPage = 1;
        let searchTerm = '';
        let selectedWilayahId = '';
        const itemsPerPage = 10;

        // Add clear search button functionality
        document.getElementById('searchDosen').addEventListener('input', function () {
            const clearBtn = document.getElementById('clearSearch');
            if (this.value) {
                clearBtn.style.display = 'block';
            } else {
                clearBtn.style.display = 'none';
            }
        });

        document.getElementById('clearSearch').addEventListener('click', function () {
            document.getElementById('searchDosen').value = '';
            searchTerm = '';
            applyFilters();
            this.style.display = 'none';
        });

        // Add hover effects for buttons
        document.addEventListener('DOMContentLoaded', function () {
            // Initial load
            loadPlottingData();

            // Add event delegation for tooltip behavior
            document.body.addEventListener('mouseover', function (e) {
                if (e.target.closest('[data-tooltip]')) {
                    const tooltip = e.target.closest('[data-tooltip]');
                    tooltip.classList.add('tooltip-active');
                }
            });

            document.body.addEventListener('mouseout', function (e) {
                if (e.target.closest('[data-tooltip]')) {
                    const tooltip = e.target.closest('[data-tooltip]');
                    tooltip.classList.remove('tooltip-active');
                }
            });
        });

        function loadPlottingData() {
            // Show loading state
            // Show loading state with improved animation
            document.getElementById('plotting-table-body').innerHTML = `
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="spinner-grow text-primary mb-2" role="status" style="width: 3rem; height: 3rem;">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <div class="d-flex justify-content-center">
                                                        <div class="spinner-grow text-secondary mx-1" style="width: 1rem; height: 1rem;"></div>
                                                        <div class="spinner-grow text-secondary mx-1" style="width: 1rem; height: 1rem; animation-delay: 0.2s"></div>
                                                        <div class="spinner-grow text-secondary mx-1" style="width: 1rem; height: 1rem; animation-delay: 0.4s"></div>
                                                    </div>
                                                    <p class="mt-3 text-secondary">Memuat data plotting...</p>
                                                </td>
                                            </tr>
                                        `;

            // Use existing endpoint instead of new one
            fetch('/api/dosen/with-perusahaan?t=' + new Date().getTime(), {
                headers: {
                    'Content-Type': 'application/json',
                    'Cache-Control': 'no-store, no-cache',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('API response status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        allDosen = data.data;
                        filteredDosen = [...allDosen];
                        renderDosenTable();
                        setupPagination();
                        loadWilayahData();
                    } else {
                        throw new Error(data.message || 'Unknown error');
                    }
                })
                .catch(error => {
                    console.error('Error loading plotting data:', error);
                    document.getElementById('plotting-table-body').innerHTML = `
                                                            <tr>
                                                                <td colspan="5" class="text-center text-danger">
                                                                    Gagal memuat data: ${error.message}
                                                                </td>
                                                            </tr>
                                                        `;
                });
        }

        function applyFilters() {
            // Apply both filters simultaneously
            filteredDosen = allDosen.filter(dosen => {
                // Apply search filter with robust type checking
                const userName = typeof dosen.user?.name === 'string' ? dosen.user.name : '';
                const nipValue = typeof dosen.nip === 'string' ? dosen.nip : '';

                const nameMatches = !searchTerm ||
                    userName.toLowerCase().includes(searchTerm) ||
                    nipValue.toLowerCase().includes(searchTerm);

                // Apply wilayah filter
                const wilayahMatches = !selectedWilayahId || dosen.wilayah_id == selectedWilayahId;

                // Item must match BOTH filters
                return nameMatches && wilayahMatches;
            });

            // Reset to first page and update UI
            currentPage = 1;
            renderDosenTable();
            setupPagination();

            // Update filter status indicator
            updateFilterStatus();
        }

        function updateFilterStatus() {
            const statusContainer = document.getElementById('filterStatus') ||
                createFilterStatusElement();

            if (searchTerm || selectedWilayahId) {
                let statusText = `Menampilkan ${filteredDosen.length} dari ${allDosen.length} dosen`;

                if (searchTerm && selectedWilayahId) {
                    const wilayahName = document.querySelector(`#wilayahFilter option[value="${selectedWilayahId}"]`).textContent;
                    statusText += ` (Filter: "${searchTerm}" di wilayah ${wilayahName})`;
                } else if (searchTerm) {
                    statusText += ` (Filter: "${searchTerm}")`;
                } else if (selectedWilayahId) {
                    const wilayahName = document.querySelector(`#wilayahFilter option[value="${selectedWilayahId}"]`).textContent;
                    statusText += ` (Filter: wilayah ${wilayahName})`;
                }

                statusContainer.innerHTML = `
                                                                                                                                                <div class="d-flex justify-content-between align-items-center">
                                                                                                                                                    <span>${statusText}</span>
                                                                                                                                                    <button class="btn btn-sm btn-outline-secondary" onclick="resetFilters()">
                                                                                                                                                        <i class="fas fa-times me-1"></i>Reset Filter
                                                                                                                                                    </button>
                                                                                                                                                </div>
                                                                                                                                            `;
                statusContainer.style.display = 'block';
            } else {
                statusContainer.style.display = 'none';
            }
        }

        // Create filter status element if it doesn't exist
        function createFilterStatusElement() {
            const filtersRow = document.querySelector('.row.g-4.mb-4');
            const statusDiv = document.createElement('div');
            statusDiv.id = 'filterStatus';
            statusDiv.className = 'alert alert-info mt-2';
            statusDiv.style.display = 'none';
            filtersRow.insertAdjacentElement('afterend', statusDiv);
            return statusDiv;
        }

        // Add a function to reset all filters
        function resetFilters() {
            document.getElementById('searchDosen').value = '';
            document.getElementById('wilayahFilter').value = '';
            searchTerm = '';
            selectedWilayahId = '';
            filteredDosen = [...allDosen];
            currentPage = 1;
            renderDosenTable();
            setupPagination();
            updateFilterStatus();
        }

        // Enhance the loadWilayahData function with better error handling
        function loadWilayahData() {
            console.log('Fetching wilayah data...');

            fetch('/api/wilayah')
                .then(response => {
                    console.log('Wilayah API response status:', response.status);
                    if (!response.ok) {
                        throw new Error('API response status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Wilayah data received:', data);
                    if (data.success) {
                        allWilayah = data.data;

                        // Populate wilayah filter
                        const wilayahFilter = document.getElementById('wilayahFilter');
                        wilayahFilter.innerHTML = '<option value="">Semua Wilayah</option>';

                        if (allWilayah && allWilayah.length > 0) {
                            allWilayah.forEach(wilayah => {
                                const option = document.createElement('option');
                                option.value = wilayah.id_wilayah || wilayah.wilayah_id;
                                option.textContent = wilayah.nama_wilayah || wilayah.nama_kota || wilayah.name;
                                wilayahFilter.appendChild(option);
                            });
                            console.log(`Added ${allWilayah.length} wilayah options to dropdown`);
                        } else {
                            console.warn('No wilayah data found in API response');
                        }
                    } else {
                        console.error('API returned success: false', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading wilayah data:', error);
                    // Add fallback data if API fails
                    addFallbackWilayahData();
                });
        }

        // Add fallback function to ensure the dropdown works even if API fails
        function addFallbackWilayahData() {
            const wilayahFilter = document.getElementById('wilayahFilter');
            if (wilayahFilter.options.length <= 1) {
                // Only add fallback if dropdown is empty
                wilayahFilter.innerHTML = '<option value="">Semua Wilayah</option>';
                wilayahFilter.innerHTML += '<option value="1">Jakarta</option>';
                wilayahFilter.innerHTML += '<option value="2">Bandung</option>';
                wilayahFilter.innerHTML += '<option value="3">Surabaya</option>';
                console.log('Added fallback wilayah options');
            }
        }

        // Update the renderDosenTable function
        function renderDosenTable() {
            const tableBody = document.getElementById('plotting-table-body');
            tableBody.innerHTML = '';

            // Hitung indeks awal dan akhir untuk halaman saat ini
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredDosen.length);

            // Update text showing count
            document.getElementById('showingCount').textContent =
                filteredDosen.length > 0 ? `${startIndex + 1}-${endIndex}` : '0-0';
            document.getElementById('totalCount').textContent = filteredDosen.length;

            // Jika tidak ada data
            if (filteredDosen.length === 0) {
                tableBody.innerHTML = `
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-5">
                                                            <img src="/assets/img/empty-data.svg" alt="No Data" class="mb-3" style="height: 120px">
                                                            <p class="mb-0">Tidak ada data dosen yang sesuai dengan pencarian</p>
                                                        </td>
                                                    </tr>
                                                `;
                return;
            }

            // Render data untuk halaman saat ini
            for (let i = startIndex; i < endIndex; i++) {
                const dosen = filteredDosen[i];

                // Count of students supervised
                let bimbinganCount = 0;
                if (Array.isArray(dosen.magang_bimbingan)) {
                    bimbinganCount = dosen.magang_bimbingan.length;
                } else if (Array.isArray(dosen.magangBimbingan)) {
                    bimbinganCount = dosen.magangBimbingan.length;
                }

                // Badge for student count with appropriate color
                const bimbinganBadge = bimbinganCount > 0
                    ? `<span class="badge rounded-pill bg-primary badge-count" data-tooltip="${bimbinganCount} mahasiswa bimbingan">${bimbinganCount}</span>`
                    : `<span class="badge rounded-pill bg-light text-dark badge-count" data-tooltip="Belum ada mahasiswa bimbingan">0</span>`;

                // Format skills with a better visual style
                const skillsList = dosen.skills && dosen.skills.length > 0
                    ? dosen.skills.map(s => `<span class="badge-skill">${s.skill.nama_skill}</span>`).join('')
                    : '<span class="text-muted fst-italic">Belum ada</span>';

                // Create row with improved styling
                const row = document.createElement('tr');
                row.innerHTML = `
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input dosen-checkbox" type="checkbox" value="${dosen.id_dosen}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-sm rounded-circle bg-gradient-primary me-3">
                                                                <span class="text-white">${dosen.user?.name.charAt(0) || '?'}</span>
                                                            </div>
                                                            <div>
                                                                <h6 class="dosen-name mb-0">${dosen.user?.name || 'Tidak diketahui'}</h6>
                                                                <p class="dosen-nip mb-0">${dosen.nip || '-'}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>${bimbinganBadge}</td>
                                                    <td>
                                                        <span class="badge badge-wilayah">${dosen.wilayah?.nama_kota || 'Tidak diketahui'}</span>
                                                    </td>
                                                    <td>
                                                        <div class="badge-container">
                                                            ${skillsList}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <button class="btn btn-sm btn-primary" onclick="assignMahasiswa('${dosen.id_dosen}')" 
                                                                data-tooltip="Assign mahasiswa">
                                                                <i class="fas fa-link me-1"></i>Assign
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger" onclick="removeAssignments('${dosen.id_dosen}')"
                                                                data-tooltip="Reset semua penugasan">
                                                                <i class="fas fa-trash me-1"></i>Reset
                                                            </button>
                                                        </div>
                                                    </td>
                                                `;
                tableBody.appendChild(row);
            }

            // Setup checkbox listeners
            setupCheckboxListeners();
        }

        // Setup pagination
        function setupPagination() {
            const pagination = document.getElementById('pagination');
            const totalPages = Math.ceil(filteredDosen.length / itemsPerPage);

            pagination.innerHTML = '';

            // Previous button
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Previous">
                                                                                                                                                                                                    <span aria-hidden="true">&laquo;</span>
                                                                                                                                                                                                </a>`;
            prevLi.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    renderDosenTable();
                    setupPagination();
                }
            });
            pagination.appendChild(prevLi);

            // Page numbers (show max 5 pages)
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                const pageLi = document.createElement('li');
                pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
                pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                pageLi.addEventListener('click', (e) => {
                    e.preventDefault();
                    currentPage = i;
                    renderDosenTable();
                    setupPagination();
                });
                pagination.appendChild(pageLi);
            }

            // Next button
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Next">
                                                                                                                                                                                                    <span aria-hidden="true">&raquo;</span>
                                                                                                                                                                                                </a>`;
            nextLi.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage < totalPages) {
                    currentPage++;
                    renderDosenTable();
                    setupPagination();
                }
            });
            pagination.appendChild(nextLi);
        }

        // Setup checkbox listeners
        function setupCheckboxListeners() {
            // Select all checkbox
            const selectAllCheckbox = document.getElementById('selectAll');
            const dosenCheckboxes = document.querySelectorAll('.dosen-checkbox');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    dosenCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }

            // Check if all checkboxes are checked
            dosenCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    if (selectAllCheckbox) {
                        const allChecked = Array.from(dosenCheckboxes).every(c => c.checked);
                        const anyChecked = Array.from(dosenCheckboxes).some(c => c.checked);

                        selectAllCheckbox.checked = allChecked;
                        selectAllCheckbox.indeterminate = anyChecked && !allChecked;
                    }
                });
            });
        }

        // Filter dosen by name or NIP
        document.getElementById('searchDosen').addEventListener('input', function () {
            searchTerm = this.value.toLowerCase().trim(); // Only update the searchTerm variable
            applyFilters(); // Let applyFilters handle the actual filtering
        });

        // Filter by wilayah
        document.getElementById('wilayahFilter').addEventListener('change', function () {
            selectedWilayahId = this.value; // Only update the selectedWilayahId variable
            applyFilters(); // Let applyFilters handle the actual filtering
        });

        function assignMahasiswa(dosenId) {
            // Set selected dosen ID
            document.getElementById('selectedDosenId').value = dosenId;

            // Show loading in SweetAlert first
            Swal.fire({
                title: 'Memuat Data',
                html: 'Sedang mengambil data mahasiswa yang tersedia...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch available mahasiswa with AJAX
            fetch('/api/magang/available')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Close loading dialog
                    Swal.close();

                    if (data.success) {
                        // Show modal with data
                        const mahasiswaSelect = document.getElementById('mahasiswaSelect');
                        mahasiswaSelect.innerHTML = '';

                        if (data.data.length === 0) {
                            mahasiswaSelect.innerHTML = '<option disabled>Tidak ada mahasiswa yang tersedia</option>';
                            document.getElementById('saveAssignBtn').disabled = true;

                            // Show modal with warning
                            const modal = new bootstrap.Modal(document.getElementById('assignModal'));
                            modal.show();

                            // Show notification about no available students
                            Swal.fire({
                                title: 'Perhatian',
                                text: 'Tidak ada mahasiswa yang tersedia untuk ditugaskan',
                                icon: 'info'
                            });
                        } else {
                            document.getElementById('saveAssignBtn').disabled = false;

                            data.data.forEach(magang => {
                                const option = document.createElement('option');
                                option.value = magang.id_magang;

                                // Handle potential null values with fallbacks
                                const mahasiswaName = magang.mahasiswa?.user?.name || 'Tidak diketahui';
                                const perusahaanName = magang.lowongan?.perusahaan?.nama_perusahaan || 'Tidak diketahui';

                                option.textContent = `${mahasiswaName} - ${perusahaanName}`;
                                mahasiswaSelect.appendChild(option);
                            });

                            // Show the modal after populating data
                            const modal = new bootstrap.Modal(document.getElementById('assignModal'));
                            modal.show();
                        }
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: `Gagal memuat data mahasiswa: ${data.message || 'Unknown error'}`,
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching available magang:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat memuat data mahasiswa: ' + error.message,
                        icon: 'error'
                    });
                });
        }

        document.getElementById('saveAssignBtn').addEventListener('click', function () {
            const dosenId = document.getElementById('selectedDosenId').value;
            const mahasiswaSelect = document.getElementById('mahasiswaSelect');
            const selectedMahasiswaIds = Array.from(mahasiswaSelect.selectedOptions).map(opt => opt.value);

            if (selectedMahasiswaIds.length === 0) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Pilih minimal satu mahasiswa',
                    icon: 'warning'
                });
                return;
            }

            // Disable button to prevent double-clicks
            this.disabled = true;
            this.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Menyimpan...`;

            // AJAX request
            fetch(`/api/dosen/${dosenId}/assign-mahasiswa`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    magang_ids: selectedMahasiswaIds
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close modal first
                        bootstrap.Modal.getInstance(document.getElementById('assignModal')).hide();

                        // Show success message
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Penugasan berhasil disimpan',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Reload data with AJAX instead of page refresh
                        loadPlottingData();
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message || 'Terjadi kesalahan tidak diketahui',
                            icon: 'error'
                        });
                        this.disabled = false;
                        this.textContent = 'Simpan';
                    }
                })
                .catch(error => {
                    console.error('Error saving assignments:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menyimpan penugasan: ' + error.message,
                        icon: 'error'
                    });
                    this.disabled = false;
                    this.textContent = 'Simpan';
                });
        });

        // Remove assignments for dosen
        function removeAssignments(dosenId) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus semua penugasan untuk dosen ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    const resetButtons = document.querySelectorAll(`.btn-outline-danger[onclick*="${dosenId}"]`);
                    resetButtons.forEach(btn => {
                        btn.disabled = true;
                        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Resetting...`;
                    });

                    // AJAX request to remove assignments
                    fetch(`/api/dosen/${dosenId}/assignments`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Penugasan dosen berhasil dihapus',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Reload data with AJAX instead of full page refresh
                                loadPlottingData();
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: data.message || 'Terjadi kesalahan tidak diketahui',
                                    icon: 'error'
                                });

                                // Re-enable buttons
                                resetButtons.forEach(btn => {
                                    btn.disabled = false;
                                    btn.innerHTML = `<i class="fas fa-trash me-1"></i>Reset`;
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error removing assignments:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menghapus penugasan: ' + error.message,
                                icon: 'error'
                            });

                            // Re-enable buttons
                            resetButtons.forEach(btn => {
                                btn.disabled = false;
                                btn.innerHTML = `<i class="fas fa-trash me-1"></i>Reset`;
                            });
                        });
                }
            });
        }

        document.getElementById('autoPlotBtn').addEventListener('click', function () {
            Swal.fire({
                title: 'Konfirmasi Plotting Otomatis',
                text: 'Apakah Anda yakin ingin melakukan plotting otomatis dengan metode SAW? Ini akan mengganti semua plotting manual yang ada.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lakukan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    this.disabled = true;
                    this.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...`;

                    // Show progress with SweetAlert
                    let timerInterval;
                    Swal.fire({
                        title: 'Memproses',
                        html: 'Sedang melakukan plotting otomatis...',
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // AJAX request
                    fetch('/api/plotting/auto', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Reset button state
                            const btn = document.getElementById('autoPlotBtn');
                            btn.disabled = false;
                            btn.innerHTML = `<i class="fas fa-magic me-2"></i>Auto-Plot Dosen`;

                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    html: `<p>Plotting otomatis berhasil dilakukan!</p>
                                                                                                           <div class="mt-3">
                                                                                                             <table class="table table-sm">
                                                                                                               <tr><td>Total Dosen</td><td>${data.stats.total_dosen}</td></tr>
                                                                                                               <tr><td>Total Magang</td><td>${data.stats.total_magang}</td></tr>
                                                                                                               <tr><td>Total Assignments</td><td>${data.stats.total_assignments}</td></tr>
                                                                                                             </table>
                                                                                                           </div>`,
                                    icon: 'success'
                                });

                                // Reload data with AJAX
                                loadPlottingData();
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: data.message || 'Terjadi kesalahan tidak diketahui',
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error during auto-plot:', error);

                            // Reset button state
                            const btn = document.getElementById('autoPlotBtn');
                            btn.disabled = false;
                            btn.innerHTML = `<i class="fas fa-magic me-2"></i>Auto-Plot Dosen`;

                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat melakukan plotting otomatis: ' + error.message,
                                icon: 'error'
                            });
                        });
                }
            });
        });

        // Show matrix visualization
        document.getElementById('showMatrixBtn').addEventListener('click', function () {
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('matrixModal'));
            modal.show();

            // Default load active matrix
            loadMatrixData('active');
        });

        // Add event listeners for the tabs
        document.getElementById('active-tab').addEventListener('click', function () {
            loadMatrixData('active');
        });

        document.getElementById('pending-tab').addEventListener('click', function () {
            loadMatrixData('pending');
        });

        function loadMatrixData(type) {
            // Show loading in appropriate container
            const containerId = type === 'active' ? 'activeMatrixContainer' : 'pendingMatrixContainer';
            document.getElementById(containerId).innerHTML = `
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2">Memuat data matrix keputusan...</p>
                        </div>
                    `;

            // Load matrix data with type parameter
            fetch(`/api/plotting/matrix?type=${type}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderDecisionMatrix(data.data, data.weights, containerId);
                    } else {
                        document.getElementById(containerId).innerHTML = `
                                    <div class="alert alert-danger">
                                        Gagal memuat data matrix: ${data.message}
                                    </div>
                                `;
                    }
                })
                .catch(error => {
                    console.error('Error loading matrix data:', error);
                    document.getElementById(containerId).innerHTML = `
                                <div class="alert alert-danger">
                                    Terjadi kesalahan: ${error.message}
                                </div>
                            `;
                });
        }

        // Function to render decision matrix
        function renderDecisionMatrix(matrixData, weights, containerId) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';

            // Add status badge based on type
            const isPending = containerId === 'pendingMatrixContainer';
            const statusInfo = isPending
                ? `<div class="alert alert-warning mb-3">
                     <i class="fas fa-exclamation-triangle me-2"></i>
                     <strong>Preview Matrix Keputusan</strong> - Magang belum aktif, berguna untuk perencanaan awal.
                   </div>`
                : '';

            // Add weights info
            container.innerHTML = statusInfo + `
                <div class="alert alert-info mb-4">
                    <h6>Bobot Kriteria SAW:</h6>
                    <div class="d-flex gap-3">
                        <div>
                            <i class="fas fa-map-marker-alt me-1"></i> Wilayah: <strong>${weights.wilayah * 100}%</strong>
                        </div>
                        <div>
                            <i class="fas fa-code me-1"></i> Skill: <strong>${weights.skill * 100}%</strong>
                        </div>
                    </div>
                </div>
            `;

            if (matrixData.length === 0) {
                container.innerHTML += `
                    <div class="alert alert-warning">
                        ${isPending
                        ? 'Tidak ada data magang pending yang memerlukan preview plotting.'
                        : 'Tidak ada data magang aktif yang memerlukan plotting.'}
                    </div>
                `;
                return;
            }

            matrixData.forEach(data => {
                const card = document.createElement('div');
                card.className = 'card mb-4';
                card.id = `matrix-${data.magang_id}`;

                let dosenScoresHtml = '';
                data.dosen_scores.forEach((score, index) => {
                    const isBest = index === 0; // First dosen is the best match
                    const isCurrentDosen = score.is_current;

                    let statusBadge = '';
                    if (isCurrentDosen) {
                        statusBadge = '<span class="badge bg-success ms-2">Current</span>';
                    } else if (isBest) {
                        statusBadge = '<span class="badge bg-primary ms-2">Best Match</span>';
                    }

                    let matchedSkillsHtml = '';
                    if (score.matched_skills.length > 0) {
                        matchedSkillsHtml = `
                            <div class="mt-1">
                                <small class="text-muted">Skills yang cocok:</small><br>
                                ${score.matched_skills.map(skill => `<span class="badge bg-success me-1">${skill}</span>`).join('')}
                            </div>
                        `;
                    }

                    // Different action button based on whether it's pending or active
                    let actionButton = '';
                    if (isPending && !isCurrentDosen) {
                        actionButton = `
                            <button class="btn btn-sm ${isBest ? 'btn-primary' : 'btn-outline-secondary'} mt-2" 
                                    onclick="assignFromPreview('${data.magang_id}', '${score.dosen_id}')">
                                <i class="fas fa-check-circle me-1"></i> Assign
                            </button>
                        `;
                    } else if (isCurrentDosen) {
                        actionButton = `
                            <button class="btn btn-sm btn-outline-success mt-2" disabled>
                                <i class="fas fa-check me-1"></i> Terassign
                            </button>
                        `;
                    }

                    dosenScoresHtml += `
                        <div class="card ${isCurrentDosen ? 'border-success' : isBest ? 'border-primary' : ''} mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">${score.dosen_name} ${statusBadge}</h6>
                                        <small class="text-muted">${score.nip}</small>
                                    </div>
                                    <span class="badge ${isBest ? 'bg-primary' : 'bg-secondary'} fs-6">${(score.total_score * 100).toFixed(1)}%</span>
                                </div>

                                <div class="mt-3">
                                    <label class="d-flex justify-content-between">
                                        <small>Wilayah (${weights.wilayah * 100}%)</small>
                                        <small>${score.wilayah_score}/1</small>
                                    </label>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar" role="progressbar" 
                                            style="width: ${score.wilayah_score * 100}%; background-color: #3498db;" 
                                            title="Wilayah Score"></div>
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <label class="d-flex justify-content-between">
                                        <small>Skill (${weights.skill * 100}%)</small>
                                        <small>${score.skill_score.toFixed(2)}/1</small>
                                    </label>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                            style="width: ${score.skill_score * 100}%;" 
                                            title="Skill Score"></div>
                                    </div>
                                </div>

                                ${matchedSkillsHtml}
                                ${actionButton}
                            </div>
                        </div>
                    `;
                });

                let mahasiswaSkillsHtml = '';
                if (data.mahasiswa_skills && data.mahasiswa_skills.length > 0) {
                    mahasiswaSkillsHtml = `
                        <div class="mt-2">
                            ${data.mahasiswa_skills.map(skill => `<span class="badge bg-secondary me-1">${skill.name}</span>`).join('')}
                        </div>
                    `;
                }

                // Add quick action for pending matrix
                let quickActionHtml = '';
                if (isPending) {
                    const bestDosenScore = data.dosen_scores[0];
                    quickActionHtml = `
                        <div class="mt-3">
                            <button class="btn btn-sm btn-primary" onclick="assignFromPreview('${data.magang_id}', '${bestDosenScore.dosen_id}')">
                                <i class="fas fa-check-circle me-1"></i> Assign ke ${bestDosenScore.dosen_name}
                            </button>
                            <button class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="collapse" data-bs-target="#otherOptions${data.magang_id}">
                                <i class="fas fa-list me-1"></i> Pilih Dosen Lain
                            </button>
                        </div>
                        <div class="collapse mt-2" id="otherOptions${data.magang_id}">
                            <div class="card card-body bg-light py-2">
                                <div class="mb-2"><strong>Pilih Dosen Lain:</strong></div>
                                <div class="d-flex flex-wrap gap-2">
                                    ${data.dosen_scores.slice(1, 5).map(score => `
                                        <button class="btn btn-sm btn-outline-secondary" onclick="assignFromPreview('${data.magang_id}', '${score.dosen_id}')">
                                            ${score.dosen_name} (${(score.total_score * 100).toFixed(1)}%)
                                        </button>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    `;
                }

                card.innerHTML = `
                    <div class="card-header ${isPending ? 'bg-warning bg-opacity-10' : ''}">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                ${data.mahasiswa_name}
                                ${isPending ? '<span class="badge bg-warning text-dark ms-2">Pending</span>' : ''}
                            </h5>
                            <span class="badge bg-info">${data.perusahaan_name}</span>
                        </div>
                        <div class="text-muted mt-1">
                            <small><i class="fas fa-map-marker-alt me-1"></i> ${data.wilayah_name}</small>
                        </div>
                        ${mahasiswaSkillsHtml}
                    </div>
                    <div class="card-body">
                        <h6>Ranking Dosen Berdasarkan Kecocokan:</h6>
                        ${dosenScoresHtml}
                        ${isPending ? quickActionHtml : ''}
                    </div>
                `;

                container.appendChild(card);
            });
        }

        // Load data when page is loaded
        document.addEventListener('DOMContentLoaded', function () {
            loadPlottingData();
        });

        // Function to assign dosen directly from preview matrix
        function assignFromPreview(magangId, dosenId) {
            Swal.fire({
                title: 'Konfirmasi Penugasan',
                text: 'Apakah Anda yakin ingin menugaskan dosen ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tugaskan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        didOpen: () => {
                            Swal.showLoading()
                        },
                        allowOutsideClick: false
                    });

                    // Send assignment request
                    fetch(`/api/dosen/assign-pending-magang`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            magang_id: magangId,
                            dosen_id: dosenId
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Dosen berhasil ditugaskan',
                                    icon: 'success'
                                });

                                // Reload the matrix data
                                loadMatrixData('pending');
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: data.message || 'Terjadi kesalahan',
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menugaskan dosen',
                                icon: 'error'
                            });
                        });
                }
            });
        }
    </script>
@endpush
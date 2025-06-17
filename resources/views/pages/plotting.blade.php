@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

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
                                    <button class="btn btn-sm btn-outline-secondary border-0" type="button"
                                        id="clearSearch" style="display:none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Remove or comment out wilayah filter section
                                                <div class="col-md-6">
                                                    <label class="form-label d-flex align-items-center">
                                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                                        <span>Filter Wilayah</span>
                                                    </label>
                                                    <select class="form-select" id="wilayahFilter">
                                                        <option value="">Semua Wilayah</option>
                                                        <!-- Will be populated dynamically
                                                    </select>
                                                </div>
                                                -->
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
                    <h5 class="modal-title" id="matrixModalLabel">
                        <i class="fas fa-chart-bar me-2"></i>Matrix Keputusan Pembimbing
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="matrixContainer">
                        <!-- Matrix content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="loadMatrixData()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh Data
                    </button>
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
        document.getElementById('searchDosen').addEventListener('input', function() {
            const clearBtn = document.getElementById('clearSearch');
            if (this.value) {
                clearBtn.style.display = 'block';
            } else {
                clearBtn.style.display = 'none';
            }
        });

        document.getElementById('clearSearch').addEventListener('click', function() {
            document.getElementById('searchDosen').value = '';
            searchTerm = '';
            applyFilters();
            this.style.display = 'none';
        });

        // Add hover effects for buttons
        document.addEventListener('DOMContentLoaded', function() {
            // Initial load
            loadPlottingData();

            // Add event delegation for tooltip behavior
            document.body.addEventListener('mouseover', function(e) {
                if (e.target.closest('[data-tooltip]')) {
                    const tooltip = e.target.closest('[data-tooltip]');
                    tooltip.classList.add('tooltip-active');
                }
            });

            document.body.addEventListener('mouseout', function(e) {
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
                        // Remove this line since we don't need wilayah data anymore
                        // loadWilayahData(); 
                    } else {
                        throw new Error(data.message || 'Unknown error');
                    }
                })
                .catch(error => {
                    console.error('Error loading plotting data:', error);
                    document.getElementById('plotting-table-body').innerHTML =
                        `
                                                                                                                            <tr>
                                                                                                                                <td colspan="5" class="text-center text-danger">
                                                                                                                                    Gagal memuat data: ${error.message}
                                                                                                                                </td>
                                                                                                                            </tr>
                                                                                                                        `;
                });
        }

        // 1. Add a helper function to check if elements exist before accessing them
        function elementExists(id) {
            return document.getElementById(id) !== null;
        }

        // 2. Update the loadWilayahData function
        function loadWilayahData() {
            console.log('Fetching wilayah data...');

            // Skip this function entirely since wilayahFilter no longer exists
            if (!elementExists('wilayahFilter')) {
                console.log('wilayahFilter element does not exist, skipping wilayah data load');
                return;
            }

            // Rest of the function (will never execute if the element doesn't exist)
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

        // 3. Fix the addFallbackWilayahData function
        function addFallbackWilayahData() {
            // Check if element exists first
            if (!elementExists('wilayahFilter')) {
                console.log('wilayahFilter element does not exist, skipping fallback data');
                return;
            }

            // Rest of the function (will never execute if the element doesn't exist)
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

        // 4. Fix the applyFilters function to not use wilayahFilter
        function applyFilters() {
            // Apply only search filter (without wilayah filter)
            filteredDosen = allDosen.filter(dosen => {
                const userName = typeof dosen.name === 'string' ? dosen.name : '';
                const nipValue = typeof dosen.nip === 'string' ? dosen.nip : '';

                return !searchTerm ||
                    userName.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    nipValue.toLowerCase().includes(searchTerm.toLowerCase());
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

            if (searchTerm) {
                let statusText = `Menampilkan ${filteredDosen.length} dari ${allDosen.length} dosen`;

                if (searchTerm) {
                    statusText += ` (Filter: "${searchTerm}")`;
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

        // 6. Fix the resetFilters function
        function resetFilters() {
            document.getElementById('searchDosen').value = '';
            // Remove reference to wilayahFilter
            // document.getElementById('wilayahFilter').value = '';
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

        // Update the renderDosenTable function
        function renderDosenTable() {
            const tableBody = document.getElementById('plotting-table-body');
            tableBody.innerHTML = '';

            // Calculate start and end indices for current page (unchanged)
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredDosen.length);

            document.getElementById('showingCount').textContent =
                filteredDosen.length > 0 ? `${startIndex + 1}-${endIndex}` : '0-0';
            document.getElementById('totalCount').textContent = filteredDosen.length;

            // If no data
            if (filteredDosen.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <img src="/assets/img/empty-data.svg" alt="No Data" class="mb-3" style="height: 120px">
                            <p class="mb-0">Tidak ada data dosen yang sesuai dengan pencarian</p>
                        </td>
                    </tr>
                `;
                return;
            }

            // Render data for current page
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
                const bimbinganBadge = bimbinganCount > 0 ?
                    `<span class="badge rounded-pill bg-primary badge-count" data-tooltip="${bimbinganCount} mahasiswa bimbingan">${bimbinganCount}</span>` :
                    `<span class="badge rounded-pill bg-light text-dark badge-count" data-tooltip="Belum ada mahasiswa bimbingan">0</span>`;

                // Format skills with a better visual style
                const skillsList = dosen.skills && dosen.skills.length > 0 ?
                    dosen.skills.map(s => `<span class="badge-skill">${s.skill.nama_skill}</span>`).join('') :
                    '<span class="text-muted fst-italic">Belum ada</span>';

                // Create row with improved styling - FIXED NAME ACCESS and REMOVED WILAYAH COLUMN
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
                                <span class="text-white">${(dosen.name ? dosen.name.charAt(0) : '?')}</span>
                            </div>
                            <div>
                                <h6 class="dosen-name mb-0">${dosen.name || 'Tidak diketahui'}</h6>
                                <p class="dosen-nip mb-0">${dosen.nip || '-'}</p>
                            </div>
                        </div>
                    </td>
                    <td>${bimbinganBadge}</td>
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
            prevLi.innerHTML =
                `<a class="page-link" href="#" aria-label="Previous">
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
            nextLi.innerHTML =
                `<a class="page-link" href="#" aria-label="Next">
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
                selectAllCheckbox.addEventListener('change', function() {
                    dosenCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }

            // Check if all checkboxes are checked
            dosenCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
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
        document.getElementById('searchDosen').addEventListener('input', function() {
            searchTerm = this.value.toLowerCase().trim(); // Only update the searchTerm variable
            applyFilters(); // Let applyFilters handle the actual filtering
        });

        // 5. Fix the event listener for wilayahFilter
        // Comment out or remove this code:
        /*
        document.getElementById('wilayahFilter').addEventListener('change', function () {
            selectedWilayahId = this.value; 
            applyFilters();
        });
        */

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

            // Fetch available mahasiswa with AJAX - ADD CSRF AND HEADERS
            fetch('/api/magang/available', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        'Cache-Control': 'no-store, no-cache'
                    },
                    credentials: 'same-origin' // Penting untuk mengirim cookies otentikasi
                })
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

                            data.data.forEach(item => {
                                const option = document.createElement('option');
                                option.value = item.id_magang; // This is actually id_lamaran with alias

                                // Handle potential null values with fallbacks
                                const mahasiswaName = item.name || 'Tidak diketahui';
                                const perusahaanName = item.nama_perusahaan || 'Tidak diketahui';

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

        document.getElementById('saveAssignBtn').addEventListener('click', function() {
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

            // AJAX request - notice this endpoint should handle t_lamaran IDs (aliased as magang_ids)
            fetch(`/api/dosen/${dosenId}/assign-mahasiswa`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        magang_ids: selectedMahasiswaIds // These are actually t_lamaran.id_lamaran values
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
                        btn.innerHTML =
                            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Resetting...`;
                    });

                    // AJAX request to remove assignments
                    fetch(`/api/dosen/${dosenId}/assignments`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
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

        document.getElementById('autoPlotBtn').addEventListener('click', function() {
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
                    this.innerHTML =
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...`;

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
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
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
                                                                                                                                                                    
                                                                                                                                                                               <tr><td>Total Assignments</td><td>${data.stats.total_assignments}</td></tr>
                                                                                                                                                                             </table>
                                                                                                                                                                           </div>`,
                                    icon: 'success'
                                });

                                // Reload data with AJAX - ADD THIS FUNCTION CALL
                                loadPlottingData();

                                // Also reload matrix data in case the matrix modal is open
                                loadMatrixData();
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
                                text: 'Terjadi kesalahan saat melakukan plotting otomatis: ' +
                                    error.message,
                                icon: 'error'
                            });
                        });
                }
            });
        });

        // Show matrix visualization
        document.getElementById('showMatrixBtn').addEventListener('click', function() {
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('matrixModal'));
            modal.show();

            // Load matrix data for inactive unassigned internships
            loadMatrixData();
        });

        // Modify the loadMatrixData function to also load wilayah data if not already loaded
        function loadMatrixData() {
            // Show loading state
            document.getElementById('matrixContainer').innerHTML = `
                                                                    <div class="text-center py-4">
                                                                        <div class="d-flex justify-content-center">
                                                                            <div class="spinner-grow text-primary mx-1" role="status" style="width: 1rem; height: 1rem;"></div>
                                                                            <div class="spinner-grow text-secondary mx-1" role="status" style="width: 1rem; height: 1rem; animation-delay: 0.2s"></div>
                                                                            <div class="spinner-grow text-info mx-1" role="status" style="width: 1rem; height: 1rem; animation-delay: 0.4s"></div>
                                                                        </div>
                                                                        <p class="mt-3 text-muted">Memuat data matrix keputusan...</p>
                                                                    </div>
                                                                `;

            // Check if we already have wilayah data, if not load it
            if (!window.allWilayah || window.allWilayah.length === 0) {
                // Load wilayah data first
                fetch('/api/wilayah')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.allWilayah = data.data;
                            // Now load the matrix data
                            loadMatrixDataFromAPI();
                        }
                    })
                    .catch(error => {
                        console.error('Error loading wilayah data:', error);
                        // Continue anyway to load matrix data
                        loadMatrixDataFromAPI();
                    });
            } else {
                // We already have wilayah data, just load matrix data
                loadMatrixDataFromAPI();
            }
        }

        // Extract the matrix data loading to a separate function
        function loadMatrixDataFromAPI() {
            const timestamp = new Date().getTime();
            fetch(`/api/plotting/matrix-decision?_=${timestamp}`, {
                    headers: {
                        'Cache-Control': 'no-cache, no-store, must-revalidate',
                        'Pragma': 'no-cache',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Server responded with status: ${response.status}`);
                    }
                    return response.json();
                })
                .then((data) => { // Added parentheses around 'data' parameter here
                    console.log(`Matrix data loaded:`, data);

                    if (data.success) {
                        renderDecisionMatrix(data.data, data.weights);
                    } else {
                        document.getElementById('matrixContainer').innerHTML = `
                                                                                <div class="alert alert-danger">
                                                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                                                    Gagal memuat data matrix: ${data.message}
                                                                                    <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadMatrixData()">
                                                                                        <i class="fas fa-sync-alt me-1"></i>Coba Lagi
                                                                                    </button>
                                                                                </div>
                                                                            `;
                    }
                })
                .catch(error => {
                    console.error('Error loading matrix data:', error);
                    document.getElementById('matrixContainer').innerHTML = `
                                                                            <div class="alert alert-danger">
                                                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                                                Terjadi kesalahan: ${error.message}
                                                                                <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadMatrixData()">
                                                                                    <i class="fas fa-sync-alt me-1"></i>Coba Lagi
                                                                                </button>
                                                                            </div>
                                                                        `;
                });
        }

        // Function to render decision matrix with improved UI
        function renderDecisionMatrix(matrixData, weights) {
            const container = document.getElementById('matrixContainer');
            container.innerHTML = '';

            // Add summary header with counts and improved design
            const studentCount = Array.isArray(matrixData) ? matrixData.length : 0;

            // Header card with summary data
            const summaryHeader = `
                                                     <div class="card mb-4 border-0 shadow-sm">
                                            <div class="card-body p-0">
                                                <div class="row g-0">
                                                    <div class="col-md-8 bg-gradient-secondary p-4 text-white" style="background-color: #64748b;">
                                                        <h5 class="mb-1 fw-bold"><i class="fas fa-chart-bar me-2"></i>Matrix Keputusan Pembimbing</h5>
                                                        <p class="mb-0 opacity-8">Pemberian rekomendasi dosen pembimbing menggunakan metode SAW</p>
                                                    </div>
                                                    <div class="col-md-4 p-4 d-flex flex-column justify-content-center align-items-center bg-light">
                                                        <div class="text-center">
                                                            <h2 class="display-4 fw-bold mb-0" style="color: #64748b;">${studentCount}</h2>
                                                            <span class="text-secondary">Mahasiswa belum memiliki pembimbing</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
            container.insertAdjacentHTML('beforeend', summaryHeader);

            // Add weights visualization for SAW criteria
            if (weights) {
                const minatWeight = Math.round((weights.minat || 0) * 100);
                const skillWeight = Math.round((weights.skill || 0) * 100);
                const bebanKerjaWeight = Math.round((weights.beban_kerja || 0) * 100);

                const weightsCard = `
                                                        <div class="card mb-4 shadow-sm border-0">
                                        <div class="card-header bg-white py-3">
                                            <h6 class="mb-0 fw-bold d-flex align-items-center">
                                                <div class="icon-circle me-2" 
                                                     style="width: 28px; height: 28px; border-radius: 50%; display: flex; 
                                                            align-items: center; justify-content: center; background-color: #f1f5f9; color: #64748b;">
                                                    <i class="fas fa-balance-scale"></i>
                                                </div>
                                                Kriteria dan Bobot SAW (Simple Additive Weighting)
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-12">
                                                <!-- Minat Criterion -->
                                                <div class="col-md-4">
                                                    <div class="card border-0 shadow-sm h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="icon-circle me-3" 
                                                                     style="background-color: var(--color-minat-light); color: var(--color-minat);
                                                                            width: 42px; height: 42px;">
                                                                    <i class="fas fa-star"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0 fw-bold" style="color: var(--color-minat);">Minat</h6>
                                                                    <p class="text-muted small mb-0">Kesamaan minat dosen dan mahasiswa</p>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span class="text-dark">Bobot</span>
                                                                <span class="badge px-3 py-2 rounded-pill" style="background-color: var(--color-minat-light); color: var(--color-minat);">${minatWeight}%</span>
                                                            </div>
                                                            <div class="progress" style="height: 8px; border-radius: 10px; background-color: #f8fafc;">
                                                                <div class="progress-bar" role="progressbar" 
                                                                     style="width: ${minatWeight}%; border-radius: 10px; background-color: var(--color-minat);" 
                                                                     aria-valuenow="${minatWeight}" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Skill Criterion -->
                                                <div class="col-md-4">
                                                    <div class="card border-0 shadow-sm h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="icon-circle me-3" 
                                                                     style="background-color: var(--color-skill-light); color: var(--color-skill);
                                                                            width: 42px; height: 42px;">
                                                                    <i class="fas fa-code"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0 fw-bold" style="color: var(--color-skill);">Skill</h6>
                                                                    <p class="text-muted small mb-0">Kecocokan skill dosen dan mahasiswa</p>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span class="text-dark">Bobot</span>
                                                                <span class="badge px-3 py-2 rounded-pill" style="background-color: var(--color-skill-light); color: var(--color-skill);">${skillWeight}%</span>
                                                            </div>
                                                            <div class="progress" style="height: 8px; border-radius: 10px; background-color: #f8fafc;">
                                                                <div class="progress-bar" role="progressbar"
                                                                     style="width: ${skillWeight}%; border-radius: 10px; background-color: var(--color-skill);" 
                                                                     aria-valuenow="${skillWeight}" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Beban Kerja Criterion -->
                                                <div class="col-md-4">
                                                    <div class="card border-0 shadow-sm h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="icon-circle me-3" 
                                                                     style="background-color: var(--color-beban-light); color: var(--color-beban);
                                                                            width: 42px; height: 42px;">
                                                                    <i class="fas fa-users"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0 fw-bold" style="color: var(--color-beban);">Beban Kerja</h6>
                                                                    <p class="text-muted small mb-0">Jumlah mahasiswa bimbingan</p>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span class="text-dark">Bobot</span>
                                                                <span class="badge px-3 py-2 rounded-pill" style="background-color: var(--color-beban-light); color: var(--color-beban);">${bebanKerjaWeight}%</span>
                                                            </div>
                                                            <div class="progress" style="height: 8px; border-radius: 10px; background-color: #f8fafc;">
                                                                <div class="progress-bar" role="progressbar"
                                                                     style="width: ${bebanKerjaWeight}%; border-radius: 10px; background-color: var(--color-beban);" 
                                                                     aria-valuenow="${bebanKerjaWeight}" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                container.insertAdjacentHTML('beforeend', weightsCard);
            }

            // Check if matrixData is valid
            if (!Array.isArray(matrixData) || matrixData.length === 0) {
                container.insertAdjacentHTML('beforeend', `
                                                        <div class="card shadow-sm border-0">
                                                            <div class="card-body text-center py-5">
                                                                <img src="/assets/img/empty-data.svg" alt="No Data" style="height: 180px; opacity: 0.8;" 
                                                                     onerror="this.src='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/icons/exclamation-circle.svg'; this.style.height='80px';">
                                                                <h5 class="mt-4 text-muted">Tidak Ada Data</h5>
                                                                <p class="text-muted">Tidak ada mahasiswa nonaktif yang membutuhkan penugasan dosen pembimbing.</p>
                                                                <button class="btn btn-outline-primary mt-2" onclick="loadMatrixData()">
                                                                    <i class="fas fa-sync-alt me-2"></i>Refresh Data
                                                                </button>
                                                            </div>
                                                        </div>
                                                    `);
                return;
            }

            // Section title for recommendations
            container.insertAdjacentHTML('beforeend', `
                                                    <div class="d-flex align-items-center mb-3 mt-4">
                                                        <div class="icon-circle bg-primary bg-opacity-10 text-primary me-2" 
                                                             style="width: 32px; height: 32px; border-radius: 50%; display: flex; 
                                                                    align-items: center; justify-content: center;">
                                                        <i class="fas fa-user-check"></i>
                                                        </div>
                                                        <h5 class="mb-0 fw-bold">Rekomendasi Dosen Pembimbing</h5>
                                                    </div>
                                                `);

            // Create row for student cards
            const studentCardsRow = document.createElement('div');
            studentCardsRow.className = 'row g-4';

            // Process each mahasiswa
            matrixData.forEach((item) => {
                // Create column for this student card
                const col = document.createElement('div');
                col.className = 'col-md-6 col-lg-4';

                // Get top 3 dosen matches
                const topDosen = item.dosen_scores.slice(0, 3);

                // Build dosen match list HTML
                let dosenMatchesHtml = '<div class="list-group list-group-flush mb-3">';

                // Process each of the top matches
                topDosen.forEach((match, idx) => {
                    // Calculate match percentage
                    const matchPercent = Math.round(match.total_score * 100);
                    let badgeColor = 'danger';

                    if (matchPercent >= 80) {
                        badgeColor = 'success';
                    } else if (matchPercent >= 60) {
                        badgeColor = 'info';
                    } else if (matchPercent >= 40) {
                        badgeColor = 'warning';
                    }

                    // Add highlight for best match
                    const isTopMatch = idx === 0;

                    // Format matched minat display with new style
                    let matchedMinatHtml = '';
                    if (match.matched_minat && match.matched_minat.length > 0) {
                        matchedMinatHtml = `
                        <div class="mt-2">
                            <small class="text-muted d-block mb-1">Minat yang cocok:</small>
                            <div class="d-flex flex-wrap gap-1">
                                ${match.matched_minat.map(minat =>
                            `<span class="badge-minat">${minat}</span>`
                        ).join('')}
                            </div>
                        </div>
                    `;
                    }

                    // Format matched skills display with new style
                    let matchedSkillsHtml = '';
                    if (match.matched_skills && match.matched_skills.length > 0) {
                        matchedSkillsHtml = `
                        <div class="mt-2">
                            <small class="text-muted d-block mb-1">Skill yang cocok:</small>
                            <div class="d-flex flex-wrap gap-1">
                                ${match.matched_skills.map(skill =>
                            `<span class="badge-skill">${skill}</span>`
                        ).join('')}
                            </div>
                        </div>
                    `;
                    }

                    // Add this lecturer match to the HTML with new styling
                    dosenMatchesHtml += `
                    <div class="list-group-item p-3 ${isTopMatch ? 'top-match' : ''} position-relative">
                        <div class="d-flex mb-2">
                            <div class="me-3 position-relative">
                                <div class="circle-match circle-match-${badgeColor}" style="--percent: ${matchPercent}%">
                                    <span>${matchPercent}%</span>
                                </div>
                                ${isTopMatch ? '<span class="position-absolute top-0 start-100 translate-middle badge top-badge rounded-pill">Top</span>' : ''}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">${match.dosen_name}</h6>
                                <small class="text-muted">${match.nip || '-'}</small>
                            </div>
                        </div>

                        <div class="mt-3 px-2">
                            <div class="row g-2">
                                <!-- Minat score -->
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="fw-medium">Minat <span class="text-muted">(${Math.round(weights.minat * 100)}%)</span></small>
                                        <small>${Math.round(match.minat_score * 100)}%</small>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-minat" role="progressbar" 
                                            style="width: ${match.minat_score * 100}%" 
                                            aria-valuenow="${match.minat_score * 100}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>

                                <!-- Skill score -->
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="fw-medium">Skill <span class="text-muted">(${Math.round(weights.skill * 100)}%)</span></small>
                                        <small>${Math.round(match.skill_score * 100)}%</small>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-skill" role="progressbar" 
                                            style="width: ${match.skill_score * 100}%" 
                                            aria-valuenow="${match.skill_score * 100}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>

                                <!-- Beban Kerja score -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="fw-medium">Beban Kerja <span class="text-muted">(${Math.round(weights.beban_kerja * 100)}%)</span></small>
                                        <small title="${match.current_beban} mahasiswa bimbingan">${Math.round(match.beban_kerja_score * 100)}%</small>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-beban" role="progressbar" 
                                            style="width: ${match.beban_kerja_score * 100}%" 
                                            aria-valuenow="${match.beban_kerja_score * 100}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Matched minat display -->
                        ${matchedMinatHtml}

                        <!-- Matched skills display -->
                        ${matchedSkillsHtml}

                        <!-- Current workload info -->
                        <div class="mt-2">
                            <small class="text-muted d-flex align-items-center">
                                <i class="fas fa-users me-1" style="color: var(--color-beban);"></i>
                                Beban Kerja: 
                                <span class="badge ${match.current_beban > 5 ? 'bg-danger' : 'bg-secondary'} bg-opacity-75 ms-1">
                                    ${match.current_beban} mahasiswa
                                </span>
                            </small>
                        </div>

                        <!-- Assign button for top match -->
      ${isTopMatch ? `
                            <div class="text-center mt-3">
                                <button class="btn btn-sm w-100 text-white" style="background-color: var(--color-primary);" onclick="assignDosenToMahasiswa('${item.id_lamaran}', '${match.dosen_id}')">
                                    <i class="fas fa-user-check me-1"></i> Assign Dosen Ini
                                </button>
                            </div>
                        ` : ''}
                    </div>
                `;
                });

                // Close the list group
                dosenMatchesHtml += '</div>';

                // Create the student card with improved UI
                col.innerHTML = `
                                                        <div class="card h-100 shadow-sm border-0 overflow-hidden">
                                                            <div class="card-header bg-white p-0">
                                                                <div class="d-flex">
                                                                    <div class="bg-gradient-primary text-white p-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                                                        <span class="fw-bold h4 mb-0">${item.mahasiswa_name.charAt(0).toUpperCase()}</span>
                                                                    </div>
                                                                    <div class="p-3">
                                                                        <h6 class="card-title mb-0 fw-bold">${item.mahasiswa_name}</h6>
                                                                        <span class="badge bg-warning text-dark rounded-pill">NONAKTIF</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body pt-0">
                                                                <!-- Student info section -->
                                                                <div class="info-section mb-3">
                                                                    <!-- Company info -->
                                                                    <div class="d-flex py-2 border-bottom">
                                                                        <div class="icon-box me-3">
                                                                            <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                                                                                <i class="fas fa-building"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div>
                                                                            <small class="text-muted d-block">Perusahaan</small>
                                                                            <div class="fw-semibold">${item.perusahaan_name}</div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Wilayah info -->
                                                                    <div class="d-flex py-2">
                                                                        <div class="icon-box me-3">
                                                                            <div class="icon-circle bg-danger bg-opacity-10 text-danger">
                                                                                <i class="fas fa-map-marker-alt"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div>
                                                                            <small class="text-muted d-block">Wilayah</small>
                                                                            <div>
                                                                                <span class="badge bg-dark bg-opacity-75 text-white rounded-pill">
                                                                                    ${item.wilayah_name}
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <h6 class="fw-bold mb-3 d-flex align-items-center border-top pt-3">
                                                                    <i class="fas fa-star me-2 text-warning"></i> Rekomendasi Dosen
                                                                </h6>

                                                                <!-- Insert the dosen matches HTML we created -->
                                                                ${dosenMatchesHtml}
                                                            </div>
                                                        </div>
                                                    `;

                // Add the student card to the row
                studentCardsRow.appendChild(col);
            });

            // Add student cards row to container
            container.appendChild(studentCardsRow);

            // Add a little CSS to make the circle matches display properly
            const style = document.createElement('style');
            style.textContent = `
                                           :root {
                        /* Warna Utama - Earth tones */
                        --color-primary: #4b6043;      /* Moss green - calming nature tone */
                        --color-secondary: #7d8e7a;    /* Sage - lighter variant */
                        --color-accent: #a9b7a1;       /* Light sage - subtle accent */

                        /* Kriteria Colors */
                        --color-minat: #546e7a;        /* Slate blue gray - soft and calm */
                        --color-minat-light: #eceff1;  /* Very light blue gray */
                        --color-minat-border: #cfd8dc; /* Light blue gray border */

                        --color-skill: #5d6e5d;        /* Muted forest green */
                        --color-skill-light: #eef3ee;  /* Very light green */
                        --color-skill-border: #d1dcd1; /* Light green border */

                        --color-beban: #73605b;        /* Muted brown - earthy tone */
                        --color-beban-light: #f3efee;  /* Very light brown */
                        --color-beban-border: #e0d6d3; /* Light brown border */

                        /* Score Colors - natural, muted tones */
                        --score-excellent: #667c66;    /* Muted green - natural and calming */
                        --score-good: #607d8b;         /* Blue gray - professional and calm */
                        --score-medium: #8d7e6b;       /* Taupe - warm neutral */
                        --score-low: #96665c;          /* Muted terracotta - earthy but not alarming */

                        /* Background tones */
                        --bg-highlight: #f9f8f5;       /* Off-white cream - warm and soft */
                        --bg-card: #ffffff;            /* Clean white */
                        --bg-section: #f7f7f7;         /* Subtle light gray */
                    }

                    /* Card styling */
                    .list-group-item {
                        border-color: #f0f0f0;
                        transition: all 0.2s ease;
                    }

                    .list-group-item:hover {
                        background-color: var(--bg-highlight);
                    }

                    /* Score circle with more refined styling */
                    .circle-match {
                        width: 48px;
                        height: 48px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        position: relative;
                        font-weight: 600;
                        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
                        background: conic-gradient(
                            var(--color) var(--percent),
                            #f7f7f7 var(--percent) 100%
                        );
                    }

                    .circle-match::before {
                        content: "";
                        position: absolute;
                        width: 36px;
                        height: 36px;
                        background: white;
                        border-radius: 50%;
                    }

                    .circle-match span {
                        position: relative;
                        z-index: 1;
                        font-size: 12px;
                        color: #454545;
                    }

                    /* Refined match circles */
                    .circle-match-success {
                        --color: var(--score-excellent);
                    }

                    .circle-match-info {
                        --color: var(--score-good);
                    }

                    .circle-match-warning {
                        --color: var(--score-medium);
                    }

                    .circle-match-danger {
                        --color: var(--score-low);
                    }

                    /* Top match styling */
                    .top-match {
                        background-color: rgba(247, 249, 245, 0.7) !important;
                        border-left: 3px solid var(--score-excellent) !important;
                    }

                    /* Progress bars - more subtle and refined */
                    .progress {
                        height: 6px !important;
                        background-color: #f0f0f0 !important;
                        border-radius: 10px !important;
                        overflow: hidden;
                    }

                    /* Progress bar colors */
                    .progress-bar-minat {
                        background-color: var(--color-minat) !important;
                    }

                    .progress-bar-skill {
                        background-color: var(--color-skill) !important;
                    }

                    .progress-bar-beban {
                        background-color: var(--color-beban) !important;
                    }

                    /* Badge styling */
                    .badge-skill, .badge-minat {
                        font-weight: 500;
                        font-size: 0.75rem;
                        border-radius: 4px;
                        padding: 0.35em 0.65em;
                    }

                    .badge-minat {
                        color: var(--color-minat);
                        background-color: var(--color-minat-light);
                        border: 1px solid var(--color-minat-border);
                    }

                    .badge-skill {
                        color: var(--color-skill);
                        background-color: var(--color-skill-light);
                        border: 1px solid var(--color-skill-border);
                    }

                    /* Clean up header styles */
                    .card-header {
                        background-color: var(--bg-card);
                        border-bottom: 1px solid rgba(0,0,0,0.05);
                    }

                    /* Top badge */
                    .top-badge {
                        background-color: var(--color-primary) !important;
                        font-weight: 500;
                    }
                `;
            document.head.appendChild(style);
        }

        function assignDosenToMahasiswa(lamaranId, dosenId) {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                html: 'Sedang menetapkan dosen pembimbing',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Use the existing API endpoint with CORRECTED PARAMETER NAME
            fetch(`/api/magang/assign-dosen/${lamaranId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        dosen_id: dosenId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Dosen pembimbing berhasil ditugaskan',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        // Reload matrix data
                        loadMatrixData();
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message || 'Gagal menetapkan dosen pembimbing',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menetapkan dosen pembimbing',
                        icon: 'error'
                    });
                });
        }
    </script>
@endpush

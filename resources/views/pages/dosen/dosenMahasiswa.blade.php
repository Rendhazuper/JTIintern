@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Mahasiswa Bimbingan'])

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('dosen.request.bimbingan') }}" class="btn btn-primary"
                style="background: #5988FF; border: none;">
                <i class="fas fa-plus me-2"></i>Request Bimbingan
            </a>
        </div>

        <!-- Search and Filter Section -->
        <div class="mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text border-end-0 bg-white">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0"
                            placeholder="Cari Mahasiswa" style="font-family: 'Open Sans', sans-serif;">
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="periodeFilter" class="form-select">
                        <option value="">Semua Periode</option>
                        <!-- Will be populated by JavaScript -->
                    </select>
                </div>
            </div>
        </div>

        <!-- Cards Container -->
        <div class="row g-3" id="mahasiswa-cards">
            <!-- Cards will be populated by JavaScript -->
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <p class="text-sm mb-0 pagination-info" style="font-family: 'Open Sans', sans-serif;">
                Memuat data...
            </p>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <!-- Will be populated by JavaScript -->
                </ul>
            </nav>
        </div>
    </div>

    @include('pages.dosen.modals.logAktivitas')
    @include('pages.dosen.modals.evaluasi')
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/mahasiswa.css') }}">
@endpush

@push('js')
    <script>
        // Initialize the dosen_id from the server-side value or use Auth facade directly
        @if (Auth::user()->role === 'dosen' && Auth::user()->dosen)
            const dosen_id = {{ Auth::user()->dosen->id_dosen ?? 'null' }};
        @else
            const dosen_id = null;
        @endif

        // Rest of your JavaScript code remains mostly unchanged
        let filterState = {
            search: '',
            status: '',
            perusahaan: '',
            periode: ''
        };

        // Add debounce function that was missing
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (dosen_id) {
                loadMahasiswaData(filterState);
                loadPerusahaanOptions();
                loadPeriodeOptions(); // Add this line
            } else {
                showErrorState('Tidak dapat menentukan ID dosen Anda. Silakan muat ulang halaman.');
            }

            // Setup event listeners (same as before)
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', debounce(function(e) {
                    filterState.search = this.value.trim();
                    loadMahasiswaData(filterState);
                }, 500));
            }

            // Setup event filter status
            const statusFilter = document.getElementById('statusFilter');
            if (statusFilter) {
                statusFilter.addEventListener('change', function(e) {
                    filterState.status = this.value;
                    loadMahasiswaData(filterState);
                });
            }

            // Update perusahaan filter event listener
            const perusahaanFilter = document.getElementById('perusahaanFilter');
            if (perusahaanFilter) {
                perusahaanFilter.addEventListener('change', function(e) {
                    filterState.perusahaan = this.value;
                    loadMahasiswaData(filterState);
                });
            }

            // Setup event filter periode
            const periodeFilter = document.getElementById('periodeFilter');
            if (periodeFilter) {
                periodeFilter.addEventListener('change', function(e) {
                    filterState.periode = this.value;
                    loadMahasiswaData(filterState);
                });
            }
        });

        // Helper function to show error state - FIXED VERSION
        function showErrorState(message, isSystemError = false) {
            const cardsContainer = document.getElementById('mahasiswa-cards');

            if (!cardsContainer) {
                console.error('Cards container not found');
                return;
            }

            cardsContainer.innerHTML = `
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-exclamation-triangle text-danger" style="font-size: 40px;"></i>
                            </div>
                            <h5 class="text-danger">${message}</h5>
                            ${isSystemError ? `
                                                                            <p class="text-muted mt-2 mb-3">Coba muat ulang halaman atau hubungi administrator</p>
                                                                        ` : ''}
                            <button class="btn btn-sm btn-primary mt-2" onclick="window.location.reload()">
                                <i class="fas fa-sync-alt me-1"></i>Coba Lagi
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        const api = axios.create({
            baseURL: '/api',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            withCredentials: true
        });

        function loadMahasiswaData(filters = {}) {
            const cardsContainer = document.getElementById('mahasiswa-cards');

            if (!cardsContainer) {
                console.error('Cards container not found');
                return;
            }

            // Show loading state
            cardsContainer.innerHTML = `
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="spinner-border text-primary mb-3" role="status"></div>
                            <div class="text-primary fw-semibold">Memuat Data Mahasiswa...</div>
                        </div>
                    </div>
                </div>
            `;

            // Get current page from filters or default to 1
            const currentPage = filters.page || 1;

            api.get(`/dosen/${dosen_id}/mahasiswa-bimbingan`, {
                    params: {
                        search: filters.search || '',
                        status: filters.status || '',
                        perusahaan: filters.perusahaan || '',
                        periode: filters.periode || '',
                        page: currentPage
                    }
                })
                .then(function(response) {
                    if (response.data.success) {
                        const mahasiswa = response.data.data;
                        const meta = response.data.meta;

                        if (mahasiswa.length === 0) {
                            showEmptyState(filters);
                            return;
                        }

                        cardsContainer.innerHTML = '';
                        mahasiswa.forEach((item, index) => {
                            const card = document.createElement('div');
                            card.className = 'col-md-6 col-lg-4 mb-3';

                            // Card content remains the same
                            // ... your existing card HTML ...
                            const jobTitle = item.judul_lowongan || 'Belum ada posisi';

                            card.innerHTML = `
                                <div data-property-1="Aktif" style="width: 100%; height: 100%; padding: 10px 20px; background: white; border-radius: 5px; outline: 1px #E8EDF5 solid; display: flex; flex-direction: column; gap: 20px;">
                                    <!-- Status Badge -->
                                    <div data-property-1="Status" style="height: 29px; display: inline-flex; align-items: center; gap: 10px;">
                                        <div data-property-1="Default" style="padding: 5px 40px; ${getStatusStyles(item.status)}">
                                            <div style="${getStatusTextStyles(item.status)}">${item.status || 'Belum Magang'}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Mahasiswa Info -->
                                    <div style="align-self: stretch; display: inline-flex; justify-content: space-between; align-items: center;">
                                        <div style="width: 50%; flex-direction: column; display: inline-flex; gap: 8px;">
                                            <div style="align-self: stretch; color: #2D2D2D; font-size: 13px; font-family: 'Open Sans', sans-serif; font-weight: 600;">${item.name}</div>
                                            <div style="align-self: stretch; color: #7D7D7D; font-size: 12px; font-family: 'Open Sans', sans-serif; font-weight: 600;">${item.nim}</div>
                                        </div>
                                        <div style="width: 50%; flex-direction: column; display: inline-flex; gap: 5px; align-items: flex-end;">
                                            <div style="color: #2D2D2D; font-size: 13px; font-family: 'Open Sans', sans-serif; font-weight: 600;">
                                                ${item.nama_perusahaan || 'Belum ada perusahaan'}
                                            </div>
                                            <div style="color: #7D7D7D; font-size: 12px; font-family: 'Open Sans', sans-serif; font-weight: 600;">
                                                ${jobTitle}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Divider -->
                                    <div style="align-self: stretch; height: 0px; outline: 1px #E8EDF5 solid;"></div>
                                    
                                    <!-- Action Buttons -->
                                    <div style="align-self: stretch; display: inline-flex; justify-content: space-between; align-items: flex-start;">
                                        <button onclick="logAktivitas('${item.id_mahasiswa}')" class="btn btn-primary btn-sm" style="padding: 5px 10px; background: #5988FF; border-radius: 5px; border: none; display: flex; align-items: center; gap: 12px;">
                                            <i class="fas fa-clipboard-list" style="font-size: 14px;"></i>
                                            <span style="color: white; font-size: 13px; font-family: 'Open Sans', sans-serif; font-weight: 700; line-height: 28px;">Log Aktivitas</span>
                                        </button>
                                        
                                        ${item.status && item.status.toLowerCase() === 'selesai' ? `
                                                                        <button onclick="evaluasiMahasiswa('${item.id_mahasiswa}')" class="btn btn-sm" style="padding: 5px 15px; background: white; border-radius: 5px; border: 1px solid #7D7D7D; display: flex; align-items: center; gap: 12px;">
                                                                            <i class="fas fa-star" style="color: #7D7D7D; font-size: 14px;"></i>
                                                                            <span style="color: #7D7D7D; font-size: 13px; font-family: 'Open Sans', sans-serif; font-weight: 700; line-height: 28px;">Evaluasi</span>
                                                                        </button>
                                                                    ` : `
                                                                        <button disabled class="btn btn-sm" style="padding: 5px 15px; background: #939393; border-radius: 5px; border: none; display: flex; align-items: center; gap: 12px;">
                                                                            <i class="fas fa-star" style="color: #D0CFCF; font-size: 14px;"></i>
                                                                            <span style="color: #D0CFCF; font-size: 13px; font-family: 'Open Sans', sans-serif; font-weight: 700; line-height: 28px;">Evaluasi</span>
                                                                        </button>
                                                                    `}
                                    </div>
                                </div>
                            `;
                            cardsContainer.appendChild(card);
                        });

                        // Update pagination UI
                        updatePaginationInfo(meta);
                        updatePaginationLinks(meta);
                    } else {
                        showErrorState('Gagal memuat data: ' + (response.data.message ||
                            'Terjadi kesalahan yang tidak diketahui'));
                    }
                })
                .catch(function(error) {
                    console.error('Error fetching mahasiswa data:', error);
                    showErrorState('Gagal memuat data mahasiswa. Coba lagi nanti.', true);
                });
        }

        // Helper function to get status badge styles
        function getStatusStyles(status) {
            if (!status) return 'background: #f5f5f5; border-radius: 10px; outline: 1px #aaaaaa solid;';

            switch (status.toLowerCase()) {
                case 'aktif':
                    return 'background: #CAFFCC; border-radius: 10px; outline: 1px #57C45A solid;';
                case 'selesai':
                    return 'background: #ffdcc3; border-radius: 10px; outline: 1px #fd6900 solid;';
                default:
                    return 'background: #f5f5f5; border-radius: 10px; outline: 1px #aaaaaa solid;';
            }
        }

        // Helper function to get status text styles
        function getStatusTextStyles(status) {
            if (!status) return 'color: #aaaaaa; font-size: 13px; font-family: Open Sans, sans-serif; font-weight: 700;';

            switch (status.toLowerCase()) {
                case 'aktif':
                    return 'color: #57C45A; font-size: 13px; font-family: Open Sans, sans-serif; font-weight: 700;';
                case 'selesai':
                    return 'color: #fd6900; font-size: 13px; font-family: Open Sans, sans-serif; font-weight: 700;';
                default:
                    return 'color: #aaaaaa; font-size: 13px; font-family: Open Sans, sans-serif; font-weight: 700;';
            }
        }

        // Helper functions remain the same
        function showEmptyState(filters) {
            const cardsContainer = document.getElementById('mahasiswa-cards');

            if (!cardsContainer) {
                console.error('Cards container not found');
                return;
            }

            let message = 'Tidak ada data mahasiswa';

            if (filters.search) {
                message = `Tidak ada hasil untuk pencarian "${filters.search}"`;
            } else if (filters.status || filters.perusahaan || filters.periode) {
                message = 'Tidak ada data yang sesuai dengan filter yang dipilih';
            }

            cardsContainer.innerHTML = `
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="empty-state-icon mb-3">
                                <i class="fas fa-search text-muted opacity-25" style="font-size: 70px;"></i>
                            </div>
                            <h5 class="fw-semibold">${message}</h5>
                            ${filters.search || filters.status || filters.perusahaan || filters.periode ? `
                                                                                <button class="btn btn-sm btn-outline-secondary mt-3" onclick="resetFilters()">
                                                                                    <i class="fas fa-times me-1"></i>Reset Filter
                                                                                </button>
                                                                            ` : ''}
                        </div>
                    </div>
                </div>
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

        // Function to handle page navigation
        function changePage(page) {
            // Update current page in filter state
            filterState = {
                ...filterState,
                page: page
            };

            // Load data for the new page
            loadMahasiswaData(filterState);

            // Scroll to top of container
            window.scrollTo({
                top: document.querySelector('.container-fluid').offsetTop - 20,
                behavior: 'smooth'
            });
        }

        function logAktivitas(id_mahasiswa) {
            Swal.fire({
                title: 'Loading...',
                text: 'Mengambil log aktivitas mahasiswa',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            api.get(`/mahasiswa/${id_mahasiswa}/log-aktivitas`)
                .then(function(response) {
                    Swal.close();
                    if (response.data.success) {
                        // Handle log aktivitas view/modal
                        // You'll need to create a modal for displaying logs
                        const modal = new bootstrap.Modal(document.getElementById('logAktivitasModal'));
                        document.getElementById('logAktivitasBody').innerHTML = generateLogAktivitasHTML(response.data
                            .data);
                        modal.show();
                    } else {
                        Swal.fire('Gagal', 'Gagal memuat log aktivitas', 'error');
                    }
                })
                .catch(function(error) {
                    Swal.close();
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat log aktivitas', 'error');
                });
        }

        function evaluasiMahasiswa(id_mahasiswa) {
            Swal.fire({
                title: 'Loading...',
                text: 'Memuat form evaluasi',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            api.get(`/mahasiswa/${id_mahasiswa}/evaluasi`)
                .then(function(response) {
                    Swal.close();
                    if (response.data.success) {
                        // Handle evaluasi view/modal
                        // You'll need to create a modal for evaluation form
                        const modal = new bootstrap.Modal(document.getElementById('evaluasiModal'));
                        document.getElementById('evaluasiBody').innerHTML = generateEvaluasiHTML(response.data.data);
                        modal.show();
                    } else {
                        Swal.fire('Gagal', 'Gagal memuat form evaluasi', 'error');
                    }
                })
                .catch(function(error) {
                    Swal.close();
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat form evaluasi', 'error');
                });
        }

        // Add function to load perusahaan options
        function loadPerusahaanOptions() {
            api.get('/perusahaan-list')
                .then(function(response) {
                    if (response.data.success) {
                        const perusahaanFilter = document.getElementById('perusahaanFilter');
                        if (perusahaanFilter) {
                            perusahaanFilter.innerHTML = '<option value="">Semua Perusahaan</option>';
                            response.data.data.forEach(function(perusahaan) {
                                perusahaanFilter.innerHTML += `
                            <option value="${perusahaan.perusahaan_id}">
                                ${perusahaan.nama_perusahaan}
                            </option>
                        `;
                            });
                        }
                    }
                })
                .catch(function(error) {
                    console.error('Gagal memuat data perusahaan:', error);
                });
        }

        // Add function to load periode options
        function loadPeriodeOptions() {
            api.get('/periode-list')
                .then(function(response) {
                    if (response.data.success) {
                        const periodeFilter = document.getElementById('periodeFilter');
                        if (periodeFilter) {
                            periodeFilter.innerHTML = '<option value="">Semua Periode</option>';
                            response.data.data.forEach(function(periode) {
                                periodeFilter.innerHTML += `
                            <option value="${periode.periode_id}">
                                ${periode.waktu}
                            </option>
                        `;
                            });
                        }
                    }
                })
                .catch(function(error) {
                    console.error('Gagal memuat data periode:', error);
                });
        }

        // Add reset filters function
        function resetFilters() {
            // Reset all filters
            filterState = {
                search: '',
                status: '',
                perusahaan: '',
                periode: '' // Add this line
            };

            // Reset form inputs
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('perusahaanFilter').value = '';
            document.getElementById('periodeFilter').value = ''; // Add this line

            // Reload data
            loadMahasiswaData(filterState);
        }

        // Add these new functions to update pagination UI
        function updatePaginationInfo(meta) {
            const paginationInfo = document.querySelector('.pagination-info');
            if (paginationInfo) {
                paginationInfo.innerHTML = `
            Menampilkan <span class="fw-bold">${meta.from}-${meta.to}</span> dari <span class="fw-bold">${meta.total}</span> Mahasiswa
        `;
            }
        }

        function updatePaginationLinks(meta) {
            const paginationContainer = document.querySelector('.pagination');
            if (!paginationContainer) return;

            // Clear existing pagination
            paginationContainer.innerHTML = '';

            // Previous page button
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${meta.current_page === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `
        <a class="page-link" href="#" ${meta.current_page === 1 ? '' : `onclick="changePage(${meta.current_page - 1}); return false;"`}>
            <i class="fas fa-chevron-left"></i>
        </a>
    `;
            paginationContainer.appendChild(prevLi);

            // Create page number buttons
            // Show at most 5 pages, centered around current page
            const startPage = Math.max(1, meta.current_page - 2);
            const endPage = Math.min(meta.last_page, startPage + 4);

            for (let i = startPage; i <= endPage; i++) {
                const pageLi = document.createElement('li');
                pageLi.className = `page-item ${i === meta.current_page ? 'active' : ''}`;
                pageLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
        `;
                paginationContainer.appendChild(pageLi);
            }

            // Show ellipsis if needed
            if (meta.last_page > endPage) {
                const ellipsisLi = document.createElement('li');
                ellipsisLi.className = 'page-item disabled';
                ellipsisLi.innerHTML = `<a class="page-link" href="#">...</a>`;
                paginationContainer.appendChild(ellipsisLi);

                // Show last page
                const lastLi = document.createElement('li');
                lastLi.className = 'page-item';
                lastLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${meta.last_page}); return false;">${meta.last_page}</a>
        `;
                paginationContainer.appendChild(lastLi);
            }

            // Next page button
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}`;
            nextLi.innerHTML = `
        <a class="page-link" href="#" ${meta.current_page === meta.last_page ? '' : `onclick="changePage(${meta.current_page + 1}); return false;"`}>
            <i class="fas fa-chevron-right"></i>
        </a>
    `;
            paginationContainer.appendChild(nextLi);
        }

        // Update the change page function
        function changePage(page) {
            // Update current page in filter state
            filterState = {
                ...filterState,
                page: page
            };

            // Load data for the new page
            loadMahasiswaData(filterState);

            // Scroll to top of container
            window.scrollTo({
                top: document.querySelector('.container-fluid').offsetTop - 20,
                behavior: 'smooth'
            });
        }
    </script>
@endpush

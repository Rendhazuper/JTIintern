@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Mahasiswa Bimbingan'])

    <div class="container-fluid py-4">
        <div class="card">
            <!-- Card Header with Filters -->
            <div class="card-header border-bottom p-3">
                <div class="row g-3">
                    <!-- Search Bar - Full Width -->
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control border-start-0"
                                placeholder="Cari Mahasiswa...">
                        </div>
                    </div>
                    <!-- Filters - Full Width -->
                    <div class="col-12">
                        <div class="row g-2">
                            <div class="col-12 col-md-4">
                                <select id="statusFilter" class="form-select">
                                    <option value="">Status</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <select id="perusahaanFilter" class="form-select">
                                    <option value="">Perusahaan</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <select id="periodeFilter" class="form-select">
                                    <option value="">Periode</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Body with Table -->
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mahasiswa
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">NIM
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kelas
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Status</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody id="mahasiswa-table-body">
                            <!-- Data akan diisi melalui JavaScript -->
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div id="pagination-container" class="d-flex justify-content-center mt-3">
                    <!-- Pagination akan diisi melalui JavaScript -->
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Log Aktivitas -->
    <div class="modal fade" id="logAktivitasModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Log Aktivitas Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="px-2 modal-body" id="logAktivitasBody">
                    <!-- Content akan diisi melalui JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Evaluasi -->
    <div class="modal fade" id="evaluasiModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Evaluasi Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="evaluasiBody">
                    <!-- Content akan diisi melalui JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="submitEvaluasi()">Simpan Evaluasi</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/mahasiswa.css') }}">
@endpush

@push('js')
    <script>
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        let filterState = {
            search: '',
            status: '',
            perusahaan: '',
            periode: ''
        };

        document.addEventListener('DOMContentLoaded', function() {
            loadMahasiswaData(filterState);
            loadPerusahaanOptions(); // Add this line to load companies

            // Setup event pencarian dengan debounce
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
                .then(function(response) {
                    if (response.data.success) {
                        const kelasFilter = document.getElementById('kelasFilter');
                        kelasFilter.innerHTML = '<option value="">Semua Kelas</option>';
                        response.data.data.forEach(function(kelas) {
                            kelasFilter.innerHTML +=
                                `<option value="${kelas.id_kelas}">${kelas.nama_kelas}</option>`;
                        });
                    }
                })
                .catch(function(error) {
                    console.error('Gagal memuat data kelas:', error);
                });
        }

        function loadKelasOptions() {
            api.get('/kelas')
                .then(function(response) {
                    if (response.data.success) {
                        const select = document.getElementById('id_kelas');
                        select.innerHTML = '<option value="">Pilih Kelas</option>';
                        response.data.data.forEach(function(kelas) {
                            select.innerHTML +=
                                `<option value="${kelas.id_kelas}">${kelas.nama_kelas}</option>`;
                        });
                    }
                })
                .catch(function(error) {
                    console.error('Gagal memuat data kelas:', error);
                });
        }

        function loadEditKelasOptions(selectedIdKelas = '') {
            api.get('/kelas')
                .then(function(response) {
                    if (response.data.success) {
                        const select = document.getElementById('edit_id_kelas');
                        select.innerHTML = '<option value="">Pilih Kelas</option>';
                        response.data.data.forEach(function(kelas) {
                            select.innerHTML +=
                                `<option value="${kelas.id_kelas}" ${kelas.id_kelas == selectedIdKelas ? 'selected' : ''}>
                                                                                                                                                                                                    ${kelas.nama_kelas}
                                                                                                                                                                                                </option>`;
                        });
                    }
                })
                .catch(function(error) {
                    console.error('Gagal memuat data kelas:', error);
                });
        }

        function loadMahasiswaData(filters = {}) {
            // Show loading state
            const tableBody = document.getElementById('mahasiswa-table-body');
            tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center p-5">
                <div class="d-flex flex-column align-items-center">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <div class="text-primary fw-semibold">Memuat Data Mahasiswa...</div>
                </div>
            </td>
        </tr>
    `;

            // Get logged in dosen_id
            @if (Auth::user()->dosen)
                const dosen_id = '{{ Auth::user()->dosen->id_dosen }}';
            @else
                const dosen_id = null;
            @endif

            if (!dosen_id) {
                showErrorState('Akses tidak valid. Anda harus login sebagai dosen.');
                return;
            }

            // Fetch data from API with search parameter
            api.get(`/dosen/${dosen_id}/mahasiswa-bimbingan`, {
                    params: {
                        search: filters.search || '',
                        status: filters.status || '',
                        perusahaan: filters.perusahaan || '',
                        periode: filters.periode || ''
                    }
                })
                .then(function(response) {
                    if (response.data.success) {
                        const mahasiswa = response.data.data;

                        if (mahasiswa.length === 0) {
                            showEmptyState(filters);
                            return;
                        }

                        tableBody.innerHTML = '';
                        mahasiswa.forEach((item, index) => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
        <td>
            <h6 class="mx-3 text-sm">${item.name}</h6>
        </td>
        <td>
            <p class="text-xs font-weight-bold mb-0">${item.nim}</p>
        </td>
        <td>
            <p class="text-xs font-weight-bold mb-0">${item.nama_kelas}</p>
        </td>
        <td class="align-middle text-center">
            <span class="badge badge-sm ${getStatusBadgeClass(item.status)}">
                ${item.status || 'Belum Magang'}
            </span>
        </td>
        <td class="align-middle">
            <div class="gap-2 d-flex">
                <button class="btn btn-primary btn-sm" 
                    onclick="logAktivitas('${item.id_mahasiswa}')"
                    data-bs-toggle="tooltip" 
                    title="Log Aktivitas">
                    <i class="fas fa-clipboard-list"></i> Log
                </button>
                ${item.status && item.status.toLowerCase() === 'selesai' ? `
                            <button style="background-color: white; box-shadow: none; border: 1.5px solid #7D7D7D;" 
                                class="btn btn-sm shadow-none" 
                                onclick="evaluasiMahasiswa('${item.id_mahasiswa}')"
                                data-bs-toggle="tooltip" 
                                title="Evaluasi Mahasiswa">
                                <i class="fas fa-star"></i> Evaluasi
                            </button>
                        ` : ''}
            </div>
        </td>
    `;
                            tableBody.appendChild(row);
                        });
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    showErrorState('Gagal memuat data mahasiswa');
                });
        }

        function getStatusBadgeClass(status) {
            switch (status?.toLowerCase()) {
                case 'aktif':
                    return 'bg-success';
                case 'selesai':
                    return 'bg-secondary';
                default:
                    return 'bg-secondary';
            }
        }

        // Helper functions remain the same
        function showEmptyState(filters) {
            const tableBody = document.getElementById('mahasiswa-table-body');
            let message = 'Tidak ada data mahasiswa';

            if (filters.search) {
                message = `Tidak ada hasil untuk pencarian "${filters.search}"`;
            } else if (filters.status || filters.perusahaan || filters.periode) {
                message = 'Tidak ada data yang sesuai dengan filter yang dipilih';
            }

            tableBody.innerHTML = `
        <tr>
            <td colspan="5">
                <div class="text-center py-5">
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

        // Function to handle page navigation
        function changePage(page) {
            // Clone current filter state and add page
            const paginatedFilter = {
                ...filterState,
                page
            };

            // Load data with pagination
            loadMahasiswaData(paginatedFilter);

            // Scroll to top of table
            document.querySelector('.card').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            return false;
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

        // Add reset filters function
        function resetFilters() {
            // Reset all filters
            filterState = {
                search: '',
                status: '',
                perusahaan: '',
                periode: ''
            };

            // Reset form inputs
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('perusahaanFilter').value = '';
            document.getElementById('periodeFilter').value = '';

            // Reload data
            loadMahasiswaData(filterState);
        }
    </script>
@endpush

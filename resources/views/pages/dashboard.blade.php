@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Dashboard'])

                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="fw-bold mb-2" style="color: #2D2D2D;">Perusahaan Mitra</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold" style="color: #5988FF; font-size: 48px;" id="perusahaan-mitra">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </span>
                            <span class="d-flex align-items-center justify-content-center rounded"
                                style="width:64px;height:64px;background:#FECDCD;">
                                <i class="fas fa-suitcase" style="color:#FF5252; font-size:42px;"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
                    </div>
                </div>
            </div>
        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100 quick-menu-card">
                <div class="card-body">
                <div class="fw-bold mb-3 text-dark">Menu Cepat</div>
                <div class="list-group d-grid gap-2">
                    <a href="#" class="quick-menu-item">
                    <i class="fas fa-graduation-cap icon" style="color:#FFAE00;"></i>
                    <span>Data Mahasiswa</span>
                    </a>
                    <a href="#" class="quick-menu-item">
                    <i class="fas fa-city icon" style="color:#2F78FF;"></i>
                    <span>Data Perusahaan</span>
                    </a>
                    <a href="#" class="quick-menu-item">
                    <i class="fas fa-user-tie icon" style="color:#E091FF;"></i>
                    <span>Data Dosen</span>
                    </a>
                </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footers.auth.footer')
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Get token from localStorage (for token-based auth)
        function getAuthToken() {
            return localStorage.getItem('auth_token');
        }

        // Konfigurasi axios dengan headers default
        const api = axios.create({
            baseURL: '/api',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        // Add auth token to each request if available
        api.interceptors.request.use(
            function(config) {
                const token = getAuthToken();
                if (token) {
                    config.headers.Authorization = `Bearer ${token}`;
                }
                return config;
            },
            function(error) {
                return Promise.reject(error);
            }
        );

        // Fungsi untuk menampilkan pesan error autentikasi
        function showAuthError() {
            document.getElementById('mahasiswa-aktif').innerHTML = '<span class="text-danger">Login Required</span>';
            document.getElementById('perusahaan-mitra').innerHTML = '<span class="text-danger">Login Required</span>';
            document.getElementById('lowongan-aktif').innerHTML = '<span class="text-danger">Login Required</span>';
            document.getElementById('latest-applications').innerHTML = `
                <tr>
                    <td colspan="3" class="text-center">
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Anda perlu <a href="{{ route('login') }}" class="alert-link">login</a> terlebih dahulu untuk melihat data.
                        </div>
                    </td>
                </tr>
            `;
        }

        // Fungsi untuk menampilkan pesan error
        function showError(elementId, message) {
            const element = document.getElementById(elementId);
            element.innerHTML = `<span class="text-danger">${message}</span>`;
        }

        // Fungsi untuk memuat data summary dashboard
        function loadDashboardSummary() {
            api.get('/dashboard/summary')
                .then(function(response) {
                    if (response.data.success) {
                        const data = response.data.data;
                        document.getElementById('mahasiswa-aktif').innerText = data.mahasiswa_aktif || '0';
                        document.getElementById('perusahaan-mitra').innerText = data.perusahaan_mitra || '0';
                        document.getElementById('lowongan-aktif').innerText = data.lowongan_aktif || '0';
                    } else {
                        showError('mahasiswa-aktif', 'Data tidak tersedia');
                        showError('perusahaan-mitra', 'Data tidak tersedia');
                        showError('lowongan-aktif', 'Data tidak tersedia');
                    }
                })
                .catch(function(error) {
                    console.error('Error loading dashboard summary:', error);
                    
                    // Jika error 401 (tidak terautentikasi), tampilkan pesan login
                    if (error.response && error.response.status === 401) {
                        showAuthError();
                    } else {
                        showError('mahasiswa-aktif', 'Gagal memuat data');
                        showError('perusahaan-mitra', 'Gagal memuat data');
                        showError('lowongan-aktif', 'Gagal memuat data');
                    }
                });
        }

        // Fungsi untuk memuat data aplikasi terbaru
        function loadLatestApplications() {
            api.get('/dashboard/latest-applications')
                .then(function(response) {
                    if (response.data.success) {
                        const applications = response.data.data;
                        const tableBody = document.getElementById('latest-applications');
                        
                        // Bersihkan konten tabel
                        tableBody.innerHTML = '';
                        
                        // Jika data kosong, tampilkan pesan
                        if (applications.length === 0) {
                            tableBody.innerHTML = `
                                <tr>
                                    <td colspan="3" class="text-center">
                                        Tidak ada permintaan magang terbaru
                                    </td>
                                </tr>
                            `;
                            return;
                        }
                        
                        // Render data ke dalam tabel
                        applications.forEach(app => {
                            const statusClass = app.status === 'diterima' ? 'bg-primary' : 'bg-secondary';
                            const statusLabel = app.status === 'diterima' ? 'Diterima' : 'Menunggu';
                            
                            const row = `
                                <tr>
                                    <td>
                                        <div class="fw-bold" style="color: #2D2D2D;">${app.nama_mahasiswa}</div>
                                        <div class="text-muted small fw-bold">NIM : ${app.nim}</div>
                                    </td>
                                    <td>${app.perusahaan}</td>
                                    <td>
                                        <span class="badge rounded-pill ${statusClass}">${statusLabel}</span>
                                    </td>
                                </tr>
                            `;
                            tableBody.innerHTML += row;
                        });
                    } else {
                        document.getElementById('latest-applications').innerHTML = `
                            <tr>
                                <td colspan="3" class="text-center">
                                    Data tidak tersedia
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(function(error) {
                    console.error('Error loading latest applications:', error);
                    
                    // Handle 401 error separately
                    if (error.response && error.response.status === 401) {
                        // Auth error is handled by loadDashboardSummary
                    } else {
                        document.getElementById('latest-applications').innerHTML = `
                            <tr>
                                <td colspan="3" class="text-center text-danger">
                                    Gagal memuat data. Coba lagi nanti.
                                </td>
                            </tr>
                        `;
                    }
                });
        }

        // Load data saat halaman dimuat jika token tersedia
        document.addEventListener('DOMContentLoaded', function() {
            if (getAuthToken()) {
                loadDashboardSummary();
                loadLatestApplications();
            } else {
                // Jika tidak ada token, tampilkan pesan untuk login
                showAuthError();
            }
        });
    </script>
@endpush
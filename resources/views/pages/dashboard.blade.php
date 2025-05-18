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
    const api = axios.create({
        baseURL: '/api',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        withCredentials: true // Penting! Ini akan mengirim cookies dengan request
    });

    function showError(elementId, message) {
        const element = document.getElementById(elementId);
        if (element) {
            element.innerHTML = `<span class="text-danger">${message}</span>`;
        }
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
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                if (error.response && error.response.status === 401) {
                    // Redirect ke login jika tidak terautentikasi
                    window.location.href = '/login';
                }
                showError('mahasiswa-aktif', 'Error');
                showError('perusahaan-mitra', 'Error');
                showError('lowongan-aktif', 'Error');
            });
    }

    // Fungsi untuk memuat data aplikasi terbaru
    function loadLatestApplications() {
        api.get('/dashboard/latest-applications')
            .then(function(response) {
                if (response.data.success) {
                    const applications = response.data.data;
                    const tableBody = document.getElementById('latest-applications');
                    
                    tableBody.innerHTML = '';
                    
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
                }
            })
            .catch(function(error) {
                console.error('Error loading latest applications:', error);
                document.getElementById('latest-applications').innerHTML = `
                    <tr>
                        <td colspan="3" class="text-center text-danger">
                            Gagal memuat data. Coba lagi nanti.
                        </td>
                    </tr>
                `;
            });
    }

    // Load data saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        loadDashboardSummary();
        loadLatestApplications();
    });
</script>
@endpush
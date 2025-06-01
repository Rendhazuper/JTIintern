@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')
    
    <div class="container-fluid px-10">
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-4">Daftar Lowongan Magang</h3>
                
                <!-- Filter Section -->
                <div class="card border-0 mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <select id="perusahaanFilter" class="form-select">
                                    <option value="">Semua Perusahaan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="skillFilter" class="form-select">
                                    <option value="">Semua Skill</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="jenisFilter" class="form-select">
                                    <option value="">Semua Jenis</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary w-100" id="applyFilter">
                                    <i class="bi bi-filter me-2"></i>Terapkan Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lowongan Cards -->
                <div class="row" id="lowonganContainer">
                    <!-- Cards will be populated here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
   <link rel="stylesheet" href="{{ asset('assets/css/Mahasiswa/lowongan.css') }}">
@endpush

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
        withCredentials: true
    });

    function loadFilterOptions() {
        // Load Perusahaan Filter
        api.get('/perusahaan').then(response => {
            if (response.data.success) {
                const select = document.getElementById('perusahaanFilter');
                response.data.data.forEach(perusahaan => {
                    select.innerHTML += `<option value="${perusahaan.perusahaan_id}">${perusahaan.nama_perusahaan}</option>`;
                });
            }
        });

        // Load Skill Filter
        api.get('/skill').then(response => {
            if (response.data.success) {
                const select = document.getElementById('skillFilter');
                response.data.data.forEach(skill => {
                    select.innerHTML += `<option value="${skill.skill_id}">${skill.nama}</option>`;
                });
            }
        });

        // Load Jenis Filter
        api.get('/jenis').then(response => {
            if (response.data.success) {
                const select = document.getElementById('jenisFilter');
                response.data.data.forEach(jenis => {
                    select.innerHTML += `<option value="${jenis.jenis_id}">${jenis.nama_jenis}</option>`;
                });
            }
        });
    }

    function loadLowongan(filters = {}) {
        const container = document.getElementById('lowonganContainer');
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Memuat lowongan...</p>
            </div>
        `;

        api.get('/lowongan', { params: filters })
            .then(response => {
                if (response.data.success) {
                    container.innerHTML = '';
                    response.data.data.forEach((lowongan, index) => {
                        const card = document.createElement('div');
                        card.className = 'col-md-4 mb-4';
                        card.style.animation = `fadeIn 0.3s ease forwards ${index * 0.1}s`;
                        card.innerHTML = `
                            <div class="card h-100 lowongan-card border-0" onclick="showDetail(${lowongan.id_lowongan})">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/img/logo-ct-dark.png" 
                                             alt="Logo ${lowongan.perusahaan.nama_perusahaan}"
                                             class="company-logo me-3">
                                        <div>
                                            <h6 class="mb-0">${lowongan.judul_lowongan}</h6>
                                            <p class="text-muted mb-0 small">${lowongan.perusahaan.nama_perusahaan}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge badge-location">
                                            <i class="bi bi-geo-alt me-1"></i>${lowongan.perusahaan.nama_kota}
                                        </span>
                                        <span class="badge bg-primary">
                                            ${lowongan.jenis.nama_jenis}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.appendChild(card);
                    });

                    if (response.data.data.length === 0) {
                        container.innerHTML = `
                            <div class="col-12 text-center py-5">
                                <i class="bi bi-search" style="font-size: 3rem; color: #8898aa;"></i>
                                <h6 class="mt-3">Tidak ada lowongan yang sesuai</h6>
                                <p class="text-muted">Coba ubah filter pencarian Anda</p>
                            </div>
                        `;
                    }
                }
            });
    }

    function showDetail(id) {
        // Implement detail view logic here
        window.location.href = `/lowongan/${id}`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadFilterOptions();
        loadLowongan();

        document.getElementById('applyFilter').addEventListener('click', function() {
            const filters = {
                perusahaan_id: document.getElementById('perusahaanFilter').value,
                skill_id: document.getElementById('skillFilter').value,
                jenis_id: document.getElementById('jenisFilter').value
            };
            loadLowongan(filters);
        });
    });
</script>
@endpush
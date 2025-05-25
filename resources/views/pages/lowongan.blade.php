@extends('layouts.app', ['class' => 'g-sidenav-show'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen Lowongan'])
    <div class="container-fluid py-4">
        <div class="card pt-4">
            <div class="d-flex justify-content-between mb-3 px-4">
                <div class="d-flex gap-2">
                    <select id="perusahaanFilter" class="form-select form-select-sm" style="width: auto; height: 38px">
                        <option value="">Semua Perusahaan</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn" style="color: white; background: #02A232;"
                        onclick="tambahLowongan()">
                        <i class="bi bi-plus-square-fill me-2"></i>Tambah Lowongan
                    </button>
                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Judul Lowongan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Perusahaan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kapasitas</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal Dibuat</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="lowongan-table-body">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>  
            </div>
        </div>
    </div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/lowongan.css') }}">
@endpush

@push('js')
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
        api.get('/perusahaan')
            .then(function(response) {
                if (response.data.success) {
                    const perusahaanFilter = document.getElementById('perusahaanFilter');
                    perusahaanFilter.innerHTML = '<option value="">Semua Perusahaan</option>';
                    response.data.data.forEach(function(perusahaan) {
                        perusahaanFilter.innerHTML += `
                            <option value="${perusahaan.perusahaan_id}">${perusahaan.nama_perusahaan}</option>
                        `;
                    });
                }
            });
    }

    function loadLowonganData(filters = {}) {
        api.get('/lowongan', { params: filters })
            .then(function(response) {
                const tableBody = document.getElementById('lowongan-table-body');
                tableBody.innerHTML = '';
                
                if (response.data.success && response.data.data.length > 0) {
                    response.data.data.forEach(lowongan => {
                        const date = new Date(lowongan.created_at);
                        const formattedDate = date.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                        
                        tableBody.innerHTML += `
                            <tr>
                                <td>
                                    <p class="text-sm font-weight-bold mb-0">${lowongan.judul_lowongan}</p>
                                </td>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">${lowongan.perusahaan.nama_perusahaan}</h6>
                                            <p class="text-xs text-secondary mb-0">${lowongan.perusahaan.kota}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">${lowongan.kapasitas} Orang</span>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">${formattedDate}</span>
                                </td>
                                <td class="align-middle">
                                    <button class="btn btn-sm btn-info" onclick="detailLowongan(${lowongan.id_lowongan})">
                                        Detail
                                    </button>
                                    <button class="btn btn-sm btn-primary" onclick="editLowongan(${lowongan.id_lowongan})">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteLowongan(${lowongan.id_lowongan})">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    // Show empty state message
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="5">
                                <div class="text-center py-4">
                                    <div class="empty-state-icon mb-3">
                                        <i class="bi bi-clipboard-x" style="font-size: 3rem; color: #8898aa;"></i>
                                    </div>
                                    <h6 class="text-muted">Tidak ada lowongan tersedia</h6>
                                    <p class="text-xs text-secondary mb-0">
                                        ${filters.perusahaan_id ? 'Belum ada lowongan untuk perusahaan ini' : 'Belum ada lowongan yang ditambahkan'}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Gagal memuat data lowongan', 'error');
            });
    }

    // Load data when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadFilterOptions();
        loadLowonganData();
    });

    // Add event listener for filter
    document.getElementById('perusahaanFilter').addEventListener('change', function(e) {
        loadLowonganData({ perusahaan_id: e.target.value });
    });
</script>
@endpush
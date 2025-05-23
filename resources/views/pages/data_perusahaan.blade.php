@extends('layouts.app')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Data Perusahaan'])
    <div class="container-fluid py-4">
        <div class="search-header mb-4">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="search-filters d-flex gap-3">
                        <div class="search-box">
                            <input type="text" class="form-control" placeholder="Cari Perusahaan" id="searchPerusahaan">
                            <i class="bi bi-search"></i>
                        </div>
                        <button class="filter-btn" id="filterWilayah">
                            <i class="bi bi-geo-alt"></i>
                            <span>Wilayah</span>
                        </button>
                        <button class="filter-btn" id="filterLowongan">
                            <i class="bi bi-briefcase"></i>
                            <span>Lowongan</span>
                        </button>
                    </div>
                    <div class="action-buttons d-flex gap-3">
                        <button type="button" class="btn" 
                                style="color: white; background: #02A232;" 
                                onclick="tambahPerusahaan()">
                            <i class="bi bi-plus-square-fill me-2"></i>Tambah Perusahaan
                        </button>
                        <button type="button" class="btn" 
                                style="color: white; background: #5988FF;" 
                                onclick="importCSV()">
                            <i class="bi bi-plus-square-fill me-2"></i>Import CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="perusahaan-grid">
            <div class="row g-4" id="perusahaanContainer">
                <!-- Data will be loaded here -->
            </div>
        </div>
    </div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/data_perusahaan.css') }}">
@endpush

@push('js')
<script>
// Load data when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadPerusahaanData();
});

function loadPerusahaanData() {
    fetch('/api/perusahaan')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePerusahaanGrid(data.data);
            }
        })
        .catch(error => console.error('Error:', error));
}

function updatePerusahaanGrid(perusahaan) {
    const grid = document.getElementById('perusahaanContainer');
    if (!perusahaan.length) {
        grid.innerHTML = `
            <div class="col-12">
                <div class="alert alert-info">
                    Belum ada data perusahaan.
                </div>
            </div>
        `;
        return;
    }

    grid.innerHTML = perusahaan.map(p => `
        <div class="col-md-4">
            <div class="card company-card" onclick="goToDetail(${p.perusahaan_id})" style="cursor: pointer;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="company-logo me-3">
                            <i class="bi bi-building" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h6 class="company-name mb-1">${p.nama_perusahaan}</h6>
                            <p class="company-location mb-0">${p.kota}</p>
                        </div>
                    </div>
                    <div class="vacancy-info">
                        <p class="text-muted mb-2">Lowongan Terbuka</p>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-briefcase me-2"></i>
                            <span class="">0 Lowongan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function tambahPerusahaan() {
    // Add your modal code here
    console.log('Tambah Perusahaan clicked');
}

function importCSV() {
    // Add your import CSV code here
    console.log('Import CSV clicked');
}

function goToDetail(id) {
    window.location.href = `/detail-perusahaan/${id}`;
}
</script>
@endpush
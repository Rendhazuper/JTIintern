@extends('layouts.app')

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')
    
    <div class="container-fluid px-8">
        <h3 class="mb-4">Selamat Datang Rendha Putra 👋</h3>     
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-info-circle fs-4 text-info"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Belum Magang</h5>
                                <p style = "width : 70%" class="mb-3">Saat ini kamu belum terdaftar pada program magang manapun. Yuk segera eksplorasi berbagai lowongan yang tersedia dan ajukan lamaran agar tidak tertinggal!</p>
                                 <a href="{{ route('mahasiswa.lowongan') }}" class="btn btn-primary">
                                <i class="bi bi-search me-2"></i>Cari Lowongan
                            </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekomendasi Section - Transparent background -->
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3">Rekomendasi Tempat Magang</h5>
                <p class="text-sm text-muted mb-4">
                    Kami Menemukan 10 Lowongan Magang Yang Sesuai Dengan Kriteria Mu
                </p>
                
                <div class="row">
                    <!-- Card 1 -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm" style="background-color: #fff;">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="/img/logo-ct-dark.png" 
                                         alt="Logo PT Sekawan Media"
                                         class="me-3"
                                         style="width: 48px; height: 48px; object-fit: contain;">
                                    <div>
                                        <h6 class="mb-0">Backend Developer Intern</h6>
                                        <p class="text-sm text-muted mb-0">PT Sekawan Media</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-geo-alt me-1"></i>Malang
                                    </span>
                                    <a href="#" class="btn btn-sm btn-outline-primary">Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm" style="background-color: #fff;">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="/img/logo-ct-dark.png" 
                                         alt="Logo PT Global Media"
                                         class="me-3"
                                         style="width: 48px; height: 48px; object-fit: contain;">
                                    <div>
                                        <h6 class="mb-0">Frontend Developer Intern</h6>
                                        <p class="text-sm text-muted mb-0">PT Global Media</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-geo-alt me-1"></i>Surabaya
                                    </span>
                                    <a href="#" class="btn btn-sm btn-outline-primary">Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm" style="background-color: #fff;">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="/img/logo-ct-dark.png" 
                                         alt="Logo PT Tech Solutions"
                                         class="me-3"
                                         style="width: 48px; height: 48px; object-fit: contain;">
                                    <div>
                                        <h6 class="mb-0">UI/UX Designer Intern</h6>
                                        <p class="text-sm text-muted mb-0">PT Tech Solutions</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-geo-alt me-1"></i>Malang
                                    </span>
                                    <a href="#" class="btn btn-sm btn-outline-primary">Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
    .container-fluid {
        padding-top: 2rem;
    }
    .card {
        background-color: #fff;
        border-radius: 10px;
    }
    .row:last-child .card {
        background-color: transparent;
    }
    .btn-primary {
        background-color: #3B82F6;
        border-color: #3B82F6;
    }
    .btn-primary:hover {
        background-color: #2563EB;
        border-color: #2563EB;
    }
</style>
@endpush
@extends('layouts.app',  ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')
    
    <div class="container-fluid px-10">
        <div class="container-fluid p-0">
            <div class="d-flex align-items-center mb-4">
            <h3 class="me-auto mb-0">Selamat Datang Rendha Putra 👋</h3>
            <div class="d-flex align-items-center">
                <h3 class="me-2" style ="margin-bottom: 0px">Tahun ajaran saat ini:</h3>
                @if(isset($activePeriod) && $activePeriod)
                    <div class="period-badge">
                        @if(is_object($activePeriod))
                            {{ $activePeriod->waktu }}
                        @else
                            {{ $activePeriod }}
                        @endif
                    </div>
                @else
                    <span class="text-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Tidak ada periode aktif
                    </span>
                @endif
            </div>
            </div>
        </div>
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
                                <p style="width: 70%" class="mb-3">Saat ini kamu belum terdaftar pada program magang manapun. Yuk segera eksplorasi berbagai lowongan yang tersedia dan ajukan lamaran agar tidak tertinggal!</p>
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
                        <a href="#" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 clickable-card" style="background-color: #fff; cursor: pointer;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/img/logo-ct-dark.png" 
                                             alt="Logo PT Sekawan Media"
                                             class="me-3"
                                             style="width: 48px; height: 48px; object-fit: contain;">
                                        <div>
                                            <h6 class="mb-0 text-dark">Backend Developer Intern</h6>
                                            <p class="text-sm text-muted mb-0">PT Sekawan Media</p>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-geo-alt me-1"></i>Malang
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Card 2 -->
                    <div class="col-md-4 mb-4">
                        <a href="#" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 clickable-card" style="background-color: #fff; cursor: pointer;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/img/logo-ct-dark.png" 
                                             alt="Logo PT Global Media"
                                             class="me-3"
                                             style="width: 48px; height: 48px; object-fit: contain;">
                                        <div>
                                            <h6 class="mb-0 text-dark">Frontend Developer Intern</h6>
                                            <p class="text-sm text-muted mb-0">PT Global Media</p>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-geo-alt me-1"></i>Surabaya
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Card 3 -->
                    <div class="col-md-4 mb-4">
                        <a href="#" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 clickable-card" style="background-color: #fff; cursor: pointer;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/img/logo-ct-dark.png" 
                                             alt="Logo PT Tech Solutions"
                                             class="me-3"
                                             style="width: 48px; height: 48px; object-fit: contain;">
                                        <div>
                                            <h6 class="mb-0 text-dark">UI/UX Designer Intern</h6>
                                            <p class="text-sm text-muted mb-0">PT Tech Solutions</p>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-geo-alt me-1"></i>Malang
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>

    @import url("https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap");

    body {
        font-family: 'Open Sans', sans-serif;
    }

     h3, h5 {
        font-family: 'Open Sans', sans-serif;
        font-weight: 600;  
        color : #2D2D2D;
    }

    p{
        font-family: 'Open Sans', sans-serif;
        font-weight: 600; 
        color: #7D7D7D
    }

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
    .clickable-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #E5E7EB;
    }
    .clickable-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
    }
    /* Fix background color issue */
    .card.clickable-card {
        background-color: #fff !important;
    }

    .card.clickable-card:hover {
        background-color: #fff !important;
    }

    .period-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 20px;
        border-radius: 50px;
        background: linear-gradient(white, white) padding-box,
                    linear-gradient(135deg, #96B3FF, #E9B9FF, #F9B591) border-box;
        border: 1px solid transparent;
        color: #2D2D2D;
        font-family: 'Open Sans', sans-serif;
        font-size: 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }


</style>
@endpush
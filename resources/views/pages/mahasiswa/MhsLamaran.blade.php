<!-- filepath: d:\laragon\www\JTIintern\resources\views\pages\mahasiswa\MhsLamaran.blade.php -->

@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')
    
    <div class="container-fluid py-4">
        
        <!-- Quick Stats Row tetap ditampilkan -->
        <div class="row stats-row">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card h-100 stats-card">
                    <div class="card-body p-3">
                        <!-- Skeleton Loader -->
                        <div class="skeleton-loader" id="skeleton-stats-1">
                            <div class="row">
                                <div class="col-8">
                                    <div class="skeleton-text skeleton-text-sm mb-2"></div>
                                    <div class="skeleton-text skeleton-text-lg"></div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="skeleton-icon"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Real Content (Hidden Initially) -->
                        <div class="real-content d-none" id="real-stats-1">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Lamaran</p>
                                        <h5 class="font-weight-bolder mb-0 text-primary counter-number" data-target="{{ $statistik['total'] ?? 0 }}">
                                            0
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                        <i class="fa fa-clipboard-list text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card h-100 stats-card">
                    <div class="card-body p-3">
                        <!-- Skeleton Loader -->
                        <div class="skeleton-loader" id="skeleton-stats-2">
                            <div class="row">
                                <div class="col-8">
                                    <div class="skeleton-text skeleton-text-sm mb-2"></div>
                                    <div class="skeleton-text skeleton-text-lg"></div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="skeleton-icon"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Real Content -->
                        <div class="real-content d-none" id="real-stats-2">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Menunggu</p>
                                        <h5 class="font-weight-bolder mb-0 text-warning counter-number" data-target="{{ $statistik['menunggu'] ?? 0 }}">
                                            0
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                        <i class="fa fa-clock text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card h-100 stats-card">
                    <div class="card-body p-3">
                        <!-- Skeleton Loader -->
                        <div class="skeleton-loader" id="skeleton-stats-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="skeleton-text skeleton-text-sm mb-2"></div>
                                    <div class="skeleton-text skeleton-text-lg"></div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="skeleton-icon"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Real Content -->
                        <div class="real-content d-none" id="real-stats-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Diterima</p>
                                        <h5 class="font-weight-bolder mb-0 text-success counter-number" data-target="{{ $statistik['diterima'] ?? 0 }}">
                                            0
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                        <i class="fa fa-check text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-sm-6">
                <div class="card h-100 stats-card">
                    <div class="card-body p-3">
                        <!-- Skeleton Loader -->
                        <div class="skeleton-loader" id="skeleton-stats-4">
                            <div class="row">
                                <div class="col-8">
                                    <div class="skeleton-text skeleton-text-sm mb-2"></div>
                                    <div class="skeleton-text skeleton-text-lg"></div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="skeleton-icon"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Real Content -->
                        <div class="real-content d-none" id="real-stats-4">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Ditolak</p>
                                        <h5 class="font-weight-bolder mb-0 text-danger counter-number" data-target="{{ $statistik['ditolak'] ?? 0 }}">
                                            0
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                        <i class="fa fa-times text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($magangInfo) && $magangInfo)
            <!-- Magang Aktif Card dengan Loading -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow border-0">
                        <!-- Skeleton Loader untuk Magang Card -->
                        <div class="skeleton-loader" id="magang-skeleton">
                            <div class="card-header pb-0">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="d-flex align-items-center">
                                            <div class="skeleton-avatar me-3"></div>
                                            <div>
                                                <div class="skeleton-text skeleton-text-lg mb-2"></div>
                                                <div class="skeleton-text skeleton-text-md mb-1"></div>
                                                <div class="skeleton-text skeleton-text-sm"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="skeleton-badge"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="skeleton-text skeleton-text-sm mb-2"></div>
                                        <div class="skeleton-progress-bar mb-3"></div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="skeleton-text skeleton-text-xs mb-1"></div>
                                                <div class="skeleton-text skeleton-text-lg"></div>
                                            </div>
                                            <div class="col-6">
                                                <div class="skeleton-text skeleton-text-xs mb-1"></div>
                                                <div class="skeleton-text skeleton-text-lg"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="skeleton-button"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Real Magang Card Content (Hidden Initially) -->
                        <div class="real-content d-none" id="real-magang">
                            <div class="card-header pb-0">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="d-flex align-items-center">
                                            <div class="company-avatar me-3">
                                                @php
                                                    $logoSrc = null;
                                                    $hasLogo = false;
                                                    
                                                    // Cek logo_url terlebih dahulu
                                                    if (isset($magangInfo['data']->logo_url) && !empty($magangInfo['data']->logo_url)) {
                                                        $logoSrc = $magangInfo['data']->logo_url;
                                                        $hasLogo = true;
                                                    } 
                                                    // Fallback ke logo_perusahaan
                                                    elseif (isset($magangInfo['data']->logo_perusahaan) && !empty($magangInfo['data']->logo_perusahaan)) {
                                                        if (str_starts_with($magangInfo['data']->logo_perusahaan, 'http')) {
                                                            $logoSrc = $magangInfo['data']->logo_perusahaan;
                                                        } elseif (str_starts_with($magangInfo['data']->logo_perusahaan, 'storage/')) {
                                                            $logoSrc = asset($magangInfo['data']->logo_perusahaan);
                                                        } else {
                                                            $logoSrc = asset('storage/' . $magangInfo['data']->logo_perusahaan);
                                                        }
                                                        $hasLogo = true;
                                                    }
                                                    // Fallback ke logo (jika ada)
                                                    elseif (isset($magangInfo['data']->logo) && !empty($magangInfo['data']->logo)) {
                                                        if (str_starts_with($magangInfo['data']->logo, 'http')) {
                                                            $logoSrc = $magangInfo['data']->logo;
                                                        } elseif (str_starts_with($magangInfo['data']->logo, 'storage/')) {
                                                            $logoSrc = asset($magangInfo['data']->logo);
                                                        } else {
                                                            $logoSrc = asset('storage/' . $magangInfo['data']->logo);
                                                        }
                                                        $hasLogo = true;
                                                    }
                                                @endphp
            
                                                @if($hasLogo && $logoSrc)
                                                    <img src="{{ $logoSrc }}" 
                                                         class="avatar avatar-lg border-radius-lg" 
                                                         alt="Logo {{ $magangInfo['data']->nama_perusahaan ?? 'Perusahaan' }}"
                                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'avatar avatar-lg bg-gradient-secondary border-radius-lg d-flex align-items-center justify-content-center\'><i class=\'fas fa-building text-white text-lg\'></i></div>';">
                                                @else
                                                    <div class="avatar avatar-lg bg-gradient-secondary border-radius-lg d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-building text-white text-lg"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="position-info">
                                                <h5 class="mb-1 font-weight-bolder">{{ $magangInfo['data']->judul_lowongan ?? 'Posisi Magang' }}</h5>
                                                <p class="mb-1 text-sm font-weight-bold text-dark">{{ $magangInfo['data']->nama_perusahaan ?? 'Nama Perusahaan' }}</p>
                                                @if(isset($magangInfo['data']->nama_kota) && $magangInfo['data']->nama_kota)
                                                    <p class="mb-0 text-xs text-secondary">
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        {{ $magangInfo['data']->nama_kota }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <span class="badge bg-gradient-success status-badge px-3 py-2">
                                            <i class="fas fa-play me-1"></i>Magang Aktif
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body pt-3">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="progress-section">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-sm font-weight-bold text-dark">Progress Magang</span>
                                                <span class="text-sm font-weight-bolder text-success">{{ $magangInfo['progress'] ?? 0 }}%</span>
                                            </div>
                                            <div class="progress progress-md mb-3">
                                                <div class="progress-bar bg-gradient-success" 
                                                     role="progressbar" 
                                                     style="width: 0%;" 
                                                     data-width="{{ $magangInfo['progress'] ?? 0 }}%"
                                                     aria-valuenow="{{ $magangInfo['progress'] ?? 0 }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            
                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <div class="magang-stat-item">
                                                        <p class="text-xs text-uppercase text-secondary mb-1 font-weight-bold">Hari Lewat</p>
                                                        <h6 class="mb-0 font-weight-bolder text-dark">
                                                            <span class="counter-number" data-target="{{ $magangInfo['lewat'] ?? 0 }}">0</span>
                                                            <span class="text-sm"> hari</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="magang-stat-item">
                                                        <p class="text-xs text-uppercase text-secondary mb-1 font-weight-bold">Sisa Hari</p>
                                                        <h6 class="mb-0 font-weight-bolder text-dark">
                                                            <span class="counter-number" data-target="{{ $magangInfo['sisaHari'] ?? 0 }}">0</span>
                                                            <span class="text-sm"> hari</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 d-flex align-items-center justify-content-end">
                                        <div class="action-buttons">
                                            <a href="{{ route('mahasiswa.magang') }}" class="btn btn-outline-primary btn-sm mb-2">
                                                <i class="fas fa-eye me-1"></i>
                                                Detail Magang
                                            </a>
                                            <br>
                                            <!-- ✅ UPDATE: Gunakan route yang benar -->
                                            <a href="{{ route('mahasiswa.logaktivitas') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-book me-1"></i>
                                                Buka Logbook
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                @if(isset($magangInfo['data']->nama_pembimbing))
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="pembimbing-info bg-gray-100 p-3 border-radius-lg">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-gradient-info me-3 border-radius-lg d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-user-tie text-white text-sm"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-uppercase text-secondary mb-1 font-weight-bold">Pembimbing</p>
                                                        <h6 class="mb-0 text-sm font-weight-bold text-dark">
                                                            {{ $magangInfo['data']->nama_pembimbing }}
                                                            @if(isset($magangInfo['data']->nip_pembimbing))
                                                                <span class="text-xs text-secondary"> • {{ $magangInfo['data']->nip_pembimbing }}</span>
                                                            @endif
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($showLamaranHistory) && $showLamaranHistory)
            <!-- ✅ CONDITIONAL: Riwayat Lamaran Card - HANYA TAMPILKAN JIKA TIDAK ADA MAGANG AKTIF -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow border-0">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Riwayat Lamaran</h6>
                                    <p class="text-sm mb-0">Pantau semua lamaran yang telah Anda ajukan</p>
                                </div>
                                <div class="card-header-controls d-flex align-items-center gap-2">
                                    <!-- Filter Dropdown -->
                                    <div class="filter-container">
                                        <select class="form-select form-select-sm" id="statusFilter" onchange="filterLamaran()" style="min-width: 140px;">
                                            <option value="all">Semua Status</option>
                                            <option value="menunggu">Menunggu</option>
                                            <option value="diterima">Diterima</option>
                                            <option value="ditolak">Ditolak</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Refresh Button -->
                                    <button class="btn btn-outline-primary btn-sm" id="refreshLamaranBtn" onclick="refreshLamaranData()">
                                        <i class="fas fa-sync-alt me-1" id="refreshIcon"></i>Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            
                            <!-- Table Skeleton Loading -->
                            <div id="table-skeleton-loading">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th><div class="skeleton-text skeleton-text-xs"></div></th>
                                                <th><div class="skeleton-text skeleton-text-xs"></div></th>
                                                <th><div class="skeleton-text skeleton-text-xs"></div></th>
                                                <th><div class="skeleton-text skeleton-text-xs"></div></th>
                                                <th><div class="skeleton-text skeleton-text-xs"></div></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($i = 1; $i <= 5; $i++)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="skeleton-avatar me-3"></div>
                                                        <div>
                                                            <div class="skeleton-text skeleton-text-sm mb-1"></div>
                                                            <div class="skeleton-text skeleton-text-xs"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="skeleton-text skeleton-text-sm mb-1"></div>
                                                    <div class="skeleton-text skeleton-text-xs"></div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="skeleton-badge mx-auto"></div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="skeleton-text skeleton-text-xs mb-1"></div>
                                                    <div class="skeleton-text skeleton-text-xs"></div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <div class="skeleton-button-sm"></div>
                                                        <div class="skeleton-button-sm"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Real Table Content (Hidden Initially) -->
                            <div id="real-table-content" class="d-none">
                                @if(isset($lamaranHistory) && $lamaranHistory->count() > 0)
                                    <!-- Table with Data -->
                                    <div class="table-responsive">
                                        <table class="table table-hover align-items-center mb-0" id="lamaranTable">
                                            <thead class="table-header">
                                                <tr>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Perusahaan</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Posisi</th>
                                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                                    <th class="text-secondary opacity-7"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="lamaranTableBody">
                                                @foreach($lamaranHistory as $index => $lamaran)
                                                    <tr class="lamaran-row fade-in-row" data-status="{{ $lamaran->status }}" data-index="{{ $index }}">
                                                        <td>
                                                            <div class="d-flex px-2 py-1">
                                                                <div class="company-avatar">
                                                                    @php
                                                                        $logoSrc = null;
                                                                        $hasLogo = false;
                                                                        
                                                                        if (isset($lamaran->logo_url) && !empty($lamaran->logo_url)) {
                                                                            $logoSrc = $lamaran->logo_url;
                                                                            $hasLogo = true;
                                                                        } elseif (isset($lamaran->logo) && !empty($lamaran->logo)) {
                                                                            if (str_starts_with($lamaran->logo, 'http')) {
                                                                                $logoSrc = $lamaran->logo;
                                                                            } elseif (str_starts_with($lamaran->logo, 'storage/')) {
                                                                                $logoSrc = asset($lamaran->logo);
                                                                            } else {
                                                                                $logoSrc = asset('storage/' . $lamaran->logo);
                                                                            }
                                                                            $hasLogo = true;
                                                                        }
                                                                    @endphp
                                                                    
                                                                    @if($hasLogo && $logoSrc)
                                                                        <img src="{{ $logoSrc }}" 
                                                                             class="avatar avatar-sm me-3 border-radius-lg" 
                                                                             alt="Logo {{ $lamaran->nama_perusahaan }}"
                                                                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'avatar avatar-sm bg-gradient-secondary me-3 border-radius-lg d-flex align-items-center justify-content-center\\'><i class=\\'fas fa-building text-white text-sm\\'></i></div>';">
                                                                    @else
                                                                        <div class="avatar avatar-sm bg-gradient-secondary me-3 border-radius-lg d-flex align-items-center justify-content-center">
                                                                            <i class="fas fa-building text-white text-sm"></i>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <h6 class="mb-0 text-sm font-weight-bold">{{ $lamaran->nama_perusahaan }}</h6>
                                                                    @if($lamaran->nama_kota)
                                                                        <p class="text-xs text-secondary mb-0">
                                                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $lamaran->nama_kota }}
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <p class="text-sm font-weight-bold mb-0">{{ $lamaran->judul_lowongan }}</p>
                                                            <p class="text-xs text-secondary mb-0">{{ Str::limit($lamaran->deskripsi_lowongan ?? 'Tidak ada deskripsi', 50) }}</p>
                                                        </td>
                                                        <td class="align-middle text-center text-sm">
                                                            @if($lamaran->status == 'diterima')
                                                                <span class="badge bg-gradient-success status-badge">
                                                                    <i class="fas fa-check me-1"></i>Diterima
                                                                </span>
                                                            @elseif($lamaran->status == 'ditolak')
                                                                <span class="badge bg-gradient-danger status-badge">
                                                                    <i class="fas fa-times me-1"></i>Ditolak
                                                                </span>
                                                            @else
                                                                <span class="badge bg-gradient-warning status-badge">
                                                                    <i class="fas fa-clock me-1"></i>Menunggu
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="text-secondary text-xs font-weight-bold">
                                                                {{ \Carbon\Carbon::parse($lamaran->tanggal_lamaran)->format('d M Y') }}
                                                            </span>
                                                            <br>
                                                            <span class="text-xs text-secondary">
                                                                {{ \Carbon\Carbon::parse($lamaran->tanggal_lamaran)->diffForHumans() }}
                                                            </span>
                                                        </td>
                                                        <td class="align-middle">
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <!-- ✅ DETAIL BUTTON -->
                                                                <button class="btn btn-link text-primary mb-0 px-1 me-1" 
                                                                        onclick="detailLamaran({{ $lamaran->id_lamaran }})" 
                                                                        data-bs-toggle="tooltip" 
                                                                        title="Lihat Detail">
                                                                    <i class="fas fa-eye text-xs"></i>
                                                                </button>
                                                                
                                                                <!-- ✅ CANCEL BUTTON (hanya untuk status menunggu) -->
                                                                @if($lamaran->status === 'menunggu')
                                                                    <button class="btn btn-link text-danger mb-0 px-1" 
                                                                            onclick="cancelLamaran({{ $lamaran->id_lamaran }})" 
                                                                            data-bs-toggle="tooltip" 
                                                                            title="Batalkan Lamaran">
                                                                        <i class="fas fa-times text-xs"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination if needed -->
                                    @if(method_exists($lamaranHistory, 'links'))
                                        <div class="mt-3">
                                            {{ $lamaranHistory->links() }}
                                        </div>
                                    @endif

                                @else
                                    <!-- Empty State Table -->
                                    <div class="empty-table-state text-center py-5">
                                        <div class="empty-table-icon mb-3">
                                            <i class="fas fa-clipboard-list"></i>
                                        </div>
                                        <h6 class="mb-2">Belum Ada Lamaran</h6>
                                        <p class="text-muted mb-4">Anda belum mengajukan lamaran magang. Mulai cari lowongan yang sesuai dengan minat Anda!</p>
                                        <a href="{{ route('mahasiswa.lowongan') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-search me-2"></i>Cari Lowongan
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- ✅ TAMPILKAN PESAN KETIKA ADA MAGANG AKTIF -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow border-0">
                        <div class="card-body text-center py-5">
                            <div class="magang-active-state">
                                <div class="magang-active-icon mb-4">
                                    <i class="fas fa-briefcase fa-4x text-success opacity-8"></i>
                                </div>
                                <h5 class="mb-3 text-success">Selamat! Anda Sedang Magang</h5>
                                <p class="text-muted mb-4">
                                    Anda saat ini sedang menjalani magang aktif. Riwayat lamaran tidak ditampilkan selama masa magang berlangsung. 
                                    Fokus pada aktivitas magang Anda dan jangan lupa untuk mengisi logbook secara rutin.
                                </p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('mahasiswa.logaktivitas') }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-book me-2"></i>Buka Logbook
                                    </a>
                                    <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-home me-2"></i>Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/Mahasiswa/lamaran.css') }}">
@endpush



@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// 1. ✅ GLOBAL VARIABLES
const serverData = {
    magangInfo: @json(isset($magangInfo) && $magangInfo ? true : false),
    lowonganRoute: "{{ route('mahasiswa.lowongan') }}"
};

// 2. ✅ API CONFIGURATION
const api = axios.create({
    baseURL: '/api',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    withCredentials: true,
    timeout: 10000
});

// 3. ✅ API INTERCEPTORS
api.interceptors.request.use(
    config => {
        console.log('📤 API Request:', {
            method: config.method?.toUpperCase(),
            url: config.url,
            data: config.data
        });
        return config;
    },
    error => {
        console.error('❌ Request Error:', error);
        return Promise.reject(error);
    }
);

api.interceptors.response.use(
    response => {
        console.log('📥 API Response:', {
            status: response.status,
            url: response.config.url,
            success: response.data?.success
        });
        return response;
    },
    error => {
        console.error('❌ Response Error:', {
            status: error.response?.status,
            url: error.config?.url,
            message: error.message
        });
        return Promise.reject(error);
    }
);

// 4. ✅ HELPER FUNCTIONS
function getStatusBadgeHTML(status) {
    const badges = {
        'menunggu': '<span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-clock me-1"></i>Menunggu Konfirmasi</span>',
        'diterima': '<span class="badge bg-success px-3 py-2"><i class="fas fa-check me-1"></i>Diterima</span>',
        'ditolak': '<span class="badge bg-danger px-3 py-2"><i class="fas fa-times me-1"></i>Ditolak</span>'
    };
    return badges[status] || '<span class="badge bg-secondary px-3 py-2">Status Tidak Diketahui</span>';
}

function getLamaranLogoHTML(lamaran) {
    if (lamaran.logo_url && lamaran.logo_url !== '') {
        return `<img src="${lamaran.logo_url}" 
                     class="avatar avatar-xl rounded-circle border" 
                     alt="Logo ${lamaran.nama_perusahaan}"
                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'avatar avatar-xl bg-gradient-secondary rounded-circle d-flex align-items-center justify-content-center\\'>'+
                             '<i class=\\'fas fa-building text-white text-lg\\'></i></div>';">`;
    } else {
        return `<div class="avatar avatar-xl bg-gradient-secondary rounded-circle d-flex align-items-center justify-content-center">
                    <i class="fas fa-building text-white text-lg"></i>
                </div>`;
    }
}

function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

function getRelativeTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) return 'Kemarin';
    if (diffDays < 7) return `${diffDays} hari lalu`;
    if (diffDays < 30) return `${Math.ceil(diffDays / 7)} minggu lalu`;
    return `${Math.ceil(diffDays / 30)} bulan lalu`;
}

function getStatusText(status) {
    const statusTexts = {
        'all': 'Semua Status',
        'menunggu': 'Menunggu',
        'diterima': 'Diterima',
        'ditolak': 'Ditolak'
    };
    return statusTexts[status] || status;
}

// 5. ✅ ANIMATION FUNCTIONS
function animateCounter(element) {
    const target = parseInt(element.dataset.target) || 0;
    const duration = 1200;
    const startTime = performance.now();
    
    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easeOut = 1 - Math.pow(1 - progress, 3);
        const current = Math.floor(easeOut * target);
        
        element.textContent = current;
        
        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        } else {
            element.style.transform = 'scale(1.05)';
            setTimeout(() => {
                element.style.transition = 'transform 0.2s ease';
                element.style.transform = 'scale(1)';
            }, 100);
        }
    }
    
    requestAnimationFrame(updateCounter);
}

function animateTableRows() {
    const rows = document.querySelectorAll('.fade-in-row');
    
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-30px)';
        
        setTimeout(() => {
            row.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, index * 100);
    });
}

function initializeTooltips() {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

// 6. ✅ LOADING FUNCTIONS
function simulateContentLoading() {
    console.log('⏳ Starting content loading simulation...');
    
    setTimeout(() => loadStatsCard(1), 300);
    setTimeout(() => loadStatsCard(2), 600);
    setTimeout(() => loadStatsCard(3), 900);
    setTimeout(() => loadStatsCard(4), 1200);
    
    if (serverData.magangInfo) {
        setTimeout(() => loadMagangCard(), 1500);
    }
    
    setTimeout(() => loadTableContent(), 2000);
}

function loadStatsCard(cardNumber) {
    const skeleton = document.getElementById('skeleton-stats-' + cardNumber);
    const realContent = document.getElementById('real-stats-' + cardNumber);
    
    if (!skeleton || !realContent) return;
    
    skeleton.style.transition = 'opacity 0.3s ease';
    skeleton.style.opacity = '0';
    
    setTimeout(() => {
        skeleton.classList.add('d-none');
        realContent.classList.remove('d-none');
        
        realContent.style.opacity = '0';
        realContent.style.transform = 'translateY(20px)';
        realContent.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        
        setTimeout(() => {
            realContent.style.opacity = '1';
            realContent.style.transform = 'translateY(0)';
            
            const counter = realContent.querySelector('.counter-number');
            if (counter) {
                setTimeout(() => animateCounter(counter), 200);
            }
        }, 50);
    }, 300);
}

function loadMagangCard() {
    const skeleton = document.getElementById('magang-skeleton');
    const realContent = document.getElementById('real-magang');
    
    if (!skeleton || !realContent) return;
    
    skeleton.style.transition = 'opacity 0.4s ease';
    skeleton.style.opacity = '0';
    
    setTimeout(() => {
        skeleton.classList.add('d-none');
        realContent.classList.remove('d-none');
        
        realContent.style.opacity = '0';
        realContent.style.transform = 'translateY(30px)';
        realContent.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        
        setTimeout(() => {
            realContent.style.opacity = '1';
            realContent.style.transform = 'translateY(0)';
            
            const progressBar = realContent.querySelector('.progress-bar[data-width]');
            if (progressBar) {
                setTimeout(() => {
                    progressBar.style.transition = 'width 1.5s ease';
                    progressBar.style.width = progressBar.dataset.width;
                }, 300);
            }
            
            const counters = realContent.querySelectorAll('.counter-number');
            counters.forEach((counter, index) => {
                setTimeout(() => animateCounter(counter), 400 + (index * 200));
            });
        }, 50);
    }, 400);
}

function loadTableContent() {
    const skeleton = document.getElementById('table-skeleton-loading');
    const realContent = document.getElementById('real-table-content');
    
    if (!skeleton || !realContent) return;
    
    skeleton.style.transition = 'opacity 0.4s ease';
    skeleton.style.opacity = '0';
    
    setTimeout(() => {
        skeleton.classList.add('d-none');
        realContent.classList.remove('d-none');
        
        realContent.style.opacity = '0';
        realContent.style.transform = 'translateY(20px)';
        realContent.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        
        setTimeout(() => {
            realContent.style.opacity = '1';
            realContent.style.transform = 'translateY(0)';
            
            animateTableRows();
            initializeTooltips();
        }, 50);
    }, 400);
}

// 7. ✅ DOM READY
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 === LOADING CONTENT AND TABLES ===');
    simulateContentLoading();
});

// 8. ✅ MAIN FUNCTIONS (detailLamaran, cancelLamaran, dll...)
function detailLamaran(id) {
    console.log('👀 Opening detail for lamaran ID:', id);
    
    // Show loading modal
    Swal.fire({
        title: 'Memuat Detail...',
        html: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"></div></div>',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    // Get detail data via AJAX
    api.get(`/mahasiswa/lamaran/${id}/detail`)
        .then(response => {
            console.log('✅ Detail lamaran response:', response.data);
            
            if (response.data?.success) {
                const lamaran = response.data.data;
                showDetailModal(lamaran);
            } else {
                throw new Error(response.data?.message || 'Data tidak ditemukan');
            }
        })
        .catch(error => {
            console.error('❌ Error loading detail:', error);
            
            let errorMessage = 'Gagal memuat detail lamaran';
            if (error.response?.status === 404) {
                errorMessage = 'Data lamaran tidak ditemukan';
            } else if (error.response?.data?.message) {
                errorMessage = error.response.data.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Detail',
                text: errorMessage,
                confirmButtonText: 'OK'
            });
        });
}

// ✅ NEW: Function untuk menampilkan modal detail yang menarik
function showDetailModal(lamaran) {
    const statusBadge = getStatusBadgeHTML(lamaran.status);
    const logoHTML = getLamaranLogoHTML(lamaran);
    
    const detailHTML = `
        <div class="modal-detail-lamaran">
            <!-- Header Section -->
            <div class="detail-header text-center mb-4">
                <div class="company-logo-large mb-3">
                    ${logoHTML}
                </div>
                <h4 class="mb-2 text-dark font-weight-bold">${lamaran.judul_lowongan}</h4>
                <h6 class="mb-2 text-muted">${lamaran.nama_perusahaan}</h6>
                ${lamaran.nama_kota ? `<p class="text-sm text-secondary mb-3"><i class="fas fa-map-marker-alt me-1"></i>${lamaran.nama_kota}</p>` : ''}
                ${statusBadge}
            </div>

            <!-- Info Cards -->
            <div class="row mb-4">
                <div class="col-6">
                    <div class="info-card bg-light p-3 rounded">
                        <div class="d-flex align-items-center">
                            <div class="info-icon bg-primary text-white rounded-circle me-3">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Tanggal Lamaran</small>
                                <strong>${formatDate(lamaran.tanggal_lamaran)}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="info-card bg-light p-3 rounded">
                        <div class="d-flex align-items-center">
                            <div class="info-icon bg-success text-white rounded-circle me-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Kapasitas</small>
                                <strong>${lamaran.kapasitas || 'Tidak terbatas'}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Section -->
            ${lamaran.deskripsi_lowongan ? `
                <div class="description-section mb-4">
                    <h6 class="mb-2"><i class="fas fa-file-alt me-2"></i>Deskripsi Posisi</h6>
                    <div class="description-content bg-light p-3 rounded">
                        <p class="mb-0 text-sm">${lamaran.deskripsi_lowongan}</p>
                    </div>
                </div>
            ` : ''}

            <!-- Requirements Section -->
            ${lamaran.min_ipk ? `
                <div class="requirements-section mb-4">
                    <h6 class="mb-2"><i class="fas fa-check-circle me-2"></i>Persyaratan</h6>
                    <div class="bg-light p-3 rounded">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-graduation-cap text-primary me-2"></i>
                            <span class="text-sm">Minimal IPK: <strong>${lamaran.min_ipk}</strong></span>
                        </div>
                    </div>
                </div>
            ` : ''}

            <!-- Company Info -->
            <div class="company-info-section">
                <h6 class="mb-2"><i class="fas fa-building me-2"></i>Informasi Perusahaan</h6>
                <div class="bg-light p-3 rounded">
                    ${lamaran.alamat_perusahaan ? `
                        <div class="d-flex align-items-start mb-2">
                            <i class="fas fa-map-marker-alt text-primary me-2 mt-1"></i>
                            <span class="text-sm">${lamaran.alamat_perusahaan}</span>
                        </div>
                    ` : ''}
                    ${lamaran.perusahaan_email ? `
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <a href="mailto:${lamaran.perusahaan_email}" class="text-sm text-decoration-none">${lamaran.perusahaan_email}</a>
                        </div>
                    ` : ''}
                    ${lamaran.website ? `
                        <div class="d-flex align-items-center">
                            <i class="fas fa-globe text-primary me-2"></i>
                            <a href="${lamaran.website}" target="_blank" class="text-sm text-decoration-none">${lamaran.website}</a>
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;

    // Show detail modal dengan action buttons
    const modalButtons = {};
    
    // Add cancel button if status is 'menunggu'
    if (lamaran.status === 'menunggu') {
        modalButtons['Batalkan Lamaran'] = {
            text: 'Batalkan Lamaran',
            value: 'cancel',
            className: 'btn btn-danger btn-sm'
        };
    }
    
    modalButtons['Tutup'] = {
        text: 'Tutup',
        value: 'close',
        className: 'btn btn-secondary btn-sm'
    };

    Swal.fire({
        title: 'Detail Lamaran',
        html: detailHTML,
        showCancelButton: false,
        showConfirmButton: false,
        width: '600px',
        customClass: {
            popup: 'detail-lamaran-modal',
            htmlContainer: 'detail-content-wrapper'
        },
        footer: generateModalFooter(lamaran),
        allowOutsideClick: true
    });
}

// ✅ ENHANCED: Generate modal footer dengan action buttons
function generateModalFooter(lamaran) {
    let footerHTML = '<div class="modal-footer-actions d-flex justify-content-between align-items-center w-100">';
    
    // Left side - Close button
    footerHTML += '<button type="button" class="btn btn-secondary btn-sm" onclick="Swal.close()">Tutup</button>';
    
    // Right side - Action buttons
    footerHTML += '<div class="action-buttons">';
    
    if (lamaran.status === 'menunggu') {
        footerHTML += `<button type="button" class="btn btn-danger btn-sm me-2" onclick="cancelLamaran(${lamaran.id_lamaran})">
            <i class="fas fa-times me-1"></i>Batalkan
        </button>`;
    }
    
    // Always show detail lowongan button
    footerHTML += `<a href="/mahasiswa/lowongan/${lamaran.id_lowongan}" class="btn btn-primary btn-sm" target="_blank">
        <i class="fas fa-external-link-alt me-1"></i>Lihat Lowongan
    </a>`;
    
    footerHTML += '</div></div>';
    
    return footerHTML;
}

// ✅ ENHANCED: Cancel lamaran dengan AJAX refresh yang benar
function cancelLamaran(id) {
    console.log('🗑️ Canceling lamaran ID:', id);
    
    Swal.fire({
        title: 'Batalkan Lamaran?',
        html: `
            <div class="text-center">
                <div class="mb-3">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                </div>
                <p class="mb-2">Apakah Anda yakin ingin membatalkan lamaran ini?</p>
                <small class="text-muted">Tindakan ini tidak dapat dibatalkan dan Anda harus melamar ulang jika berubah pikiran.</small>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check me-1"></i>Ya, Batalkan',
        cancelButtonText: '<i class="fas fa-times me-1"></i>Tidak',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        customClass: {
            confirmButton: 'btn btn-danger btn-sm me-2',
            cancelButton: 'btn btn-secondary btn-sm'
        },
        buttonsStyling: false,
        reverseButtons: true,
        focusCancel: true
    }).then(function(result) {
        if (result.isConfirmed) {
            // Show processing modal
            Swal.fire({
                title: 'Membatalkan Lamaran...',
                html: `
                    <div class="d-flex flex-column align-items-center">
                        <div class="spinner-border text-danger mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mb-0 text-muted">Sedang memproses pembatalan lamaran</p>
                    </div>
                `,
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // AJAX Request untuk cancel
            api.delete(`/mahasiswa/lamaran/${id}/cancel`)
                .then(response => {
                    console.log('✅ Cancel success:', response.data);
                    
                    if (response.data?.success) {
                        // Success modal
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: `
                                <div class="text-center">
                                    <p class="mb-2">Lamaran telah berhasil dibatalkan</p>
                                    <small class="text-muted">Data akan diperbarui secara otomatis</small>
                                </div>
                            `,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745',
                            timer: 2000,
                            timerProgressBar: true,
                            customClass: {
                                confirmButton: 'btn btn-success btn-sm'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            // ✅ PERBAIKAN: Panggil refresh data dengan benar
                            refreshLamaranData();
                        });
                    } else {
                        throw new Error(response.data?.message || 'Gagal membatalkan lamaran');
                    }
                })
                .catch(error => {
                    console.error('❌ Cancel error:', error);
                    
                    let errorMessage = 'Terjadi kesalahan saat membatalkan lamaran';
                    
                    if (error.response?.status === 404) {
                        errorMessage = 'Lamaran tidak ditemukan atau sudah dibatalkan';
                    } else if (error.response?.status === 400) {
                        errorMessage = 'Lamaran tidak dapat dibatalkan karena sudah diproses';
                    } else if (error.response?.data?.message) {
                        errorMessage = error.response.data.message;
                    } else if (error.code === 'ECONNABORTED') {
                        errorMessage = 'Request timeout. Silakan refresh halaman untuk melihat perubahan.';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Membatalkan',
                        text: errorMessage,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545',
                        customClass: {
                            confirmButton: 'btn btn-danger btn-sm'
                        },
                        buttonsStyling: false
                    });
                });
        }
    });
}

// ✅ NEW: Function untuk refresh data lamaran via AJAX
function refreshLamaranData() {
    console.log('🔄 === REFRESHING LAMARAN DATA ===');
    
    // Show loading state pada button refresh
    const refreshBtn = document.getElementById('refreshLamaranBtn');
    const refreshIcon = document.getElementById('refreshIcon');
    
    if (refreshBtn) {
        refreshBtn.disabled = true;
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';
    }
    
    if (refreshIcon) {
        refreshIcon.classList.add('fa-spin');
    }
    
    // Reset ke skeleton loading
    resetToSkeletonLoading();
    
    // AJAX call untuk reload data
    api.get('/mahasiswa/lamaran/reload')
        .then(response => {
            console.log('✅ Refresh success:', response.data);
            
            if (response.data?.success) {
                // Update data global
                updatePageData(response.data);
                
                // Simulate loading animation dengan data fresh
                setTimeout(() => {
                    simulateContentLoadingWithFreshData(response.data);
                }, 300);
                
                // Show success toast
                showToast('success', 'Data berhasil diperbarui');
                
            } else {
                throw new Error(response.data?.message || 'Gagal memuat data');
            }
        })
        .catch(error => {
            console.error('❌ Refresh error:', error);
            
            // Show current content jika error
            showCurrentContent();
            
            let errorMessage = 'Gagal memuat ulang data';
            if (error.code === 'ECONNABORTED') {
                errorMessage = 'Request timeout. Silakan coba lagi.';
            } else if (error.response?.status === 500) {
                errorMessage = 'Server error. Silakan refresh halaman.';
            } else if (error.response?.data?.message) {
                errorMessage = error.response.data.message;
            }
            
            showToast('error', errorMessage);
        })
        .finally(() => {
            // Reset button refresh
            if (refreshBtn) {
                refreshBtn.disabled = false;
                refreshBtn.innerHTML = '<i class="fas fa-sync-alt me-1"></i>Refresh';
            }
            
            if (refreshIcon) {
                refreshIcon.classList.remove('fa-spin');
            }
        });
}

// ✅ NEW: Update data halaman dengan response fresh
function updatePageData(responseData) {
    // Update server data global
    serverData.magangInfo = !!responseData.magangInfo;
    
    // Store fresh data untuk digunakan dalam animasi
    window.freshData = {
        statistik: responseData.statistik || {},
        lamaranHistory: responseData.lamaranHistory || [],
        magangInfo: responseData.magangInfo,
        showLamaranHistory: responseData.showLamaranHistory
    };
    
    console.log('📊 Fresh data stored:', window.freshData);
}

// ✅ NEW: Simulate loading dengan data fresh
function simulateContentLoadingWithFreshData(responseData) {
    console.log('⏳ Starting fresh content loading...');
    
    // Load stats cards dengan data baru
    setTimeout(() => loadStatsCardWithFreshData(1, responseData.statistik?.total || 0), 300);
    setTimeout(() => loadStatsCardWithFreshData(2, responseData.statistik?.menunggu || 0), 600);
    setTimeout(() => loadStatsCardWithFreshData(3, responseData.statistik?.diterima || 0), 900);
    setTimeout(() => loadStatsCardWithFreshData(4, responseData.statistik?.ditolak || 0), 1200);
    
    // Load magang card jika ada
    if (responseData.magangInfo) {
        setTimeout(() => loadMagangCardWithFreshData(responseData.magangInfo), 1500);
    }
    
    // Load table dengan data baru
    setTimeout(() => loadTableContentWithFreshData(responseData), 2000);
}

// ✅ NEW: Load stats card dengan data fresh
function loadStatsCardWithFreshData(cardNumber, newValue) {
    const skeleton = document.getElementById('skeleton-stats-' + cardNumber);
    const realContent = document.getElementById('real-stats-' + cardNumber);
    
    if (!skeleton || !realContent) return;
    
    skeleton.style.transition = 'opacity 0.3s ease';
    skeleton.style.opacity = '0';
    
    setTimeout(() => {
        skeleton.classList.add('d-none');
        realContent.classList.remove('d-none');
        
        // Update counter dengan nilai baru
        const counter = realContent.querySelector('.counter-number');
        if (counter) {
            counter.dataset.target = newValue;
            counter.textContent = '0'; // Reset counter
        }
        
        realContent.style.opacity = '0';
        realContent.style.transform = 'translateY(20px)';
        realContent.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        
        setTimeout(() => {
            realContent.style.opacity = '1';
            realContent.style.transform = 'translateY(0)';
            
            if (counter) {
                setTimeout(() => animateCounter(counter), 200);
            }
        }, 50);
    }, 300);
}

// ✅ NEW: Load table dengan data fresh
function loadTableContentWithFreshData(responseData) {
    const skeleton = document.getElementById('table-skeleton-loading');
    const realContent = document.getElementById('real-table-content');
    
    if (!skeleton || !realContent) return;
    
    skeleton.style.transition = 'opacity 0.4s ease';
    skeleton.style.opacity = '0';
    
    setTimeout(() => {
        skeleton.classList.add('d-none');
        
        // ✅ UPDATE: Rebuild table dengan data fresh
        if (responseData.lamaranHistory && responseData.lamaranHistory.length > 0) {
            rebuildLamaranTable(responseData.lamaranHistory);
        } else {
            showEmptyTableState();
        }
        
        realContent.classList.remove('d-none');
        realContent.style.opacity = '0';
        realContent.style.transform = 'translateY(20px)';
        realContent.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        
        setTimeout(() => {
            realContent.style.opacity = '1';
            realContent.style.transform = 'translateY(0)';
            
            animateTableRows();
            initializeTooltips();
            
            // Reset filter setelah refresh
            const statusFilter = document.getElementById('statusFilter');
            if (statusFilter) {
                statusFilter.value = 'all';
            }
        }, 50);
    }, 400);
}

// ✅ NEW: Rebuild table dengan data fresh
function rebuildLamaranTable(lamaranData) {
    const tableBody = document.getElementById('lamaranTableBody');
    if (!tableBody) return;
    
    let tableHTML = '';
    
    lamaranData.forEach((lamaran, index) => {
        // Generate logo HTML
        let logoHTML = '';
        if (lamaran.logo_url && lamaran.logo_url !== '') {
            logoHTML = `<img src="${lamaran.logo_url}" 
                             class="avatar avatar-sm me-3 border-radius-lg" 
                             alt="Logo ${lamaran.nama_perusahaan}"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'avatar avatar-sm bg-gradient-secondary me-3 border-radius-lg d-flex align-items-center justify-content-center\\'><i class=\\'fas fa-building text-white text-sm\\'></i></div>';">`;
        } else {
            logoHTML = `<div class="avatar avatar-sm bg-gradient-secondary me-3 border-radius-lg d-flex align-items-center justify-content-center">
                            <i class="fas fa-building text-white text-sm"></i>
                        </div>`;
        }
        
        // Generate status badge
        let statusBadge = '';
        if (lamaran.status === 'diterima') {
            statusBadge = '<span class="badge bg-gradient-success status-badge"><i class="fas fa-check me-1"></i>Diterima</span>';
        } else if (lamaran.status === 'ditolak') {
            statusBadge = '<span class="badge bg-gradient-danger status-badge"><i class="fas fa-times me-1"></i>Ditolak</span>';
        } else {
            statusBadge = '<span class="badge bg-gradient-warning status-badge"><i class="fas fa-clock me-1"></i>Menunggu</span>';
        }
        
        // Generate action buttons
        let actionButtons = `
            <button class="btn btn-link text-primary mb-0 px-1 me-1" 
                    onclick="detailLamaran(${lamaran.id_lamaran})" 
                    data-bs-toggle="tooltip" 
                    title="Lihat Detail">
                <i class="fas fa-eye text-xs"></i>
            </button>
        `;
        
        if (lamaran.status === 'menunggu') {
            actionButtons += `
                <button class="btn btn-link text-danger mb-0 px-1" 
                        onclick="cancelLamaran(${lamaran.id_lamaran})" 
                        data-bs-toggle="tooltip" 
                        title="Batalkan Lamaran">
                    <i class="fas fa-times text-xs"></i>
                </button>
            `;
        }
        
        // Format tanggal
        const tanggalLamaran = new Date(lamaran.tanggal_lamaran);
        const formattedDate = tanggalLamaran.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
        const relativeTime = getRelativeTime(lamaran.tanggal_lamaran);
        
        tableHTML += `
            <tr class="lamaran-row fade-in-row" data-status="${lamaran.status}" data-index="${index}">
                <td>
                    <div class="d-flex px-2 py-1">
                        <div class="company-avatar">
                            ${logoHTML}
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm font-weight-bold">${lamaran.nama_perusahaan}</h6>
                            ${lamaran.nama_kota ? `<p class="text-xs text-secondary mb-0"><i class="fas fa-map-marker-alt me-1"></i>${lamaran.nama_kota}</p>` : ''}
                        </div>
                    </div>
                </td>
                <td>
                    <p class="text-sm font-weight-bold mb-0">${lamaran.judul_lowongan}</p>
                    <p class="text-xs text-secondary mb-0">${lamaran.deskripsi_lowongan ? lamaran.deskripsi_lowongan.substring(0, 50) + '...' : 'Tidak ada deskripsi'}</p>
                </td>
                <td class="align-middle text-center text-sm">
                    ${statusBadge}
                </td>
                <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">${formattedDate}</span>
                    <br>
                    <span class="text-xs text-secondary">${relativeTime}</span>
                </td>
                <td class="align-middle">
                    <div class="d-flex align-items-center justify-content-center">
                        ${actionButtons}
                    </div>
                </td>
            </tr>
        `;
    });
    
    tableBody.innerHTML = tableHTML;
}

// ✅ NEW: Show empty state untuk table
function showEmptyTableState() {
    const realContent = document.getElementById('real-table-content');
    if (!realContent) return;
    
    realContent.innerHTML = `
        <div class="empty-table-state text-center py-5">
            <div class="empty-table-icon mb-3">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h6 class="mb-2">Belum Ada Lamaran</h6>
            <p class="text-muted mb-4">Anda belum mengajukan lamaran magang. Mulai cari lowongan yang sesuai dengan minat Anda!</p>
            <a href="${serverData.lowonganRoute}" class="btn btn-primary btn-sm">
                <i class="fas fa-search me-2"></i>Cari Lowongan
            </a>
        </div>
    `;
}

// ✅ NEW: Filter lamaran berdasarkan status
function filterLamaran() {
    const filterValue = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.lamaran-row');
    
    console.log('🔍 Filtering lamaran by status:', filterValue);
    
    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        
        if (filterValue === 'all' || status === filterValue) {
            row.style.display = '';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        } else {
            row.style.opacity = '0';
            row.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                row.style.display = 'none';
            }, 200);
        }
    });
    
    // Show/hide empty state jika tidak ada data yang cocok
    setTimeout(() => {
        const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
        const tableContainer = document.querySelector('.table-responsive');
        const emptyState = document.querySelector('.filter-empty-state');
        
        if (visibleRows.length === 0 && filterValue !== 'all') {
            if (!emptyState) {
                const emptyHTML = `
                    <div class="filter-empty-state text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-filter text-muted" style="font-size: 2rem;"></i>
                        </div>
                        <h6 class="mb-2">Tidak Ada Data</h6>
                        <p class="text-muted mb-3">Tidak ada lamaran dengan status "${getStatusText(filterValue)}"</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="resetFilter()">
                            <i class="fas fa-times me-1"></i>Reset Filter
                        </button>
                    </div>
                `;
                
                if (tableContainer) {
                    tableContainer.insertAdjacentHTML('afterend', emptyHTML);
                }
            }
            
            if (tableContainer) tableContainer.style.display = 'none';
        } else {
            if (emptyState) emptyState.remove();
            if (tableContainer) tableContainer.style.display = 'block';
        }
    }, 250);
    
    showToast('info', `Filter diterapkan: ${getStatusText(filterValue)}`);
}

// ✅ NEW: Reset filter
function resetFilter() {
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.value = 'all';
        filterLamaran();
    }
}

// ✅ PERBAIKI: Reset ke skeleton loading yang lebih baik
function resetToSkeletonLoading() {
    console.log('🔄 Resetting to skeleton loading...');
    
    // Hide real contents
    const realContents = document.querySelectorAll('#real-stats-1, #real-stats-2, #real-stats-3, #real-stats-4, #real-table-content, #real-magang');
    realContents.forEach(content => {
        content.style.transition = 'opacity 0.2s ease';
        content.style.opacity = '0';
        setTimeout(() => {
            content.classList.add('d-none');
        }, 200);
    });
    
    // Show skeletons
    const skeletons = document.querySelectorAll('#skeleton-stats-1, #skeleton-stats-2, #skeleton-stats-3, #skeleton-stats-4, #table-skeleton-loading, #magang-skeleton');
    skeletons.forEach(skeleton => {
        skeleton.classList.remove('d-none');
        skeleton.style.opacity = '1';
    });
    
    // Clear any filter empty states
    const filterEmptyState = document.querySelector('.filter-empty-state');
    if (filterEmptyState) {
        filterEmptyState.remove();
    }
}

// ✅ PERBAIKI: Show current content yang lebih smooth
function showCurrentContent() {
    console.log('📱 Showing current content...');
    
    // Hide skeletons
    document.querySelectorAll('#skeleton-stats-1, #skeleton-stats-2, #skeleton-stats-3, #skeleton-stats-4, #table-skeleton-loading, #magang-skeleton')
        .forEach(skeleton => {
            skeleton.style.transition = 'opacity 0.2s ease';
            skeleton.style.opacity = '0';
            setTimeout(() => {
                skeleton.classList.add('d-none');
            }, 200);
        });
    
    // Show real contents
    setTimeout(() => {
        document.querySelectorAll('#real-stats-1, #real-stats-2, #real-stats-3, #real-stats-4, #real-table-content, #real-magang')
            .forEach(content => {
                content.classList.remove('d-none');
                content.style.transition = 'opacity 0.3s ease';
                content.style.opacity = '1';
            });
    }, 250);
}

// ✅ ENHANCED: Toast dengan lebih banyak tipe
function showToast(type, message, duration = 5000) {
    const toastColors = {
        'success': { bg: '#d4edda', border: '#c3e6cb', color: '#155724', icon: 'check-circle' },
        'error': { bg: '#f8d7da', border: '#f5c6cb', color: '#721c24', icon: 'exclamation-circle' },
        'info': { bg: '#d1ecf1', border: '#bee5eb', color: '#0c5460', icon: 'info-circle' },
        'warning': { bg: '#fff3cd', border: '#ffeaa7', color: '#856404', icon: 'exclamation-triangle' }
    };
    
    const colors = toastColors[type] || toastColors.info;
    
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type} position-fixed`;
    toast.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 400px;
        background: ${colors.bg};
        border: 1px solid ${colors.border};
        color: ${colors.color};
        padding: 12px 16px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${colors.icon} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" 
                    onclick="this.parentElement.parentElement.remove()"
                    style="font-size: 0.75rem;"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, duration);
}

// ✅ ADD: Tambahkan semua fungsi yang hilang setelah API interceptors
</script>
@endpush
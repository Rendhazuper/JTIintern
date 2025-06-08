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
                                <div class="card-header-controls">
                                    <!-- Refresh Button -->
                                    <button class="btn btn-outline-primary btn-sm" id="refreshLamaranBtn" onclick="refreshLamaranData()">
                                        <i class="fas fa-sync-alt me-1" id="refreshIcon"></i>Refresh
                                    </button>
                                    
                                    <!-- Filter Dropdown -->
                                    <select class="form-select form-select-sm" id="statusFilter" onchange="filterLamaran()">
                                        <option value="all">Semua Status</option>
                                        <option value="menunggu">Menunggu</option>
                                        <option value="diterima">Diterima</option>
                                        <option value="ditolak">Ditolak</option>
                                    </select>
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
                                                                        
                                                                        // Cek logo_url terlebih dahulu
                                                                        if (isset($lamaran->logo_url) && !empty($lamaran->logo_url)) {
                                                                            $logoSrc = $lamaran->logo_url;
                                                                            $hasLogo = true;
                                                                        }
                                                                        // Fallback ke logo_perusahaan
                                                                        elseif (isset($lamaran->logo_perusahaan) && !empty($lamaran->logo_perusahaan)) {
                                                                            if (str_starts_with($lamaran->logo_perusahaan, 'http')) {
                                                                                $logoSrc = $lamaran->logo_perusahaan;
                                                                            } elseif (str_starts_with($lamaran->logo_perusahaan, 'storage/')) {
                                                                                $logoSrc = asset($lamaran->logo_perusahaan);
                                                                            } else {
                                                                                $logoSrc = asset('storage/' . $lamaran->logo_perusahaan);
                                                                            }
                                                                            $hasLogo = true;
                                                                        }
                                                                        // Fallback ke logo (jika ada)
                                                                        elseif (isset($lamaran->logo) && !empty($lamaran->logo)) {
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
                                                                             alt="Logo {{ $lamaran->nama_perusahaan ?? 'Perusahaan' }}"
                                                                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'avatar avatar-sm bg-gradient-danger me-3 border-radius-lg d-flex align-items-center justify-content-center\'><i class=\'fas fa-building text-white text-sm\' title=\'Logo tidak dapat dimuat\'></i></div>';">
                                                                    @else
                                                                        <div class="avatar avatar-sm bg-gradient-secondary me-3 border-radius-lg d-flex align-items-center justify-content-center">
                                                                            <i class="fas fa-building text-white text-sm"></i>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <h6 class="mb-0 text-sm font-weight-bold">{{ $lamaran->nama_perusahaan ?? 'Nama Perusahaan' }}</h6>
                                                                    @if(isset($lamaran->nama_kota) && $lamaran->nama_kota)
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
                                                                <button class="btn btn-link text-secondary mb-0 px-1" onclick="viewLamaranDetail({{ $lamaran->id_lamaran }})" data-bs-toggle="tooltip" title="Lihat Detail">
                                                                    <i class="fas fa-eye text-xs"></i>
                                                                </button>
                                                                @if($lamaran->status == 'menunggu')
                                                                    <button class="btn btn-link text-danger mb-0 px-1" onclick="cancelLamaran({{ $lamaran->id_lamaran }})" data-bs-toggle="tooltip" title="Batalkan Lamaran">
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
<script>
// Definisikan data dari server di awal script
const serverData = {
    magangInfo: @json(isset($magangInfo) && $magangInfo ? true : false),
    lowonganRoute: "{{ route('mahasiswa.lowongan') }}"
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 === LOADING CONTENT AND TABLES ===');
    
    // Start loading simulation when page loads
    simulateContentLoading();
});

// Tambahkan function yang hilang
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function showToast(type, message) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'toast-notification toast-' + type;
    toast.innerHTML = 
        '<div class="toast-content">' +
            '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + ' me-2"></i>' +
            message +
        '</div>';
    
    document.body.appendChild(toast);
    
    // Show toast
    setTimeout(function() {
        toast.classList.add('show');
    }, 100);
    
    // Hide and remove toast
    setTimeout(function() {
        toast.classList.remove('show');
        setTimeout(function() {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

function simulateContentLoading() {
    console.log('⏳ Starting content loading simulation...');
    
    // Step 1: Load stats cards (staggered)
    setTimeout(function() {
        loadStatsCard(1);
    }, 300);
    
    setTimeout(function() {
        loadStatsCard(2);
    }, 600);
    
    setTimeout(function() {
        loadStatsCard(3);
    }, 900);
    
    setTimeout(function() {
        loadStatsCard(4);
    }, 1200);
    
    // Step 2: Load magang card if exists
    if (serverData.magangInfo) {
        setTimeout(function() {
            loadMagangCard();
        }, 1500);
    }
    
    // Step 3: Load table content
    setTimeout(function() {
        loadTableContent();
    }, 2000);
}

function loadStatsCard(cardNumber) {
    const skeleton = document.getElementById('skeleton-stats-' + cardNumber);
    const realContent = document.getElementById('real-stats-' + cardNumber);
    
    if (!skeleton || !realContent) return;
    
    // Fade out skeleton
    skeleton.style.transition = 'opacity 0.3s ease';
    skeleton.style.opacity = '0';
    
    setTimeout(function() {
        skeleton.classList.add('d-none');
        realContent.classList.remove('d-none');
        
        // Animate real content in
        realContent.style.opacity = '0';
        realContent.style.transform = 'translateY(20px)';
        realContent.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        
        setTimeout(function() {
            realContent.style.opacity = '1';
            realContent.style.transform = 'translateY(0)';
            
            // Animate counter
            const counter = realContent.querySelector('.counter-number');
            if (counter) {
                setTimeout(function() {
                    animateCounter(counter);
                }, 200);
            }
        }, 50);
    }, 300);
}

function loadMagangCard() {
    const skeleton = document.getElementById('magang-skeleton');
    const realContent = document.getElementById('real-magang');
    
    if (!skeleton || !realContent) return;
    
    console.log('📊 Loading magang card...');
    
    // Fade out skeleton
    skeleton.style.transition = 'opacity 0.4s ease';
    skeleton.style.opacity = '0';
    
    setTimeout(function() {
        skeleton.classList.add('d-none');
        realContent.classList.remove('d-none');
        
        // Animate real content in
        realContent.style.opacity = '0';
        realContent.style.transform = 'translateY(30px)';
        realContent.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        
        setTimeout(function() {
            realContent.style.opacity = '1';
            realContent.style.transform = 'translateY(0)';
            
            // Animate progress bar
            const progressBar = realContent.querySelector('.progress-bar[data-width]');
            if (progressBar) {
                setTimeout(function() {
                    progressBar.style.transition = 'width 1.5s ease';
                    progressBar.style.width = progressBar.dataset.width;
                }, 300);
            }
            
            // Animate counters
            const counters = realContent.querySelectorAll('.counter-number');
            counters.forEach(function(counter, index) {
                setTimeout(function() {
                    animateCounter(counter);
                }, 400 + (index * 200));
            });
        }, 50);
    }, 400);
}

function loadTableContent() {
    const skeleton = document.getElementById('table-skeleton-loading');
    const realContent = document.getElementById('real-table-content');
    
    if (!skeleton || !realContent) return;
    
    console.log('📋 Loading table content...');
    
    // Fade out skeleton
    skeleton.style.transition = 'opacity 0.4s ease';
    skeleton.style.opacity = '0';
    
    setTimeout(function() {
        skeleton.classList.add('d-none');
        realContent.classList.remove('d-none');
        
        // Animate real content in
        realContent.style.opacity = '0';
        realContent.style.transform = 'translateY(20px)';
        realContent.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        
        setTimeout(function() {
            realContent.style.opacity = '1';
            realContent.style.transform = 'translateY(0)';
            
            // Animate table rows
            animateTableRows();
            
            // Initialize tooltips
            initializeTooltips();
        }, 50);
    }, 400);
}

function animateTableRows() {
    const rows = document.querySelectorAll('.fade-in-row');
    
    rows.forEach(function(row, index) {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-30px)';
        
        setTimeout(function() {
            row.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, index * 100);
    });
}

function animateCounter(element) {
    const target = parseInt(element.dataset.target) || 0;
    const duration = 1200;
    const startTime = performance.now();
    
    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function
        const easeOut = 1 - Math.pow(1 - progress, 3);
        const current = Math.floor(easeOut * target);
        
        element.textContent = current;
        
        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        } else {
            // Add bounce effect
            element.style.transform = 'scale(1.05)';
            setTimeout(function() {
                element.style.transition = 'transform 0.2s ease';
                element.style.transform = 'scale(1)';
            }, 100);
        }
    }
    
    requestAnimationFrame(updateCounter);
}

function animateCounterUpdate(element, newValue) {
    const currentValue = parseInt(element.textContent) || 0;
    const duration = 800;
    const startTime = performance.now();
    
    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function
        const easeOut = 1 - Math.pow(1 - progress, 3);
        const current = Math.floor(easeOut * (newValue - currentValue)) + currentValue;
        
        element.textContent = current;
        
        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        }
    }
    
    requestAnimationFrame(updateCounter);
}

// Ganti function cancelLamaran dengan versi lengkap:
function cancelLamaran(id) {
    Swal.fire({
        title: 'Batalkan Lamaran?',
        text: 'Tindakan ini tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d'
    }).then(function(result) {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Membatalkan...',
                html: 'Sedang memproses pembatalan lamaran',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: function() {
                    Swal.showLoading();
                }
            });

            // AJAX Request untuk cancel
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/api/mahasiswa/lamaran/' + id + '/cancel',
                type: 'DELETE',
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    console.log('✅ Cancel success:', response);
                    
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Lamaran telah dibatalkan.',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            timer: 2000
                        }).then(function() {
                            // Show success toast
                            showToast('success', 'Lamaran berhasil dibatalkan');
                            
                            // 🎯 GUNAKAN FULL PAGE RELOAD VIA AJAX
                            fullPageReloadViaAjax();
                        });
                    } else {
                        throw new Error(response.message || 'Gagal membatalkan lamaran');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Cancel error:', error);
                    
                    let errorMessage = 'Terjadi kesalahan saat membatalkan lamaran';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (status === 'timeout') {
                        errorMessage = 'Request timeout. Silakan refresh halaman untuk melihat perubahan.';
                    }
                    
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

// ✅ TAMBAHKAN SEMUA FUNCTION YANG HILANG:

function fullPageReloadViaAjax() {
    console.log('🔄 === FULL PAGE RELOAD VIA AJAX ===');
    
    // 1. Reset semua content ke skeleton loading
    resetToSkeletonLoading();
    
    // 2. Fetch fresh content dari server
    $.ajax({
        url: '/api/mahasiswa/lamaran/reload',
        type: 'GET',
        dataType: 'json',
        timeout: 20000,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('✅ Full reload success:', response);
            
            if (response.success) {
                // 3. Update serverData dengan data fresh
                updateServerData(response);
                
                // 4. Simulate loading sequence seperti pertama kali
                setTimeout(function() {
                    simulateContentLoadingAfterReload(response);
                }, 500);
                
                showToast('success', 'Data berhasil diperbarui');
            } else {
                throw new Error(response.message || 'Gagal memuat data');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Full reload error:', {
                status: status,
                error: error,
                responseText: xhr.responseText,
                statusCode: xhr.status
            });
            
            // Fallback: show current content
            showCurrentContent();
            
            let errorMessage = 'Gagal memuat ulang data';
            if (status === 'timeout') {
                errorMessage = 'Request timeout. Data mungkin sudah diperbarui.';
            } else if (xhr.status === 500) {
                errorMessage = 'Server error. Silakan refresh halaman.';
            }
            
            showToast('error', errorMessage);
        }
    });
}

function resetToSkeletonLoading() {
    console.log('🔄 Resetting to skeleton loading...');
    
    // Hide all real content
    const realContents = document.querySelectorAll('#real-stats-1, #real-stats-2, #real-stats-3, #real-stats-4, #real-table-content, #real-magang');
    realContents.forEach(function(content) {
        content.classList.add('d-none');
    });
    
    // Show all skeletons
    const skeletons = document.querySelectorAll('#skeleton-stats-1, #skeleton-stats-2, #skeleton-stats-3, #skeleton-stats-4, #table-skeleton-loading, #magang-skeleton');
    skeletons.forEach(function(skeleton) {
        skeleton.classList.remove('d-none');
        skeleton.style.opacity = '1';
    });
}

function updateServerData(response) {
    console.log('🔄 Updating server data...');
    
    // Update global serverData
    if (response.magangInfo) {
        serverData.magangInfo = true;
    } else {
        serverData.magangInfo = false;
    }
    
    // Store fresh data in global variable
    window.freshData = {
        statistik: response.statistik,
        lamaranHistory: response.lamaranHistory,
        magangInfo: response.magangInfo
    };
    
    console.log('📊 Fresh data stored:', window.freshData);
}

function simulateContentLoadingAfterReload(response) {
    console.log('⏳ Starting content loading with fresh data...');
    
    // Step 1: Load stats cards dengan data fresh (staggered)
    setTimeout(function() {
        loadStatsCardWithData(1, response.statistik.total);
    }, 300);
    
    setTimeout(function() {
        loadStatsCardWithData(2, response.statistik.menunggu);
    }, 600);
    
    setTimeout(function() {
        loadStatsCardWithData(3, response.statistik.diterima);
    }, 900);
    
    setTimeout(function() {
        loadStatsCardWithData(4, response.statistik.ditolak);
    }, 1200);
    
    // Step 2: Load magang card if exists
    if (response.magangInfo) {
        setTimeout(function() {
            loadMagangCardWithData(response.magangInfo);
        }, 1500);
    }
    
    // Step 3: Load table content dengan data fresh
    setTimeout(function() {
        loadTableContentWithData(response);
    }, 2000);
}

function loadStatsCardWithData(cardNumber, value) {
    const skeleton = document.getElementById('skeleton-stats-' + cardNumber);
    const realContent = document.getElementById('real-stats-' + cardNumber);
    
    if (!skeleton || !realContent) return;
    
    // Update counter target dengan nilai fresh
    const counter = realContent.querySelector('.counter-number');
    if (counter) {
        counter.setAttribute('data-target', value);
        counter.textContent = '0'; // Reset to 0
    }
    
    // Fade out skeleton
    skeleton.style.transition = 'opacity 0.3s ease';
    skeleton.style.opacity = '0';
    
    setTimeout(function() {
        skeleton.classList.add('d-none');
        realContent.classList.remove('d-none');
        
        // Animate real content in
        realContent.style.opacity = '0';
        realContent.style.transform = 'translateY(20px)';
        realContent.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        
        setTimeout(function() {
            realContent.style.opacity = '1';
            realContent.style.transform = 'translateY(0)';
            
            // Animate counter dengan nilai fresh
            if (counter) {
                setTimeout(function() {
                    animateCounter(counter);
                }, 200);
            }
        }, 50);
    }, 300);
}

// ✅ PERBAIKI: Function generateTableHTML dengan logo yang dinamis
function generateTableHTML(lamaranHistory) {
    if (!lamaranHistory || lamaranHistory.length === 0) {
        return '';
    }
    
    let tableHtml = '';
    
    lamaranHistory.forEach(function(lamaran, index) {
        const statusBadge = getStatusBadge(lamaran.status);
        const cancelButton = lamaran.status === 'menunggu' ? 
            '<button class="btn btn-link text-danger mb-0 px-1" onclick="cancelLamaran(' + lamaran.id_lamaran + ')" data-bs-toggle="tooltip" title="Batalkan Lamaran">' +
                '<i class="fas fa-times text-xs"></i>' +
            '</button>' : '';
        
        // ✅ PERBAIKI: Logo handling yang dinamis
        let logoHtml;
        if (lamaran.logo_url && lamaran.logo_url !== null && lamaran.logo_url !== '') {
            logoHtml = '<img src="' + lamaran.logo_url + '" ' +
                      'class="avatar avatar-sm me-3 border-radius-lg" ' +
                      'alt="Logo ' + (lamaran.nama_perusahaan || 'Perusahaan') + '" ' +
                      'onerror="handleTableLogoError(this, \'' + (lamaran.nama_perusahaan || 'Perusahaan') + '\')">';
        } else if (lamaran.logo_perusahaan && lamaran.logo_perusahaan !== null && lamaran.logo_perusahaan !== '') {
            // ✅ FALLBACK: Gunakan logo_perusahaan jika logo_url tidak ada
            let logoSrc;
            if (lamaran.logo_perusahaan.startsWith('http')) {
                logoSrc = lamaran.logo_perusahaan;
            } else if (lamaran.logo_perusahaan.startsWith('storage/')) {
                logoSrc = '/' + lamaran.logo_perusahaan;
            } else {
                logoSrc = '/storage/' + lamaran.logo_perusahaan;
            }
            
            logoHtml = '<img src="' + logoSrc + '" ' +
                      'class="avatar avatar-sm me-3 border-radius-lg" ' +
                      'alt="Logo ' + (lamaran.nama_perusahaan || 'Perusahaan') + '" ' +
                      'onerror="handleTableLogoError(this, \'' + (lamaran.nama_perusahaan || 'Perusahaan') + '\')">';
        } else {
            // ✅ DEFAULT: Placeholder jika tidak ada logo sama sekali
            logoHtml = '<div class="avatar avatar-sm bg-gradient-secondary me-3 border-radius-lg d-flex align-items-center justify-content-center">' +
                      '<i class="fas fa-building text-white text-sm"></i>' +
                      '</div>';
        }
        
        const kotaHtml = lamaran.nama_kota ? 
            '<p class="text-xs text-secondary mb-0">' +
                '<i class="fas fa-map-marker-alt me-1"></i>' + lamaran.nama_kota +
            '</p>' : '';
        
        const deskripsi = lamaran.deskripsi_lowongan || lamaran.deskripsi || 'Tidak ada deskripsi';
        const deskripsiShort = deskripsi.length > 50 ? deskripsi.substring(0, 50) + '...' : deskripsi;
        
        tableHtml += 
            '<tr class="lamaran-row fade-in-row" data-status="' + lamaran.status + '" data-index="' + index + '" data-lamaran-id="' + lamaran.id_lamaran + '">' +
                '<td>' +
                    '<div class="d-flex px-2 py-1">' +
                        '<div class="company-avatar">' + logoHtml + '</div>' +
                        '<div class="d-flex flex-column justify-content-center">' +
                            '<h6 class="mb-0 text-sm font-weight-bold">' + (lamaran.nama_perusahaan || 'Nama Perusahaan') + '</h6>' +
                            kotaHtml +
                        '</div>' +
                    '</div>' +
                '</td>' +
                '<td>' +
                    '<p class="text-sm font-weight-bold mb-0">' + (lamaran.judul_lowongan || 'Posisi Tidak Diketahui') + '</p>' +
                    '<p class="text-xs text-secondary mb-0">' + deskripsiShort + '</p>' +
                '</td>' +
                '<td class="align-middle text-center text-sm">' + statusBadge + '</td>' +
                '<td class="align-middle text-center">' +
                    '<span class="text-secondary text-xs font-weight-bold">' + formatDate(lamaran.tanggal_lamaran) + '</span>' +
                    '<br>' +
                    '<span class="text-xs text-secondary">' + getRelativeTime(lamaran.tanggal_lamaran) + '</span>' +
                '</td>' +
                '<td class="align-middle">' +
                    '<div class="d-flex align-items-center justify-content-center">' +
                        '<button class="btn btn-link text-secondary mb-0 px-1" onclick="viewLamaranDetail(' + lamaran.id_lamaran + ')" data-bs-toggle="tooltip" title="Lihat Detail">' +
                            '<i class="fas fa-eye text-xs"></i>' +
                        '</button>' +
                        cancelButton +
                    '</div>' +
                '</td>' +
            '</tr>';
    });
    
    return tableHtml;
}

// ✅ PERBAIKI: Function loadMagangCardWithData dengan logo yang dinamis
function loadMagangCardWithData(magangInfo) {
    const skeleton = document.getElementById('magang-skeleton');
    const realContent = document.getElementById('real-magang');
    
    if (!skeleton || !realContent) return;
    
    console.log('📊 Loading magang card with fresh data:', magangInfo);
    
    // ✅ PERBAIKI: Update logo di magang card dengan logic yang dinamis
    if (magangInfo.data) {
        const logoContainer = realContent.querySelector('.company-avatar');
        
        if (logoContainer) {
            let logoHtml;
            
            // Cek logo_url terlebih dahulu
            if (magangInfo.data.logo_url && magangInfo.data.logo_url !== null && magangInfo.data.logo_url !== '') {
                logoHtml = '<img src="' + magangInfo.data.logo_url + '" ' +
                          'class="avatar avatar-lg border-radius-lg" ' +
                          'alt="Logo ' + (magangInfo.data.nama_perusahaan || 'Perusahaan') + '" ' +
                          'onerror="handleMagangLogoError(this, \'' + (magangInfo.data.nama_perusahaan || 'Perusahaan') + '\')">';
            } else if (magangInfo.data.logo_perusahaan && magangInfo.data.logo_perusahaan !== null && magangInfo.data.logo_perusahaan !== '') {
                // ✅ FALLBACK: Gunakan logo_perusahaan
                let logoSrc;
                if (magangInfo.data.logo_perusahaan.startsWith('http')) {
                    logoSrc = magangInfo.data.logo_perusahaan;
                } else if (magangInfo.data.logo_perusahaan.startsWith('storage/')) {
                    logoSrc = '/' + magangInfo.data.logo_perusahaan;
                } else {
                    logoSrc = '/storage/' + magangInfo.data.logo_perusahaan;
                }
                
                logoHtml = '<img src="' + logoSrc + '" ' +
                          'class="avatar avatar-lg border-radius-lg" ' +
                          'alt="Logo ' + (magangInfo.data.nama_perusahaan || 'Perusahaan') + '" ' +
                          'onerror="handleMagangLogoError(this, \'' + (magangInfo.data.nama_perusahaan || 'Perusahaan') + '\')">';
            } else {
                // ✅ DEFAULT: Placeholder
                logoHtml = '<div class="avatar avatar-lg bg-gradient-secondary border-radius-lg d-flex align-items-center justify-content-center">' +
                          '<i class="fas fa-building text-white text-lg"></i>' +
                          '</div>';
            }
            
            logoContainer.innerHTML = logoHtml;
        }
    }
    
    // Real content animation
    skeleton.style.transition = 'opacity 0.4s ease';
    skeleton.style.opacity = '0';
    
    setTimeout(function() {
        skeleton.classList.add('d-none');
        realContent.classList.remove('d-none');
        
        // Animate real content in
        realContent.style.opacity = '0';
        realContent.style.transform = 'translateY(30px)';
        realContent.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        
        setTimeout(function() {
            realContent.style.opacity = '1';
            realContent.style.transform = 'translateY(0)';
            
            // Animate progress bar
            const progressBar = realContent.querySelector('.progress-bar[data-width]');
            if (progressBar) {
                setTimeout(function() {
                    progressBar.style.transition = 'width 1.5s ease';
                    progressBar.style.width = progressBar.dataset.width;
                }, 300);
            }
            
            // Animate counters
            const counters = realContent.querySelectorAll('.counter-number');
            counters.forEach(function(counter, index) {
                setTimeout(function() {
                    animateCounter(counter);
                }, 400 + (index * 200));
            });
        }, 50);
    }, 400);
}

// ✅ TAMBAHKAN: Function untuk handle error logo di table
function handleTableLogoError(img, companyName) {
    console.error('Table logo failed to load:', img.src);
    
    // Replace dengan placeholder
    const placeholder = document.createElement('div');
    placeholder.className = 'avatar avatar-sm bg-gradient-danger me-3 border-radius-lg d-flex align-items-center justify-content-center';
    placeholder.innerHTML = '<i class="fas fa-building text-white text-sm" title="Logo ' + companyName + ' tidak dapat dimuat"></i>';
    
    // Replace the img element
    img.parentNode.replaceChild(placeholder, img);
}

// ✅ TAMBAHKAN: Function untuk handle error logo di magang card
function handleMagangLogoError(img, companyName) {
    console.error('Magang logo failed to load:', img.src);
    
    // Replace dengan placeholder
    const placeholder = document.createElement('div');
    placeholder.className = 'avatar avatar-lg bg-gradient-danger border-radius-lg d-flex align-items-center justify-content-center';
    placeholder.innerHTML = '<i class="fas fa-building text-white text-lg" title="Logo ' + companyName + ' tidak dapat dimuat"></i>';
    
    // Replace the img element
    img.parentNode.replaceChild(placeholder, img);
}
</script>
@endpush
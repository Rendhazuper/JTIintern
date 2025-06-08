<!-- filepath: d:\laragon\www\JTIintern\resources\views\pages\mahasiswa\dashboard.blade.php -->

@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')
    
    <div class="container-fluid py-4">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <!-- ✅ PERBAIKI: Welcome Skeleton -->
                        <div id="welcome-skeleton" class="welcome-skeleton">
                            <div class="d-flex align-items-center">
                                <div class="skeleton-welcome-icon me-3"></div>
                                <div class="flex-grow-1">
                                    <div class="skeleton-text-xl mb-2"></div>
                                    <div class="skeleton-text-md"></div>
                                </div>
                                <div class="ms-auto">
                                    <div class="skeleton-status-badge"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Real Content (Hidden Initially) -->
                        <div id="welcome-content" class="real-welcome d-none">
                            <div class="d-flex align-items-center">
                                <div class="welcome-icon me-3">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div>
                                    <h4 class="mb-2">
                                        Selamat Datang, 
                                        @if(isset($userData) && $userData)
                                            {{ $userData->name ?? 'Mahasiswa' }}
                                        @else
                                            Mahasiswa
                                        @endif 
                                        👋
                                    </h4>
                                    <p class="text-muted mb-0">Mari mulai perjalanan magang Anda dan raih pengalaman terbaik!</p>
                                </div>
                                <div class="ms-auto">
                                    @if(isset($activePeriod) && $activePeriod)
                                        <span class="badge bg-primary px-3 py-2">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ $activePeriod->waktu ?? $activePeriod->nama_periode ?? 'Periode Aktif' }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning px-3 py-2">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Tidak ada periode aktif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Completion Card - dengan skeleton jika perlu -->
        @if(isset($profileCompletion) && !$profileCompletion['is_complete'])
        <div class="row mb-4">
            <div class="col-12">
                <!-- ✅ TAMBAH: Profile skeleton (opsional, bisa langsung show) -->
                <div id="profile-skeleton" class="profile-incomplete-skeleton d-none">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="skeleton-warning-icon me-3"></div>
                            <div class="flex-grow-1">
                                <div class="skeleton-text-lg mb-2"></div>
                                <div class="skeleton-text-md"></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="skeleton-complete-button me-2"></div>
                            <div class="skeleton-icon-box"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Real Profile Card -->
                <div id="profile-content" class="profile-incomplete-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="warning-icon me-3">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Profil Belum Lengkap</h6>
                                <p class="mb-0 text-sm">Lengkapi profil Anda untuk mendapatkan rekomendasi lowongan yang lebih akurat dan komunikasi yang tepat.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-complete-now me-2" onclick="showProfileCompletionModal()">
                                <i class="fas fa-user-edit me-1"></i>Lengkapi Sekarang
                            </button>
                            <button type="button" class="btn-close-card" onclick="hideProfileCard()" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Dashboard Content -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">Status Magang</h6>
                                <p class="text-sm mb-0">Informasi terkini tentang program magang Anda</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- ✅ PERBAIKI: Skeleton Loading for Magang Content -->
                        <div id="magang-skeleton" class="magang-skeleton-loading">
                            <div class="magang-skeleton-card">
                                <!-- ✅ Skeleton Header dengan struktur yang benar -->
                                <div class="p-4 border-bottom" style="border-color: #f0f2f5;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="skeleton-company-logo me-3"></div>
                                            <div class="flex-grow-1">
                                                <div class="skeleton-text-lg mb-2"></div>
                                                <div class="skeleton-text-md mb-1"></div>
                                                <div class="skeleton-text-sm"></div>
                                            </div>
                                        </div>
                                        <div class="skeleton-status-badge"></div>
                                    </div>
                                </div>
                                
                                <!-- ✅ Skeleton Progress dengan struktur yang benar -->
                                <div class="skeleton-progress-section">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="skeleton-text-md" style="width: 120px;"></div>
                                        <div class="skeleton-text-lg" style="width: 60px;"></div>
                                    </div>
                                    <div class="skeleton-progress-bar mb-4"></div>
                                    <div class="d-flex justify-content-between">
                                        <div class="text-center">
                                            <div class="skeleton-text-xs mb-1" style="width: 80px; margin: 0 auto;"></div>
                                            <div class="skeleton-text-sm" style="width: 60px; margin: 0 auto;"></div>
                                        </div>
                                        <div class="text-center">
                                            <div class="skeleton-text-xs mb-1" style="width: 80px; margin: 0 auto;"></div>
                                            <div class="skeleton-text-sm" style="width: 60px; margin: 0 auto;"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- ✅ Skeleton Details -->
                                <div class="p-4 border-bottom" style="border-color: #f0f2f5;">
                                    <div class="d-flex align-items-center">
                                        <div class="skeleton-detail-icon me-3"></div>
                                        <div class="flex-grow-1">
                                            <div class="skeleton-text-xs mb-1" style="width: 100px;"></div>
                                            <div class="skeleton-text-md" style="width: 150px;"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- ✅ Skeleton Action -->
                                <div class="p-4 text-center">
                                    <div class="skeleton-action-button"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Real Magang Content (Hidden Initially) -->
                        <div id="magang-content" class="real-magang d-none">
                            @if(isset($magangInfo) && $magangInfo)
                                <!-- MAGANG AKTIF CARD -->
                                <div class="magang-card">
                                    <div class="magang-header">
                                        <div class="company-info">
                                            <div class="company-logo">
                                                @if(isset($magangInfo['data']->logo_perusahaan) && $magangInfo['data']->logo_perusahaan)
                                                    <img src="{{ asset('storage/' . $magangInfo['data']->logo_perusahaan) }}" alt="Logo {{ $magangInfo['data']->nama_perusahaan }}">
                                                @else
                                                    <div class="company-initial">
                                                        {{ substr($magangInfo['data']->nama_perusahaan ?? 'P', 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="position-info">
                                                <h5 class="mb-1">{{ $magangInfo['data']->judul_lowongan ?? 'Posisi Magang' }}</h5>
                                                <p class="company-name">{{ $magangInfo['data']->nama_perusahaan ?? 'Nama Perusahaan' }}</p>
                                                @if(isset($magangInfo['data']->nama_kota) && $magangInfo['data']->nama_kota)
                                                    <small class="text-muted">
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        {{ $magangInfo['data']->nama_kota }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="status-badge">
                                            <span class="status-indicator"></span>
                                            Magang Aktif
                                        </div>
                                    </div>
                                    
                                    <div class="progress-container">
                                        <div class="progress-header">
                                            <span class="label">
                                                Progress Magang
                                                @if(isset($magangInfo['status_text']))
                                                    <small class="text-muted">({{ $magangInfo['status_text'] }})</small>
                                                @endif
                                            </span>
                                            <span class="value">{{ $magangInfo['progress'] ?? 0 }}%</span>
                                        </div>
                                        <div class="progress-bar-container">
                                            <div class="progress-bar" data-width="{{ $magangInfo['progress'] ?? 0 }}%" style="width: 0%;"></div>
                                        </div>
                                        <div class="date-info">
                                            <div class="date">
                                                <span class="label">
                                                    @if(($magangInfo['status_progress'] ?? '') === 'belum_mulai')
                                                        MULAI DALAM
                                                    @else
                                                        HARI LEWAT
                                                    @endif
                                                </span>
                                                <span class="value counter-number" data-target="{{ $magangInfo['lewat'] ?? 0 }}">
                                                    0 hari
                                                </span>
                                            </div>
                                            <div class="date">
                                                <span class="label">SISA HARI</span>
                                                <span class="value counter-number" data-target="{{ $magangInfo['sisaHari'] ?? 0 }}">
                                                    0 hari
                                                </span>
                                            </div>
                                        </div>
                                        
                                        @if(isset($magangInfo['tgl_mulai_formatted']) && isset($magangInfo['tgl_selesai_formatted']))
                                        <div class="date-range-info mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                {{ $magangInfo['tgl_mulai_formatted'] }} - {{ $magangInfo['tgl_selesai_formatted'] }}
                                                @if(isset($magangInfo['totalDurasi']))
                                                    ({{ $magangInfo['totalDurasi'] }} hari)
                                                @endif
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    @if(isset($magangInfo['data']->nama_pembimbing) && $magangInfo['data']->nama_pembimbing)
                                    <div class="details-container">
                                        <div class="detail-item">
                                            <div class="detail-icon">
                                                <i class="fas fa-chalkboard-teacher"></i>
                                            </div>
                                            <div class="detail-content">
                                                <span class="label">PEMBIMBING</span>
                                                <span class="value">{{ $magangInfo['data']->nama_pembimbing }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="action-container">
                                        <a href="{{ route('mahasiswa.magang') }}" class="action-button">
                                            <i class="fas fa-eye"></i>
                                            Lihat Detail Magang
                                        </a>
                                    </div>
                                </div>
                            @else
                                <!-- CARD BELUM MAGANG -->
                                <div class="empty-magang-card">
                                    <div class="empty-icon">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <h5 class="mb-3">Belum Memiliki Magang</h5>
                                    <p class="text-muted mb-4">
                                        Saat ini Anda belum terdaftar pada program magang manapun. 
                                        Eksplorasi berbagai lowongan yang tersedia dan ajukan lamaran agar tidak tertinggal!
                                    </p>
                                    <a href="{{ route('mahasiswa.lowongan') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-search me-2"></i>Cari Lowongan
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekomendasi Section -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Rekomendasi Tempat Magang</h6>
                                <p class="text-sm mb-0">Rekomendasi sesuai dengan lokasi, keahlian, dan IPK Anda</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- ✅ PERBAIKI: Skeleton Loading for Recommendations -->
                        <div id="recommendations-skeleton" class="recommendations-skeleton">
                            <div class="row">
                                @for($i = 1; $i <= 6; $i++)
                                <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                                    <div class="recommendation-skeleton-card skeleton-enhanced">
                                        <div class="p-3">
                                            <!-- ✅ Skeleton Header yang lebih rapi -->
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="skeleton-company-logo-small me-3"></div>
                                                <div class="flex-grow-1">
                                                    <div class="skeleton-text-md mb-1"></div>
                                                    <div class="skeleton-text-sm"></div>
                                                </div>
                                            </div>
                                            
                                            <!-- ✅ Skeleton Badges dengan spacing yang benar -->
                                            <div class="d-flex gap-2 mb-3">
                                                <div class="skeleton-badge-small"></div>
                                                <div class="skeleton-badge-small"></div>
                                            </div>
                                            
                                            <!-- ✅ Skeleton Progress Indicators dengan struktur yang benar -->
                                            <div class="mt-3">
                                                <div class="mb-2">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <div class="skeleton-text-xs" style="width: 60px;"></div>
                                                        <div class="skeleton-text-xs" style="width: 30px;"></div>
                                                    </div>
                                                    <div class="skeleton-progress-bar-small"></div>
                                                </div>
                                                <div class="mb-0">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <div class="skeleton-text-xs" style="width: 50px;"></div>
                                                        <div class="skeleton-text-xs" style="width: 30px;"></div>
                                                    </div>
                                                    <div class="skeleton-progress-bar-small"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        </div>
                        
                        <!-- Loading state -->
                        <div id="recommendations-loading" class="text-center py-5 d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Mencari rekomendasi terbaik untuk Anda...</p>
                        </div>
                        
                        <!-- Empty state -->
                        <div id="recommendations-empty" class="text-center py-5 d-none">
                            <div class="empty-icon mb-3">
                                <i class="fas fa-search"></i>
                            </div>
                            <h6 class="mb-2">Tidak ada rekomendasi</h6>
                            <p class="text-muted">Belum ada lowongan yang sesuai dengan profil Anda saat ini.</p>
                        </div>
                        
                        <!-- Recommendations cards -->
                        <div id="recommendations-container" class="row d-none">
                            <!-- Cards will be injected here by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/Mahasiswa/Dashboard.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ✅ PERBAIKI: JavaScript untuk mendukung skeleton baru
    const profileCompletion = @json($profileCompletion ?? ['is_complete' => true]);
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 === DASHBOARD LOADED ===');
        
        // Start progressive loading simulation
        simulateContentLoading();
        
        // Check profile completion
        checkAndShowProfileCompletion();
    });
    
    function simulateContentLoading() {
        console.log('⏳ Starting progressive content loading...');
        
        // Step 1: Load welcome section (fastest)
        setTimeout(() => {
            loadWelcomeSection();
        }, 800);
        
        // Step 2: Load magang section
        setTimeout(() => {
            loadMagangSection();
        }, 1500);
        
        // Step 3: Load recommendations section (longest)
        setTimeout(() => {
            loadRecommendationsSection();
        }, 2500);
    }
    
    function loadWelcomeSection() {
        console.log('👋 Loading welcome section...');
        
        const skeleton = document.getElementById('welcome-skeleton');
        const content = document.getElementById('welcome-content');
        
        if (!skeleton || !content) return;
        
        // ✅ PERBAIKI: Fade transition yang smooth
        skeleton.classList.add('skeleton-fade-out');
        
        setTimeout(() => {
            skeleton.classList.add('d-none');
            content.classList.remove('d-none');
            content.classList.add('content-fade-in');
        }, 400);
    }
    
    function loadMagangSection() {
        console.log('💼 Loading magang section...');
        
        const skeleton = document.getElementById('magang-skeleton');
        const content = document.getElementById('magang-content');
        
        if (!skeleton || !content) return;
        
        // ✅ PERBAIKI: Fade transition yang smooth
        skeleton.classList.add('skeleton-fade-out');
        
        setTimeout(() => {
            skeleton.classList.add('d-none');
            content.classList.remove('d-none');
            content.classList.add('content-fade-in');
            
            // Animate progress bars and counters
            setTimeout(() => {
                animateProgressBars();
                animateCounters();
            }, 300);
        }, 500);
    }
    
    function loadRecommendationsSection() {
        console.log('📋 Loading recommendations section...');
        
        const skeleton = document.getElementById('recommendations-skeleton');
        
        if (!skeleton) return;
        
        // ✅ PERBAIKI: Fade transition yang smooth
        skeleton.classList.add('skeleton-fade-out');
        
        setTimeout(() => {
            skeleton.classList.add('d-none');
            
            // Start loading real recommendations
            loadRecommendations();
        }, 500);
    }
    
    function animateProgressBars() {
        console.log('🎬 Animating progress bars...');
        
        const progressBars = document.querySelectorAll('.progress-bar[data-width]');
        
        progressBars.forEach((bar, index) => {
            const targetWidth = bar.getAttribute('data-width') || '0%';
            
            // Start from 0 and animate to target
            bar.style.width = '0%';
            bar.style.transition = 'width 1.5s cubic-bezier(0.4, 0, 0.2, 1)';
            
            setTimeout(() => {
                bar.style.width = targetWidth;
                console.log(`Animated bar ${index} to ${targetWidth}`);
            }, 100 + (index * 100));
        });
    }
    
    function animateCounters() {
        const counters = document.querySelectorAll('.counter-number');
        
        counters.forEach((counter, index) => {
            setTimeout(() => {
                animateCounter(counter);
            }, index * 200);
        });
    }
    
    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-target')) || 0;
        const duration = 1500;
        const startTime = performance.now();
        const suffix = element.textContent.includes('hari') ? ' hari' : '';
        
        element.classList.add('counting');
        
        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function for smooth animation
            const easedProgress = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(easedProgress * target);
            
            element.textContent = current + suffix;
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                element.classList.remove('counting');
                element.textContent = target + suffix;
            }
        }
        
        requestAnimationFrame(updateCounter);
    }
    
    // ... rest of your existing functions remain the same ...
    
    function loadRecommendations() {
        const loadingContainer = document.getElementById('recommendations-loading');
        const emptyContainer = document.getElementById('recommendations-empty');
        const recommendationsContainer = document.getElementById('recommendations-container');
        
        if (!loadingContainer || !emptyContainer || !recommendationsContainer) return;
        
        // Show loading state
        loadingContainer.classList.remove('d-none');
        loadingContainer.classList.add('content-fade-in');
        emptyContainer.classList.add('d-none');
        recommendationsContainer.classList.add('d-none');
        
        if (typeof axios === 'undefined') {
            loadingContainer.classList.add('d-none');
            emptyContainer.classList.remove('d-none');
            return;
        }
        
        const api = axios.create({
            baseURL: '/api',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        });
        
        api.get('/mahasiswa/recommendations')
            .then(response => {
                loadingContainer.classList.add('d-none');
                
                if (response.data.success) {
                    const recommendations = response.data.data;
                    
                    if (recommendations.length === 0) {
                        emptyContainer.classList.remove('d-none');
                        
                        if (profileCompletion && profileCompletion.is_complete === false) {
                            emptyContainer.innerHTML = `
                                <div class="empty-icon mb-3">
                                    <i class="fas fa-user-exclamation"></i>
                                </div>
                                <h6 class="mb-2">Tidak ada rekomendasi</h6>
                                <p class="text-muted mb-3">Lengkapi profil Anda terlebih dahulu untuk mendapatkan rekomendasi yang lebih akurat.</p>
                                <button class="btn btn-primary btn-sm" onclick="showProfileCompletionModal()">
                                    <i class="fas fa-user-edit me-2"></i>Lengkapi Profil
                                </button>
                            `;
                        }
                    } else {
                        renderRecommendations(recommendations);
                        recommendationsContainer.classList.remove('d-none');
                    }
                } else {
                    emptyContainer.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('💥 Error loading recommendations:', error);
                loadingContainer.classList.add('d-none');
                emptyContainer.classList.remove('d-none');
            });
    }
    
    function renderRecommendations(recommendations) {
        const container = document.getElementById('recommendations-container');
        if (!container) return;
        
        container.innerHTML = '';
        
        recommendations.forEach((item, index) => {
            if (index >= 6) return;
            
            const logoUrl = item.logo_perusahaan 
                ? `/storage/${item.logo_perusahaan}`
                : '/img/default-company.png';
                
            const cardWrapper = document.createElement('div');
            cardWrapper.className = 'col-xl-4 col-lg-6 col-md-6 mb-4 recommendation-card-wrapper';
            cardWrapper.style.animationDelay = `${index * 0.1}s`;
            
            cardWrapper.innerHTML = `
                <a href="/mahasiswa/lowongan/${item.id_lowongan}" class="text-decoration-none">
                    <div class="card recommendation-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="${logoUrl}" 
                                     alt="Logo ${item.nama_perusahaan || 'Company'}"
                                     class="company-logo-small me-3"
                                     onerror="this.src='/img/default-company.png'">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 text-dark">${item.judul_lowongan || 'Lowongan'}</h6>
                                    <p class="text-sm text-muted mb-0">${item.nama_perusahaan || 'Perusahaan'}</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-map-marker-alt me-1"></i>${item.lokasi || 'Lokasi tidak tersedia'}
                                </span>
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    ${Math.round((item.appraisal_score || 0) * 100)}%
                                </span>
                            </div>
                            <div class="match-indicators">
                                <div class="match-item mb-2">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">Keahlian</small>
                                        <small class="text-muted">${Math.round(item.skill_match || 0)}%</small>
                                    </div>
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar bg-success animated-match-bar" role="progressbar" 
                                            data-width="${Math.round(item.skill_match || 0)}"></div>
                                    </div>
                                </div>
                                <div class="match-item">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">Lokasi</small>
                                        <small class="text-muted">${Math.round(item.location_match || 0)}%</small>
                                    </div>
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar bg-primary animated-match-bar" role="progressbar" 
                                            data-width="${Math.round(item.location_match || 0)}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            `;
            
            container.appendChild(cardWrapper);
            
            // Animate card appearance
            setTimeout(() => {
                cardWrapper.style.opacity = '1';
                cardWrapper.style.transform = 'translateY(0) scale(1)';
                
                // Animate match bars
                const matchBars = cardWrapper.querySelectorAll('.animated-match-bar');
                matchBars.forEach((bar, barIndex) => {
                    setTimeout(() => {
                        const targetWidth = bar.getAttribute('data-width') || 0;
                        bar.style.width = '0%';
                        setTimeout(() => {
                            bar.style.width = targetWidth + '%';
                        }, 100);
                    }, (barIndex + 1) * 200);
                });
            }, (index * 100) + 200);
        });
    }
</script>
@endpush
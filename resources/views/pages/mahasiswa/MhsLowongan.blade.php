@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')

    <div class="container-fluid px-10">
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-4">Daftar Lowongan Magang</h3>

                <!-- Filter Section -->
                <div class="filter-section mb-4">
                    <h5 class="mb-3"><i class="bi bi-funnel-fill me-2"></i>Filter Lowongan</h5>
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label small text-muted">Perusahaan</label>
                            <select id="perusahaanFilter" class="form-select">
                                <option value="">Semua Perusahaan</option>
                                @foreach($perusahaan as $p)
                                    <option value="{{ $p->perusahaan_id }}">{{ $p->nama_perusahaan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label small text-muted">Skill</label>
                            <select id="skillFilter" class="form-select">
                                <option value="">Semua Skill</option>
                                @foreach($skills as $s)
                                    <option value="{{ $s->skill_id }}">{{ $s->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100" id="applyFilter">
                                <i class="bi bi-filter me-2"></i>Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lowongan Cards Container -->
                <div class="row" id="lowonganContainer">
                    <!-- Initial Skeleton Loading Cards -->
                    @for($i = 1; $i <= 6; $i++)
                    <div class="col-md-4 mb-4 skeleton-card-wrapper" id="skeleton-card-{{ $i }}">
                        <div class="lowongan-skeleton-card">
                            <div class="skeleton-card-header">
                                <div class="skeleton-company-section">
                                    <div class="skeleton-company-logo"></div>
                                    <div class="skeleton-company-text">
                                        <div class="skeleton-text skeleton-text-md mb-2"></div>
                                        <div class="skeleton-text skeleton-text-sm"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="skeleton-card-divider"></div>
                            
                            <div class="skeleton-card-body">
                                <div class="skeleton-info-section mb-3">
                                    <div class="skeleton-info-item">
                                        <div class="skeleton-icon-small"></div>
                                        <div class="skeleton-text skeleton-text-sm"></div>
                                    </div>
                                    <div class="skeleton-info-item">
                                        <div class="skeleton-icon-small"></div>
                                        <div class="skeleton-text skeleton-text-sm"></div>
                                    </div>
                                </div>
                                
                                <div class="skeleton-skills-section mb-3">
                                    <div class="skeleton-skill-tag"></div>
                                    <div class="skeleton-skill-tag"></div>
                                    <div class="skeleton-skill-tag-small"></div>
                                </div>
                                
                                <div class="skeleton-card-footer">
                                    <div class="skeleton-capacity-badge"></div>
                                    <div class="skeleton-view-button"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                    
                    <!-- Real content will replace skeleton cards -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Lowongan dengan Loading State -->
    <div class="modal fade" id="lowonganDetailModal" tabindex="-1" aria-labelledby="lowonganDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="lowonganDetailModalLabel">Detail Lowongan</h5>
                    <button type="button" class="btn-close btn-close-white position-absolute"
                        style="right: 20px; top: 20px; z-index: 10;" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="lowonganDetailContent">
                    <!-- Modal Skeleton Loading -->
                    <div class="modal-skeleton-loading" id="modal-skeleton">
                        <div class="modal-hero-skeleton">
                            <div class="skeleton-hero-badge"></div>
                            <div class="skeleton-hero-title"></div>
                            <div class="skeleton-hero-company"></div>
                            <div class="skeleton-hero-meta">
                                <div class="skeleton-meta-item"></div>
                                <div class="skeleton-meta-item"></div>
                            </div>
                        </div>
                        
                        <div class="modal-content-skeleton">
                            <div class="skeleton-highlights-section">
                                <div class="skeleton-highlight-card">
                                    <div class="skeleton-highlight-icon"></div>
                                    <div class="skeleton-highlight-text">
                                        <div class="skeleton-text skeleton-text-xs mb-1"></div>
                                        <div class="skeleton-text skeleton-text-sm"></div>
                                    </div>
                                </div>
                                <div class="skeleton-highlight-card">
                                    <div class="skeleton-highlight-icon"></div>
                                    <div class="skeleton-highlight-text">
                                        <div class="skeleton-text skeleton-text-xs mb-1"></div>
                                        <div class="skeleton-text skeleton-text-sm"></div>
                                    </div>
                                </div>
                                <div class="skeleton-highlight-card">
                                    <div class="skeleton-highlight-icon"></div>
                                    <div class="skeleton-highlight-text">
                                        <div class="skeleton-text skeleton-text-xs mb-1"></div>
                                        <div class="skeleton-text skeleton-text-sm"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="skeleton-section">
                                <div class="skeleton-section-title"></div>
                                <div class="skeleton-description-block">
                                    <div class="skeleton-text skeleton-text-lg mb-2"></div>
                                    <div class="skeleton-text skeleton-text-md mb-2"></div>
                                    <div class="skeleton-text skeleton-text-sm"></div>
                                </div>
                            </div>
                            
                            <div class="skeleton-section">
                                <div class="skeleton-section-title"></div>
                                <div class="skeleton-skills-row">
                                    <div class="skeleton-skill-pill"></div>
                                    <div class="skeleton-skill-pill"></div>
                                    <div class="skeleton-skill-pill"></div>
                                    <div class="skeleton-skill-pill-small"></div>
                                </div>
                            </div>
                            
                            <div class="skeleton-section">
                                <div class="skeleton-section-title"></div>
                                <div class="skeleton-company-details">
                                    <div class="skeleton-text skeleton-text-lg mb-2"></div>
                                    <div class="skeleton-text skeleton-text-md mb-3"></div>
                                    <div class="skeleton-company-meta">
                                        <div class="skeleton-meta-row">
                                            <div class="skeleton-icon-small"></div>
                                            <div class="skeleton-text skeleton-text-sm"></div>
                                        </div>
                                        <div class="skeleton-meta-row">
                                            <div class="skeleton-icon-small"></div>
                                            <div class="skeleton-text skeleton-text-sm"></div>
                                        </div>
                                        <div class="skeleton-meta-row">
                                            <div class="skeleton-icon-small"></div>
                                            <div class="skeleton-text skeleton-text-sm"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Real modal content will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Tutup
                    </button>
                    <button type="button" class="btn btn-apply" id="btnApplyLowongan">
                        <i class="bi bi-send-fill me-2"></i>Ajukan Lamaran
                    </button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Perbaiki konfigurasi Axios
    const api = axios.create({
        baseURL: '/api',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        withCredentials: true
    });

    // Variabel global untuk menyimpan data aplikasi user dan status magang aktif
    let userApplications = [];
    let hasActiveMagang = false;
    let activeMagangLowonganId = null;
    let isInitialLoad = true;

    // Fungsi untuk memeriksa apakah user telah melamar
    function hasApplied(lowonganId) {
        return userApplications.some(app => app.id_lowongan == lowonganId);
    }

    // Fungsi untuk mendapatkan status lamaran
    function getApplicationStatus(lowonganId) {
        const app = userApplications.find(app => app.id_lowongan == lowonganId);
        return app ? app.status : null;
    }

    // Fungsi untuk memuat status magang aktif
    function loadActiveMagangStatus() {
        return api.get('/mahasiswa/active-internship')
            .then(response => {
                if (response.data.success) {
                    hasActiveMagang = response.data.has_active_internship;
                    activeMagangLowonganId = response.data.active_internship ? 
                        response.data.active_internship.id_lowongan : null;
                    
                    if (hasActiveMagang) {
                        Swal.fire({
                            title: 'Informasi',
                            text: 'Anda memiliki magang aktif saat ini. Tidak dapat mengajukan lamaran baru.',
                            icon: 'info',
                            confirmButtonText: 'Mengerti'
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error checking active internship status:', error);
            });
    }

    // Enhanced function dengan loading animation
    function loadLowongan(filters = {}) {
        console.log('🔄 Loading lowongan with filters:', filters);
        
        const container = document.getElementById('lowonganContainer');
        
        // Jika bukan initial load, tampilkan skeleton loading
        if (!isInitialLoad) {
            showSkeletonCards();
        }
        
        // Load data
        api.get('/mahasiswa/lowongan', { params: filters })
            .then(response => {
                // Delay untuk menunjukkan skeleton loading effect
                const delay = isInitialLoad ? 2000 : 1500; // Initial load lebih lama
                
                setTimeout(() => {
                    if (response.data.success) {
                        renderLowonganCards(response.data.data);
                    } else {
                        showErrorState(response.data.message || 'Terjadi kesalahan saat memuat data');
                    }
                    isInitialLoad = false;
                }, delay);
            })
            .catch(error => {
                setTimeout(() => {
                    showErrorState('Terjadi kesalahan pada server');
                    isInitialLoad = false;
                }, isInitialLoad ? 2000 : 1500);
            });
    }

    function showSkeletonCards() {
        const container = document.getElementById('lowonganContainer');
        container.innerHTML = '';
        
        for (let i = 1; i <= 6; i++) {
            const skeletonCard = createSkeletonCard(i);
            container.appendChild(skeletonCard);
        }
    }

    function createSkeletonCard(index) {
        const wrapper = document.createElement('div');
        wrapper.className = 'col-md-4 mb-4 skeleton-card-wrapper';
        wrapper.style.animationDelay = `${index * 0.1}s`;
        
        wrapper.innerHTML = `
            <div class="lowongan-skeleton-card">
                <div class="skeleton-card-header">
                    <div class="skeleton-company-section">
                        <div class="skeleton-company-logo"></div>
                        <div class="skeleton-company-text">
                            <div class="skeleton-text skeleton-text-md mb-2"></div>
                            <div class="skeleton-text skeleton-text-sm"></div>
                        </div>
                    </div>
                </div>
                
                <div class="skeleton-card-divider"></div>
                
                <div class="skeleton-card-body">
                    <div class="skeleton-info-section mb-3">
                        <div class="skeleton-info-item">
                            <div class="skeleton-icon-small"></div>
                            <div class="skeleton-text skeleton-text-sm"></div>
                        </div>
                        <div class="skeleton-info-item">
                            <div class="skeleton-icon-small"></div>
                            <div class="skeleton-text skeleton-text-sm"></div>
                        </div>
                    </div>
                    
                    <div class="skeleton-skills-section mb-3">
                        <div class="skeleton-skill-tag"></div>
                        <div class="skeleton-skill-tag"></div>
                        <div class="skeleton-skill-tag-small"></div>
                    </div>
                    
                    <div class="skeleton-card-footer">
                        <div class="skeleton-capacity-badge"></div>
                        <div class="skeleton-view-button"></div>
                    </div>
                </div>
            </div>
        `;
        
        return wrapper;
    }

    function renderLowonganCards(lowonganData) {
        const container = document.getElementById('lowonganContainer');
        
        // Fade out skeleton cards
        const skeletonCards = container.querySelectorAll('.skeleton-card-wrapper');
        skeletonCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(-20px)';
            }, index * 50);
        });
        
        // Clear container after fade out
        setTimeout(() => {
            container.innerHTML = '';
            
            if (lowonganData.length === 0) {
                showEmptyState();
                return;
            }
            
            // Render real cards with staggered animation
            lowonganData.forEach((lowongan, index) => {
                setTimeout(() => {
                    const card = createLowonganCard(lowongan, index);
                    container.appendChild(card);
                    
                    // Animate card in
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0) scale(1)';
                    }, 50);
                }, index * 100);
            });
        }, 300);
    }

    // ✅ PERBAIKI: Function createLowonganCard dengan logo yang benar
    function createLowonganCard(lowongan, index) {
        const wrapper = document.createElement('div');
        wrapper.className = 'col-md-4 mb-4 lowongan-card-wrapper';
        wrapper.style.opacity = '0';
        wrapper.style.transform = 'translateY(30px) scale(0.95)';
        wrapper.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        
        const perusahaan = lowongan.perusahaan || {};
        const skills = lowongan.skills || [];
        
        // Cek status aplikasi
        const applied = hasApplied(lowongan.id_lowongan);
        const status = getApplicationStatus(lowongan.id_lowongan);
        let statusBadge = '';
        
        if (applied) {
            if (status === 'diterima') {
                statusBadge = `<span class="badge-status badge-accepted">
                    <i class="bi bi-check-circle-fill me-1"></i> Diterima
                </span>`;
            } else if (status === 'ditolak') {
                statusBadge = `<span class="badge-status badge-rejected">
                    <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                </span>`;
            } else {
                statusBadge = `<span class="badge-status badge-waiting">
                    <i class="bi bi-hourglass-split me-1"></i> Menunggu
                </span>`;
            }
        } else if (hasActiveMagang && lowongan.id_lowongan == activeMagangLowonganId) {
            statusBadge = `<span class="badge-status badge-active-magang">
                <i class="bi bi-briefcase-fill me-1"></i> Magang Aktif
            </span>`;
        }
        
        // Generate skills HTML
        let skillsHTML = '';
        if (skills && skills.length > 0) {
            const visibleSkills = skills.slice(0, 3);
            skillsHTML = visibleSkills.map(skill => 
                `<span class="badge-skill">${skill?.nama_skill || 'Skill'}</span>`
            ).join('');
            
            if (skills.length > 3) {
                skillsHTML += `<span class="badge-more">+${skills.length - 3}</span>`;
            }
        } else {
            skillsHTML = '<span class="text-muted small">Tidak ada skill yang disebutkan</span>';
        }
        
        // ✅ PERBAIKI: Logo handling yang benar
        let logoHTML;
        if (perusahaan.logo_url && perusahaan.logo_url !== null && perusahaan.logo_url !== '') {
            logoHTML = `<img src="${perusahaan.logo_url}" 
                       alt="Logo ${perusahaan.nama_perusahaan || 'Perusahaan'}"
                       class="company-logo"
                       onerror="handleCompanyLogoError(this, '${perusahaan.nama_perusahaan || 'Perusahaan'}')">`;
        } else {
            // Fallback ke placeholder jika tidak ada logo
            logoHTML = `<div class="company-logo-placeholder">
                       <i class="bi bi-building" style="font-size: 1.5rem; color: #6c757d;"></i>
                   </div>`;
        }
        
        // ✅ TAMBAHKAN: Min IPK badge jika ada
        let ipkBadge = '';
        if (lowongan.min_ipk && lowongan.min_ipk > 0) {
            ipkBadge = `<div class="d-flex align-items-center mb-2">
                       <i class="bi bi-star-fill text-warning me-2"></i>
                       <span class="text-muted small">Min. IPK ${parseFloat(lowongan.min_ipk).toFixed(2)}</span>
                   </div>`;
        }
        
        wrapper.innerHTML = `
            <div class="card h-100 lowongan-card border-0 position-relative" onclick="showDetail(${lowongan.id_lowongan})">
                ${statusBadge}
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="company-logo-wrapper me-3">
                            ${logoHTML}
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">${lowongan.judul_lowongan || 'Tidak ada judul'}</h5>
                            <p class="text-primary mb-0 company-name">${perusahaan.nama_perusahaan || 'Tidak disebutkan'}</p>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                            <span class="text-muted">${perusahaan.nama_kota || 'Lokasi tidak disebutkan'}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-calendar-fill text-success me-2"></i>
                            <span class="text-muted">${lowongan.periode?.waktu || 'Periode tidak tersedia'}</span>
                        </div>
                        ${ipkBadge}
                    </div>

                    <div class="d-flex flex-wrap gap-1 mt-3">
                        ${skillsHTML}
                    </div>

                    <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                        <span class="capacity-badge">
                            <i class="bi bi-people-fill me-1"></i> ${lowongan.kapasitas || 0} kuota
                        </span>
                        <button class="btn btn-sm btn-view">Lihat Detail <i class="bi bi-arrow-right ms-1"></i></button>
                    </div>
                </div>
            </div>
        `;
        
        return wrapper;
    }

    // ✅ TAMBAHKAN: Function untuk handle error logo perusahaan
    function handleCompanyLogoError(img, companyName) {
        console.error('Company logo failed to load:', img.src);
        
        // Replace dengan placeholder
        const placeholder = document.createElement('div');
        placeholder.className = 'company-logo-placeholder';
        placeholder.innerHTML = `<i class="bi bi-building" style="font-size: 1.5rem; color: #dc3545;" title="Logo ${companyName} tidak dapat dimuat"></i>`;
        
        // Replace the img element
        img.parentNode.replaceChild(placeholder, img);
    }

    function showEmptyState() {
        const container = document.getElementById('lowonganContainer');
        container.innerHTML = `
            <div class="col-12 text-center py-5 empty-state">
                <div class="empty-icon mb-3">
                    <i class="bi bi-search"></i>
                </div>
                <h6 class="mb-2">Tidak ada lowongan yang sesuai</h6>
                <p class="text-muted">Coba ubah filter pencarian Anda</p>
            </div>
        `;
    }

    function showErrorState(message) {
        const container = document.getElementById('lowonganContainer');
        container.innerHTML = `
            <div class="col-12 text-center py-5 error-state">
                <div class="error-icon mb-3">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
                <h6 class="mb-2">Gagal memuat data</h6>
                <p class="text-muted">${message}</p>
                <button class="btn btn-primary btn-sm" onclick="loadLowongan()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Coba Lagi
                </button>
            </div>
        `;
    }

    // Enhanced showDetail function with modal skeleton
    function showDetail(id) {
        console.log('👁️ Showing detail for lowongan:', id);
        
        // Show modal with skeleton loading
        const modal = new bootstrap.Modal(document.getElementById('lowonganDetailModal'));
        modal.show();
        
        // Show modal skeleton
        showModalSkeleton();
        
        // Fetch detail data
        api.get(`/mahasiswa/lowongan/${id}`)
            .then(response => {
                // Delay untuk menunjukkan skeleton effect
                setTimeout(() => {
                    if (response.data.success) {
                        hideModalSkeleton();
                        renderDetailLowongan(response.data.data, id);
                        
                        // Setup apply button
                        setupApplyButton(id);
                    } else {
                        showModalError(response.data.message || 'Terjadi kesalahan');
                    }
                }, 1200);
            })
            .catch(error => {
                setTimeout(() => {
                    showModalError('Terjadi kesalahan pada server');
                }, 1200);
            });
    }

    function showModalSkeleton() {
        const content = document.getElementById('lowonganDetailContent');
        const modalSkeleton = content.querySelector('#modal-skeleton');
        
        if (modalSkeleton) {
            modalSkeleton.style.display = 'block';
            modalSkeleton.style.opacity = '1';
        }
    }

    function hideModalSkeleton() {
        const content = document.getElementById('lowonganDetailContent');
        const modalSkeleton = content.querySelector('#modal-skeleton');
        
        if (modalSkeleton) {
            modalSkeleton.style.transition = 'opacity 0.4s ease';
            modalSkeleton.style.opacity = '0';
            
            setTimeout(() => {
                modalSkeleton.style.display = 'none';
            }, 400);
        }
    }

    function showModalError(message) {
        const content = document.getElementById('lowonganDetailContent');
        content.innerHTML = `
            <div class="text-center py-5">
                <div class="error-icon mb-3">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
                <h6 class="mb-2">Gagal memuat detail</h6>
                <p class="text-muted">${message}</p>
            </div>
        `;
    }

    // ✅ PERBAIKI: Function renderDetailLowongan dengan logo yang benar
    function renderDetailLowongan(lowongan, id) {
        const perusahaan = lowongan.perusahaan || {};
        const skills = Array.isArray(lowongan.skills) ? lowongan.skills : [];
        const periode = lowongan.periode || {};

        const content = document.getElementById('lowonganDetailContent');

        // Generate skills HTML untuk modal
        let skillsHTML = '';
        if (skills && skills.length > 0) {
            skillsHTML = skills.map(skill =>
                `<span class="skill-pill">${skill?.nama_skill || 'Skill'}</span>`
            ).join('');
        } else {
            skillsHTML = '<p class="text-muted mb-0">Tidak ada keahlian khusus yang disebutkan.</p>';
        }

        // ✅ PERBAIKI: Logo handling untuk modal
        let modalLogoHTML;
        if (perusahaan.logo_url && perusahaan.logo_url !== null && perusahaan.logo_url !== '') {
            modalLogoHTML = `<img src="${perusahaan.logo_url}" 
                           alt="${perusahaan.nama_perusahaan || 'Perusahaan'}" 
                           class="company-hero-logo"
                           onerror="handleModalLogoError(this, '${perusahaan.nama_perusahaan || 'Perusahaan'}')">`;
        } else {
            modalLogoHTML = `<div class="company-hero-logo-placeholder">
                           <i class="bi bi-building"></i>
                        </div>`;
        }

        // ✅ TAMBAHKAN: Min IPK info jika ada
        let ipkInfo = '';
        if (lowongan.min_ipk && lowongan.min_ipk > 0) {
            ipkInfo = `
            <div class="highlight-card">
                <div class="highlight-icon">
                    <i class="bi bi-star-fill"></i>
                </div>
                <div class="highlight-content">
                    <div class="highlight-title">Min. IPK</div>
                    <div class="highlight-value">${parseFloat(lowongan.min_ipk).toFixed(2)}</div>
                </div>
            </div>
        `;
        }

        content.innerHTML = `
        <div class="modal-hero">
            <div class="modal-hero-overlay"></div>
            <div class="modal-hero-content">
                <div class="company-badge">
                    ${modalLogoHTML}
                </div>
                <h2 class="modal-hero-title">${lowongan.judul_lowongan || 'Tidak ada judul'}</h2>
                <div class="modal-hero-company">${perusahaan.nama_perusahaan || 'Tidak disebutkan'}</div>
                <div class="modal-hero-meta">
                    <span><i class="bi bi-geo-alt-fill"></i> ${perusahaan.nama_kota || 'Lokasi tidak disebutkan'}</span>
                    <span class="meta-divider">•</span>
                    <span><i class="bi bi-calendar-event-fill"></i> ${periode?.waktu || 'Periode tidak diketahui'}</span>
                </div>
            </div>
        </div>

        <div class="detail-container">
            <div class="detail-highlights">
                <div class="highlight-card">
                    <div class="highlight-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="highlight-content">
                        <div class="highlight-title">Kuota</div>
                        <div class="highlight-value">${lowongan.kapasitas || 0} orang</div>
                    </div>
                </div>

                <div class="highlight-card">
                    <div class="highlight-icon">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <div class="highlight-content">
                        <div class="highlight-title">Periode Magang</div>
                        <div class="highlight-value">${periode?.waktu || 'Tidak diketahui'}</div>
                    </div>
                </div>

                ${ipkInfo}

                <div class="highlight-card">
                    <div class="highlight-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="highlight-content">
                        <div class="highlight-title">Status</div>
                        <div class="highlight-value">Aktif</div>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h4 class="section-title">
                    <i class="bi bi-file-text-fill me-2"></i>
                    Deskripsi
                </h4>
                <div class="section-content description-text">
                    ${lowongan.deskripsi || 'Tidak ada deskripsi tersedia.'}
                </div>
            </div>

            <div class="detail-section">
                <h4 class="section-title">
                    <i class="bi bi-award-fill me-2"></i>
                    Keahlian yang Dibutuhkan
                </h4>
                <div class="section-content">
                    <div class="skills-container">
                        ${skillsHTML}
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h4 class="section-title">
                    <i class="bi bi-building-fill me-2"></i>
                    Tentang Perusahaan
                </h4>
                <div class="section-content">
                    <p class="company-description">
                        ${generateCompanyDescription(perusahaan)}
                    </p>
                    <div class="company-meta">
                        <div class="company-meta-item">
                            <i class="bi bi-link-45deg"></i>
                            <span>${perusahaan.website || 'Website tidak tersedia'}</span>
                        </div>
                        <div class="company-meta-item">
                            <i class="bi bi-envelope-fill"></i>
                            <span>${perusahaan.email || 'Email tidak tersedia'}</span>
                        </div>
                        <div class="company-meta-item">
                            <i class="bi bi-telephone-fill"></i>
                            <span>${perusahaan.contact_person || 'No. Telepon tidak tersedia'}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-section apply-section">
                <div class="apply-info">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Pastikan kualifikasi Anda sesuai dengan kebutuhan perusahaan sebelum mengajukan lamaran.</span>
                </div>
            </div>
        </div>
    `;
    }

    // ✅ TAMBAHKAN: Function untuk handle error logo di modal
    function handleModalLogoError(img, companyName) {
        console.error('Modal logo failed to load:', img.src);
    
        const placeholder = document.createElement('div');
        placeholder.className = 'company-hero-logo-placeholder';
        placeholder.innerHTML = `<i class="bi bi-building" title="Logo ${companyName} tidak dapat dimuat"></i>`;
    
        img.parentNode.replaceChild(placeholder, img);
    }

    function setupApplyButton(id) {
        const applied = hasApplied(id);
        const btnApply = document.getElementById('btnApplyLowongan');

        if (hasActiveMagang) {
            btnApply.disabled = true;
            btnApply.classList.remove('btn-primary', 'btn-apply');
            btnApply.classList.add('btn-outline-secondary');
            btnApply.innerHTML = '<i class="bi bi-info-circle me-2"></i>Anda memiliki magang aktif';
        } else if (applied) {
            const status = getApplicationStatus(id);
            btnApply.disabled = true;
            btnApply.classList.remove('btn-primary', 'btn-apply');
            btnApply.classList.add('btn-outline-secondary');

            if (status === 'diterima') {
                btnApply.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Diterima';
            } else if (status === 'ditolak') {
                btnApply.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i>Ditolak';
            } else {
                btnApply.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menunggu Konfirmasi';
            }
        } else {
            btnApply.disabled = false;
            btnApply.classList.add('btn-primary', 'btn-apply');
            btnApply.classList.remove('btn-outline-secondary');
            btnApply.innerHTML = '<i class="bi bi-send-fill me-2"></i>Ajukan Lamaran';
            btnApply.onclick = () => applyLowongan(id);
        }
    }

    function applyLowongan(id) {
        if (hasActiveMagang) {
            Swal.fire({
                title: 'Tidak dapat mengajukan lamaran',
                text: 'Anda sudah memiliki magang aktif saat ini.',
                icon: 'warning',
                confirmButtonText: 'Mengerti'
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Lamaran',
            text: 'Apakah Anda yakin ingin mengajukan lamaran untuk lowongan ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ajukan Lamaran',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang mengajukan lamaran Anda',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                api.post(`/mahasiswa/apply/${id}`)
                    .then(response => {
                        if (response.data.success) {
                            userApplications.push({
                                id_lowongan: id,
                                status: 'menunggu',
                                tanggal_lamaran: new Date().toISOString()
                            });

                            bootstrap.Modal.getInstance(document.getElementById('lowonganDetailModal')).hide();

                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Lamaran berhasil diajukan!',
                                icon: 'success',
                                timer: 3000,
                                timerProgressBar: true
                            });

                            setTimeout(() => loadLowongan(), 3000);
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.data.message || 'Terjadi kesalahan saat mengajukan lamaran',
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        let errorMessage = 'Terjadi kesalahan pada server';

                        if (error.response && error.response.data && error.response.data.message) {
                            errorMessage = error.response.data.message;
                        }

                        Swal.fire({
                            title: 'Gagal!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    });
            }
        });
    }

    function generateCompanyDescription(perusahaan) {
        if (!perusahaan.nama_perusahaan) {
            return 'Informasi tentang perusahaan tidak tersedia.';
        }

        let description = `${perusahaan.nama_perusahaan} adalah perusahaan yang berlokasi di ${perusahaan.nama_kota || 'Indonesia'}`;

        if (perusahaan.bidang || perusahaan.jenis_perusahaan) {
            description += ` yang bergerak di bidang ${perusahaan.bidang || perusahaan.jenis_perusahaan}.`;
        } else {
            description += '.';
        }

        description += ' Perusahaan ini membuka kesempatan magang bagi mahasiswa JTI untuk mengembangkan keterampilan praktis dalam lingkungan kerja yang profesional.';

        return description;
    }

    // Document ready event
    document.addEventListener('DOMContentLoaded', function () {
        console.log('🚀 Initializing MhsLowongan page...');
        
        // Load active magang status and user applications first
        Promise.all([
            loadActiveMagangStatus(),
            api.get('/mahasiswa/applications/user')
                .then(response => {
                    if (response.data.success) {
                        userApplications = response.data.data.map(app => ({
                            id_lowongan: app.id_lowongan,
                            status: app.status,
                            tanggal_lamaran: app.tanggal_lamaran
                        }));
                    }
                })
                .catch(error => {
                    console.error('Error fetching applications:', error);
                })
        ]).then(() => {
            // Start initial loading dengan skeleton
            loadLowongan();
        });

        // Event listener untuk tombol filter
        document.getElementById('applyFilter').addEventListener('click', function () {
            const filters = {
                perusahaan_id: document.getElementById('perusahaanFilter').value,
                skill_id: document.getElementById('skillFilter').value
            };

            loadLowongan(filters);

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Filter diterapkan',
                showConfirmButton: false,
                timer: 1500
            });
        });
    });
</script>
@endpush
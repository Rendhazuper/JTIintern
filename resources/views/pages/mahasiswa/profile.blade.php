@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')

    <div class="container-fluid px-10 py-4">
        <!-- Profile Header -->
        <div class="card mb-4 bg-gradient-primary border-0">
            <div class="card-body p-4">
                <!-- Header Skeleton -->
                <div id="header-skeleton" class="profile-header-skeleton">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="skeleton-avatar-container">
                                <div class="skeleton-avatar-large"></div>
                                <div class="skeleton-avatar-edit"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="skeleton-text-white skeleton-text-lg mb-2"></div>
                            <div class="skeleton-text-white skeleton-text-md"></div>
                        </div>
                        <div class="col-auto">
                            <div class="skeleton-button"></div>
                        </div>
                    </div>
                </div>

                <!-- Real Header Content -->
                <div id="header-content" class="real-header-content d-none">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="avatar-container">
                                <div id="current-avatar" class="avatar-profile"
                                    style="{{ $mahasiswaData && isset($mahasiswaData->foto) && $mahasiswaData->foto ? 'background-image: url(' . asset('storage/' . $mahasiswaData->foto) . ')' : '' }}">
                                    @if(!$mahasiswaData || !isset($mahasiswaData->foto) || !$mahasiswaData->foto)
                                        <span>{{ $userData ? substr($userData->name, 0, 1) : 'M' }}</span>
                                    @endif
                                </div>
                                <div class="avatar-edit" id="upload-trigger">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <form id="avatar-form" style="display:none">
                                    <input type="file" id="avatar-upload" name="foto" accept="image/*">
                                </form>
                            </div>
                        </div>
                        <div class="col">
                            <h4 class="text-white mb-1">{{ $userData->name ?? 'Nama Mahasiswa' }}</h4>
                            <p class="text-white-50 mb-0">{{ $userData->email ?? 'mahasiswa@email.com' }}</p>
                        </div>
                        <div class="col-auto">
                            <button id="edit-profile-btn" class="btn btn-sm btn-light">
                                <i class="fas fa-pencil-alt me-2"></i>Edit Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Completion Alert -->
        @if(isset($profileCompletion) && !$profileCompletion['is_complete'])
        <div class="row mb-4">
            <div class="col-12">
                <div class="profile-incomplete-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="warning-icon me-3">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Profil Belum Lengkap</h6>
                                <p class="mb-0 text-sm">Lengkapi profil untuk mendapatkan rekomendasi yang lebih akurat.</p>
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

        <div class="row">
            <!-- Personal Information -->
            <div class="col-12 col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header p-3">
                        <!-- Info Header Skeleton -->
                        <div id="info-header-skeleton" class="info-header-skeleton">
                            <div class="skeleton-text-md mb-0"></div>
                        </div>
                        
                        <!-- Real Info Header -->
                        <div id="info-header-content" class="real-info-header d-none">
                            <h6 class="mb-0">Informasi Mahasiswa</h6>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Profile Info Skeleton -->
                        <div id="profile-info-skeleton" class="profile-info-skeleton">
                            <div class="row mb-4">
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="skeleton-section-title mb-3"></div>
                                    @for($i = 1; $i <= 4; $i++)
                                    <div class="skeleton-info-item mb-3">
                                        <div class="skeleton-label mb-1"></div>
                                        <div class="skeleton-text-md"></div>
                                    </div>
                                    @endfor
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="skeleton-section-title mb-3"></div>
                                    @for($i = 1; $i <= 4; $i++)
                                    <div class="skeleton-info-item mb-3">
                                        <div class="skeleton-label mb-1"></div>
                                        <div class="skeleton-text-md"></div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="skeleton-section-title mb-3"></div>
                                    <div class="skeleton-text-lg mb-2"></div>
                                    <div class="skeleton-text-md"></div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="skeleton-section-title mb-3"></div>
                                    <div class="skeleton-text-md"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Real Profile Content -->
                        <div id="profile-info-content" class="real-profile-content d-none">
                            <!-- View Mode -->
                            <div id="profile-view-mode">
                                <div class="row mb-4">
                                    <div class="col-12 col-md-6 mb-3">
                                        <h6 class="text-uppercase text-sm text-muted mb-2">Informasi Pribadi</h6>
                                        <div class="mb-3">
                                            <small class="d-block text-uppercase text-xs text-muted">NIM</small>
                                            <p class="mb-0" data-profile="nim">{{ $mahasiswaData->nim ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <small class="d-block text-uppercase text-xs text-muted">Nama Lengkap</small>
                                            <p class="mb-0" data-profile="name">{{ $userData->name ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <small class="d-block text-uppercase text-xs text-muted">Email</small>
                                            <p class="mb-0" data-profile="email">{{ $userData->email ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <small class="d-block text-uppercase text-xs text-muted">No. Telepon</small>
                                            <p class="mb-0" data-profile="no_hp">{{ $mahasiswaData->telp ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <h6 class="text-uppercase text-sm text-muted mb-2">Informasi Akademik</h6>
                                        <div class="mb-3">
                                            <small class="d-block text-uppercase text-xs text-muted">Program Studi</small>
                                            <p class="mb-0" data-profile="prodi">{{ $kelasData->kode_prodi ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <small class="d-block text-uppercase text-xs text-muted">Kelas</small>
                                            <p class="mb-0" data-profile="kelas">{{ $kelasData->nama_kelas ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <small class="d-block text-uppercase text-xs text-muted">Tahun Masuk</small>
                                            <p class="mb-0" data-profile="tahun_masuk">{{ $kelasData->tahun_masuk ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <small class="d-block text-uppercase text-xs text-muted">IPK</small>
                                            <p class="mb-0" data-profile="ipk">{{ $mahasiswaData->ipk ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <h6 class="text-uppercase text-sm text-muted mb-2">Alamat</h6>
                                        <p class="mb-0" data-profile="alamat">{{ $mahasiswaData->alamat ?? 'Belum ada data alamat' }}</p>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <h6 class="text-uppercase text-sm text-muted mb-2">Preferensi Wilayah Magang</h6>
                                        <p class="mb-0" data-profile="wilayah">{{ $wilayahData->nama_kota ?? 'Belum memilih preferensi wilayah' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Mode -->
                            <div id="profile-edit-mode" style="display: none;">
                                <form id="profile-form">
                                    <div class="row mb-4">
                                        <div class="col-12 col-md-6 mb-3">
                                            <h6 class="text-uppercase text-sm text-muted mb-2">Informasi Pribadi</h6>
                                            <div class="mb-3">
                                                <label class="form-label text-xs text-uppercase text-muted">NIM</label>
                                                <input type="text" class="form-control" name="nim" value="{{ $mahasiswaData->nim ?? '' }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs text-uppercase text-muted">Nama Lengkap</label>
                                                <input type="text" class="form-control" name="name" value="{{ $userData->name ?? '' }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs text-uppercase text-muted">Email</label>
                                                <input type="email" class="form-control" name="email" value="{{ $userData->email ?? '' }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs text-uppercase text-muted">No. Telepon</label>
                                                <input type="text" class="form-control" name="telp" value="{{ $mahasiswaData->telp ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <h6 class="text-uppercase text-sm text-muted mb-2">Informasi Akademik & Preferensi</h6>
                                            <div class="mb-3">
                                                <label class="form-label text-xs text-uppercase text-muted">Program Studi</label>
                                                <input type="text" class="form-control" name="prodi" value="{{ $kelasData->kode_prodi ?? '' }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs text-uppercase text-muted">Kelas</label>
                                                <input type="text" class="form-control" name="kelas" value="{{ $kelasData->nama_kelas ?? '' }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs text-uppercase text-muted">IPK</label>
                                                <input type="number" class="form-control" name="ipk" value="{{ $mahasiswaData->ipk ?? '' }}" step="0.01" min="0" max="4">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs text-uppercase text-muted">Preferensi Wilayah Magang</label>
                                                <select class="form-select" name="wilayah_id" id="wilayah-select">
                                                    <option value="">Pilih Wilayah</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <h6 class="text-uppercase text-sm text-muted mb-2">Alamat</h6>
                                            <textarea class="form-control" name="alamat" rows="2">{{ $mahasiswaData->alamat ?? '' }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end">
                                                <button type="button" id="cancel-edit-btn" class="btn btn-sm btn-outline-secondary me-2">
                                                    <i class="fas fa-times me-1"></i>Batal
                                                </button>
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <div class="d-flex align-items-center">
                                                        <span id="save-btn-text">Simpan Perubahan</span>
                                                        <div id="save-btn-loader" class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skills, Interests & Password -->
            <div class="col-12 col-lg-4">
                <!-- Skills Card -->
                <div class="card mb-4">
                    <div class="card-header p-3">
                        <!-- Skills Header Skeleton -->
                        <div id="skills-header-skeleton" class="skills-header-skeleton">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="skeleton-text-sm"></div>
                                </div>
                                <div class="col-auto">
                                    <div class="skeleton-edit-button"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Real Skills Header -->
                        <div id="skills-header-content" class="real-skills-header d-none">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-0">Keahlian</h6>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-sm btn-link" id="edit-skills-btn">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-3">
                        <!-- Skills Content Skeleton -->
                        <div id="skills-content-skeleton" class="skills-content-skeleton">
                            <div class="d-flex flex-wrap gap-2">
                                @for($i = 1; $i <= 4; $i++)
                                <div class="skeleton-badge skeleton-badge-{{ $i <= 2 ? 'md' : 'sm' }}"></div>
                                @endfor
                            </div>
                        </div>
                        
                        <!-- Real Skills Content -->
                        <div id="skills-content" class="real-skills-content d-none">
                            <div id="skills-view-mode">
                                <div id="skills-container" class="d-flex flex-wrap gap-2">
                                    @if(isset($skills) && count($skills) > 0)
                                        @foreach($skills as $skill)
                                            <span class="badge bg-light-primary">{{ $skill->nama }}</span>
                                        @endforeach
                                    @else
                                        <p class="text-muted mb-0">Belum ada keahlian yang ditambahkan</p>
                                    @endif
                                </div>
                            </div>

                            <div id="skills-edit-mode" style="display: none;">
                                <form id="skills-form">
                                    <div class="mb-3">
                                        <label class="form-label">Pilih Keahlian</label>
                                        <select class="form-select" id="skill-select" multiple></select>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="button" id="cancel-skills-btn" class="btn btn-sm btn-outline-secondary me-2">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interests Card -->
                <div class="card mb-4">
                    <div class="card-header p-3">
                        <!-- Minat Header Skeleton -->
                        <div id="minat-header-skeleton" class="minat-header-skeleton">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="skeleton-text-sm"></div>
                                </div>
                                <div class="col-auto">
                                    <div class="skeleton-edit-button"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Real Minat Header -->
                        <div id="minat-header-content" class="real-minat-header d-none">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-0">Minat</h6>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-sm btn-link" id="edit-minat-btn">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-3">
                        <!-- Minat Content Skeleton -->
                        <div id="minat-content-skeleton" class="minat-content-skeleton">
                            <div class="d-flex flex-wrap gap-2">
                                @for($i = 1; $i <= 3; $i++)
                                <div class="skeleton-badge skeleton-badge-md"></div>
                                @endfor
                            </div>
                        </div>
                        
                        <!-- Real Minat Content -->
                        <div id="minat-content" class="real-minat-content d-none">
                            <div id="minat-view-mode">
                                <div id="minat-container" class="d-flex flex-wrap gap-2">
                                    @if(isset($minat) && count($minat) > 0)
                                        @foreach($minat as $item)
                                            <span class="badge bg-light-success">{{ $item->nama_minat }}</span>
                                        @endforeach
                                    @else
                                        <p class="text-muted mb-0">Belum ada minat yang ditambahkan</p>
                                    @endif
                                </div>
                            </div>

                            <div id="minat-edit-mode" style="display: none;">
                                <form id="minat-form">
                                    <div class="mb-3">
                                        <label class="form-label">Pilih Minat</label>
                                        <select class="form-select" id="minat-select" multiple></select>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="button" id="cancel-minat-btn" class="btn btn-sm btn-outline-secondary me-2">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password Card -->
                <div class="card">
                    <div class="card-header p-3">
                        <!-- Password Header Skeleton -->
                        <div id="password-header-skeleton" class="password-header-skeleton">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="skeleton-text-md"></div>
                                </div>
                                <div class="col-auto">
                                    <div class="skeleton-text-xs"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Real Password Header -->
                        <div id="password-header-content" class="real-password-header d-none">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-0">
                                        <i class="fas fa-lock me-2"></i>Ubah Password
                                    </h6>
                                </div>
                                <div class="col-auto">
                                    <small class="text-muted">Keamanan Akun</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-3">
                        <!-- Password Form Skeleton -->
                        <div id="password-form-skeleton" class="password-form-skeleton">
                            @for($i = 1; $i <= 3; $i++)
                            <div class="mb-3">
                                <div class="skeleton-label mb-2"></div>
                                <div class="skeleton-input-group">
                                    <div class="skeleton-input"></div>
                                    <div class="skeleton-toggle-button"></div>
                                </div>
                                @if($i == 2)
                                <div class="skeleton-progress-section mt-2">
                                    <div class="skeleton-progress-bar mb-2"></div>
                                    <div class="skeleton-text-xs"></div>
                                </div>
                                @endif
                            </div>
                            @endfor
                            
                            <div class="mb-4">
                                <div class="skeleton-button-full"></div>
                            </div>
                            
                            <div class="skeleton-requirements">
                                <div class="skeleton-text-sm mb-2"></div>
                                @for($i = 1; $i <= 4; $i++)
                                <div class="skeleton-requirement-item mb-1"></div>
                                @endfor
                            </div>
                        </div>
                        
                        <!-- Real Password Form -->
                        <div id="password-form-content" class="real-password-form d-none">
                            <form id="password-form">
                                <div class="mb-3">
                                    <label class="form-label text-sm font-weight-bold">Password Saat Ini</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="current-password" name="current_password" placeholder="Masukkan password saat ini" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label text-sm font-weight-bold">Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new-password" name="password" placeholder="Masukkan password baru" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="password-strength mt-2" id="password-strength">
                                        <div class="progress" style="height: 5px;">
                                            <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small id="password-strength-text" class="form-text text-muted mt-1">
                                            Minimal 8 karakter dengan kombinasi huruf dan angka
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label text-sm font-weight-bold">Konfirmasi Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm-password" name="password_confirmation" placeholder="Konfirmasi password baru" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div id="password-match-message" class="mt-1"></div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-key me-2"></i>
                                            <span id="password-btn-text">Update Password</span>
                                            <div id="password-btn-loader" class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Password Requirements -->
                            <div class="mt-3 p-3 bg-light rounded">
                                <h6 class="text-sm font-weight-bold mb-2">
                                    <i class="fas fa-info-circle me-2 text-info"></i>Persyaratan Password
                                </h6>
                                <ul class="list-unstyled mb-0 text-sm">
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Minimal 8 karakter</li>
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Mengandung huruf besar dan kecil</li>
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Mengandung angka</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Mengandung karakter khusus (opsional)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="success-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Berhasil</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="success-message">Perubahan telah disimpan</div>
        </div>
    </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="error-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-danger text-white">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong class="me-auto">Error</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="error-message">Terjadi kesalahan</div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/Mahasiswa/profile.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Global variables
    const api = axios.create({
        baseURL: '/api',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        withCredentials: true
    });

    // Document ready handler
    document.addEventListener('DOMContentLoaded', function () {
        console.log('🚀 === PROFILE PAGE LOADED ===');
        
        // Start progressive loading simulation
        simulateProfileLoading();
        
        // Initialize all functionalities after loading completes
        setTimeout(() => {
            initPasswordToggles();
            initPasswordStrengthMeter();
            initProfileEdit();
            initSkillsEdit();
            initMinatEdit();
            initAvatarUpload();
            initFormSubmissions();
            loadWilayahData();
        }, 3000);
    });

    // Progressive Loading System
    function simulateProfileLoading() {
        console.log('⏳ Starting progressive profile loading...');
        
        setTimeout(() => loadHeaderSection(), 800);
        setTimeout(() => loadProfileInfoSection(), 1500);
        setTimeout(() => loadSkillsSection(), 2200);
        setTimeout(() => loadMinatSection(), 2500);
        setTimeout(() => loadPasswordSection(), 3000);
    }

    function loadHeaderSection() {
        console.log('👤 Loading header section...');
        transitionSection('header-skeleton', 'header-content', 500);
    }

    function loadProfileInfoSection() {
        console.log('📋 Loading profile info section...');
        
        // Load header first
        transitionSection('info-header-skeleton', 'info-header-content', 400);
        
        // Load content with delay
        setTimeout(() => {
            transitionSection('profile-info-skeleton', 'profile-info-content', 500);
        }, 200);
    }

    function loadSkillsSection() {
        console.log('🎯 Loading skills section...');
        loadSectionWithStagger('skills-header-skeleton', 'skills-header-content', 'skills-content-skeleton', 'skills-content');
    }

    function loadMinatSection() {
        console.log('❤️ Loading minat section...');
        loadSectionWithStagger('minat-header-skeleton', 'minat-header-content', 'minat-content-skeleton', 'minat-content');
    }

    function loadPasswordSection() {
        console.log('🔒 Loading password section...');
        loadSectionWithStagger('password-header-skeleton', 'password-header-content', 'password-form-skeleton', 'password-form-content');
    }

    function loadSectionWithStagger(headerSkeletonId, headerContentId, contentSkeletonId, contentRealId) {
        // Load header first
        transitionSection(headerSkeletonId, headerContentId, 400);
        
        // Load content with delay
        setTimeout(() => {
            transitionSection(contentSkeletonId, contentRealId, 500);
        }, 200);
    }

    function transitionSection(skeletonId, contentId, duration) {
        const skeleton = document.getElementById(skeletonId);
        const content = document.getElementById(contentId);
        
        if (!skeleton || !content) return;
        
        // Fade out skeleton
        skeleton.style.transition = `opacity ${duration}ms ease`;
        skeleton.style.opacity = '0';
        
        setTimeout(() => {
            skeleton.classList.add('d-none');
            content.classList.remove('d-none');
            
            // Add show class for animation
            setTimeout(() => {
                content.classList.add('show');
            }, 50);
        }, duration);
    }

    // Initialize password toggles
    function initPasswordToggles() {
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    }

    // Initialize password strength meter
    function initPasswordStrengthMeter() {
        const passwordInput = document.getElementById('new-password');
        const confirmInput = document.getElementById('confirm-password');
        const strengthBar = document.getElementById('password-strength-bar');
        const strengthText = document.getElementById('password-strength-text');
        const matchMessage = document.getElementById('password-match-message');

        if (!passwordInput) return;

        passwordInput.addEventListener('input', function () {
            const password = this.value;
            let strength = 0;

            if (password.length >= 8) strength += 25;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 25;
            if (/[^A-Za-z0-9]/.test(password)) strength += 25;

            strengthBar.style.width = strength + '%';

            if (strength === 0) {
                strengthBar.className = 'progress-bar';
                strengthText.textContent = 'Minimal 8 karakter dengan kombinasi huruf dan angka';
                strengthText.className = 'form-text text-muted mt-1';
            } else if (strength < 50) {
                strengthBar.className = 'progress-bar bg-danger';
                strengthText.textContent = 'Password lemah - perlu perbaikan';
                strengthText.className = 'form-text text-danger mt-1';
            } else if (strength < 75) {
                strengthBar.className = 'progress-bar bg-warning';
                strengthText.textContent = 'Password sedang - cukup baik';
                strengthText.className = 'form-text text-warning mt-1';
            } else {
                strengthBar.className = 'progress-bar bg-success';
                strengthText.textContent = 'Password kuat - sangat baik!';
                strengthText.className = 'form-text text-success mt-1';
            }

            checkPasswordMatch();
        });

        if (confirmInput) {
            confirmInput.addEventListener('input', checkPasswordMatch);
        }

        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmInput.value;

            if (confirmPassword === '') {
                matchMessage.innerHTML = '';
                return;
            }

            if (password === confirmPassword) {
                matchMessage.innerHTML = '<small class="password-match-success"><i class="fas fa-check me-1"></i>Password cocok</small>';
            } else {
                matchMessage.innerHTML = '<small class="password-match-error"><i class="fas fa-times me-1"></i>Password tidak cocok</small>';
            }
        }
    }

    // Initialize profile edit functionality
    function initProfileEdit() {
        const editBtn = document.getElementById('edit-profile-btn');
        const cancelBtn = document.getElementById('cancel-edit-btn');
        const viewMode = document.getElementById('profile-view-mode');
        const editMode = document.getElementById('profile-edit-mode');

        if (editBtn) {
            editBtn.addEventListener('click', () => {
                viewMode.style.display = 'none';
                editMode.style.display = 'block';
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                editMode.style.display = 'none';
                viewMode.style.display = 'block';
            });
        }
    }

    // Initialize skills edit functionality
    function initSkillsEdit() {
        const editBtn = document.getElementById('edit-skills-btn');
        const cancelBtn = document.getElementById('cancel-skills-btn');
        const viewMode = document.getElementById('skills-view-mode');
        const editMode = document.getElementById('skills-edit-mode');

        if (editBtn) {
            editBtn.addEventListener('click', () => {
                loadSkillsData();
                viewMode.style.display = 'none';
                editMode.style.display = 'block';
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                editMode.style.display = 'none';
                viewMode.style.display = 'block';
            });
        }

        $('#skill-select').select2({
            placeholder: 'Pilih keahlian',
            multiple: true
        });
    }

    // Initialize minat edit functionality
    function initMinatEdit() {
        const editBtn = document.getElementById('edit-minat-btn');
        const cancelBtn = document.getElementById('cancel-minat-btn');
        const viewMode = document.getElementById('minat-view-mode');
        const editMode = document.getElementById('minat-edit-mode');

        if (editBtn) {
            editBtn.addEventListener('click', () => {
                loadMinatData();
                viewMode.style.display = 'none';
                editMode.style.display = 'block';
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                editMode.style.display = 'none';
                viewMode.style.display = 'block';
            });
        }

        $('#minat-select').select2({
            placeholder: 'Pilih minat',
            multiple: true
        });
    }

    // Load skills data
    function loadSkillsData() {
        api.get('/mahasiswa/skills')
            .then(response => {
                if (response.data?.success) {
                    const skillSelect = document.getElementById('skill-select');
                    skillSelect.innerHTML = '';

                    response.data.allSkills.forEach(skill => {
                        const option = document.createElement('option');
                        option.value = skill.skill_id;
                        option.textContent = skill.nama_skill;

                        if (response.data.userSkills.some(userSkill => userSkill.skill_id === skill.skill_id)) {
                            option.selected = true;
                        }

                        skillSelect.appendChild(option);
                    });

                    $('#skill-select').trigger('change');
                }
            })
            .catch(error => {
                console.error('Error loading skills:', error);
                showErrorToast('Gagal memuat data keahlian');
            });
    }

    // Load minat data
    function loadMinatData() {
        api.get('/mahasiswa/minat')
            .then(response => {
                if (response.data?.success) {
                    const minatSelect = document.getElementById('minat-select');
                    minatSelect.innerHTML = '';

                    response.data.data.all_minat.forEach(minat => {
                        const option = document.createElement('option');
                        option.value = minat.minat_id;
                        option.textContent = minat.nama_minat;

                        if (response.data.data.user_minat.some(userMinat => userMinat.minat_id === minat.minat_id)) {
                            option.selected = true;
                        }

                        minatSelect.appendChild(option);
                    });

                    $('#minat-select').trigger('change');
                }
            })
            .catch(error => {
                console.error('Error loading minat:', error);
                showErrorToast('Gagal memuat data minat');
            });
    }

    // Load wilayah data
    function loadWilayahData() {
        api.get('/wilayah')
            .then(response => {
                if (response.data?.success) {
                    const wilayahSelect = document.getElementById('wilayah-select');
                    const currentWilayahId = '{{ $mahasiswaData->wilayah_id ?? "" }}';

                    response.data.data.forEach(wilayah => {
                        const option = document.createElement('option');
                        option.value = wilayah.wilayah_id;
                        option.textContent = wilayah.nama_kota;

                        if (currentWilayahId && wilayah.wilayah_id == currentWilayahId) {
                            option.selected = true;
                        }

                        wilayahSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading wilayah:', error);
            });
    }

    // Initialize avatar upload
    function initAvatarUpload() {
        const uploadTrigger = document.getElementById('upload-trigger');
        const avatarUpload = document.getElementById('avatar-upload');

        if (uploadTrigger && avatarUpload) {
            uploadTrigger.addEventListener('click', () => avatarUpload.click());

            avatarUpload.addEventListener('change', function (event) {
                if (this.files && this.files[0]) {
                    const formData = new FormData();
                    formData.append('foto', this.files[0]);

                    Swal.fire({
                        title: 'Upload Foto Profil',
                        text: 'Sedang mengunggah foto...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => Swal.showLoading()
                    });

                    api.post('/mahasiswa/profile/avatar', formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    })
                    .then(response => {
                        if (response.data?.success) {
                            const avatar = document.getElementById('current-avatar');
                            avatar.style.backgroundImage = `url(${response.data.foto_url})`;
                            avatar.innerHTML = '';

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Foto profil berhasil diperbarui',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Gagal mengunggah foto profil'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error uploading avatar:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Gagal mengunggah foto profil'
                        });
                    });
                }
            });
        }
    }

    // Initialize form submissions
    function initFormSubmissions() {
        // Profile form
        const profileForm = document.getElementById('profile-form');
        if (profileForm) {
            profileForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(this);
                const saveBtn = document.getElementById('save-btn-text');
                const saveLoader = document.getElementById('save-btn-loader');

                saveBtn.style.opacity = '0.5';
                saveLoader.classList.remove('d-none');

                api.post('/mahasiswa/profile/update', formData)
                    .then(response => {
                        if (response.data?.success) {
                            document.getElementById('profile-edit-mode').style.display = 'none';
                            updateProfileViewData(response.data.user, response.data.mahasiswa);
                            
                            // Update wilayah
                            const wilayahId = formData.get('wilayah_id');
                            if (wilayahId) {
                                const wilayahSelect = document.getElementById('wilayah-select');
                                const selectedOption = wilayahSelect.querySelector(`option[value="${wilayahId}"]`);
                                const wilayahName = selectedOption ? selectedOption.textContent : 'Belum memilih preferensi wilayah';
                                
                                document.querySelectorAll('[data-profile="wilayah"]').forEach(el => {
                                    el.textContent = wilayahName;
                                });
                            }
                            
                            document.getElementById('profile-view-mode').style.display = 'block';

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Profil berhasil diperbarui',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Gagal memperbarui profil'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error updating profile:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Gagal memperbarui profil'
                        });
                    })
                    .finally(() => {
                        saveBtn.style.opacity = '1';
                        saveLoader.classList.add('d-none');
                    });
            });
        }

        // Skills form
        const skillsForm = document.getElementById('skills-form');
        if (skillsForm) {
            skillsForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const selectedSkills = $('#skill-select').val();

                api.post('/mahasiswa/profile/skills', { skills: selectedSkills })
                    .then(response => {
                        if (response.data?.success) {
                            document.getElementById('skills-edit-mode').style.display = 'none';
                            updateSkillsView(response.data.skills);
                            document.getElementById('skills-view-mode').style.display = 'block';

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Keahlian berhasil diperbarui',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Gagal memperbarui keahlian'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error updating skills:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Gagal memperbarui keahlian'
                        });
                    });
            });
        }

        // Minat form
        const minatForm = document.getElementById('minat-form');
        if (minatForm) {
            minatForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const selectedMinat = $('#minat-select').val();

                api.post('/mahasiswa/profile/minat', { minat: selectedMinat })
                    .then(response => {
                        if (response.data?.success) {
                            document.getElementById('minat-edit-mode').style.display = 'none';
                            updateMinatView(response.data.data.minat);
                            document.getElementById('minat-view-mode').style.display = 'block';

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Minat berhasil diperbarui',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Gagal memperbarui minat'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error updating minat:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Gagal memperbarui minat'
                        });
                    });
            });
        }

        // Password form
        const passwordForm = document.getElementById('password-form');
        if (passwordForm) {
            passwordForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const password = document.getElementById('new-password').value;
                const confirmPassword = document.getElementById('confirm-password').value;

                if (password !== confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Password baru dan konfirmasi password tidak cocok'
                    });
                    return;
                }

                if (password.length < 8) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Password minimal harus 8 karakter'
                    });
                    return;
                }

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const btnText = document.getElementById('password-btn-text');
                const btnLoader = document.getElementById('password-btn-loader');

                submitBtn.disabled = true;
                btnText.style.opacity = '0.5';
                btnLoader.classList.remove('d-none');

                api.post('/mahasiswa/profile/password', formData)
                    .then(response => {
                        if (response.data?.success) {
                            passwordForm.reset();

                            // Reset strength indicator
                            document.getElementById('password-strength-bar').style.width = '0%';
                            document.getElementById('password-strength-bar').className = 'progress-bar';
                            document.getElementById('password-strength-text').textContent = 'Minimal 8 karakter dengan kombinasi huruf dan angka';
                            document.getElementById('password-strength-text').className = 'form-text text-muted mt-1';
                            document.getElementById('password-match-message').innerHTML = '';

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Password berhasil diubah',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.data.message || 'Gagal mengubah password'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error updating password:', error);
                        let errorMessage = 'Gagal mengubah password';

                        if (error.response?.data?.message) {
                            errorMessage = error.response.data.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: errorMessage
                        });
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        btnText.style.opacity = '1';
                        btnLoader.classList.add('d-none');
                    });
            });
        }
    }

    // Update profile view data
    function updateProfileViewData(user, mahasiswa) {
        if (user) {
            document.querySelectorAll('[data-profile="name"]').forEach(el => {
                el.textContent = user.name || '-';
            });
            document.querySelectorAll('[data-profile="email"]').forEach(el => {
                el.textContent = user.email || '-';
            });
        }

        if (mahasiswa) {
            document.querySelectorAll('[data-profile="nim"]').forEach(el => {
                el.textContent = mahasiswa.nim || '-';
            });
            document.querySelectorAll('[data-profile="no_hp"]').forEach(el => {
                el.textContent = mahasiswa.telp || '-';
            });
            document.querySelectorAll('[data-profile="ipk"]').forEach(el => {
                el.textContent = mahasiswa.ipk || '-';
            });
            document.querySelectorAll('[data-profile="alamat"]').forEach(el => {
                el.textContent = mahasiswa.alamat || 'Belum ada data alamat';
            });
        }
    }

    // Update skills view
    function updateSkillsView(skills) {
        const skillsContainer = document.getElementById('skills-container');

        if (skillsContainer) {
            skillsContainer.innerHTML = '';

            if (skills && skills.length > 0) {
                skills.forEach(skill => {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-light-primary';
                    badge.textContent = skill.nama_skill || skill.nama;
                    skillsContainer.appendChild(badge);
                });
            } else {
                skillsContainer.innerHTML = '<p class="text-muted mb-0">Belum ada keahlian yang ditambahkan</p>';
            }
        }
    }

    // Update minat view
    function updateMinatView(minat) {
        const minatContainer = document.getElementById('minat-container');

        if (minatContainer) {
            minatContainer.innerHTML = '';

            if (minat && minat.length > 0) {
                minat.forEach(item => {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-light-success';
                    badge.textContent = item.nama_minat;
                    minatContainer.appendChild(badge);
                });
            } else {
                minatContainer.innerHTML = '<p class="text-muted mb-0">Belum ada minat yang ditambahkan</p>';
            }
        }
    }

    // Utility functions
    function showSuccessToast(message) {
        const toast = new bootstrap.Toast(document.getElementById('success-toast'));
        document.getElementById('success-message').textContent = message;
        toast.show();
    }

    function showErrorToast(message) {
        const toast = new bootstrap.Toast(document.getElementById('error-toast'));
        document.getElementById('error-message').textContent = message;
        toast.show();
    }
</script>
@endpush
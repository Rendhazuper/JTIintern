@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')
    
    <div class="container-fluid px-10">
        <!-- Current Application Section -->
        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6>Lamaran Saya</h6>
            </div>
            <div class="card-body">
                <div class="application-status">
                    <div class="d-flex align-items-center mb-4">
                        <img src="path_to_company_logo.jpg" alt="Company Logo" class="company-logo me-3">
                        <div class="flex-grow-1">
                            <h5 class="mb-1">PT Sekawan Media | UI/UX Designer</h5>
                            <span class="status-badge pending">Menunggu Verifikasi</span>
                            <p class="text-muted mb-0">Lamaran sedang diproses</p>
                        </div>
                    </div>
                    
                    <!-- Documents Section -->
                    <div class="documents-section">
                        <h6 class="mb-3">Dokumen</h6>
                        <div class="document-list">
                            <div class="document-item">
                                <i class="far fa-file-pdf me-2"></i>
                                <span>CV.PDF</span>
                            </div>
                            <div class="document-item">
                                <i class="far fa-file-pdf me-2"></i>
                                <span>Cover Letter.PDF</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="application-note mt-3">
                        <p class="text-muted">
                            Kamu akan mendapatkan notifikasi begitu status lamaran berubah. Terima kasih atas kesabarannya!
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application History Section -->
        <div class="card">
            <div class="card-header pb-0 d-flex align-items-center">
                <h6 class="mb-0">Riwayat Lamaran</h6>
                <div class="ms-auto">
                    <select class="form-select form-select-sm" id="statusFilter">
                        <option selected>Status Lamaran</option>
                        <option value="pending">Menunggu Verifikasi</option>
                        <option value="accepted">Diterima</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="application-history">
                    <!-- History Item 1 -->
                    <div class="history-item">
                        <div class="d-flex align-items-center">
                            <img src="path_to_company_logo.jpg" alt="Company Logo" class="company-logo me-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">PT Sekawan Media | UI/UX Designer</h6>
                                <span class="location-badge"><i class="fas fa-map-marker-alt me-1"></i>Wilayah</span>
                                <p class="text-muted mb-0">Lamaran sedang diproses</p>
                            </div>
                            <span class="status-badge pending">Menunggu Verifikasi</span>
                        </div>
                    </div>

                    <!-- History Item 2 -->
                    <div class="history-item">
                        <div class="d-flex align-items-center">
                            <img src="path_to_company_logo.jpg" alt="Company Logo" class="company-logo me-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">PT Telkom Indonesia | UI/UX Designer</h6>
                                <span class="location-badge"><i class="fas fa-map-marker-alt me-1"></i>Wilayah</span>
                                <p class="text-muted mb-0">Lamaran sedang diproses</p>
                            </div>
                            <span class="status-badge pending">Menunggu Verifikasi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/Mahasiswa/lamaran.css') }}">
@endpush
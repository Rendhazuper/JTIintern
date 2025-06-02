@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')
    
    <div class="container-fluid px-10">
        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-header pb-0">
                <div class="d-flex align-items-center">
                    <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Evaluasi</h6>
                    <button id="reset-filters" class="btn btn-link text-secondary ms-auto mb-0">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dosen</label>
                        <select class="form-select" id="filter-dosen">
                            <option value="">Semua Dosen</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Perusahaan</label>
                        <select class="form-select" id="filter-perusahaan">
                            <option value="">Semua Perusahaan</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State Container -->
        <div id="empty-container" class="d-none">
            <div class="card">
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-3x text-secondary mb-3"></i>
                        <h5 class="mb-2">Tidak ada evaluasi</h5>
                        <p class="text-muted mb-0" id="empty-message">
                            Belum ada evaluasi yang tercatat
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evaluasi Cards Container -->
        <div id="evaluasi-container">
            <div class="row" id="evaluasi-cards">
                <!-- Static Evaluation Card 1 -->
                <div class="col-lg-6 mb-4">
                    <div class="card card-evaluation">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar bg-gradient-primary me-3">DS</div>
                                <div>
                                    <h6 class="mb-0">Dr. Dosen Satu</h6>
                                    <p class="text-sm mb-0">Dosen Pembimbing</p>
                                </div>
                                <div class="ms-auto">
                                    <span class="score-badge">
                                        <i class="fas fa-star text-warning me-1"></i>85
                                </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="badge bg-gradient-info">PT ABC Technology</span>
                                <span class="text-sm ms-2">28 Mei 2025</span>
                            </div>
                            <p class="mb-0">Lorem ipsum dolor sit amet consectetur. Pulvinar sapien justo diam ante. Mauris faucibus at sem cursus urna vel enim.</p>
                        </div>
                    </div>
                </div>

                <!-- Static Evaluation Card 2 -->
                <div class="col-lg-6 mb-4">
                    <div class="card card-evaluation">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar bg-gradient-success me-3">DD</div>
                                <div>
                                    <h6 class="mb-0">Dr. Dosen Dua</h6>
                                    <p class="text-sm mb-0">Dosen Pembimbing</p>
                                </div>
                                <div class="ms-auto">
                                    <span class="score-badge">
                                        <i class="fas fa-star text-warning me-1"></i>90
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="badge bg-gradient-info">PT XYZ Digital</span>
                                <span class="text-sm ms-2">29 Mei 2025</span>
                            </div>
                            <p class="mb-0">Morbi mi velit in etiam viverra aliquam quisque pellentesque cursus. Pellentesque faucibus neque at vel proin auctor facilisis eu.</p>
                        </div>
                    </div>
                </div>

                <!-- Static Evaluation Card 3 -->
                <div class="col-lg-6 mb-4">
                    <div class="card card-evaluation">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar bg-gradient-warning me-3">DT</div>
                                <div>
                                    <h6 class="mb-0">Dr. Dosen Tiga</h6>
                                    <p class="text-sm mb-0">Dosen Pembimbing</p>
                                </div>
                                <div class="ms-auto">
                                    <span class="score-badge">
                                        <i class="fas fa-star text-warning me-1"></i>88
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="badge bg-gradient-info">PT DEF Solutions</span>
                                <span class="text-sm ms-2">30 Mei 2025</span>
                            </div>
                            <p class="mb-0">Pulvinar sapien justo diam ante. Mauris faucibus at sem cursus urna vel enim. Morbi mi velit in etiam viverra aliquam.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
 <link rel="stylesheet" href="{{ asset('assets/css/Mahasiswa/evaluasi.css') }}">
@endpush

@extends('layouts.app', ['class' => 'g-sidenav-show'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Evaluasi Dosen'])
    <div class="container-fluid py-4">
        <!-- Filter Buttons -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Filter berdasarkan Dosen</label>
                        <select class="form-select select2" id="dosenFilter">
                            <option value="">Semua Dosen</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Filter berdasarkan Perusahaan</label>
                        <select class="form-select select2" id="perusahaanFilter">
                            <option value="">Semua Perusahaan</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!-- Evaluation Cards -->
        <div class="evaluation-cards">
            <!-- Evaluation Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Nama Dosen</h5>
                        <span class="text-muted small">5 menit yang lalu</span>
                    </div>
                    <div class="evaluation-info mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1">Nama Mahasiswa</p>
                                <p class="text-primary mb-0">PT. Indah Jaya</p>
                            </div>
                            <div class="score-badge">
                                Nilai : 90
                            </div>
                        </div>
                    </div>
                    <div class="evaluation-text">
                        <h6 class="text-muted mb-2">Evaluasi</h6>
                        <p class="text-secondary">Lorem ipsum dolor sit amet consectetur. Pulvinar sapien justo diam ante. Mauris faucibus at sem cursus urna vel enim. Morbi mi velit in etiam viverra aliquam quisque pellentesque cursus. Pellentesque faucibus neque at vel porin suctor facilisis eu.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/evaluasi.css') }}">
@endpush

@push('js')
<script src="{{ asset('assets/js/evaluasi.js') }}"></script>
@endpush
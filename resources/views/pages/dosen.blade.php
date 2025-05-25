@extends('layouts.app', ['class' => 'g-sidenav-show'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Data Dosen'])
    <div class="container-fluid py-4">
        <!-- Stats Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="icon-dosen icon-warning">
                            <i class="bi bi-exclamation-triangle fs-1"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h1 class="mb-1 fw-bold">56</h1>
                        <p class="text-muted mb-0">Dosen yang tersedia menjadi pembimbing</p>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" onclick="tambahDosen()">Mulai Plotting</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Dosen Card -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Daftar Dosen</h5>
                    <div class="d-flex gap-2">
                        <button type="button"style ="color: white; background: #02A232;"class="btn" onclick="tambahDosen()">
                            <i class="bi bi-plus-square-fill me-2"></i>Tambah Dosen
                        </button>
                        <button type="button" class="btn btn-primary" onclick="importCSV()">
                            <i class="bi w me-2"></i>Import CSV
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-items-center">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Pembimbing Untuk Perusahaan</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="dosen-table-body">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <p class="text-muted mb-0">Menampilkan 1-4 dari 100 Dosen</p>
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0">
                                <li class="page-item"><a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">...</a></li>
                                <li class="page-item"><a class="page-link" href="#">50</a></li>
                                <li class="page-item"><a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/dosen.css') }}">
@push('js')
<script src="{{ asset('assets/js/dosen.js') }}"></script>
<script>
    function tambahDosen() {
        window.location.href = '/plotting';
    }
</script>
@endpush
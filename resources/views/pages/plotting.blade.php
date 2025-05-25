@extends('layouts.app', ['class' => 'g-sidenav-show'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Plotting Dosen'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-4">Plotting Manual</h5>
                
                <div class="row g-4">
                    <!-- Search Perusahaan -->
                    <div class="col-md-6">
                        <label class="form-label">Pilih Perusahaan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="searchPerusahaan" placeholder="Cari Perusahaan">
                        </div>
                    </div>

                    <!-- Search Dosen -->
                    <div class="col-md-6">
                        <label class="form-label">Cari Dosen</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="searchDosen" placeholder="Nama dosen atau NIP">
                        </div>
                    </div>
                </div>

                <!-- Dosen List -->
                <div class="table-responsive mt-4">
                    <table class="table align-items-center">
                        <thead>
                            <tr>
                                <th class="w-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th>Nama</th>
                                <th>Pembimbing Untuk Perusahaan</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="plotting-table-body">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <p class="text-muted mb-0">Menampilkan <span id="showingCount">1-4</span> dari <span id="totalCount">100</span> Dosen</p>
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0" id="pagination">
                                <!-- Pagination will be populated here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto Plot Section -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="mb-4">Plotting Otomatis</h5>
                <button type="button" class="btn btn-primary" id="autoPlotBtn">
                    <i class="bi bi-magic me-2"></i>Auto-Plot Dosen
                </button>
            </div>
        </div>
    </div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/plotting.css') }}">
@endpush

@push('js')
<script src="{{ asset('assets/js/plotting.js') }}"></script>
@endpush
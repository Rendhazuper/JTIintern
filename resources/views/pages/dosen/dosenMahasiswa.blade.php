@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Data Mahasiswa'])

    <div class="container-fluid py-4">
        <div class="card pt-0">
            <!-- Card Header with Title & Controls -->
                    <div class="input-group input-group-sm">
                        <span  class="input-group-text">
                            <i class="fas fa-search text-muted"></i>
                            placeholder="Cari Mahasiswa...">
                    </div>
                    <select id="statusFilter" class="form-select form-select-sm">
                        <option value="">Status Magang</option>
                        <option value="aktif">Sedang Magang</option>
                        <option value="selesai">Selesai Magang</option>
                    </select>
                    <select id="perusahaanFilter" class="form-select form-select-sm">
                        <option value="">Perusahaan</option>
                    </select>
                    <select id="periodeFilter" class="form-select form-select-sm">
                        <option value="">Periode</option>
                    </select>
            </div>

            <!-- Card Body with Table -->
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary">Mahasiswa</th>
                                <th class="text-uppercase text-secondary">NIM</th>
                                <th class="text-center text-uppercase text-secondary">Status</th>
                                <th class="text-uppercase text-secondary">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="mahasiswa-table-body">
                            <!-- Data akan diisi melalui JavaScript -->
                        </tbody>
                    </table>
                    <!-- Pagination Container -->
                    <div id="pagination-container" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Detail Mahasiswa - Struktur Lengkap -->
    <div class="modal fade" id="detailMahasiswaModal" tabindex="-1" aria-labelledby="detailMahasiswaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailMahasiswaModalLabel">Detail Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailMahasiswaBody">
                    <!-- Content akan diisi melalui JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/Dosen/mahasiswa.css') }}">
@endpush


@push('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endpush

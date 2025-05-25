@extends('layouts.app', ['class' => 'g-sidenav-show'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen Prodi'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6>Daftar Program Studi</h6>
                    <button class="btn btn-success btn-sm mb-0" onclick="tambahProdi()">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Prodi
                    </button>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th>Kode Prodi</th>
                                <th>Nama Prodi</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="prodi-table-body">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Prodi -->
    <div class="modal fade" id="prodiModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="prodiModalLabel">Tambah Prodi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="prodiForm" onsubmit="handleSubmitProdi(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kode_prodi">Kode Prodi</label>
                            <input type="text" class="form-control" id="kode_prodi" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_prodi">Nama Prodi</label>
                            <input type="text" class="form-control" id="nama_prodi" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // Initialize axios instance
    const api = axios.create({
        baseURL: '/api',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        withCredentials: true
    });

    function loadProdiData() {
        api.get('/prodi')
            .then(response => {
                if (response.data.success) {
                    const tableBody = document.getElementById('prodi-table-body');
                    tableBody.innerHTML = '';
                    
                    response.data.data.forEach(prodi => {
                        tableBody.innerHTML += `
                            <tr>
                                <td>${prodi.kode_prodi}</td>
                                <td>${prodi.nama_prodi}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editProdi('${prodi.kode_prodi}')">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteProdi('${prodi.kode_prodi}')">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Gagal memuat data prodi', 'error');
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadProdiData();
    });
</script>
@endpush
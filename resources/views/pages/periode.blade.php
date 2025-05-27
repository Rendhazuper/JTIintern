@extends('layouts.app', ['class' => 'g-sidenav-show'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen Periode'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6>Daftar Periode</h6>
                    <button class="btn btn-success btn-sm mb-0" onclick="tambahPeriode()">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Periode
                    </button>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="periode-table-body">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Periode -->
    <div class="modal fade" id="periodeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="periodeModalLabel">Tambah Periode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="periodeForm" onsubmit="handleSubmitPeriode(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="waktu">Waktu</label>
                            <input type="text" class="form-control" id="waktu" required>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        function loadPeriodeData() {
            api.get('/periode')
                .then(response => {
                    if (response.data.success) {
                        const tableBody = document.getElementById('periode-table-body');
                        tableBody.innerHTML = '';

                        response.data.data.forEach(periode => {
                            tableBody.innerHTML += `
                                        <tr>
                                            <td>${periode.waktu}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="editPeriode(${periode.periode_id})">
                                                    Edit
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deletePeriode(${periode.periode_id})">
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
                    Swal.fire('Error', 'Gagal memuat data periode', 'error');
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadPeriodeData();
        });

        function tambahPeriode() {
            document.getElementById('periodeForm').reset();
            document.getElementById('periodeModalLabel').innerText = 'Tambah Periode';
            document.getElementById('periodeForm').removeAttribute('data-id');
            const modal = new bootstrap.Modal(document.getElementById('periodeModal'));
            modal.show();
        }

        function editPeriode(id) {
            api.get(`/periode/${id}`)
                .then(response => {
                    if (response.data.success) {
                        const periode = response.data.data;
                        document.getElementById('periodeModalLabel').innerText = 'Edit Periode';
                        document.getElementById('waktu').value = periode.waktu;
                        document.getElementById('periodeForm').setAttribute('data-id', periode.periode_id);
                        const modal = new bootstrap.Modal(document.getElementById('periodeModal'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', 'Data periode tidak ditemukan.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengambil data periode.', 'error');
                });
        }

        function handleSubmitPeriode(event) {
            event.preventDefault();
            const id = document.getElementById('periodeForm').getAttribute('data-id');
            const formData = {
                waktu: document.getElementById('waktu').value
            };
            const method = id ? 'put' : 'post';
            const url = id ? `/periode/${id}` : '/periode';

            api[method](url, formData)
                .then(response => {
                    if (response.data.success) {
                        Swal.fire('Berhasil', 'Periode berhasil disimpan!', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('periodeModal')).hide();
                        loadPeriodeData();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal menyimpan periode.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan periode.', 'error');
                });
        }

        function deletePeriode(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus periode ini?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    api.delete(`/periode/${id}`)
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire('Berhasil', 'Periode berhasil dihapus!', 'success');
                                loadPeriodeData();
                            } else {
                                Swal.fire('Gagal', response.data.message || 'Gagal menghapus periode.', 'error');
                            }
                        })
                        .catch(() => {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus periode.', 'error');
                        });
                }
            });
        }
    </script>
@endpush
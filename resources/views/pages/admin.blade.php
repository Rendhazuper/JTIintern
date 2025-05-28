@extends('layouts.app', ['class' => 'g-sidenav-show'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen Admin'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6>Daftar Admin</h6>
                    <button class="btn btn-success btn-sm mb-0" onclick="tambahAdmin()">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Admin
                    </button>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody id="admin-table-body">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Admin -->
    <div class="modal fade" id="modalTambahAdmin" tabindex="-1" aria-labelledby="modalTambahAdminLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formTambahAdmin" onsubmit="submitTambahAdmin(event)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahAdminLabel">Tambah Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_admin" class="form-label">Nama Admin</label>
                            <input type="text" id="nama_admin" name="nama_admin" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email_admin" class="form-label">Email</label>
                            <input type="email" id="email_admin" name="email_admin" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="password_admin" class="form-label">Password</label>
                            <input type="password" id="password_admin" name="password_admin" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Admin -->
    <div class="modal fade" id="modalEditAdmin" tabindex="-1" aria-labelledby="modalEditAdminLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEditAdmin" onsubmit="submitEditAdmin(event)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditAdminLabel">Edit Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_admin" name="id_admin">
                        <div class="mb-3">
                            <label for="edit_nama_admin" class="form-label">Nama Admin</label>
                            <input type="text" id="edit_nama_admin" name="nama_admin" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email_admin" class="form-label">Email</label>
                            <input type="email" id="edit_email_admin" name="email_admin" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password_admin" class="form-label">Password</label>
                            <input type="password" id="edit_password_admin" name="password_admin" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('css')
    <link href="{{ asset('assets/css/data-mahasiswa.css') }}" rel="stylesheet" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const api = axios.create({
            baseURL: '/api',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            withCredentials: true
        });

        function loadAdminData() {
            api.get('/admin')
                .then(function (response) {
                    if (response.data.success) {
                        const tableBody = document.getElementById('admin-table-body');
                        tableBody.innerHTML = '';
                        response.data.data.forEach(admin => {
                            tableBody.innerHTML += `
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex px-2">
                                                                    <div class="my-auto">
                                                                        <h6 class="mb-0 text-sm">${admin.name}</h6>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="text-sm font-weight-normal">${admin.email}</span>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-sm btn-info" onclick="editAdmin(${admin.id_user})">Edit</button>
                                                                <button class="btn btn-sm btn-danger" onclick="deleteAdmin(${admin.id_user})">Hapus</button>
                                                            </td>
                                                        </tr>
                                                    `;
                        });
                    } else {
                        console.error('Error response:', response.data.message);
                        Swal.fire('Error', 'Gagal memuat data admin', 'error');
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal memuat data admin', 'error');
                });
        }

        // Load data when page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadAdminData();
        });

        function tambahAdmin() {
            var modal = new bootstrap.Modal(document.getElementById('modalTambahAdmin'));
            modal.show();
        }

        function editAdmin(id) {
            api.get(`/admin/${id}`)
                .then(function (response) {
                    if (response.data.success) {
                        const admin = response.data.data;
                        document.getElementById('edit_id_admin').value = admin.id_user;
                        document.getElementById('edit_nama_admin').value = admin.name;
                        document.getElementById('edit_email_admin').value = admin.email;
                        const modal = new bootstrap.Modal(document.getElementById('modalEditAdmin'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal memuat data admin', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat data admin', 'error');
                });
        }

        function submitEditAdmin(event) {
            event.preventDefault();
            const form = event.target;
            const id = document.getElementById('edit_id_admin').value;
            const data = {
                name: form.nama_admin.value,
                email: form.email_admin.value,
                password: form.password_admin.value
            };

            api.put(`/admin/${id}`, data)
                .then(res => {
                    if (res.data.success) {
                        Swal.fire('Berhasil!', 'Data admin berhasil diperbarui!', 'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditAdmin'));
                        modal.hide();
                        form.reset();
                        loadAdminData(); // Refresh data admin
                    } else {
                        Swal.fire('Gagal', res.data.message || 'Gagal memperbarui data admin', 'error');
                    }
                })
                .catch(err => {
                    let msg = 'Terjadi kesalahan saat memperbarui data admin.';
                    if (err.response && err.response.data && err.response.data.message) {
                        msg = err.response.data.message;
                    }
                    Swal.fire('Error', msg, 'error');
                });
        }

        function deleteAdmin(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data admin ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim permintaan DELETE ke backend
                    api.delete(`/admin/${id}`)
                        .then(res => {
                            if (res.data.success) {
                                Swal.fire(
                                    'Terhapus!',
                                    'Data admin berhasil dihapus.',
                                    'success'
                                );
                                loadAdminData(); // Refresh data admin
                            } else {
                                Swal.fire('Gagal', res.data.message || 'Gagal menghapus data admin', 'error');
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            Swal.fire('Error', 'Terjadi kesalahan saat menghapus data admin.', 'error');
                        });
                }
            });
        }

        function submitTambahAdmin(event) {
            event.preventDefault();
            const form = event.target;
            const data = {
                name: form.nama_admin.value,
                email: form.email_admin.value,
                password: form.password_admin.value
            };

            api.post('/admin', data)
                .then(res => {
                    if (res.data.success) {
                        Swal.fire('Berhasil!', 'Admin berhasil ditambahkan!', 'success');
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modalTambahAdmin'));
                        modal.hide();
                        form.reset();
                        loadAdminData();
                    } else {
                        Swal.fire('Gagal', res.data.message || 'Gagal menambahkan admin', 'error');
                    }
                })
                .catch(err => {
                    let msg = 'Terjadi kesalahan saat menambahkan admin.';
                    if (err.response && err.response.data && err.response.data.message) {
                        msg = err.response.data.message;
                    }
                    Swal.fire('Error', msg, 'error');
                });
        }
    </script>
@endpush
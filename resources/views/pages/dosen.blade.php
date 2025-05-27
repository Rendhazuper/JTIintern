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
                        <h1 class="mb-1 fw-bold" id="jumlah-dosen">0</h1>
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
                        <button type="button" style="color: white; background: #02A232;" class="btn"
                            onclick="tambahDosen()">
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
                                <th>Wilayah</th>
                                <th>NIP</th>
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
                                <li class="page-item"><a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">...</a></li>
                                <li class="page-item"><a class="page-link" href="#">50</a></li>
                                <li class="page-item"><a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Dosen -->
    <div class="modal fade" id="tambahDosenModal" tabindex="-1" aria-labelledby="tambahDosenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="tambahDosenForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahDosenModalLabel">Tambah Dosen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_dosen" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama_dosen" name="nama_dosen" required>
                        </div>
                        <div class="mb-3">
                            <label for="wilayah_id" class="form-label">Wilayah</label>
                            <select class="form-select" id="wilayah_id" name="wilayah_id" required>
                                <option value="">Pilih Wilayah</option>
                                <!-- Data wilayah akan dimuat via JS -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Edit Dosen -->
    <div class="modal fade" id="editDosenModal" tabindex="-1" aria-labelledby="editDosenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editDosenForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDosenModalLabel">Edit Dosen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editDosenId">
                        <div class="mb-3">
                            <label for="edit_nama_dosen" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="edit_nama_dosen" name="nama_dosen" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_wilayah_id" class="form-label">Wilayah</label>
                            <select class="form-select" id="edit_wilayah_id" name="wilayah_id" required>
                                <option value="">Pilih Wilayah</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="edit_nip" name="nip" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/dosen.css') }}">
@push('js')
    <script src="{{ asset('assets/js/dosen.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        function loadWilayahOptions() {
            axios.get('/api/wilayah')
                .then(function (response) {
                    if (response.data.success) {
                        const wilayahSelect = document.getElementById('wilayah_id');
                        wilayahSelect.innerHTML = '<option value="">Pilih Wilayah</option>';
                        response.data.data.forEach(function (wilayah) {
                            wilayahSelect.innerHTML += `<option value="${wilayah.wilayah_id}">${wilayah.nama_kota}</option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading wilayah:', error);
                });
        }

        // Panggil fungsi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            loadDosenData();
            loadDosenSummary(); // Tambahkan ini
        });

        function loadDosenData() {
            axios.get('/api/dosen')
                .then(function (response) {
                    const tableBody = document.getElementById('dosen-table-body');
                    tableBody.innerHTML = '';

                    if (response.data.success && Array.isArray(response.data.data)) {
                        // Update jumlah dosen
                        document.getElementById('jumlah-dosen').innerText = response.data.data.length;

                        if (response.data.data.length > 0) {
                            response.data.data.forEach(function (dosen) {
                                tableBody.innerHTML += `
                                <tr>
                                    <td>
                                        ${dosen.nama_dosen ?? '-'}<br>
                                        <span class="text-muted small">${dosen.email ?? '-'}</span>
                                    </td>
                                    <td>${dosen.wilayah ?? '-'}</td>
                                    <td>${dosen.nip ?? '-'}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-warning me-1" onclick="editDosen('${dosen.id_dosen}')">Edit</button>
                                        <button class="btn btn-sm btn-danger" onclick="hapusDosen('${dosen.id_dosen}')">Hapus</button>
                                    </td>
                                </tr>
                            `;
                            });
                        } else {
                            tableBody.innerHTML = `
                            <tr>
                                <td colspan="4" class="text-center text-muted">Tidak ada data dosen</td>
                            </tr>
                        `;
                        }
                    } else {
                        document.getElementById('jumlah-dosen').innerText = '0';
                        tableBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-muted">Tidak ada data dosen</td>
                        </tr>
                    `;
                    }
                })
                .catch(function (error) {
                    document.getElementById('jumlah-dosen').innerText = '0';
                    console.error('Error loading dosen:', error);
                });
        }
        
        document.addEventListener('DOMContentLoaded', loadDosenData);

        function tambahDosen() {
            loadWilayahOptions();
            document.getElementById('tambahDosenForm').reset();
            const modal = new bootstrap.Modal(document.getElementById('tambahDosenModal'));
            modal.show();
        }

        document.getElementById('tambahDosenForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = {
                nama_dosen: document.getElementById('nama_dosen').value,
                wilayah_id: parseInt(document.getElementById('wilayah_id').value, 10), // pastikan integer
                nip: document.getElementById('nip').value
            };

            if (isNaN(formData.wilayah_id) || !formData.wilayah_id) {
                Swal.fire('Peringatan', 'Silakan pilih wilayah!', 'warning');
                return;
            }

            axios.post('/api/dosen', formData)
                .then(function (response) {
                    if (response.data.success) {
                        Swal.fire('Berhasil', 'Dosen berhasil ditambahkan!', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('tambahDosenModal')).hide();
                        loadDosenData();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal menambah dosen.', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat menambah dosen.', 'error');
                });
        });

        function loadEditWilayahOptions(selectedId = null) {
            axios.get('/api/wilayah')
                .then(function (response) {
                    if (response.data.success) {
                        const wilayahSelect = document.getElementById('edit_wilayah_id');
                        wilayahSelect.innerHTML = '<option value="">Pilih Wilayah</option>';
                        response.data.data.forEach(function (wilayah) {
                            const selected = selectedId == wilayah.wilayah_id ? 'selected' : '';
                            wilayahSelect.innerHTML += `<option value="${wilayah.wilayah_id}" ${selected}>${wilayah.nama_kota}</option>`;
                        });
                    }
                });
        }

        function editDosen(id) {
            axios.get(`/api/dosen/${id}`)
                .then(function (response) {
                    if (response.data.success) {
                        const dosen = response.data.data;
                        document.getElementById('editDosenId').value = dosen.id_dosen;
                        document.getElementById('edit_nama_dosen').value = dosen.nama_dosen;
                        document.getElementById('edit_nip').value = dosen.nip;
                        loadEditWilayahOptions(dosen.wilayah_id);

                        const modal = new bootstrap.Modal(document.getElementById('editDosenModal'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', 'Data dosen tidak ditemukan.', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengambil data dosen.', 'error');
                });
        }

        document.getElementById('editDosenForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const id = document.getElementById('editDosenId').value;
            const formData = {
                nama_dosen: document.getElementById('edit_nama_dosen').value,
                wilayah_id: parseInt(document.getElementById('edit_wilayah_id').value, 10),
                nip: document.getElementById('edit_nip').value
            };

            if (isNaN(formData.wilayah_id) || !formData.wilayah_id) {
                Swal.fire('Peringatan', 'Silakan pilih wilayah!', 'warning');
                return;
            }

            axios.put(`/api/dosen/${id}`, formData)
                .then(function (response) {
                    if (response.data.success) {
                        Swal.fire('Berhasil', 'Dosen berhasil diperbarui!', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('editDosenModal')).hide();
                        loadDosenData();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal memperbarui dosen.', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat memperbarui dosen.', 'error');
                });
        });

        function hapusDosen(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus dosen ini?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/api/dosen/${id}`)
                        .then(function (response) {
                            if (response.data.success) {
                                Swal.fire('Berhasil', 'Dosen berhasil dihapus!', 'success');
                                loadDosenData();
                            } else {
                                Swal.fire('Gagal', response.data.message || 'Gagal menghapus dosen.', 'error');
                            }
                        })
                        .catch(function (error) {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus dosen.', 'error');
                        });
                }
            });
        }
    </script>
@endpush
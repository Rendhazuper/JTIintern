@extends('layouts.app', ['class' => 'g-sidenav-show'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen Kelas'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6>Daftar Kelas</h6>
                    <button class="btn btn-success btn-sm mb-0" onclick="tambahKelas()">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Kelas
                    </button>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th>Nama Kelas</th>
                                <th>Kode Prodi</th>
                                <th>Tahun Masuk</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="kelas-table-body">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Kelas -->
    <div class="modal fade" id="kelasModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kelasModalLabel">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="kelasForm" onsubmit="handleSubmitKelas(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_kelas">Nama Kelas</label>
                            <input type="text" class="form-control" id="nama_kelas" required>
                        </div>
                        <div class="mb-3">
                            <label for="kode_prodi">Kode Prodi</label>
                            <select class="form-select" id="kode_prodi" required>
                                <!-- Options will be populated here -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_masuk">Tahun Masuk</label>
                            <input type="number" class="form-control" id="tahun_masuk" required>
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

        function loadKelasData() {
            api.get('/kelas')
                .then(response => {
                    if (response.data.success) {
                        const tableBody = document.getElementById('kelas-table-body');
                        tableBody.innerHTML = '';

                        response.data.data.forEach(kelas => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td>${kelas.nama_kelas}</td>
                                    <td>${kelas.kode_prodi}</td>
                                    <td>${kelas.tahun_masuk}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary me-1" onclick="editKelas('${kelas.id_kelas}')">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteKelas('${kelas.id_kelas}')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            `;

                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal memuat data kelas', 'error');
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadKelasData();
        });

        function loadProdiOptions() {
            api.get('/prodi')
                .then(response => {
                    if (response.data.success) {
                        const kodeProdiSelect = document.getElementById('kode_prodi');
                        kodeProdiSelect.innerHTML = '';
                        response.data.data.forEach(prodi => {
                            kodeProdiSelect.innerHTML += `<option value="${prodi.kode_prodi}">${prodi.nama_prodi}</option>`;
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal memuat data program studi', 'error');
                });
        }

        function tambahKelas() {
            document.getElementById('kelasForm').reset();
            document.getElementById('kelasModalLabel').innerText = 'Tambah Kelas';
            loadProdiOptions();
            // Simpan id_kelas di atribut data jika mode tambah
            document.getElementById('kelasForm').removeAttribute('data-id');
            const modal = new bootstrap.Modal(document.getElementById('kelasModal'));
            modal.show();
        }

        function handleSubmitKelas(event) {
            event.preventDefault();

            const idKelas = document.getElementById('kelasForm').getAttribute('data-id');
            const formData = {
                nama_kelas: document.getElementById('nama_kelas').value,
                kode_prodi: document.getElementById('kode_prodi').value,
                tahun_masuk: document.getElementById('tahun_masuk').value
            };

            // Jika idKelas ada, berarti mode edit, jika tidak berarti tambah
            const method = idKelas ? 'put' : 'post';
            const url = idKelas ? `/kelas/${idKelas}` : '/kelas';

            api[method](url, formData)
                .then(response => {
                    if (response.data.success) {
                        Swal.fire('Berhasil', 'Kelas berhasil disimpan!', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('kelasModal')).hide();
                        loadKelasData();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal menyimpan kelas.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan kelas.', 'error');
                });
        }

        function editKelas(id) {
            // Ambil data kelas berdasarkan id
            api.get(`/kelas/${id}`)
                .then(response => {
                    if (response.data.success) {
                        const kelas = response.data.data;
                        document.getElementById('kelasModalLabel').innerText = 'Edit Kelas';
                        document.getElementById('nama_kelas').value = kelas.nama_kelas;
                        document.getElementById('tahun_masuk').value = kelas.tahun_masuk;
                        // Load prodi lalu set value
                        loadProdiOptions();
                        setTimeout(() => {
                            document.getElementById('kode_prodi').value = kelas.kode_prodi;
                        }, 300);
                        // Simpan id_kelas di atribut data
                        document.getElementById('kelasForm').setAttribute('data-id', kelas.id_kelas);
                        const modal = new bootstrap.Modal(document.getElementById('kelasModal'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', 'Data kelas tidak ditemukan.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengambil data kelas.', 'error');
                });
        }

        function deleteKelas(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus kelas ini?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    api.delete(`/kelas/${id}`)
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire('Berhasil', 'Kelas berhasil dihapus!', 'success');
                                loadKelasData();
                            } else {
                                Swal.fire('Gagal', response.data.message || 'Gagal menghapus kelas.', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus kelas.', 'error');
                        });
                }
            });
        }
    </script>
@endpush
@extends('layouts.app', ['class' => 'g-sidenav-show'])

@section('content')
 @include('layouts.navbars.auth.topnav', ['title' => 'Data Mahasiswa'])
    <div class="container-fluid py-4">
        <div class="card pt-4">
            <div class="d-flex justify-content-between mb-3 px-4">
                <div class="d-flex gap-2">
                    <select id="prodiFilter" class="form-select form-select-sm" style="width: auto; height: 38px">
                        <option value="">
                            <span>Semua Prodi</span>
                        </option>
                    </select>
                    <select id="kelasFilter" class="form-select form-select-sm"
                        style="width: auto; padding-right: 2.75rem; height: 38px">
                        <option value="">
                            <span>Semua Kelas</span>
                        </option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn" style="color: white; background: #02A232;"
                        onclick="tambahMahasiswa()">
                        <i class="bi bi-plus-square-fill me-2"></i>Tambah Mahasiswa
                    </button>
                    <button type="button" class="btn" style="color: white; background: #5988FF;" onclick="importCSV()">
                        <i class="bi bi-plus-square-fill me-2"></i>Import CSV
                    </button>
                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mahasiswa
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">NIM
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Status</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="mahasiswa-table-body">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Mahasiswa -->
    <div class="modal fade" id="modalTambahMahasiswa" tabindex="-1" aria-labelledby="modalTambahMahasiswaLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="formTambahMahasiswa" onsubmit="submitTambahMahasiswa(event)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahMahasiswaLabel">Tambah Mahasiswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Mahasiswa</label>
                            <input type="text" id="nama" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="kode_prodi" class="form-label">Pilih Prodi</label>
                            <select id="kode_prodi" name="kode_prodi" class="form-select form-select-sm" required>
                                <option value="">Pilih Prodi</option>
                                <!-- Option prodi akan diisi via JS -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input type="text" id="alamat" name="alamat" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nim" class="form-label">NIM</label>
                            <input type="text" id="nim" name="nim" class="form-control" maxlength="15" required>
                        </div>
                        <div class="mb-3">
                            <label for="ipk" class="form-label">IPK</label>
                            <input type="number" step="0.01" min="0" max="4" id="ipk" name="ipk" class="form-control"
                                required>
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
    <!-- Modal Detail Mahasiswa -->
    <div class="modal fade" id="detailMahasiswaModal" tabindex="-1" aria-labelledby="detailMahasiswaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailMahasiswaModalLabel">Detail Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailMahasiswaBody">
                    <!-- Konten detail mahasiswa akan diisi melalui JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Edit Mahasiswa -->
    <div class="modal fade" id="modalEditMahasiswa" tabindex="-1" aria-labelledby="modalEditMahasiswaLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEditMahasiswa" onsubmit="submitEditMahasiswa(event)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditMahasiswaLabel">Edit Mahasiswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_mahasiswa" name="id_mahasiswa">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama Mahasiswa</label>
                            <input type="text" id="edit_name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_kode_prodi" class="form-label">Pilih Prodi</label>
                            <select id="edit_kode_prodi" name="kode_prodi" class="form-select form-select-sm" required>
                                <option value="">Pilih Prodi</option>
                                <!-- Option prodi akan diisi via JS -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_alamat" class="form-label">Alamat</label>
                            <input type="text" id="edit_alamat" name="alamat" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nim" class="form-label">NIM</label>
                            <input type="text" id="edit_nim" name="nim" class="form-control" maxlength="15" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_ipk" class="form-label">IPK</label>
                            <input type="number" step="0.01" min="0" max="4" id="edit_ipk" name="ipk" class="form-control"
                                required>
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

        function loadFilterOptions() {
            api.get('/prodi')
                .then(function (response) {
                    if (response.data.success) {
                        const prodiFilter = document.getElementById('prodiFilter');
                        prodiFilter.innerHTML = '<option value="">Semua Prodi</option>';
                        response.data.data.forEach(function (prodi) {
                            prodiFilter.innerHTML += `<option value="${prodi.nama_prodi}">${prodi.nama_prodi}</option>`;
                        });
                    }
                });
        }

        function loadProdiOptions() {
            api.get('/prodi')
                .then(function (response) {
                    if (response.data.success) {
                        const select = document.getElementById('kode_prodi');
                        select.innerHTML = '<option value="">Pilih Prodi</option>';
                        response.data.data.forEach(function (prodi) {
                            select.innerHTML += `<option value="${prodi.kode_prodi}">${prodi.nama_prodi}</option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Gagal memuat data prodi:', error);
                });
        }

        function loadEditProdiOptions(selectedKodeProdi = '') {
            api.get('/prodi')
                .then(function (response) {
                    if (response.data.success) {
                        const select = document.getElementById('edit_kode_prodi');
                        select.innerHTML = '<option value="">Pilih Prodi</option>';
                        response.data.data.forEach(function (prodi) {
                            select.innerHTML += `<option value="${prodi.kode_prodi}" ${prodi.kode_prodi === selectedKodeProdi ? 'selected' : ''}>
                                        ${prodi.nama_prodi}
                                    </option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Gagal memuat data prodi:', error);
                });
        }

        function loadMahasiswaData(filters = {}) {
            api.get('/mahasiswa', { params: filters })
                .then(function (response) {
                    if (response.data.success) {
                        const tableBody = document.getElementById('mahasiswa-table-body');
                        tableBody.innerHTML = ''; // Kosongkan tabel sebelum diisi ulang
                        response.data.data.forEach(mahasiswa => {
                            tableBody.innerHTML += `
                                                                                                                     <tr>
                                                                                                                                <td>
                                                                                                                                    ${mahasiswa.name}
                                                                                                                                    <br>
                                                                                                                                    <small class="text-muted">${mahasiswa.email}</small>
                                                                                                                                </td>
                                                                                                                                <td>${mahasiswa.nim}</td>
                                                                                                                                <td class="text-center">
                                                                                                                                    <span class="status-badge ${mahasiswa.status_magang === 'Sedang Magang' ? 'magang' :
                                    mahasiswa.status_magang === 'Selesai Magang' ? 'selesai' :
                                        mahasiswa.status_magang === 'Menunggu Konfirmasi' ? 'menunggu' : 'belum'
                                }">
                                                                                                                                        ${mahasiswa.status_magang}
                                                                                                                                    </span>
                                                                                                                                </td>
                                                                                                                                <td>
                                                                                                                                    <button class="btn btn-sm btn-info" onclick="detailMahasiswa(${mahasiswa.id_mahasiswa})">Detail</button>
                                                                                                                                    <button class="btn btn-sm btn-primary" onclick="editMahasiswa(${mahasiswa.id_mahasiswa})">Edit</button>
                                                                                                                                    <button class="btn btn-sm btn-danger" onclick="deleteMahasiswa(${mahasiswa.id_mahasiswa})">Hapus</button>
                                                                                                                                </td>
                                                                                                                            </tr>
                                                                                                                        `;
                        });
                    } else {
                        console.error('Error response:', response.data.message);
                        alert('Gagal memuat data mahasiswa');
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);
                    alert('Gagal memuat data mahasiswa');
                });
        }

        // Load data when page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadFilterOptions();
            loadMahasiswaData();
        });

        // Add event listeners for filters
        document.getElementById('prodiFilter').addEventListener('change', function (e) {
            loadMahasiswaData({ prodi: e.target.value });
        });

        document.getElementById('kelasFilter').addEventListener('change', function (e) {
            loadMahasiswaData({ kelas: e.target.value });
        });

        function tambahMahasiswa() {
            loadProdiOptions();
            var modal = new bootstrap.Modal(document.getElementById('modalTambahMahasiswa'));
            modal.show();
        }

        function detailMahasiswa(id) {
            api.get(`/mahasiswa/${id}`)
                .then(function (response) {
                    if (response.data.success) {
                        const mahasiswa = response.data.data;

                        console.log('Data skills:', mahasiswa.skills); // Debug log

                        // Render skills
                        const skills = mahasiswa.skills.map(skill => `
                                                                <span class="badge bg-primary me-1">
                                                                    ${skill.name || 'Tidak Diketahui'} (${skill.lama_skill || 'Tidak Diketahui'})
                                                                </span>
                                                            `).join('');

                        // Render dokumen
                        const dokumen = mahasiswa.dokumen.map(doc => `
                                                                <li class="list-group-item">
                                                                    <strong>${doc.file_type}:</strong> 
                                                                    <a href="${doc.file_url}" target="_blank">${doc.file_name}</a>
                                                                    <br><small>${doc.description || 'Tidak ada deskripsi'}</small>
                                                                </li>
                                                            `).join('');

                        // Isi modal dengan data mahasiswa
                        document.getElementById('detailMahasiswaModalLabel').innerText = `Detail Mahasiswa - ${mahasiswa.name}`;
                        document.getElementById('detailMahasiswaBody').innerHTML = `
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <p><strong>Nama:</strong> ${mahasiswa.name}</p>
                                                                        <p><strong>Email:</strong> ${mahasiswa.email}</p>
                                                                        <p><strong>NIM:</strong> ${mahasiswa.nim}</p>
                                                                        <p><strong>Prodi:</strong> ${mahasiswa.prodi}</p>
                                                                        <p><strong>Status:</strong> ${mahasiswa.status_magang}</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p><strong>Alamat:</strong> ${mahasiswa.alamat}</p>
                                                                        <p><strong>IPK:</strong> ${mahasiswa.ipk}</p>
                                                                        <p><strong>Skills:</strong></p>
                                                                        <div>${skills}</div>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <p><strong>Dokumen:</strong></p>
                                                                <ul class="list-group">${dokumen}</ul>
                                                            `;

                        // Tampilkan modal
                        const modal = new bootstrap.Modal(document.getElementById('detailMahasiswaModal'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal memuat detail mahasiswa', 'error');
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat detail mahasiswa', 'error');
                });
        }

        function editMahasiswa(id) {
            api.get(`/mahasiswa/${id}`)
                .then(function (response) {
                    if (response.data.success) {
                        const mahasiswa = response.data.data;

                        // Isi data ke dalam form edit
                        document.getElementById('edit_id_mahasiswa').value = mahasiswa.id_mahasiswa;
                        document.getElementById('edit_name').value = mahasiswa.name;
                        document.getElementById('edit_alamat').value = mahasiswa.alamat;
                        document.getElementById('edit_nim').value = mahasiswa.nim;
                        document.getElementById('edit_ipk').value = mahasiswa.ipk;

                        // Muat data prodi ke dropdown dan pilih prodi yang sesuai
                        loadEditProdiOptions(mahasiswa.kode_prodi);

                        // Tampilkan modal edit
                        const modal = new bootstrap.Modal(document.getElementById('modalEditMahasiswa'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal memuat data mahasiswa', 'error');
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat data mahasiswa', 'error');
                });
        }

        function submitEditMahasiswa(event) {
            event.preventDefault();
            const form = event.target;
            const id = document.getElementById('edit_id_mahasiswa').value;
            const data = {
                name: form.name.value,
                kode_prodi: form.kode_prodi.value,
                alamat: form.alamat.value,
                nim: form.nim.value,
                ipk: form.ipk.value
            };

            api.put(`/mahasiswa/${id}`, data)
                .then(res => {
                    if (res.data.success) {
                        Swal.fire('Berhasil!', 'Data mahasiswa berhasil diperbarui!', 'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditMahasiswa'));
                        modal.hide();
                        form.reset();
                        loadMahasiswaData(); // Refresh data mahasiswa
                    } else {
                        Swal.fire('Gagal', res.data.message || 'Gagal memperbarui data mahasiswa', 'error');
                    }
                })
                .catch(err => {
                    let msg = 'Terjadi kesalahan saat memperbarui data mahasiswa.';
                    if (err.response && err.response.data && err.response.data.message) {
                        msg = err.response.data.message;
                    }
                    Swal.fire('Error', msg, 'error');
                });
        }

        function deleteMahasiswa(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data mahasiswa ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim permintaan DELETE ke backend
                    api.delete(`/mahasiswa/${id}`)
                        .then(res => {
                            if (res.data.success) {
                                Swal.fire(
                                    'Terhapus!',
                                    'Data mahasiswa berhasil dihapus.',
                                    'success'
                                );
                                loadMahasiswaData(); // Refresh data mahasiswa
                            } else {
                                Swal.fire('Gagal', res.data.message || 'Gagal menghapus data mahasiswa', 'error');
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            Swal.fire('Error', 'Terjadi kesalahan saat menghapus data mahasiswa.', 'error');
                        });
                }
            });
        }

        function submitTambahMahasiswa(event) {
            event.preventDefault();
            const form = event.target;
            const nim = form.nim.value;
            const data = {
                name: form.name.value,
                email: nim + '@student.com',
                password: nim,
                nim: nim,
                kode_prodi: form.kode_prodi.value,
                alamat: form.alamat.value,
                ipk: form.ipk.value
            };

            api.post('/mahasiswa', data)
                .then(res => {
                    if (res.data.success) {
                        Swal.fire('Berhasil!', 'Mahasiswa berhasil ditambahkan!', 'success');
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modalTambahMahasiswa'));
                        modal.hide();
                        form.reset();
                        loadMahasiswaData();
                    } else {
                        Swal.fire('Gagal', res.data.message || 'Gagal menambahkan mahasiswa', 'error');
                    }
                })
                .catch(err => {
                    let msg = 'Terjadi kesalahan saat menambahkan mahasiswa.';
                    if (err.response && err.response.data && err.response.data.message) {
                        msg = err.response.data.message;
                    }
                    Swal.fire('Error', msg, 'error');
                });
        }
    </script>
@endpush
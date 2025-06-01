@extends('layouts.app',  ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen Lowongan'])
    <div class="container-fluid py-4">
        <div class="card pt-4">
            <div class="d-flex justify-content-between mb-3 px-4">
                <div class="d-flex gap-2">
                    <select id="perusahaanFilter" class="form-select form-select-sm" style="width: auto; height: 38px">
                        <option value="">Semua Perusahaan</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn" style="color: white; background: #02A232;" onclick="tambahLowongan()">
                        <i class="bi bi-plus-square-fill me-2"></i>Tambah Lowongan
                    </button>
                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Judul
                                    Lowongan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Perusahaan
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Kapasitas</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Tanggal Dibuat</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="lowongan-table-body">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambahLowonganModal" tabindex="-1" aria-labelledby="tambahLowonganModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahLowonganModalLabel">Tambah Lowongan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="tambahLowonganForm">
                        <div class="mb-3">
                            <label for="judul_lowongan" class="form-label">Judul Lowongan</label>
                            <input type="text" class="form-control" id="judul_lowongan" name="judul_lowongan" required>
                        </div>
                        <div class="mb-3">
                            <label for="perusahaan_id" class="form-label">Perusahaan</label>
                            <select class="form-select" id="perusahaan_id" name="perusahaan_id" required>
                                <option value="">Pilih Perusahaan</option>
                                <!-- Perusahaan akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="periode_id" class="form-label">Periode</label>
                            <select class="form-select" id="periode_id" name="periode_id" required>
                                <option value="">Pilih Periode</option>
                                <!-- Periode akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="skill_id" class="form-label">Skill</label>
                            <select class="form-select" id="skill_id" name="skill_id" required>
                                <option value="">Pilih Skill</option>
                                <!-- Skill akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_id" class="form-label">Jenis</label>
                            <select class="form-select" id="jenis_id" name="jenis_id" required>
                                <option value="">Pilih Jenis</option>
                                <!-- Jenis akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kapasitas" class="form-label">Kapasitas</label>
                            <input type="number" class="form-control" id="kapasitas" name="kapasitas" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="detailLowonganModal" tabindex="-1" aria-labelledby="detailLowonganModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailLowonganModalLabel">
                        <i class="bi bi-info-circle me-2"></i> Detail Lowongan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Judul Lowongan</label>
                                <p id="detailJudulLowongan" class="form-control-plaintext text-secondary"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Perusahaan</label>
                                <p id="detailPerusahaan" class="form-control-plaintext text-secondary"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Periode</label>
                                <p id="detailPeriode" class="form-control-plaintext text-secondary"></p>
                            </div>
                        </div>
                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kapasitas</label>
                                <p id="detailKapasitas" class="form-control-plaintext text-secondary"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi</label>
                                <p id="detailDeskripsi" class="form-control-plaintext text-secondary"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Skill</label>
                                <p id="detailSkill" class="form-control-plaintext text-secondary"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis</label>
                                <p id="detailJenis" class="form-control-plaintext text-secondary"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editLowonganModal" tabindex="-1" aria-labelledby="editLowonganModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editLowonganModalLabel">
                        <i class="bi bi-pencil-square me-2"></i> Edit Lowongan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editLowonganForm">
                        <input type="hidden" id="editLowonganId" name="id_lowongan">
                        <div class="mb-3">
                            <label for="editJudulLowongan" class="form-label">Judul Lowongan</label>
                            <input type="text" class="form-control" id="editJudulLowongan" name="judul_lowongan" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPerusahaanId" class="form-label">Perusahaan</label>
                            <select class="form-select" id="editPerusahaanId" name="perusahaan_id" required>
                                <option value="">Pilih Perusahaan</option>
                                <!-- Perusahaan akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPeriodeId" class="form-label">Periode</label>
                            <select class="form-select" id="editPeriodeId" name="periode_id" required>
                                <option value="">Pilih Periode</option>
                                <!-- Periode akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editSkillId" class="form-label">Skill</label>
                            <select class="form-select" id="editSkillId" name="skill_id" required>
                                <option value="">Pilih Skill</option>
                                <!-- Skill akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editJenisId" class="form-label">Jenis</label>
                            <select class="form-select" id="editJenisId" name="jenis_id" required>
                                <option value="">Pilih Jenis</option>
                                <!-- Jenis akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editKapasitas" class="form-label">Kapasitas</label>
                            <input type="number" class="form-control" id="editKapasitas" name="kapasitas" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDeskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning text-white">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/lowongan.css') }}">
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

        // Fungsi untuk memuat data perusahaan ke dropdown filter
        function loadFilterOptions() {
            api.get('/perusahaan')
                .then(function (response) {
                    console.log('API /perusahaan Response:', response.data); // Debugging

                    if (response.data.success && response.data.data) {
                        const perusahaanSelect = document.getElementById('perusahaan_id');
                        const editPerusahaanSelect = document.getElementById('editPerusahaanId');
                        const perusahaanFilter = document.getElementById('perusahaanFilter');

                        // Pastikan elemen ditemukan
                        if (!perusahaanSelect || !editPerusahaanSelect || !perusahaanFilter) {
                            console.error('One or more dropdown elements not found!');
                            return;
                        }

                        // Tambahkan opsi default
                        perusahaanSelect.innerHTML = '<option value="">Pilih Perusahaan</option>';
                        editPerusahaanSelect.innerHTML = '<option value="">Pilih Perusahaan</option>';
                        perusahaanFilter.innerHTML = '<option value="">Semua Perusahaan</option>';

                        // Tambahkan opsi perusahaan
                        response.data.data.forEach(function (perusahaan) {
                            const option = `<option value="${perusahaan.perusahaan_id}">${perusahaan.nama_perusahaan}</option>`;
                            perusahaanSelect.innerHTML += option;
                            editPerusahaanSelect.innerHTML += option;
                            perusahaanFilter.innerHTML += option; // Tambahkan ke filter
                        });
                    } else {
                        console.error('Invalid API response:', response.data);
                    }
                })
                .catch(function (error) {
                    console.error('Error loading perusahaan:', error);
                });
        }

        // Fungsi untuk memuat data periode ke dropdown
        function loadPeriodeOptions() {
            api.get('/periode')
                .then(function (response) {
                    console.log('API /periode Response:', response.data); // Debugging

                    if (response.data.success) {
                        const periodeSelect = document.getElementById('periode_id');
                        const editPeriodeSelect = document.getElementById('editPeriodeId');

                        periodeSelect.innerHTML = '<option value="">Pilih Periode</option>';
                        editPeriodeSelect.innerHTML = '<option value="">Pilih Periode</option>';

                        response.data.data.forEach(function (periode) {
                            const option = `<option value="${periode.periode_id}">${periode.waktu}</option>`;
                            periodeSelect.innerHTML += option;
                            editPeriodeSelect.innerHTML += option;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading periode:', error);
                });
        }

        function loadSkillOptions() {
            api.get('/skill')
                .then(function (response) {
                    if (response.data.success) {
                        const skillSelect = document.getElementById('skill_id');
                        skillSelect.innerHTML = '<option value="">Pilih Skill</option>';
                        response.data.data.forEach(function (skill) {
                            const option = `<option value="${skill.skill_id}">${skill.nama}</option>`;
                            skillSelect.innerHTML += option;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading skill:', error);
                });
        }

        function loadJenisOptions() {
            api.get('/jenis')
                .then(function (response) {
                    if (response.data.success) {
                        const jenisSelect = document.getElementById('jenis_id');
                        jenisSelect.innerHTML = '<option value="">Pilih Jenis</option>';
                        response.data.data.forEach(function (jenis) {
                            const option = `<option value="${jenis.jenis_id}">${jenis.nama_jenis}</option>`;
                            jenisSelect.innerHTML += option;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading jenis:', error);
                });
        }

        function loadEditSkillOptions(selectedId = null) {
            api.get('/skill')
                .then(function (response) {
                    if (response.data.success) {
                        const skillSelect = document.getElementById('editSkillId');
                        skillSelect.innerHTML = '<option value="">Pilih Skill</option>';
                        response.data.data.forEach(function (skill) {
                            const selected = selectedId == skill.skill_id ? 'selected' : '';
                            skillSelect.innerHTML += `<option value="${skill.skill_id}" ${selected}>${skill.nama}</option>`;
                        });
                    }
                });
        }

        function loadEditJenisOptions(selectedId = null) {
            api.get('/jenis')
                .then(function (response) {
                    if (response.data.success) {
                        const jenisSelect = document.getElementById('editJenisId');
                        jenisSelect.innerHTML = '<option value="">Pilih Jenis</option>';
                        response.data.data.forEach(function (jenis) {
                            const selected = selectedId == jenis.jenis_id ? 'selected' : '';
                            jenisSelect.innerHTML += `<option value="${jenis.jenis_id}" ${selected}>${jenis.nama_jenis}</option>`;
                        });
                    }
                });
        }

        function loadEditPeriodeOptions(selectedId = null) {
            api.get('/periode')
                .then(function (response) {
                    if (response.data.success) {
                        const editPeriodeSelect = document.getElementById('editPeriodeId');
                        editPeriodeSelect.innerHTML = '<option value="">Pilih Periode</option>';
                        response.data.data.forEach(function (periode) {
                            const selected = selectedId == periode.periode_id ? 'selected' : '';
                            editPeriodeSelect.innerHTML += `<option value="${periode.periode_id}" ${selected}>${periode.waktu}</option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading periode:', error);
                });
        }

        function loadLowonganData(filters = {}) {
            // Show loading state
            const tableBody = document.getElementById('lowongan-table-body');
            tableBody.innerHTML = `
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="spinner-border text-primary" role="status"></div>
                                                <p class="mt-2 text-sm text-secondary">Memuat data lowongan...</p>
                                            </td>
                                        </tr>
                                    `;

            api.get('/lowongan', { params: filters })
                .then(function (response) {
                    tableBody.innerHTML = ''; // Kosongkan tabel sebelum memuat data baru

                    if (response.data.success && response.data.data.length > 0) {
                        response.data.data.forEach((lowongan, index) => {
                            const date = new Date(lowongan.created_at);
                            const formattedDate = date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });

                            const row = document.createElement('tr');
                            row.style.animation = `fadeIn 0.3s ease forwards ${index * 0.05}s`;
                            row.innerHTML = `
                                                        <td>
                                                            <p class="text-sm font-weight-bold mb-0">${lowongan.judul_lowongan}</p>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex px-2 py-1">
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <h6 class="mb-0 text-sm">${lowongan.perusahaan.nama_perusahaan}</h6>
                                                                    <p class="text-xs text-secondary mb-0">${lowongan.perusahaan.nama_kota}</p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="text-secondary text-xs font-weight-bold">${lowongan.kapasitas} Orang</span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="text-secondary text-xs font-weight-bold">${formattedDate}</span>
                                                        </td>
                                                    <td class="align-middle">
        <div class="action-buttons">  <!-- Ganti class dari "d-flex gap-1" menjadi "action-buttons" -->
            <button class="btn btn-sm btn-info me-1" onclick="detailLowongan(${lowongan.id_lowongan})" title="Lihat Detail">
                <i class="fas fa-eye me-1"></i>Detail
            </button>
            <button class="btn btn-sm btn-primary me-1" onclick="editLowongan(${lowongan.id_lowongan})" title="Edit Lowongan">
                <i class="fas fa-edit me-1"></i>Edit
            </button>
            <button class="btn btn-sm btn-danger" onclick="deleteLowongan(${lowongan.id_lowongan})" title="Hapus Lowongan">
                <i class="fas fa-trash-alt me-1"></i>Hapus
            </button>
        </div>
    </td>
                                                    `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = `
                                                    <tr>
                                                        <td colspan="5">
                                                            <div class="text-center py-5">
                                                                <div class="empty-state-icon mb-3">
                                                                    <i class="bi bi-clipboard-x" style="font-size: 3rem; color: #8898aa;"></i>
                                                                </div>
                                                                <h6 class="text-muted">Tidak ada lowongan tersedia</h6>
                                                                <p class="text-xs text-secondary mb-0">
                                                                    ${filters.perusahaan_id ? 'Belum ada lowongan untuk perusahaan ini' : 'Belum ada lowongan yang ditambahkan'}
                                                                </p>
                                                                <button class="btn btn-sm btn-outline-primary mt-3" onclick="tambahLowongan()">
                                                                    <i class="bi bi-plus-lg me-1"></i>Tambah Lowongan Baru
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                `;
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);
                    tableBody.innerHTML = `
                                                <tr>
                                                    <td colspan="5">
                                                        <div class="alert alert-danger mx-3 my-4">
                                                            <div class="d-flex">
                                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                                <div>
                                                                    <h6 class="alert-heading mb-1">Gagal memuat data</h6>
                                                                    <p class="mb-0">Terjadi kesalahan saat memuat data lowongan. Silakan coba lagi.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            `;
                });
        }

        function detailLowongan(id) {
            console.log('Fetching detail for Lowongan ID:', id);

            // Show modal with loading state
            const detailModal = document.getElementById('detailLowonganModal');
            const modalBody = detailModal.querySelector('.modal-body');

            modalBody.innerHTML = `
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-primary mb-3" role="status"></div>
                                        <p class="text-muted">Memuat detail lowongan...</p>
                                    </div>
                                `;

            // Show the modal while loading
            const modal = new bootstrap.Modal(detailModal);
            modal.show();

            api.get(`/lowongan/${id}`)
                .then(function (response) {
                    if (response.data.success) {
                        const lowongan = response.data.data;
                        console.log('Lowongan Detail Data:', lowongan);

                        // Add animation to the content when it loads
                        modalBody.style.opacity = "0";
                        modalBody.innerHTML = `
                                                <div class="row">
                                                    <!-- Kolom Kiri -->
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Judul Lowongan</label>
                                                            <p id="detailJudulLowongan" class="form-control-plaintext text-secondary">${lowongan.judul_lowongan}</p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Perusahaan</label>
                                                            <p id="detailPerusahaan" class="form-control-plaintext text-secondary">${lowongan.perusahaan.nama_perusahaan}</p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Periode</label>
                                                            <p id="detailPeriode" class="form-control-plaintext text-secondary">${lowongan.periode.waktu}</p>
                                                        </div>
                                                    </div>
                                                    <!-- Kolom Kanan -->
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Kapasitas</label>
                                                            <p id="detailKapasitas" class="form-control-plaintext text-secondary">${lowongan.kapasitas} Orang</p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Skill</label>
                                                            <p id="detailSkill" class="form-control-plaintext text-secondary">${lowongan.skill.nama_skill}</p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Jenis</label>
                                                            <p id="detailJenis" class="form-control-plaintext text-secondary">${lowongan.jenis.nama_jenis}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Deskripsi</label>
                                                            <div class="p-3 bg-light rounded-3">
                                                                ${lowongan.deskripsi}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;

                        // Fade in animation
                        setTimeout(() => {
                            modalBody.style.transition = "opacity 0.3s ease";
                            modalBody.style.opacity = "1";
                        }, 150);

                    } else {
                        modalBody.innerHTML = `
                                                <div class="alert alert-danger">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                    Gagal memuat detail lowongan.
                                                </div>
                                            `;
                    }
                })
                .catch(function (error) {
                    console.error('Error fetching detail lowongan:', error);
                    modalBody.innerHTML = `
                                            <div class="alert alert-danger">
                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                Terjadi kesalahan saat memuat detail lowongan.
                                            </div>
                                        `;
                });
        }

        // Fungsi untuk membuka modal tambah lowongan
        function tambahLowongan() {
            loadFilterOptions();
            loadPeriodeOptions();
            loadSkillOptions();   // <--- Tambahkan ini
            loadJenisOptions();   // <--- Tambahkan ini

            const modal = new bootstrap.Modal(document.getElementById('tambahLowonganModal'));
            modal.show();
        }

        document.getElementById('tambahLowonganForm').addEventListener('submit', function (e) {
            e.preventDefault();

            // Show loading state on button
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...`;

            const formData = new FormData(this);

            api.post('/lowongan', formData)
                .then(function (response) {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;

                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Lowongan berhasil ditambahkan!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('tambahLowonganModal'));
                            modal.hide();
                            loadLowonganData();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.data.message || 'Gagal menambahkan lowongan.',
                        });
                    }
                })
                .catch(function (error) {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;

                    console.error('Error adding lowongan:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menambahkan lowongan.',
                    });
                });
        });

        // Fungsi untuk membuka modal edit lowongan
        function editLowongan(id) {
            api.get(`/lowongan/${id}`)
                .then(function (response) {
                    if (response.data.success) {
                        const lowongan = response.data.data;

                        document.getElementById('editLowonganId').value = lowongan.id_lowongan;
                        document.getElementById('editJudulLowongan').value = lowongan.judul_lowongan;
                        document.getElementById('editPerusahaanId').value = lowongan.perusahaan.perusahaan_id;
                        loadEditPeriodeOptions(lowongan.periode.periode_id); // <-- ini yang benar
                        document.getElementById('editKapasitas').value = lowongan.kapasitas;
                        document.getElementById('editDeskripsi').value = lowongan.deskripsi;
                        loadEditSkillOptions(lowongan.skill.skill_id);
                        loadEditJenisOptions(lowongan.jenis.jenis_id);

                        const modal = new bootstrap.Modal(document.getElementById('editLowonganModal'));
                        modal.show();
                    } else {
                        Swal.fire('Error', 'Gagal memuat data lowongan.', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat data lowongan.', 'error');
                });
        }

        document.getElementById('editLowonganForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const id = document.getElementById('editLowonganId').value;
            const formData = new FormData(this);

            // Show loading state on button
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...`;

            api.put(`/lowongan/${id}`, formData)
                .then(function (response) {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;

                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Lowongan berhasil diperbarui!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editLowonganModal'));
                            modal.hide();
                            loadLowonganData();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.data.message || 'Gagal memperbarui lowongan.',
                        });
                    }
                })
                .catch(function (error) {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;

                    console.error('Error updating lowongan:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memperbarui lowongan.',
                    });
                });
        });

        // Fungsi untuk menghapus lowongan
        function deleteLowongan(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Lowongan yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return api.delete(`/lowongan/${id}`)
                        .then(response => {
                            if (!response.data.success) {
                                throw new Error(response.data.message || 'Gagal menghapus lowongan');
                            }
                            return response.data;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(
                                `Terjadi kesalahan: ${error.response?.data?.message || error.message}`
                            );
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Terhapus!',
                        text: 'Lowongan berhasil dihapus.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        loadLowonganData();
                    });
                }
            });
        }

        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            loadFilterOptions();
            loadLowonganData();
        });

        // Event listener untuk filter perusahaan
        document.getElementById('perusahaanFilter').addEventListener('change', function (e) {
            loadLowonganData({ perusahaan_id: e.target.value });
        });
    </script>
@endpush
@extends('layouts.app',  ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Permintaan Magang'])
    <div class="card">
        <div class="card-header px-4 py-3">
            <div class="search_card">
                <div class="search-filter d-flex gap-3">
                    <!-- Komponen Pencarian -->
                    <div class="search-box">
                        <input type="text" class="form-control search-input" placeholder="Cari Lowongan">
                        <i class="fas fa-search search-icon"></i>
                    </div>

                    <!-- Filter Perusahaan (terpisah dari search-box) -->
                    <div class="dropdown">
                        <button class="btn filter-btn dropdown-toggle" type="button" id="dropdownPerusahaan"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-building"></i>
                            <span>Perusahaan</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" id="perusahaanDropdown"
                            aria-labelledby="dropdownPerusahaan">
                            <li><a class="dropdown-item active" href="#" data-perusahaan-id="all">Semua Perusahaan</a></li>
                            <!-- Daftar perusahaan akan dimuat di sini secara dinamis -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body px-4">
            <div class="permintaan-list">
                <!-- Data permintaan akan dimuat di sini melalui JavaScript -->
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Permintaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Data detail akan dimuat di sini melalui JavaScript -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="{{ asset('assets/css/permintaan.css') }}" rel="stylesheet" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Variabel global untuk menyimpan semua permintaan
        let allPermintaanData = [];

        // Modifikasi loadPermintaanData untuk menyimpan data dan memuat dropdown perusahaan
        function loadPermintaanData() {
            showLoadingState(); // Tampilkan state loading sebelum memuat data
            fetch('/api/magang', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(response => {
                    if (response.success) {
                        // Simpan data untuk filtering
                        allPermintaanData = response.data;

                        // Render daftar permintaan
                        renderPermintaanList(allPermintaanData);

                        // Muat opsi perusahaan untuk filter
                        loadPerusahaanOptions(allPermintaanData);
                    } else {
                        Swal.fire(
                            'Gagal Memuat Data',
                            response.message || 'Terjadi kesalahan saat memuat data permintaan magang.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat memuat data permintaan magang.',
                        'error'
                    );
                });
        }

        function showLoadingState() {
            const permintaanList = document.querySelector('.permintaan-list');
            if (permintaanList) {
                permintaanList.innerHTML = `
                                            <div class="text-center py-5">
                                                <div class="spinner-border text-primary mb-3" role="status"></div>
                                                <p class="text-muted mb-0">Memuat data permintaan magang...</p>
                                            </div>
                                        `;
            }
        }


        // Fungsi untuk menampilkan daftar permintaan
        // Fungsi untuk menampilkan daftar permintaan dengan animasi
        function renderPermintaanList(items) {
            const permintaanList = document.querySelector('.permintaan-list');
            if (!permintaanList) {
                console.error('Element .permintaan-list tidak ditemukan di halaman.');
                return;
            }

            permintaanList.innerHTML = ''; // Kosongkan daftar permintaan

            if (items.length === 0) {
                permintaanList.innerHTML = `
                                        <div class="empty-state text-center py-5">
                                            <div class="empty-state-icon mb-3">
                                                <i class="fas fa-clipboard-list text-muted" style="font-size: 60px; opacity: 0.2;"></i>
                                            </div>
                                            <h5 class="mb-1">Tidak ada data permintaan</h5>
                                            <p class="text-muted mb-0">Tidak ada data permintaan yang sesuai dengan filter Anda.</p>
                                        </div>`;
                return;
            }

            // Tambahkan item dengan animasi fade-in bertahap
            items.forEach((permintaan, index) => {
                const item = document.createElement('div');
                item.className = 'permintaan-item';
                item.style.opacity = '0';
                item.style.transform = 'translateY(10px)';
                item.style.animation = `fadeInUp 0.3s ease-out ${index * 0.05}s forwards`;

                // Tentukan status badge class
                let statusBadgeClass = '';
                switch (permintaan.auth.toLowerCase()) {
                    case 'diterima':
                        statusBadgeClass = 'diterima';
                        break;
                    case 'ditolak':
                        statusBadgeClass = 'ditolak';
                        break;
                    default:
                        statusBadgeClass = 'menunggu';
                        break;
                }

                item.innerHTML = `
                                        <div class="mahasiswa-info">
                                            <h6 class="nama">${permintaan.mahasiswa.name}</h6>
                                            <p class="nim">NIM: ${permintaan.mahasiswa.nim}</p>
                                        </div>

                                        <div class="posisi">
                                            <span class="job-title font-weight-bold">${permintaan.judul_lowongan}</span>
                                        </div>

                                        <div class="perusahaan">
                                            <span class="company-badge font-weight-bold">
                                                ${permintaan.perusahaan.nama_perusahaan}
                                            </span>
                                        </div>

                                        <div class="status">
                                            <span class="status-badge ${statusBadgeClass}">
                                                ${permintaan.auth}
                                            </span>
                                        </div>

                                       <div class="action">
        <div class="hover-actions">
            <button class="btn btn-sm btn-info me-1" onclick="showDetail(${permintaan.id})" title="Lihat Detail"> 
                <i class="fas fa-eye me-md-1"></i><span class="d-none d-md-inline">Detail</span>
            </button>
            <button class="btn btn-sm btn-success me-1" onclick="acceptRequest(${permintaan.id})" title="Terima Permintaan">
                <i class="fas fa-check me-md-1"></i><span class="d-none d-md-inline">Terima</span>
            </button>
            <button class="btn btn-sm btn-danger" onclick="rejectRequest(${permintaan.id})" title="Tolak Permintaan">
                <i class="fas fa-times me-md-1"></i><span class="d-none d-md-inline">Tolak</span>
            </button>
        </div>
    </div>
                                    `;

                permintaanList.appendChild(item);
            });

            // Tambahkan keyframes untuk animasi jika belum ada
            if (!document.getElementById('fadeInUp-animation')) {
                const style = document.createElement('style');
                style.id = 'fadeInUp-animation';
                style.textContent = `
                                        @keyframes fadeInUp {
                                            from {
                                                opacity: 0;
                                                transform: translateY(10px);
                                            }
                                            to {
                                                opacity: 1;
                                                transform: translateY(0);
                                            }
                                        }
                                    `;
                document.head.appendChild(style);
            }
        }

        // Fungsi untuk memuat opsi perusahaan di dropdown
        function loadPerusahaanOptions(data) {
            // Kumpulkan nama perusahaan unik
            const uniquePerusahaan = [];
            data.forEach(item => {
                const perusahaanName = item.perusahaan.nama_perusahaan;
                if (perusahaanName && !uniquePerusahaan.some(p => p === perusahaanName)) {
                    uniquePerusahaan.push(perusahaanName);
                }
            });

            // Urutkan perusahaan berdasarkan abjad
            uniquePerusahaan.sort();

            // Dapatkan elemen dropdown
            const dropdownMenu = document.getElementById('perusahaanDropdown');
            if (!dropdownMenu) return;

            // Kosongkan dropdown kecuali opsi "Semua Perusahaan"
            while (dropdownMenu.children.length > 1) {
                dropdownMenu.removeChild(dropdownMenu.lastChild);
            }

            // Tambahkan setiap perusahaan ke dropdown
            uniquePerusahaan.forEach(perusahaan => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.classList.add('dropdown-item');
                a.href = '#';
                a.dataset.perusahaanName = perusahaan;
                a.textContent = perusahaan;

                a.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Perbarui teks pada tombol dropdown
                    document.querySelector('#dropdownPerusahaan span').textContent = perusahaan;

                    // Tandai item ini sebagai aktif
                    document.querySelectorAll('#perusahaanDropdown .dropdown-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    this.classList.add('active');

                    // Filter data berdasarkan perusahaan yang dipilih
                    applyFilters();
                });

                li.appendChild(a);
                dropdownMenu.appendChild(li);
            });

            // Tambahkan event listener untuk opsi "Semua Perusahaan"
            const allOption = dropdownMenu.querySelector('[data-perusahaan-id="all"]');
            if (allOption) {
                allOption.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Reset teks tombol dropdown
                    document.querySelector('#dropdownPerusahaan span').textContent = 'Perusahaan';

                    // Tandai item ini sebagai aktif
                    document.querySelectorAll('#perusahaanDropdown .dropdown-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    this.classList.add('active');

                    // Tampilkan semua data
                    applyFilters();
                });
            }
        }

        // Fungsi untuk menerapkan semua filter yang aktif
        function applyFilters() {
            if (!Array.isArray(allPermintaanData) || allPermintaanData.length === 0) {
                console.warn("Data belum dimuat - tidak dapat melakukan pencarian");
                return;
            }
            // Ambil nilai pencarian
            const searchTerm = document.querySelector('.search-input').value.toLowerCase().trim();

            // Ambil filter perusahaan yang aktif
            const selectedPerusahaan = document.querySelector('#dropdownPerusahaan span').textContent;
            const isPerusahaanFilterActive = selectedPerusahaan !== 'Perusahaan';

            // Filter data berdasarkan kedua kriteria
            const filteredData = allPermintaanData.filter(permintaan => {
                // Filter berdasarkan pencarian
                const matchesSearch = !searchTerm ||
                    (permintaan.judul_lowongan?.toLowerCase() || '').includes(searchTerm) ||
                    (permintaan.perusahaan?.nama_perusahaan?.toLowerCase() || '').includes(searchTerm) ||
                    (permintaan.mahasiswa?.name?.toLowerCase() || '').includes(searchTerm) ||
                    (String(permintaan.mahasiswa?.nim || '')).toLowerCase().includes(searchTerm);

                // Filter berdasarkan perusahaan
                const matchesPerusahaan = !isPerusahaanFilterActive ||
                    permintaan.perusahaan.nama_perusahaan === selectedPerusahaan;

                // Item harus memenuhi kedua kondisi
                return matchesSearch && matchesPerusahaan;
            });

            // Tampilkan hasil filter
            renderPermintaanList(filteredData);
        }

        // Tambahkan event listener setelah DOM di-load
        document.addEventListener('DOMContentLoaded', function () {
            // Load data permintaan
            loadPermintaanData();

            // Tambahkan event listener untuk input pencarian
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {

                // Pastikan event listener ditambahkan hanya sekali
                searchInput.removeEventListener('input', handleSearchInput);
                searchInput.addEventListener('input', handleSearchInput);

                // Gunakan fungsi bernama agar dapat dihapus
                function handleSearchInput() {
                    clearTimeout(this.debounceTimer);

                    // Tambahkan indikator pencarian
                    const searchIcon = document.querySelector('.search-icon');
                    if (searchIcon) {
                        searchIcon.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
                    }

                    this.debounceTimer = setTimeout(() => {
                        applyFilters();

                        // Kembalikan ikon pencarian
                        if (searchIcon) {
                            searchIcon.innerHTML = '<i class="bi bi-search"></i>';
                        }
                    }, 300);
                }
            } else {
                console.error("Search input tidak ditemukan!");
            }

            // Tambahkan event listener untuk ikon search
            const searchIcon = document.querySelector('.search-icon');
            if (searchIcon) {
                searchIcon.addEventListener('click', applyFilters);
            }
        });

        function showDetail(id) {
            // Tampilkan loading state
            const detailModalBody = document.querySelector('#detailModal .modal-body');
            detailModalBody.innerHTML = `
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary mb-2" role="status"></div>
                                    <p class="mb-0">Memuat detail permintaan...</p>
                                </div>
                            `;

            // Tampilkan modal dengan loading state
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();

            // Ambil data detail
            fetch(`/api/magang/${id}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(response => {
                    if (response.success) {
                        const data = response.data;

                        // Perbaiki title modal
                        document.querySelector('#detailModal .modal-title').innerText = `Detail Permintaan - ${data.lowongan.judul_lowongan}`;

                        // Tambahkan konten detail dengan styling yang lebih baik
                        detailModalBody.innerHTML = `
                                        <div class="p-2 mb-3 rounded-3" style="background-color: rgba(89, 136, 255, 0.05);">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar avatar-md bg-gradient-primary rounded-circle p-2 me-3">
                                                    <span class="text-white fs-5">${data.mahasiswa.name.charAt(0)}</span>
                                                </div>
                                                <div>
                                                    <h5 class="mb-0">${data.mahasiswa.name}</h5>
                                                    <p class="text-muted mb-0">${data.mahasiswa.nim} | ${data.mahasiswa.email}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <h6 class="text-uppercase text-muted mb-3 border-bottom pb-2">Detail Mahasiswa</h6>
                                                <div class="mb-2">
                                                    <label class="text-muted small">Prodi</label>
                                                    <p class="mb-0">${data.mahasiswa.prodi || '-'}</p>
                                                </div>
                                                <div>
                                                    <label class="text-muted small">Skills</label>
                                                    <div>
                                                        ${(data.mahasiswa.skills || []).map(skill =>
                            `<span class="badge bg-light text-dark me-1 mb-1">${skill}</span>`
                        ).join('')}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-4">
                                                <h6 class="text-uppercase text-muted mb-3 border-bottom pb-2">Detail Lowongan</h6>
                                                <div class="mb-2">
                                                    <label class="text-muted small">Judul</label>
                                                    <p class="mb-0">${data.lowongan.judul_lowongan}</p>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="text-muted small">Kapasitas</label>
                                                    <p class="mb-0">${data.lowongan.persyaratan} Kandidat</p>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="text-muted small">Periode</label>
                                                    <p class="mb-0">${data.lowongan.tanggal_mulai} s/d ${data.lowongan.tanggal_selesai}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 mb-4">
                                                <h6 class="text-uppercase text-muted mb-3 border-bottom pb-2">Detail Perusahaan</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-2">
                                                            <label class="text-muted small">Nama Perusahaan</label>
                                                            <p class="mb-0">${data.perusahaan.nama_perusahaan}</p>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="text-muted small">Kota</label>
                                                            <p class="mb-0">${data.perusahaan.kota}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-2">
                                                            <label class="text-muted small">Contact Person</label>
                                                            <p class="mb-0">${data.perusahaan.contact_person}</p>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="text-muted small">Email</label>
                                                            <p class="mb-0">${data.perusahaan.email}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="text-muted small">Alamat</label>
                                                    <p class="mb-0">${data.perusahaan.alamat_perusahaan}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="text-uppercase text-muted mb-3 border-bottom pb-2">Dokumen</h6>
                                                <div class="d-flex gap-2">
                                                    <a href="${data.dokumen.cv_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-file-alt me-1"></i>Download CV
                                                    </a>
                                                    <a href="${data.dokumen.surat_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-file-pdf me-1"></i>Download Surat Lamaran
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-3 border-top">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <label class="text-uppercase text-muted small">Status</label>
                                                    <div>
                                                        <span class="status-badge ${data.status === 'aktif' ? 'diterima' : data.status === 'tidak aktif' ? 'ditolak' : 'menunggu'}">
                                                            ${data.status || 'Menunggu'}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="action-buttons">
                                                    <button class="btn btn-sm btn-danger me-2" onclick="rejectRequest(${data.id}); bootstrap.Modal.getInstance(document.getElementById('detailModal')).hide();">
                                                        <i class="fas fa-times me-1"></i>Tolak
                                                    </button>
                                                    <button class="btn btn-sm btn-success" onclick="acceptRequest(${data.id}); bootstrap.Modal.getInstance(document.getElementById('detailModal')).hide();">
                                                        <i class="fas fa-check me-1"></i>Terima
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                    } else {
                        detailModalBody.innerHTML = `
                                        <div class="alert alert-danger">
                                            Gagal memuat detail: ${response.message || 'Terjadi kesalahan.'}
                                        </div>
                                    `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    detailModalBody.innerHTML = `
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Gagal memuat detail permintaan magang
                                    </div>
                                `;
                });
        }

        function acceptRequest(id) {
            // Tampilkan dialog konfirmasi yang lebih konsisten
            Swal.fire({
                title: 'Terima Permintaan Magang?',
                text: "Permintaan ini akan diterima dan mahasiswa akan memulai magang",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Terima',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang memproses permintaan',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`/api/magang/${id}/accept`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ status: 'aktif' })
                    })
                        .then(response => response.json())
                        .then(response => {
                            console.log('Respons dari server:', response);
                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Permintaan magang telah diterima',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    loadPermintaanData(); // Refresh data
                                });
                            } else {
                                Swal.fire('Gagal!', response.message || 'Terjadi kesalahan saat menerima permintaan.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Terjadi kesalahan saat memproses permintaan.', 'error');
                        });
                }
            });
        }

        function rejectRequest(id) {
            // Dialog konfirmasi yang konsisten
            Swal.fire({
                title: 'Tolak Permintaan Magang?',
                text: "Permintaan ini akan ditolak dan data terkait akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tolak',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang memproses permintaan',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`/api/magang/${id}/reject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                        .then(response => response.json())
                        .then(response => {
                            console.log('Respons dari server:', response);
                            if (response.success) {
                                Swal.fire({
                                    title: 'Ditolak!',
                                    text: 'Permintaan magang telah ditolak',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    loadPermintaanData(); // Refresh data
                                });
                            } else {
                                Swal.fire('Gagal!', response.message || 'Terjadi kesalahan saat menolak permintaan.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Terjadi kesalahan saat memproses permintaan.', 'error');
                        });
                }
                onclick = "rejectRequest(${data.id}, 'detailModal')"
            });
        }
    </script>
@endpush
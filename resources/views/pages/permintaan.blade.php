@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

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
                            <li><a class="dropdown-item active" href="#" data-perusahaan-id="all">Semua Perusahaan</a>
                            </li>
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
        // ✅ PERBAIKAN: Function renderPermintaanList - pastikan status ditolak ditampilkan dengan benar
        function renderPermintaanList(items) {
            const permintaanList = document.querySelector('.permintaan-list');
            if (!permintaanList) {
                console.error('Element .permintaan-list tidak ditemukan di halaman.');
                return;
            }

            permintaanList.innerHTML = '';

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

                // ✅ PERBAIKAN: Status badge class yang lebih akurat
                let statusBadgeClass = '';
                let statusText = '';

                switch (permintaan.auth.toLowerCase()) {
                    case 'diterima':
                        statusBadgeClass = 'diterima';
                        statusText = 'Diterima';
                        break;
                    case 'ditolak':
                        statusBadgeClass = 'ditolak';
                        statusText = 'Ditolak';
                        break;
                    case 'menunggu':
                    default:
                        statusBadgeClass = 'menunggu';
                        statusText = 'Menunggu';
                        break;
                }

                // ✅ PERBAIKAN: Action buttons yang berbeda berdasarkan status
                let actionButtons = `
                        <button class="btn btn-sm btn-info me-1" onclick="showDetail(${permintaan.id})" title="Lihat Detail"> 
                            <i class="fas fa-eye me-md-1"></i><span class="d-none d-md-inline">Detail</span>
                        </button>
                    `;

                // ✅ SIMPLE: Hanya tampilkan tombol terima/tolak untuk status "menunggu"
                if (permintaan.auth.toLowerCase() === 'menunggu') {
                    actionButtons += `
                    <button class="btn btn-sm btn-success me-1" onclick="acceptRequest(${permintaan.id})" title="Terima Permintaan">
                        <i class="fas fa-check me-md-1"></i><span class="d-none d-md-inline">Terima</span>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="rejectRequest(${permintaan.id})" title="Tolak Permintaan">
                        <i class="fas fa-times me-md-1"></i><span class="d-none d-md-inline">Tolak</span>
                    </button>
                `;
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
                        ${statusText}
                    </span>
                </div>

                <div class="action">
                    <div class="hover-actions">
                        ${actionButtons}
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

                a.addEventListener('click', function(e) {
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
                allOption.addEventListener('click', function(e) {
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
        document.addEventListener('DOMContentLoaded', function() {
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

        // ✅ PERBAIKAN: Function showDetail dengan safe checks yang lengkap
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
                        document.querySelector('#detailModal .modal-title').innerText =
                            `Detail Permintaan - ${data.lowongan?.judul_lowongan || 'Lowongan'}`;

                        // ✅ UPDATE: Bagian periode info dengan data yang benar
                        const periodeInfo = data.lowongan?.periode ? `
    <div>
        <label class="text-muted small">Periode Magang</label>
        <p class="mb-0">${data.lowongan.periode.waktu}</p>
        <small class="text-muted">
            ${data.lowongan.periode.tanggal_mulai} - ${data.lowongan.periode.tanggal_selesai}
        </small>
    </div>
` : `
    <div>
        <label class="text-muted small">Periode Magang</label>
        <p class="mb-0 text-muted">Informasi periode tidak tersedia</p>
    </div>
`;

                        // ✅ UPDATE: Skills dengan lama pengalaman
                        let skillsHTML = '';
                        if (data.mahasiswa?.skills && Array.isArray(data.mahasiswa.skills) && data.mahasiswa.skills
                            .length > 0) {
                            skillsHTML = data.mahasiswa.skills.map(skill =>
                                `<span class="badge bg-primary text-white me-1 mb-1" title="Pengalaman: ${skill.lama_skill}">
            ${skill.nama_skill}
        </span>`
                            ).join('');
                        } else {
                            skillsHTML = '<span class="text-muted small">Belum ada skill yang ditambahkan</span>';
                        }

                        // ✅ UPDATE: Minat mahasiswa
                        let minatHTML = '';
                        if (data.mahasiswa?.minat && Array.isArray(data.mahasiswa.minat) && data.mahasiswa.minat
                            .length > 0) {
                            minatHTML = data.mahasiswa.minat.map(minat =>
                                `<span class="badge bg-info text-white me-1 mb-1">${minat}</span>`
                            ).join('');
                        } else {
                            minatHTML = '<span class="text-muted small">Belum ada minat yang dipilih</span>';
                        }

                        // ✅ UPDATE: Skills yang dibutuhkan lowongan
                        let skillsRequiredHTML = '';
                        if (data.lowongan?.skills_required && Array.isArray(data.lowongan.skills_required) && data
                            .lowongan.skills_required.length > 0) {
                            skillsRequiredHTML = data.lowongan.skills_required.map(skill =>
                                `<span class="badge bg-warning text-dark me-1 mb-1">${skill}</span>`
                            ).join('');
                        } else {
                            skillsRequiredHTML =
                                '<span class="text-muted small">Tidak ada skill khusus yang dibutuhkan</span>';
                        }

                        // ✅ SAFE CHECK: Dokumen dengan fallback
                        let dokumenHTML = '';
                        if (data.dokumen && Array.isArray(data.dokumen) && data.dokumen.length > 0) {
                            dokumenHTML = data.dokumen.map(doc => {
                                // Tentukan icon berdasarkan tipe file
                                let icon = 'fas fa-file';
                                const fileType = (doc.file_type || '').toLowerCase();

                                if (fileType.includes('cv')) {
                                    icon = 'fas fa-file-alt text-primary';
                                } else if (fileType.includes('surat')) {
                                    icon = 'fas fa-file-pdf text-danger';
                                } else if (fileType.includes('transkrip')) {
                                    icon = 'fas fa-file-excel text-success';
                                } else if (fileType.includes('sertifikat')) {
                                    icon = 'fas fa-certificate text-warning';
                                } else if (fileType.includes('portofolio')) {
                                    icon = 'fas fa-folder text-info';
                                }

                                return `
                                <div class="document-item border rounded p-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="document-info flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="${icon} me-2"></i>
                                                <strong class="document-type">${doc.file_type || 'Dokumen'}</strong>
                                            </div>
                                            <p class="file-name mb-1">${doc.file_name || 'Nama file tidak tersedia'}</p>
                                            <small class="text-muted">${doc.description || 'Tidak ada deskripsi'}</small>
                                            <div class="file-meta mt-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>${doc.upload_date || 'Tanggal tidak tersedia'}
                                                    <span class="ms-2">
                                                        <i class="fas fa-weight-hanging me-1"></i>${doc.file_size || 'Ukuran tidak diketahui'}
                                                    </span>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            ${doc.file_url ? `
                                                    <a href="${doc.file_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download me-1"></i>Download
                                                    </a>
                                                ` : `
                                                    <span class="text-muted small">File tidak tersedia</span>
                                                `}
                                        </div>
                                    </div>
                                </div>
                            `;
                            }).join('');
                        } else {
                            dokumenHTML = `
                            <div class="text-center py-3">
                                <i class="fas fa-file-excel text-muted mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                <p class="text-muted mb-0">Belum ada dokumen yang diupload</p>
                            </div>
                        `;
                        }

                        // ✅ SAFE CHECK: Dosen pembimbing dengan fallback yang aman
                        let dosenHTML = '';
                        if (data.dosen_pembimbing && typeof data.dosen_pembimbing === 'object') {
                            if (data.dosen_pembimbing.assigned === true || data.dosen_pembimbing.assigned === 'true') {
                                dosenHTML = `
                        <div class="alert alert-info">
                            <i class="fas fa-user-graduate me-2"></i>
                            <strong>Dosen Pembimbing:</strong> ${data.dosen_pembimbing.nama || 'Nama tidak tersedia'} 
                            <span class="text-muted">(NIP: ${data.dosen_pembimbing.nip || 'NIP tidak tersedia'})</span>
                        </div>
                    `;
                            } else {
                                dosenHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Belum ada dosen pembimbing yang ditugaskan</strong>
                            <div class="mt-2">
                                <a href="/plotting" class="btn btn-sm btn-primary">
                                    <i class="fas fa-user-plus me-1"></i>Assign Dosen Pembimbing
                                </a>
                            </div>
                        </div>
                    `;
                            }
                        } else {
                            // Fallback jika data.dosen_pembimbing tidak ada atau null
                            dosenHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Belum ada dosen pembimbing yang ditugaskan</strong>
                            <div class="mt-2">
                                <a href="/plotting" class="btn btn-sm btn-primary">
                                    <i class="fas fa-user-plus me-1"></i>Assign Dosen Pembimbing
                                </a>
                            </div>
                        </div>
                    `;
                        }

                        // ✅ ENHANCED: Konten detail yang lengkap dengan safe checks
                        detailModalBody.innerHTML = `
                <div class="p-2 mb-3 rounded-3" style="background-color: rgba(89, 136, 255, 0.05);">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md bg-gradient-primary rounded-circle p-2 me-3">
                            <span class="text-white fs-5">${(data.mahasiswa?.name || 'N').charAt(0)}</span>
                        </div>
                        <div>
                            <h5 class="mb-0">${data.mahasiswa?.name || 'Nama tidak tersedia'}</h5>
                            <p class="text-muted mb-0">${data.mahasiswa?.nim || 'NIM tidak tersedia'} | ${data.mahasiswa?.email || 'Email tidak tersedia'}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h6 class="text-uppercase text-muted mb-3 border-bottom pb-2">
                            <i class="fas fa-user me-2"></i>Detail Mahasiswa
                        </h6>
                        <div class="mb-2">
                            <label class="text-muted small">Prodi/Kelas</label>
                            <p class="mb-0">${data.mahasiswa?.prodi || 'Prodi tidak tersedia'} - ${data.mahasiswa?.kelas || 'Kelas tidak tersedia'}</p>
                        </div>
                        <div class="mb-2">
                            <label class="text-muted small">IPK</label>
                            <p class="mb-0">${data.mahasiswa?.ipk || 'IPK tidak tersedia'}</p>
                        </div>
                        <div class="mb-2">
                            <label class="text-muted small">Alamat</label>
                            <p class="mb-0">${data.mahasiswa?.alamat || 'Alamat tidak tersedia'}</p>
                        </div>
                        <div class="mb-2">
                            <label class="text-muted small">No. Telepon</label>
                            <p class="mb-0">${data.mahasiswa?.telp || 'Telepon tidak tersedia'}</p>
                        </div>
                        <div>
                            <label class="text-muted small">Skills</label>
                            <div>${skillsHTML}</div>
                        </div>
                        <div>
                            <label class="text-muted small">Minat</label>
                            <div>${minatHTML}</div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <h6 class="text-uppercase text-muted mb-3 border-bottom pb-2">
                            <i class="fas fa-briefcase me-2"></i>Detail Lowongan
                        </h6>
                        <div class="mb-2">
                            <label class="text-muted small">Posisi</label>
                            <p class="mb-0">${data.lowongan?.judul_lowongan || 'Posisi tidak tersedia'}</p>
                        </div>
                        <div class="mb-2">
                            <label class="text-muted small">Deskripsi</label>
                            <p class="mb-0">${data.lowongan?.deskripsi || 'Deskripsi tidak tersedia'}</p>
                        </div>
                        <div class="mb-2">
                            <label class="text-muted small">Kapasitas</label>
                            <p class="mb-0">${data.lowongan?.kapasitas || 'Tidak diketahui'} orang</p>
                        </div>
                        <div class="mb-2">
                            <label class="text-muted small">Minimal IPK</label>
                            <p class="mb-0">${data.lowongan?.min_ipk || 'Tidak ditentukan'}</p>
                        </div>
                        ${periodeInfo}
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-4">
                        <h6 class="text-uppercase text-muted mb-3 border-bottom pb-2">
                            <i class="fas fa-building me-2"></i>Detail Perusahaan
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="text-muted small">Nama Perusahaan</label>
                                    <p class="mb-0">${data.perusahaan?.nama_perusahaan || 'Nama perusahaan tidak tersedia'}</p>
                                </div>
                                <div class="mb-2">
                                    <label class="text-muted small">Kota</label>
                                    <p class="mb-0">${data.perusahaan?.kota || 'Kota tidak tersedia'}</p>
                                </div>
                                <div class="mb-2">
                                    <label class="text-muted small">Website</label>
                                    <p class="mb-0">${data.perusahaan?.website || 'Website tidak tersedia'}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="text-muted small">Contact Person</label>
                                    <p class="mb-0">${data.perusahaan?.contact_person || 'Contact person tidak tersedia'}</p>
                                </div>
                                <div class="mb-2">
                                    <label class="text-muted small">Email</label>
                                    <p class="mb-0">${data.perusahaan?.email || 'Email tidak tersedia'}</p>
                                </div>
                                <div class="mb-2">
                                    <label class="text-muted small">Instagram</label>
                                    <p class="mb-0">${data.perusahaan?.instagram || 'Instagram tidak tersedia'}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="text-muted small">Alamat</label>
                            <p class="mb-0">${data.perusahaan?.alamat_perusahaan || 'Alamat perusahaan tidak tersedia'}</p>
                        </div>
                        <div class="mt-2">
                            <label class="text-muted small">Deskripsi</label>
                            <p class="mb-0">${data.perusahaan?.deskripsi || 'Deskripsi perusahaan tidak tersedia'}</p>
                        </div>
                    </div>
                </div>

                ${dosenHTML}

                <div class="row">
                    <div class="col-12 mb-4">
                        <h6 class="text-uppercase text-muted mb-3 border-bottom pb-2">
                            <i class="fas fa-file-alt me-2"></i>Dokumen Lamaran
                        </h6>
                        ${dokumenHTML}
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <label class="text-uppercase text-muted small">Status Lamaran</label>
                            <div>
                                <span class="status-badge ${(data.auth || '').toLowerCase() === 'diterima' ? 'diterima' : (data.auth || '').toLowerCase() === 'ditolak' ? 'ditolak' : 'menunggu'}">
                                    ${data.status || 'Status tidak tersedia'}
                                </span>
                            </div>
                            <small class="text-muted">Tanggal lamaran: ${data.tanggal_lamaran || 'Tanggal tidak tersedia'}</small>
                        </div>
                        <div class="action-buttons">
                            ${(data.auth || '').toLowerCase() === 'menunggu' ? `
                                    <button class="btn btn-sm btn-danger me-2" onclick="rejectRequest(${data.id}); bootstrap.Modal.getInstance(document.getElementById('detailModal')).hide();">
                                        <i class="fas fa-times me-1"></i>Tolak
                                    </button>
                                    <button class="btn btn-sm btn-success" onclick="acceptRequest(${data.id}); bootstrap.Modal.getInstance(document.getElementById('detailModal')).hide();">
                                        <i class="fas fa-check me-1"></i>Terima
                                    </button>
                                ` : (data.auth || '').toLowerCase() === 'ditolak' ? `
                                    <span class="text-muted">
                                        <i class="fas fa-times-circle me-1"></i>Permintaan telah ditolak
                                        ${data.tanggal_ditolak ? ` pada ${data.tanggal_ditolak}` : ''}
                                    </span>
                                ` : `
                                    <span class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>Permintaan telah diterima
                                    </span>
                                `}
                        </div>
                    </div>
                </div>

                ${data.catatan ? `
                        <div class="mt-3 pt-3 border-top">
                            <h6 class="text-uppercase text-muted mb-2">
                                <i class="fas fa-comment me-2"></i>Catatan Penolakan
                            </h6>
                            <div class="alert alert-warning">
                                <i class="fas fa-comment me-2"></i>
                                ${data.catatan}
                            </div>
                        </div>
                    ` : ''}
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
                Gagal memuat detail permintaan magang. 
                <br><small class="text-muted">Error: ${error.message}</small>
                <div class="mt-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="showDetail(${id})">
                        <i class="fas fa-redo me-1"></i>Coba Lagi
                    </button>
                </div>
            </div>
        `;
                });
        }

        function acceptRequest(id) {
            // First, check if this magang has an assigned dosen
            fetch(`/api/magang/${id}/check-dosen`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(response => {
                    if (response.has_dosen) {
                        // Proceed with acceptance - dosen is assigned
                        proceedWithAcceptance(id);
                    } else {
                        // Show warning with plotting link
                        Swal.fire({
                            title: 'Tidak Dapat Menerima!',
                            text: 'Magang tidak dapat diterima karena belum memiliki dosen pembimbing.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ke Plotting Dosen',
                            cancelButtonText: 'Tutup'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Navigate to plotting page
                                window.location.href = '/plotting';
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error!', 'Terjadi kesalahan saat memeriksa data dosen pembimbing.', 'error');
                });
        }

        function proceedWithAcceptance(id) {
            // This contains the original acceptance logic
            Swal.fire({
                title: 'Terima Permintaan Magang?',
                html: `
                    <div class="text-start">
                        <p class="mb-3">Silakan tentukan jadwal magang untuk mahasiswa ini:</p>

                        <div class="mb-3">
                            <label for="tgl_mulai" class="form-label fw-bold">Tanggal Mulai Magang</label>
                            <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai" required>
                            <small class="text-muted">Tanggal mahasiswa mulai magang</small>
                        </div>

                        <div class="mb-3">
                            <label for="tgl_selesai" class="form-label fw-bold">Tanggal Selesai Magang</label>
                            <input type="date" class="form-control" id="tgl_selesai" name="tgl_selesai" required>
                            <small class="text-muted">Tanggal selesai magang (biasanya 3-6 bulan)</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <small>Pastikan tanggal yang dipilih sesuai dengan periode magang yang telah ditentukan.</small>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Terima & Jadwalkan',
                cancelButtonText: 'Batal',
                width: '500px',
                customClass: {
                    htmlContainer: 'text-start'
                },
                preConfirm: () => {
                    const tglMulai = document.getElementById('tgl_mulai').value;
                    const tglSelesai = document.getElementById('tgl_selesai').value;

                    // ✅ VALIDASI: Pastikan kedua tanggal diisi
                    if (!tglMulai || !tglSelesai) {
                        Swal.showValidationMessage('Harap isi kedua tanggal!');
                        return false;
                    }

                    // ✅ VALIDASI: Tanggal selesai harus setelah tanggal mulai
                    if (new Date(tglSelesai) <= new Date(tglMulai)) {
                        Swal.showValidationMessage('Tanggal selesai harus setelah tanggal mulai!');
                        return false;
                    }

                    // ✅ VALIDASI: Minimal durasi magang 1 bulan
                    const diffTime = new Date(tglSelesai) - new Date(tglMulai);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    if (diffDays < 30) {
                        Swal.showValidationMessage('Durasi magang minimal 30 hari!');
                        return false;
                    }

                    // ✅ VALIDASI: Maksimal durasi magang 6 bulan
                    if (diffDays > 180) {
                        Swal.showValidationMessage('Durasi magang maksimal 180 hari (6 bulan)!');
                        return false;
                    }

                    return {
                        tgl_mulai: tglMulai,
                        tgl_selesai: tglSelesai,
                        durasi_hari: diffDays
                    };
                },
                didOpen: () => {
                    // ✅ SET DEFAULT: Tanggal mulai = hari ini + 7 hari
                    const today = new Date();
                    const nextWeek = new Date(today);
                    nextWeek.setDate(today.getDate() + 7);

                    // ✅ SET DEFAULT: Tanggal selesai = 3 bulan setelah tanggal mulai
                    const threeMonthsLater = new Date(nextWeek);
                    threeMonthsLater.setMonth(nextWeek.getMonth() + 3);

                    // Format tanggal ke YYYY-MM-DD
                    const formatDate = (date) => {
                        return date.toISOString().split('T')[0];
                    };

                    document.getElementById('tgl_mulai').value = formatDate(nextWeek);
                    document.getElementById('tgl_selesai').value = formatDate(threeMonthsLater);

                    // ✅ SET MIN DATE: Tidak bisa pilih tanggal yang sudah lewat
                    document.getElementById('tgl_mulai').min = formatDate(today);

                    // ✅ EVENT LISTENER: Update tanggal selesai otomatis ketika tanggal mulai berubah
                    document.getElementById('tgl_mulai').addEventListener('change', function() {
                        const selectedStart = new Date(this.value);
                        const autoEnd = new Date(selectedStart);
                        autoEnd.setMonth(selectedStart.getMonth() + 3);

                        document.getElementById('tgl_selesai').value = formatDate(autoEnd);
                        document.getElementById('tgl_selesai').min = formatDate(new Date(selectedStart
                            .getTime() + 24 * 60 * 60 * 1000)); // Min = start + 1 day
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const {
                        tgl_mulai,
                        tgl_selesai,
                        durasi_hari
                    } = result.value;

                    // ✅ KONFIRMASI AKHIR dengan info durasi
                    Swal.fire({
                        title: 'Konfirmasi Jadwal Magang',
                        html: `
                                <div class="text-start">
                                    <p class="mb-3">Apakah jadwal magang berikut sudah benar?</p>

                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <strong>Tanggal Mulai:</strong><br>
                                                    <span class="text-primary">${new Date(tgl_mulai).toLocaleDateString('id-ID', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}</span>
                                                </div>
                                                <div class="col-6">
                                                    <strong>Tanggal Selesai:</strong><br>
                                                    <span class="text-danger">${new Date(tgl_selesai).toLocaleDateString('id-ID', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}</span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="text-center">
                                                <strong>Total Durasi: ${durasi_hari} hari (${Math.round(durasi_hari / 30)} bulan)</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Proses Sekarang',
                        cancelButtonText: 'Ubah Jadwal',
                        width: '600px'
                    }).then((finalResult) => {
                        if (finalResult.isConfirmed) {
                            // ✅ PROSES: Kirim data ke server dengan tanggal
                            processAcceptanceWithDates(id, tgl_mulai, tgl_selesai);
                        } else if (finalResult.dismiss === Swal.DismissReason.cancel) {
                            // ✅ KEMBALI: Kembali ke form input tanggal
                            proceedWithAcceptance(id);
                        }
                    });
                }
            });
        }

        // ✅ FUNGSI BARU: Proses penerimaan dengan tanggal
        function processAcceptanceWithDates(id, tglMulai, tglSelesai) {
            // Tampilkan loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang memproses permintaan dan menjadwalkan magang...',
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
                    body: JSON.stringify({
                        status: 'aktif',
                        tgl_mulai: tglMulai,
                        tgl_selesai: tglSelesai
                    })
                })
                .then(response => response.json())
                .then(response => {
                    console.log('Respons dari server:', response);
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            html: `
                            <div class="text-start">
                                <p class="mb-3">✅ Permintaan magang telah diterima</p>
                                <p class="mb-3">📅 Jadwal magang telah ditetapkan</p>
                                <p class="mb-0">📨 Notifikasi telah dikirim ke mahasiswa</p>
                            </div>
                        `,
                            icon: 'success',
                            timer: 3000,
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

        // ✅ PERBAIKAN: Function rejectRequest - ubah status tanpa menghapus data
        function rejectRequest(id) {
            // Dialog konfirmasi yang konsisten
            Swal.fire({
                title: 'Tolak Permintaan Magang?',
                html: `
                        <div class="text-start">
                            <p class="mb-3">Apakah Anda yakin ingin menolak permintaan magang ini?</p>

                            <div class="mb-3">
                                <label for="catatan_penolakan" class="form-label fw-bold">Catatan Penolakan (Opsional)</label>
                                <textarea class="form-control" id="catatan_penolakan" name="catatan_penolakan" rows="3" 
                                          placeholder="Berikan alasan penolakan untuk memberikan feedback kepada mahasiswa..."></textarea>
                                <small class="text-muted">Catatan ini akan dikirim kepada mahasiswa sebagai feedback</small>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <small><strong>Catatan:</strong> Status permintaan akan diubah menjadi "Ditolak" dan mahasiswa akan menerima notifikasi penolakan.</small>
                            </div>
                        </div>
                    `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tolak Permintaan',
                cancelButtonText: 'Batal',
                width: '500px',
                customClass: {
                    htmlContainer: 'text-start'
                },
                preConfirm: () => {
                    const catatan = document.getElementById('catatan_penolakan').value.trim();

                    return {
                        catatan_penolakan: catatan || null
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const {
                        catatan_penolakan
                    } = result.value;

                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses Penolakan...',
                        text: 'Sedang mengubah status permintaan menjadi ditolak...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // ✅ PERBAIKAN: Kirim request untuk update status, bukan delete
                    fetch(`/api/magang/${id}/reject`, {
                            method: 'PUT', // ✅ UBAH: Gunakan PUT untuk update, bukan POST
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                auth: 'ditolak', // ✅ UBAH: Set status menjadi ditolak
                                catatan: catatan_penolakan,
                                tanggal_ditolak: new Date().toISOString().split('T')[
                                    0] // ✅ TAMBAH: Tanggal penolakan
                            })
                        })
                        .then(response => response.json())
                        .then(response => {
                            console.log('Respons dari server:', response);
                            if (response.success) {
                                Swal.fire({
                                    title: 'Permintaan Ditolak!',
                                    html: `
                                        <div class="text-start">
                                            <p class="mb-3">✅ Status permintaan telah diubah menjadi "Ditolak"</p>
                                            <p class="mb-3">📨 Notifikasi penolakan telah dikirim ke mahasiswa</p>
                                            ${catatan_penolakan ? `<p class="mb-0">📝 Catatan penolakan: "${catatan_penolakan}"</p>` : ''}
                                        </div>
                                    `,
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: false
                                }).then(() => {
                                    loadPermintaanData(); // Refresh data untuk menampilkan status baru
                                });
                            } else {
                                Swal.fire('Gagal!', response.message ||
                                    'Terjadi kesalahan saat menolak permintaan.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Terjadi kesalahan saat memproses permintaan.', 'error');
                        });
                }
            });
        }

        // ✅ FUNGSI BARU: Mengaktifkan kembali permintaan yang ditolak
        function reactivateRequest(id) {
            Swal.fire({
                title: 'Aktifkan Kembali Permintaan?',
                html: `
                        <div class="text-start">
                            <p class="mb-3">Apakah Anda yakin ingin mengaktifkan kembali permintaan yang telah ditolak ini?</p>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>Status akan diubah dari "Ditolak" menjadi "Menunggu" dan mahasiswa akan menerima notifikasi.</small>
                            </div>
                        </div>
                    `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Aktifkan Kembali',
                cancelButtonText: 'Batal',
                customClass: {
                    htmlContainer: 'text-start'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengaktifkan kembali permintaan...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`/api/magang/${id}/reactivate`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                auth: 'menunggu',
                                catatan: null, // Reset catatan penolakan
                                tanggal_reaktivasi: new Date().toISOString().split('T')[0]
                            })
                        })
                        .then(response => response.json())
                        .then(response => {
                            console.log('Respons dari server:', response);
                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil Diaktifkan!',
                                    html: `
                                    <div class="text-start">
                                        <p class="mb-3">✅ Status permintaan telah diubah menjadi "Menunggu"</p>
                                        <p class="mb-0">📨 Notifikasi telah dikirim ke mahasiswa</p>
                                    </div>
                                `,
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: false
                                }).then(() => {
                                    loadPermintaanData(); // Refresh data
                                });
                            } else {
                                Swal.fire('Gagal!', response.message ||
                                    'Terjadi kesalahan saat mengaktifkan permintaan.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Terjadi kesalahan saat memproses permintaan.', 'error');
                        });
                }
            });
        }
    </script>
@endpush

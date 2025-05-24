@extends('layouts.app')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Data Perusahaan'])
    <div class="container-fluid py-4">
        <div class="search-header mb-4">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="search-filters d-flex gap-3">
                        <div class="search-box">
                            <input type="text" class="form-control" placeholder="Cari Perusahaan" id="searchPerusahaan">
                            <i class="bi bi-search"></i>
                        </div>
                        <button class="filter-btn" id="filterWilayah">
                            <i class="bi bi-geo-alt"></i>
                            <span>Wilayah</span>
                        </button>
                        <button class="filter-btn" id="filterLowongan">
                            <i class="bi bi-briefcase"></i>
                            <span>Lowongan</span>
                        </button>
                    </div>
                    <div class="action-buttons d-flex gap-3">
                        <button type="button" class="btn" style="color: white; background: #02A232;"
                            onclick="tambahPerusahaan()">
                            <i class="bi bi-plus-square-fill me-2"></i>Tambah Perusahaan
                        </button>
                        <button type="button" class="btn" style="color: white; background: #5988FF;" onclick="importCSV()">
                            <i class="bi bi-plus-square-fill me-2"></i>Import CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Perusahaan -->
        <div class="modal fade" id="tambahPerusahaanModal" tabindex="-1" aria-labelledby="tambahPerusahaanModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahPerusahaanModalLabel">Tambah Perusahaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="tambahPerusahaanForm">
                            <div class="mb-3">
                                <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                                <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="alamat_perusahaan" class="form-label">Alamat Perusahaan</label>
                                <input type="text" class="form-control" id="alamat_perusahaan" name="alamat_perusahaan">
                            </div>
                            <div class="mb-3">
                                <label for="kota" class="form-label">Kota</label>
                                <input type="text" class="form-control" id="kota" name="kota">
                            </div>
                            <div class="mb-3">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="instagram" class="form-label">Instagram</label>
                                <input type="text" class="form-control" id="instagram" name="instagram">
                            </div>
                            <div class="mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="text" class="form-control" id="website" name="website">
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="perusahaan-grid">
            <div class="row g-4" id="perusahaanContainer">
                <!-- Data will be loaded here -->
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/data_perusahaan.css') }}">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Load data when page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadPerusahaanData();
        });

        let perusahaanData = []; // Variabel global untuk menyimpan data perusahaan

        function loadPerusahaanData() {
            fetch('/api/perusahaan')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        perusahaanData = data.data; // Simpan data perusahaan ke variabel global
                        updatePerusahaanGrid(perusahaanData); // Tampilkan semua data perusahaan
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function filterPerusahaan() {
            const searchInput = document.getElementById('searchPerusahaan').value.toLowerCase(); // Ambil nilai input pencarian
            const filteredData = perusahaanData.filter(p =>
                p.nama_perusahaan.toLowerCase().includes(searchInput) || // Filter berdasarkan nama perusahaan
                p.kota.toLowerCase().includes(searchInput) // Filter berdasarkan kota
            );
            updatePerusahaanGrid(filteredData); // Perbarui grid dengan data yang difilter
        }

        document.getElementById('searchPerusahaan').addEventListener('input', filterPerusahaan);

        function updatePerusahaanGrid(perusahaan) {
            const grid = document.getElementById('perusahaanContainer');
            if (!perusahaan.length) {
                grid.innerHTML = `
            <div class="col-12">
                <div class="alert alert-info">
                    Belum ada data perusahaan.
                </div>
            </div>
        `;
                return;
            }

            grid.innerHTML = perusahaan.map(p => `
        <div class="col-md-4 mb-4">
            <div class="card company-card" onclick="goToDetail(${p.perusahaan_id})" style="cursor: pointer;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="company-logo me-3">
                            <i class="bi bi-building" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h6 class="company-name">${p.nama_perusahaan}</h6>
                            <div class="company-location">
                                <i class="bi bi-geo-alt"></i>
                                <span>${p.kota}</span>
                            </div>
                        </div>
                    </div>
                    <div class="vacancy-info">
                        <p class="text-muted">Lowongan Terbuka</p>
                        <div class="vacancy-count">
                            <i class="bi bi-briefcase"></i>
                            <span>${p.lowongan_count || 0} Lowongan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
        }

        function tambahPerusahaan() {
            const modal = new bootstrap.Modal(document.getElementById('tambahPerusahaanModal'));
            modal.show();
        }

        document.getElementById('tambahPerusahaanForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('/api/perusahaan', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message
                        }).then(() => {
                            // Tutup modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('tambahPerusahaanModal'));
                            modal.hide();

                            // Reload data perusahaan
                            loadPerusahaanData();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan. Silakan coba lagi.'
                    });
                });
        });

        function importCSV() {
            // Add your import CSV code here
            console.log('Import CSV clicked');
        }

        function goToDetail(id) {
            window.location.href = `/detail-perusahaan/${id}`;
        }
    </script>
@endpush
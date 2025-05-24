@extends('layouts.app')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Permintaan Magang'])
    <div class="card">
        <div class="card-header px-4 py-3">
            <div class="search_card">
                <div class="search-filter d-flex gap-3">
                    <div class="search-box">
                        <input type="text" class="form-control search-input" placeholder="Cari Lowongan">
                        <i class="bi bi-search search-icon"></i>
                    </div>
                    <button class="btn filter-btn">
                        <i class="bi bi-funnel"></i>
                        <span>Periode</span>
                    </button>
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

    @include('components.modals.detail_permintaan')
@endsection

@push('css')
    <link href="{{ asset('assets/css/permintaan.css') }}" rel="stylesheet" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function loadPermintaanData() {
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
                        const permintaanList = document.querySelector('.permintaan-list');
                        if (!permintaanList) {
                            console.error('Element .permintaan-list tidak ditemukan di halaman.');
                            return;
                        }

                        permintaanList.innerHTML = ''; // Kosongkan daftar permintaan

                        response.data.forEach(permintaan => {
                            permintaanList.innerHTML += `
                                                    <div class="permintaan-item">
                                                        <div class="mahasiswa-info">
                                                            <h6 class="nama">${permintaan.mahasiswa.name}</h6>
                                                            <p class="nim">NIM: ${permintaan.mahasiswa.nim}</p>
                                                        </div>

                                                        <div class="posisi">
                                                            <span class="job-title font-weight-bold">${permintaan.judul_lowongan}</span>
                                                        </div>

                                                        <div class="perusahaan">
                                                            <span style="border: 1px solid #5988ff" class="company-badge font-weight-bold">
                                                                ${permintaan.perusahaan.nama_perusahaan}
                                                            </span>
                                                        </div>

                                                        <div class="status">
                                                            <span class="status-badge ${permintaan.auth === 'diterima' ? 'diterima' : permintaan.auth === 'ditolak' ? 'ditolak' : 'menunggu'} font-weight-bold">
                                                                ${permintaan.auth}
                                                            </span>
                                                        </div>

                                                        <div class="action">
                                                            <div class="hover-actions">
                                                                  <button class="btn btn-tolak" onclick="rejectRequest(${permintaan.id})">
                                                                    <i class="bi bi-x"></i>
                                                                    Tolak
                                                                </button>
                                                                <button class="btn btn-terima" onclick="acceptRequest(${permintaan.id})">
                                                                    <i class="bi bi-check2"></i>
                                                                    Terima
                                                                </button>
                                                                <button class="btn btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal" onclick="showDetail(${permintaan.id})">
                                                                    Detail
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                `;
                        });
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

        document.addEventListener('DOMContentLoaded', function () {
            loadPermintaanData();
        });

        function showDetail(id) {
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

                        // Isi modal dengan data detail
                        document.querySelector('#detailModal .modal-title').innerText = `Detail Permintaan - ${data.lowongan.judul_lowongan}`;
                        document.querySelector('#detailModal .modal-body').innerHTML = `
                                                                            <h5>Detail Mahasiswa</h5>
                                                                            <p><strong>Nama:</strong> ${data.mahasiswa.name}</p>
                                                                            <p><strong>NIM:</strong> ${data.mahasiswa.nim}</p>
                                                                            <p><strong>Email:</strong> ${data.mahasiswa.email}</p>
                                                                            <p><strong>Prodi:</strong> ${data.mahasiswa.prodi}</p>
                                                                            <p><strong>Skill:</strong> ${data.mahasiswa.skills.join(', ')}</p>
                                                                            <hr>
                                                                            <h5>Detail Lowongan</h5>
                                                                            <p><strong>Judul:</strong> ${data.lowongan.judul_lowongan}</p>
                                                                            <p><strong>Deskripsi:</strong> ${data.lowongan.deskripsi}</p>
                                                                            <p><strong>Kapasitas:</strong> ${data.lowongan.persyaratan} Kandidat</p>
                                                                            <p><strong>Tanggal Mulai:</strong> ${data.lowongan.tanggal_mulai}</p>
                                                                            <p><strong>Tanggal Selesai:</strong> ${data.lowongan.tanggal_selesai}</p>
                                                                            <hr>
                                                                            <h5>Detail Perusahaan</h5>
                                                                            <p><strong>Nama:</strong> ${data.perusahaan.nama_perusahaan}</p>
                                                                            <p><strong>Alamat:</strong> ${data.perusahaan.alamat_perusahaan}</p>
                                                                            <p><strong>Kota:</strong> ${data.perusahaan.kota}</p>
                                                                            <p><strong>Contact Person:</strong> ${data.perusahaan.contact_person}</p>
                                                                            <p><strong>Email:</strong> ${data.perusahaan.email}</p>
                                                                            <hr>
                                                                            <h5>Dokumen</h5>
                                                                            <p><strong>CV:</strong> <a href="${data.dokumen.cv_url}" target="_blank">Download CV</a></p>
                                                                            <p><strong>Surat Lamaran:</strong> <a href="${data.dokumen.surat_url}" target="_blank">Download Surat Lamaran</a></p>
                                                                            <hr>
                                                                            <h5>Status</h5>
                                                                            <p>${data.status}</p>
                                                                        `;

                        // Tampilkan modal
                        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
                        modal.show();

                        // Reset konten modal saat ditutup
                        const detailModal = document.getElementById('detailModal');
                        detailModal.addEventListener('hidden.bs.modal', function () {
                            document.querySelector('#detailModal .modal-body').innerHTML = '';
                        });
                    } else {
                        alert('Gagal memuat detail permintaan magang: ' + response.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat detail permintaan magang');
                });
        }

        function acceptRequest(id) {
            console.log(`Tombol Terima diklik untuk ID: ${id}`); // Debugging
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Permintaan ini akan diterima!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Terima!'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log(`Mengirim permintaan untuk menerima ID: ${id}`); // Debugging
                    fetch(`/api/magang/${id}/accept`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Kirim token CSRF
                        },
                        body: JSON.stringify({ status: 'aktif' }) // Kirim status diterima
                    })
                        .then(response => response.json())
                        .then(response => {
                            console.log('Respons dari server:', response); // Debugging
                            if (response.success) {
                                Swal.fire(
                                    'Diterima!',
                                    'Permintaan magang telah diterima.',
                                    'success'
                                );

                                // Muat ulang data permintaan
                                loadPermintaanData();
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    response.message || 'Terjadi kesalahan saat menerima permintaan.',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat memproses permintaan.',
                                'error'
                            );
                        });
                }
            });
        }

        function rejectRequest(id) {
            console.log(`Tombol Tolak diklik untuk ID: ${id}`); // Debugging
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Permintaan ini akan ditolak dan semua data terkait akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Tolak!'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log(`Mengirim permintaan untuk menolak ID: ${id}`); // Debugging
                    fetch(`/api/magang/${id}/reject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Kirim token CSRF
                        }
                    })
                        .then(response => response.json())
                        .then(response => {
                            console.log('Respons dari server:', response); // Debugging
                            if (response.success) {
                                Swal.fire(
                                    'Ditolak!',
                                    'Permintaan magang dan data terkait telah dihapus.',
                                    'success'
                                );

                                // Muat ulang data permintaan
                                loadPermintaanData();
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    response.message || 'Terjadi kesalahan saat menolak permintaan.',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat memproses permintaan.',
                                'error'
                            );
                        });
                }
            });
        }
    </script>
@endpush
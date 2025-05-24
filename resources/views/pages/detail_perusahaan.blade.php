@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Detail Perusahaan'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-body">
                <div id="perusahaanDetail">
                    <!-- Data perusahaan akan dimuat di sini -->
                </div>
                <hr>
                <h5>Lowongan Terkait</h5>
                <div id="lowonganList" class="row">
                    <!-- Daftar lowongan akan dimuat di sini -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="{{ asset('assets/css/data_perusahaan.css') }}" rel="stylesheet" />
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const perusahaanId = {{ $id }}; // Pastikan ID perusahaan dikirim dari controller
            console.log('ID Perusahaan:', perusahaanId); // Debugging
            loadDetailPerusahaan(perusahaanId);
        });

        function loadDetailPerusahaan(id) {
            fetch(`/api/perusahaan/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const perusahaan = data.data;

                        // Tampilkan detail perusahaan
                        document.getElementById('perusahaanDetail').innerHTML = `
                        <h4>${perusahaan.nama_perusahaan}</h4>
                        <p><strong>Lokasi:</strong> ${perusahaan.kota}</p>
                        <p><strong>Deskripsi:</strong> ${perusahaan.alamat_perusahaan}</p>
                        <p><strong>Website:</strong> <a href="${perusahaan.website}" target="_blank">${perusahaan.website}</a></p>
                        <p><strong>Telepon:</strong> ${perusahaan.contact_person}</p>
                        <p><strong>Email:</strong> ${perusahaan.email}</p>
                        <p><strong>Website:</strong> ${perusahaan.website}</p>
                        <p><strong>Instagram:</strong> ${perusahaan.instagram}</p>
                    `;

                        // Tampilkan daftar lowongan
                        const lowonganList = perusahaan.lowongan.map(l => `
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6>${l.judul_lowongan}</h6>
                                    <p class="text-muted">${l.deskripsi || 'Tidak ada deskripsi.'}</p>
                                    <a href="/lowongan/${l.lowongan_id}" class="btn btn-primary btn-sm">Detail</a>
                                </div>
                            </div>
                        </div>
                    `).join('');
                        document.getElementById('lowonganList').innerHTML = lowonganList;
                    } else {
                        document.getElementById('perusahaanDetail').innerHTML = `
                        <div class="alert alert-danger">${data.message || 'Perusahaan tidak ditemukan.'}</div>
                    `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('perusahaanDetail').innerHTML = `
                    <div class="alert alert-danger">Terjadi kesalahan saat memuat data.</div>
                `;
                });
        }
    </script>
@endpush
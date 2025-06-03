@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen Minat'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Daftar Minat</h6>
                                <p class="text-sm text-secondary mb-0">
                                    Manajemen data minat untuk program magang
                                </p>
                            </div>
                            <button class="btn btn-sm btn-success" id="btnAddMinat">
                                <i class="fas fa-plus me-2"></i>Tambah Minat
                            </button>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="minatTable">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No.
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama
                                            Minat</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Deskripsi</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Add/Edit Minat -->
        <div class="modal fade" id="minatModal" tabindex="-1" aria-labelledby="minatModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="minatModalLabel">Tambah Minat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="minatForm">
                            <input type="hidden" id="minatId">
                            <div class="mb-3">
                                <label for="namaMinat" class="form-label">Nama Minat</label>
                                <input type="text" class="form-control" id="namaMinat" required>
                                <div class="invalid-feedback" id="namaMinatFeedback"></div>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsiMinat" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsiMinat" rows="3"></textarea>
                                <div class="invalid-feedback" id="deskripsiMinatFeedback"></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btnSaveMinat">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus minat ini?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" id="btnConfirmDelete">Hapus</button>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth.footer')
    </div>
@endsection

@push('css')
    <link href="{{ asset('assets/css/data-mahasiswa.css') }}" rel="stylesheet" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global variables
        let minatToDeleteId = null;

        // Initialize when document is ready
        $(document).ready(function () {
            // Load minat data
            loadMinatData();

            // Add minat button click
            $('#btnAddMinat').click(function () {
                resetForm();
                $('#minatModalLabel').text('Tambah Minat');
                $('#minatModal').modal('show');
            });

            // Save minat button click
            $('#btnSaveMinat').click(function () {
                saveMinat();
            });

            // Confirm delete button click
            $('#btnConfirmDelete').click(function () {
                if (minatToDeleteId) {
                    deleteMinat(minatToDeleteId);
                }
            });
        });

        // Load minat data from API
        function loadMinatData() {
            $.ajax({
                url: '/api/minat',
                type: 'GET',
                success: function (response) {
                    populateMinatTable(response);
                },
                error: function (error) {
                    console.error('Error loading minat data:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat data minat'
                    });
                }
            });
        }

        // Populate minat table with data
        function populateMinatTable(data) {
            const tbody = $('#minatTable tbody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="4" class="text-center py-4">Tidak ada data minat</td>
                    </tr>
                `);
                return;
            }

            data.forEach((item, index) => {
                tbody.append(`
                    <tr>
                        <td class="ps-4">
                            <span class="text-secondary text-xs font-weight-bold">${index + 1}</span>
                        </td>
                        <td class="ps-4">
                            <span class="text-secondary text-xs font-weight-bold">${item.nama_minat}</span>
                        </td>
                        <td class="ps-2">
                            <span class="text-secondary text-xs font-weight-bold">${item.deskripsi || '-'}</span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-info btn-sm mb-0 btn-edit" data-id="${item.minat_id}">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            <button type="button" class="btn btn-danger btn-sm mb-0 btn-delete" data-id="${item.minat_id}">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </td>
                    </tr>
                `);
            });

            // Add event listeners
            $('.btn-edit').click(function () {
                const id = $(this).data('id');
                editMinat(id);
            });

            $('.btn-delete').click(function () {
                const id = $(this).data('id');
                minatToDeleteId = id;
                $('#deleteModal').modal('show');
            });
        }

        // Reset form
        function resetForm() {
            $('#minatId').val('');
            $('#namaMinat').val('');
            $('#deskripsiMinat').val('');
            $('#namaMinat').removeClass('is-invalid');
            $('#deskripsiMinat').removeClass('is-invalid');
        }

        // Edit minat
        function editMinat(id) {
            $.ajax({
                url: `/api/minat/${id}`,
                type: 'GET',
                success: function (response) {
                    $('#minatId').val(response.minat_id);
                    $('#namaMinat').val(response.nama_minat);
                    $('#deskripsiMinat').val(response.deskripsi);
                    $('#minatModalLabel').text('Edit Minat');
                    $('#minatModal').modal('show');
                },
                error: function (error) {
                    console.error('Error fetching minat details:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat detail minat'
                    });
                }
            });
        }

        // Save minat (create or update)
        function saveMinat() {
            const id = $('#minatId').val();
            const namaMinat = $('#namaMinat').val();
            const deskripsiMinat = $('#deskripsiMinat').val();

            // Reset validation
            $('#namaMinat').removeClass('is-invalid');
            $('#deskripsiMinat').removeClass('is-invalid');

            const data = {
                nama_minat: namaMinat,
                deskripsi: deskripsiMinat
            };

            const url = id ? `/api/minat/${id}` : '/api/minat';
            const method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                contentType: 'application/json',
                data: JSON.stringify(data),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#minatModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadMinatData();
                },
                error: function (error) {
                    console.error('Error saving minat:', error);
                    if (error.responseJSON && error.responseJSON.errors) {
                        const errors = error.responseJSON.errors;
                        if (errors.nama_minat) {
                            $('#namaMinat').addClass('is-invalid');
                            $('#namaMinatFeedback').text(errors.nama_minat[0]);
                        }
                        if (errors.deskripsi) {
                            $('#deskripsiMinat').addClass('is-invalid');
                            $('#deskripsiMinatFeedback').text(errors.deskripsi[0]);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menyimpan data'
                        });
                    }
                }
            });
        }

        // Delete minat
        function deleteMinat(id) {
            $.ajax({
                url: `/api/minat/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#deleteModal').modal('hide');
                    minatToDeleteId = null;
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadMinatData();
                },
                error: function (error) {
                    console.error('Error deleting minat:', error);
                    $('#deleteModal').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal menghapus minat'
                    });
                }
            });
        }
    </script>
@endpush
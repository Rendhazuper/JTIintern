@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Evaluasi Mahasiswa'])

    <div class="container-fluid py-4 ">
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Evaluasi</p>
                                    <h5 class="font-weight-bolder" id="total-evaluasi">
                                        <div class="placeholder-glow">
                                            <span class="placeholder col-4"></span>
                                        </div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="fas fa-clipboard-check text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Rata-Rata Nilai</p>
                                    <h5 class="font-weight-bolder" id="avg-nilai">
                                        <div class="placeholder-glow">
                                            <span class="placeholder col-4"></span>
                                        </div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="fas fa-chart-bar text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Mahasiswa</p>
                                    <h5 class="font-weight-bolder" id="total-mahasiswa">
                                        <div class="placeholder-glow">
                                            <span class="placeholder col-4"></span>
                                        </div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                    <i class="fas fa-user-tie text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Perusahaan</p>
                                    <h5 class="font-weight-bolder" id="total-perusahaan">
                                        <div class="placeholder-glow">
                                            <span class="placeholder col-4"></span>
                                        </div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="fas fa-building text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- form Card -->
        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6 class="mb-0"><i class="fas fa-edit me-2"></i>Form Evaluasi Mahasiswa</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('dosen.evaluasi') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Mahasiswa</label>
                            <select class="form-select" name="mahasiswa_id" required>
                                <option value="">Pilih Mahasiswa</option>
                                <!-- Add dynamic options here -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Perusahaan</label>
                            <select class="form-select" name="perusahaan_id" required>
                                <option value="">Pilih Perusahaan</option>
                                <!-- Add dynamic options here -->
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nilai Kinerja (0-100)</label>
                            <input type="number" class="form-control" name="nilai" min="0" max="100"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Evaluasi</label>
                            <input type="date" class="form-control" name="tanggal_evaluasi" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Komentar</label>
                        <textarea class="form-control" name="komentar" rows="4" required
                            placeholder="Masukkan komentar evaluasi untuk mahasiswa..."></textarea>
                    </div>

                    <div class="text-end">
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn bg-gradient-primary">Simpan Evaluasi</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Riwayat Evaluasi -->
        <div class="card">
            <div class="card-header pb-0">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Evaluasi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Mahasiswa</th>
                                <th>Perusahaan</th>
                                <th>Nilai</th>
                                <th>Komentar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Add dynamic table rows here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // Add any JavaScript functionality here
    </script>
@endpush

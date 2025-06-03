@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Dashboard'])

    <div class="container-fluid py-4">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Mahasiswa</p>
                                    <h5 class="font-weight-bolder" id="total-mahasiswa">
                                        <div class="spinner-border spinner-border-sm" role="status"></div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="fas fa-users text-lg opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Sedang Magang</p>
                                    <h5 class="font-weight-bolder" id="mahasiswa-magang">
                                        <div class="spinner-border spinner-border-sm" role="status"></div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="fas fa-briefcase text-lg opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Evaluasi Terakhir</p>
                                    <h5 class="font-weight-bolder" id="recent-evaluations">
                                        <div class="spinner-border spinner-border-sm" role="status"></div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="fas fa-clipboard-check text-lg opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Rata-rata Nilai</p>
                                    <h5 class="font-weight-bolder" id="average-score">
                                        <div class="spinner-border spinner-border-sm" role="status"></div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                    <i class="fas fa-chart-line text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <!-- Recent Evaluations -->
            <div class="col-lg-7 mb-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0">Evaluasi Terbaru</h6>
                            <a href="{{ route('dosen.evaluasi') }}" class="btn btn-link text-sm mb-0 px-0 ms-sm-auto">Lihat Semua</a>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mahasiswa</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nilai</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody id="recent-evaluations-table">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Status -->
            <div class="col-lg-5 mb-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6 class="mb-0">Status Mahasiswa</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <canvas id="student-status-chart" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadDashboardStats();
        loadRecentEvaluations();
        loadStudentStatusChart();
    });

    function loadDashboardStats() {
        axios.get('/api/dashboard/stats')
            .then(response => {
                if(response.data.success) {
                    const stats = response.data.data;
                    document.getElementById('total-mahasiswa').innerHTML = stats.total_mahasiswa;
                    document.getElementById('mahasiswa-magang').innerHTML = stats.sedang_magang;
                    document.getElementById('recent-evaluations').innerHTML = stats.total_evaluasi;
                    document.getElementById('average-score').innerHTML = stats.rata_rata_nilai;
                }
            })
            .catch(error => {
                console.error('Error loading dashboard stats:', error);
            });
    }

    function loadRecentEvaluations() {
        axios.get('/api/dashboard/recent-evaluations')
            .then(response => {
                if(response.data.success) {
                    const tableBody = document.getElementById('recent-evaluations-table');
                    tableBody.innerHTML = '';

                    response.data.data.forEach(eval => {
                        const row = `
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">${eval.mahasiswa_name}</h6>
                                            <p class="text-xs text-secondary mb-0">${eval.nim}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-gradient-${eval.nilai >= 70 ? 'success' : 'warning'}">${eval.nilai}</span>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">${eval.tanggal_evaluasi}</p>
                                </td>
                                <td class="align-middle">
                                    <a href="/dosen/evaluasi/${eval.id_evaluasi}" class="text-secondary font-weight-bold text-xs">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                }
            })
            .catch(error => {
                console.error('Error loading recent evaluations:', error);
            });
    }

    function loadStudentStatusChart() {
        axios.get('/api/dashboard/student-status')
            .then(response => {
                if(response.data.success) {
                    const data = response.data.data;
                    const ctx = document.getElementById('student-status-chart').getContext('2d');
                    
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Belum Magang', 'Sedang Magang', 'Selesai Magang'],
                            datasets: [{
                                data: [
                                    data.belum_magang,
                                    data.sedang_magang,
                                    data.selesai_magang
                                ],
                                backgroundColor: [
                                    '#f5365c',
                                    '#2dce89',
                                    '#11cdef'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error loading student status chart:', error);
            });
    }
</script>
@endpush
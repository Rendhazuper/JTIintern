@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Dashboard'])
    {{-- filepath: c:\laragon\www\JTIintern\resources\views\pages\dashboard.blade.php --}}
{{-- ...existing code... --}}
<div class="container-fluid py-4" style="background: #F3F3FF; border-radius: 10px;">
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="fw-bold mb-2" style="color: #2D2D2D;">Mahasiswa Aktif Magang</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold" style="color: #5988FF; font-size: 48px;">56</span>
                        <span class="d-flex align-items-center justify-content-center rounded" style="width:68px;height:68px;background:rgba(182,203,255,0.4);">
                            <i class="fas fa-user-graduate" style="color:#5988FF; font-size:45px;"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="fw-bold mb-2" style="color: #2D2D2D;">Perusahaan Mitra</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold" style="color: #5988FF; font-size: 48px;">56</span>
                        <span class="d-flex align-items-center justify-content-center rounded" style="width:64px;height:64px;background:#FECDCD;">
                            <i class="fas fa-suitcase" style="color:#FF5252; font-size:42px;"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="fw-bold mb-2" style="color: #2D2D2D;">Lowongan Magang Aktif</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold" style="color: #5988FF; font-size: 48px;">56</span>
                        <span class="d-flex align-items-center justify-content-center rounded" style="width:64px;height:64px;background:#FFE8BE;">
                            <i class="fas fa-laptop-code" style="color:#F8A100; font-size:42px;"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold" style="color: #2D2D2D;">Permintaan Magang Terbaru</span>
                        <a href="#" class="fw-semibold" style="color: #4278FF;">Semua Permintaan</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Perusahaan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="fw-bold" style="color: #2D2D2D;">Rizky Ananda Putra</div>
                                        <div class="text-muted small fw-bold">NIM : 2341720010</div>
                                    </td>
                                    <td>PT. Bagus Sejahtera</td>
                                    <td>
                                        <span class="status-badge accepted"><span class="dot"></span>Diterima</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-bold" style="color: #2D2D2D;">Rizky Ananda Putra</div>
                                        <div class="text-muted small fw-bold">NIM : 2341720010</div>
                                    </td>
                                    <td>PT. Indah Sejahtera</td>
                                    <td>
                                        <span class="status-badge pending"><span class="dot"></span>Menunggu</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-bold" style="color: #2D2D2D;">Rizky Ananda Putra</div>
                                        <div class="text-muted small fw-bold">NIM : 2341720010</div>
                                    </td>
                                    <td>PT. Keren Sejahtera</td>
                                    <td>
                                        <span class="status-badge pending"><span class="dot"></span>Menunggu</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-bold" style="color: #2D2D2D;">Rizky Ananda Putra</div>
                                        <div class="text-muted small fw-bold">NIM : 2341720010</div>
                                    </td>
                                    <td>PT. Apik Sejahtera</td>
                                    <td>
                                        <span class="status-badge pending"><span class="dot"></span>Menunggu</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-bold" style="color: #2D2D2D;">Rizky Ananda Putra</div>
                                        <div class="text-muted small fw-bold">NIM : 2341720010</div>
                                    </td>
                                    <td>PT. Good Sejahtera</td>
                                    <td>
                                        <span class="status-badge accepted"><span class="dot"></span>Diterima</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100" style="background: linear-gradient(158deg, rgba(187,206,255,0.58) 0%, rgba(246,230,247,0.62) 100%);">
                <div class="card-body">
                    <div class="fw-bold mb-3" style="color: #2D2D2D;">Menu Cepat</div>
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center gap-2 rounded mb-2">
                            <i class="fas fa-graduation-cap" style="color:#FFAE00;"></i>
                            <span class="fw-semibold" style="color: #2D2D2D;">Data Mahasiswa</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center gap-2 rounded mb-2">
                            <i class="fas fa-city" style="color:#2F78FF;"></i>
                            <span class="fw-semibold" style="color: #2D2D2D;">Data Perusahaan</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center gap-2 rounded mb-2">
                            <i class="fas fa-user-tie" style="color:#E091FF;"></i>
                            <span class="fw-semibold" style="color: #2D2D2D;">Data Dosen</span>
                        </a>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100 quick-menu-card">
                <div class="card-body">
                <div class="fw-bold mb-3 text-dark">Menu Cepat</div>
                <div class="list-group d-grid gap-2">
                    <a href="#" class="quick-menu-item">
                    <i class="fas fa-graduation-cap icon" style="color:#FFAE00;"></i>
                    <span>Data Mahasiswa</span>
                    </a>
                    <a href="#" class="quick-menu-item">
                    <i class="fas fa-city icon" style="color:#2F78FF;"></i>
                    <span>Data Perusahaan</span>
                    </a>
                    <a href="#" class="quick-menu-item">
                    <i class="fas fa-user-tie icon" style="color:#E091FF;"></i>
                    <span>Data Dosen</span>
                    </a>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- ...existing code... --}}
        @include('layouts.footers.auth.footer')
@endsection

@push('js')
    <script src="./assets/js/plugins/chartjs.min.js"></script>
    <script>
        var ctx1 = document.getElementById("chart-line").getContext("2d");

        var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

        gradientStroke1.addColorStop(1, 'rgba(251, 99, 64, 0.2)');
        gradientStroke1.addColorStop(0.2, 'rgba(251, 99, 64, 0.0)');
        gradientStroke1.addColorStop(0, 'rgba(251, 99, 64, 0)');
        new Chart(ctx1, {
            type: "line",
            data: {
                labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Mobile apps",
                    tension: 0.4,
                    borderWidth: 0,
                    pointRadius: 0,
                    borderColor: "#fb6340",
                    backgroundColor: gradientStroke1,
                    borderWidth: 3,
                    fill: true,
                    data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
                    maxBarThickness: 6

                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#fbfbfb',
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            color: '#ccc',
                            padding: 20,
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        });
    </script>
@endpush

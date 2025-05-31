<!-- Navbar -->
<nav class="navbar navbar-expand-lg mt-3">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="/img/Jti_polinema.png" alt="Logo" style="height: 32px;">
                <span class="ms-2" style="color: #2D2D2D; font-size: 20px; font-weight: 600;">JTIintern</span>
            </a>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item px-2">
                    <a href="#" class="nav-link active fw-medium">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item px-2">
                    <a href="#" class="nav-link fw-medium">
                        Daftar Lowongan
                    </a>
                </li>
                <li class="nav-item px-2">
                    <a href="#" class="nav-link fw-medium">
                        Lamaran Saya
                    </a>
                </li>
                <li class="nav-item px-2">
                    <a href="#" class="nav-link fw-medium">
                        Log Aktivitas
                    </a>
                </li>
                <li class="nav-item px-2">
                    <a href="#" class="nav-link fw-medium">
                        Evaluasi
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <div class="position-relative">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        2
                    </span>
                </div>

                <div class="dropdown">
                    <button class="btn dropdown-toggle p-0" type="button" data-bs-toggle="dropdown">
                        <span class="me-2 fw-medium">R</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person me-2"></i>Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

@push('css')
<link href="{{ asset('assets/css/topnav.css') }}" rel="stylesheet" />
@endpush

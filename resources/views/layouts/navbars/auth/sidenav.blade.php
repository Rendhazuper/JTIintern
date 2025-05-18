<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 "
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="bi bi-x-lg p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ route('home') }}"
            target="_blank">
           <img src="/img/Jti_polinema.png"alt="Logo" />
            <span class="fw-bold" style="color:#2D2D2D; font-size:24px; font-family:'Poppins', sans-serif;">JTIintern</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'home' ? 'active' : '' }}" href="{{ route('home') }}">
                <div
                class="icon icon-shape icon-sm border-radius-md  me-2 d-flex align-items-center justify-content-center">
                <i class="bi bi-grid-fill text-sm opacity-10"></i>
                </div>
                <span class="nav-link-text ms-1">Dashboard</span>
            </a>
            </li>
            <li class="nav-item">
            <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Mahasiswa</h6>
            <a class="nav-link {{ Route::currentRouteName() == 'profile' ? 'active' : '' }}" href="{{ route('profile') }}">
                <div
                class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="bi bi-mortarboard text-sm opacity-10 "></i>
                </div>
                <span class="nav-link-text ms-1">Data Mahasiswa</span>
            </a>

            <a class="nav-link {{ str_contains(request()->url(), 'user-management') == true ? 'active' : '' }}" href="{{ route('page', ['page' => 'user-management']) }}">
                <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="bi bi-envelope text-sm opacity-10 "></i>
                </div>
                <span class="nav-link-text ms-1">Permintaan Magang</span>
            </a>
            </li>
            <li class="nav-item mt-3">
            <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Perusahaan</h6>
            </li>
            <li class="nav-item">
            <a class="nav-link {{ str_contains(request()->url(), 'tables') == true ? 'active' : '' }}" href="{{ route('page', ['page' => 'tables']) }}">
                <div
                class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="bi bi-building text-sm opacity-10 "></i>
                </div>
                <span class="nav-link-text ms-1">Data Perusahaan</span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link {{  str_contains(request()->url(), 'billing') == true ? 'active' : '' }}" href="{{ route('page', ['page' => 'billing']) }}">
                <div
                class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="bi bi-briefcase text-sm opacity-10 "></i>
                </div>
                <span class="nav-link-text ms-1">Manajemen Lowongan</span>
            </a>
            </li>
            <li class="nav-item mt-3">
            <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account pages</h6>
            </li>
            <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'profile-static' ? 'active' : '' }}" href="{{ route('profile-static') }}">
                <div
                class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="bi bi-file-person text-sm opacity-10 text-purple"></i>
                </div>
                <span class="nav-link-text ms-1">Data Dosen</span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link {{  str_contains(request()->url(), 'billing') == true ? 'active' : '' }}" href="{{ route('page', ['page' => 'billing']) }}">
                <div
                class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                <i class="bi bi-file-bar-graph text-sm opacity-10 text-purple"></i>
                </div>
                <span class="nav-link-text ms-1">Evaluasi Magang</span>
            </a>
            </li>
        </ul>
    </div>
        <div class="d-flex justify-content-center my-3">
            <a class="btn btn-danger btn-sm w-90"
                href="https://www.creative-tim.com/product/argon-dashboard-pro-laravel" target="_blank" type="button">Log out</a>
        </div>
    </div>
</aside>

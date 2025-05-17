@extends('layouts.app')

@section('content')
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                <!-- Navbar -->
                <nav
                    class="navbar navbar-expand-lg blur border-radius-lg top-0 z-index-3 shadow position-absolute mt-4 py-2 start-0 end-0 mx-4">
                    <div class="container-fluid">
                        <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 " href="{{ route('home') }}">
                            JTIintern Dashboard
                        </a>
                        <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon mt-2">
                                <span class="navbar-toggler-bar bar1"></span>
                                <span class="navbar-toggler-bar bar2"></span>
                                <span class="navbar-toggler-bar bar3"></span>
                            </span>
                        </button>
                        <div class="collapse navbar-collapse" id="navigation">
                            <ul class="navbar-nav mx-auto">
                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center me-2 active" aria-current="page"
                                        href="{{ route('home') }}">
                                        <i class="fa fa-chart-pie opacity-6 text-dark me-1"></i>
                                        Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link me-2" href="{{ route('profile-static') }}">
                                        <i class="fa fa-user opacity-6 text-dark me-1"></i>
                                        Profile
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link me-2" href="{{ route('sign-up-static') }}">
                                        <i class="fas fa-user-circle opacity-6 text-dark me-1"></i>
                                        Sign Up
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link me-2" href="{{ route('sign-in-static') }}">
                                        <i class="fas fa-key opacity-6 text-dark me-1"></i>
                                        Sign In
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                <!-- End Navbar -->
            </div>
        </div>
    </div>
    <main class="main-content mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                            <div class="card card-plain">
                                <div class="card-header pb-0 text-start">
                                    <h4 class="font-weight-bolder">Sign In</h4>
                                    <p class="mb-0">Enter your email and password to sign in</p>
                                </div>
                                <div class="card-body">
                                    <!-- Alert for displaying messages -->
                                    <div id="login-alert" class="alert alert-danger d-none">
                                        <span id="login-message"></span>
                                    </div>

                                    <form role="form" id="login-form">
                                        @csrf
                                        <div class="mb-3">
                                            <input type="email" id="email" name="email" class="form-control form-control-lg" 
                                                placeholder="Email" aria-label="Email">
                                        </div>
                                        <div class="mb-3">
                                            <input type="password" id="password" name="password" class="form-control form-control-lg" 
                                                placeholder="Password" aria-label="Password">
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                                            <label class="form-check-label" for="rememberMe">Remember me</label>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" id="login-button" class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0">
                                                <span id="login-button-text">Sign in</span>
                                                <span id="login-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                    <p class="mb-4 text-sm mx-auto">
                                        Don't have an account?
                                        <a href="{{ route('sign-up-static') }}" class="text-primary text-gradient font-weight-bold">
                                            Sign up
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                            <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden"
                                style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signin-ill.jpg');
                                background-size: cover;">
                                <span class="mask bg-gradient-primary opacity-6"></span>
                                <h4 class="mt-5 text-white font-weight-bolder position-relative">JTIintern - Sistem Magang JTI</h4>
                                <p class="text-white position-relative">Platform manajemen magang untuk jurusan Teknologi Informasi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('login-form');
        const loginAlert = document.getElementById('login-alert');
        const loginMessage = document.getElementById('login-message');
        const loginButton = document.getElementById('login-button');
        const loginButtonText = document.getElementById('login-button-text');
        const loginSpinner = document.getElementById('login-spinner');

        // Function to show error message
        function showError(message) {
            loginAlert.classList.remove('d-none');
            loginMessage.textContent = message;
        }

        // Function to hide error message
        function hideError() {
            loginAlert.classList.add('d-none');
        }

        // Function to start loading state
        function startLoading() {
            loginButton.disabled = true;
            loginButtonText.textContent = 'Signing in...';
            loginSpinner.classList.remove('d-none');
        }

        // Function to end loading state
        function endLoading() {
            loginButton.disabled = false;
            loginButtonText.textContent = 'Sign in';
            loginSpinner.classList.add('d-none');
        }

        // Handle form submission
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            hideError();
            startLoading();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Validate input
            if (!email || !password) {
                showError('Please enter both email and password.');
                endLoading();
                return;
            }

            // Create API client
            const api = axios.create({
                baseURL: '/api',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            // Send login request
            api.post('/login', {
                email: email,
                password: password
            })
            .then(function(response) {
                // Handle successful login
                if (response.data.success) {
                    window.location.href = response.data.redirect_url;
                } else {
                    showError(response.data.message);
                }
            })
            .catch(function(error) {
                // Handle error
                if (error.response && error.response.status === 422) {
                    showError('Invalid email or password.');
                } else {
                    showError('An error occurred. Please try again later.');
                }
            })
            .finally(function() {
                endLoading();
            });
        });
    });
</script>
@endpush
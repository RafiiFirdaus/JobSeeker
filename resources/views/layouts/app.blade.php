<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Job Seekers Platform')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Job Seekers Platform</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav ms-auto">
                @if(Session::has('user_logged_in'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="validationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Data Validation
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="validationDropdown">
                            <li><a class="dropdown-item" href="{{ route('data-validation.create') }}">Request Validation</a></li>
                            <li><a class="dropdown-item" href="{{ route('data-validation.progress') }}">View Progress</a></li>
                            <li><a class="dropdown-item" href="{{ route('data-validation.results') }}">View Results</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('job-vacancies.index') }}">Job Vacancies</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('job-applications.index') }}">My Applications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">{{ Session::get('user_name', 'User') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="logoutLink">Logout</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<main>
    @hasSection('header')
        <!-- S: Header -->
        <header class="jumbotron">
            <div class="container">
                @yield('header')
            </div>
        </header>
        <!-- E: Header -->
    @endif

    <div class="container">
        <!-- Alert Container for AJAX messages -->
        <div class="alert-container"></div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<!-- S: Footer -->
<footer>
    <div class="container">
        <div class="text-center py-4 text-muted">
            Copyright &copy; {{ date('Y') }} - Job Seekers Platform
        </div>
    </div>
</footer>
<!-- E: Footer -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/jobseeker-api.js') }}"></script>
<script>
// Handle logout
$(document).ready(function() {
    $('#logoutLink').on('click', async function(e) {
        e.preventDefault();

        try {
            await jobSeekerAPI.logout();
            jobSeekerAPI.showSuccess('Logged out successfully! Redirecting...');

            // Clear session storage
            sessionStorage.clear();

            setTimeout(() => {
                window.location.href = '/login';
            }, 1500);
        } catch (error) {
            console.error('Logout error:', error);
            // Even if API fails, clear local storage and redirect
            jobSeekerAPI.clearToken();
            sessionStorage.clear();
            window.location.href = '/login';
        }
    });
});
</script>
@stack('scripts')
</body>
</html>

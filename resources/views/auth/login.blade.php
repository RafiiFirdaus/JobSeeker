@extends('layouts.app')

@section('title', 'Login - Job Seekers Platform')

@section('header')
    <h1 class="display-4">Job Seekers Platform</h1>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <form id="loginForm" class="card card-default ajax-form">
            <div class="card-header">
                <h4 class="mb-0">Login</h4>
            </div>
            <div class="card-body">
                <div class="form-group row align-items-center mb-3">
                    <div class="col-4 text-end">ID Card Number</div>
                    <div class="col-8">
                        <input type="text" class="form-control" name="nik" required
                               placeholder="Enter your 16-digit NIK">
                    </div>
                </div>
                <div class="form-group row align-items-center mb-3">
                    <div class="col-4 text-end">Password</div>
                    <div class="col-8">
                        <input type="password" class="form-control" name="password" required
                               placeholder="Enter your password">
                    </div>
                </div>
                <div class="form-group row align-items-center mt-4">
                    <div class="col-4"></div>
                    <div class="col-8">
                        <button type="submit" class="btn btn-primary" id="loginBtn">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                        <a href="{{ route('register') }}" class="btn btn-link">Don't have an account?</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#loginForm').on('submit', async function(e) {
        e.preventDefault();

        const $form = $(this);
        const $submitBtn = $('#loginBtn');

        // Get form data
        const nik = $form.find('[name="nik"]').val();
        const password = $form.find('[name="password"]').val();

        // Validate required fields
        if (!nik || !password) {
            jobSeekerAPI.showError('Please fill in all required fields.');
            return;
        }

        try {
            // Set loading state
            jobSeekerAPI.setButtonLoading($submitBtn, true);

            // Call API
            const response = await jobSeekerAPI.login({
                nik: nik,
                password: password
            });

            // Handle different response formats
            if (response.token || response.success || response.redirect) {
                jobSeekerAPI.showSuccess('Login successful! Redirecting...');

                // Store user info in session storage for compatibility
                sessionStorage.setItem('user_logged_in', 'true');
                sessionStorage.setItem('user_name', response.user?.name || 'User');
                sessionStorage.setItem('society_id', response.user?.id);

                // Redirect to dashboard after short delay
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1500);
            } else {
                throw new Error('Invalid response from server');
            }

        } catch (error) {
            console.error('Login error:', error);

            if (error.errors) {
                jobSeekerAPI.showFormErrors(error.errors, $form);
            }

            const message = error.message || 'Login failed. Please check your credentials.';
            jobSeekerAPI.showError(message);

        } finally {
            jobSeekerAPI.setButtonLoading($submitBtn, false);
        }
    });

    // Clear any existing tokens on login page
    if (window.location.pathname === '/login') {
        jobSeekerAPI.clearToken();
        sessionStorage.clear();
    }
});
</script>
@endpush

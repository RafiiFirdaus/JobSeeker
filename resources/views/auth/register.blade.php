@extends('layouts.app')

@section('title', 'Register - Job Seekers Platform')

@section('header')
    <h1 class="display-4">Register</h1>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <form id="registerForm" class="card card-default" novalidate>
            <div class="card-header">
                <h4 class="mb-0">Create Account</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback-ajax" id="nameError" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback-ajax" id="emailError" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="id_card_number">ID Card Number</label>
                            <input type="text" class="form-control" id="id_card_number" name="id_card_number" required>
                            <div class="invalid-feedback-ajax" id="id_card_numberError" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="phone">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                            <div class="invalid-feedback-ajax" id="phoneError" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback-ajax" id="passwordError" style="display: none;"></div>
                            <small class="form-text text-muted">
                                Password must be at least 8 characters long.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            <div class="invalid-feedback-ajax" id="password_confirmationError" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-primary me-2" id="registerBtn">
                        <i class="fas fa-user-plus"></i> Register
                    </button>
                    <a href="{{ route('login') }}" class="btn btn-link">Already have an account?</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        handleRegistration();
    });

    // Real-time validation for password confirmation
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();

        if (confirmPassword && password !== confirmPassword) {
            showFieldError('password_confirmation', 'Passwords do not match.');
        } else {
            clearFieldError('password_confirmation');
        }
    });

    // Real-time validation for password
    $('#password').on('input', function() {
        const password = $(this).val();

        if (password && password.length < 8) {
            showFieldError('password', 'Password must be at least 8 characters long.');
        } else {
            clearFieldError('password');
        }

        // Re-check confirmation if it's already filled
        const confirmPassword = $('#password_confirmation').val();
        if (confirmPassword) {
            $('#password_confirmation').trigger('input');
        }
    });

    // Real-time validation for email
    $('#email').on('blur', function() {
        const email = $(this).val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email && !emailRegex.test(email)) {
            showFieldError('email', 'Please enter a valid email address.');
        } else {
            clearFieldError('email');
        }
    });

    // Real-time validation for ID card number
    $('#id_card_number').on('input', function() {
        const idCard = $(this).val();

        if (idCard && (idCard.length < 16 || idCard.length > 16)) {
            showFieldError('id_card_number', 'ID Card number must be exactly 16 digits.');
        } else if (idCard && !/^\d+$/.test(idCard)) {
            showFieldError('id_card_number', 'ID Card number must contain only numbers.');
        } else {
            clearFieldError('id_card_number');
        }
    });

    // Real-time validation for phone
    $('#phone').on('input', function() {
        const phone = $(this).val();

        if (phone && (phone.length < 10 || phone.length > 15)) {
            showFieldError('phone', 'Phone number must be between 10-15 digits.');
        } else if (phone && !/^\d+$/.test(phone)) {
            showFieldError('phone', 'Phone number must contain only numbers.');
        } else {
            clearFieldError('phone');
        }
    });

    async function handleRegistration() {
        try {
            // Clear previous errors
            clearAllErrors();

            // Client-side validation
            if (!validateForm()) {
                return;
            }

            jobSeekerAPI.setButtonLoading('#registerBtn', true);

            const formData = {
                name: $('#name').val().trim(),
                email: $('#email').val().trim(),
                id_card_number: $('#id_card_number').val().trim(),
                phone: $('#phone').val().trim(),
                password: $('#password').val(),
                password_confirmation: $('#password_confirmation').val()
            };

            const response = await jobSeekerAPI.register(formData);

            if (response.success) {
                jobSeekerAPI.showAlert('success', 'Registration Successful!',
                    'Your account has been created successfully. You can now log in.');

                // Clear form
                $('#registerForm')[0].reset();

                // Redirect to login page after a short delay
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);

            } else {
                // Handle validation errors
                if (response.errors) {
                    displayValidationErrors(response.errors);
                } else {
                    throw new Error(response.message || 'Registration failed');
                }
            }
        } catch (error) {
            console.error('Registration error:', error);

            if (error.status === 422 && error.responseJSON?.errors) {
                displayValidationErrors(error.responseJSON.errors);
            } else {
                jobSeekerAPI.showAlert('error', 'Registration Failed',
                    error.message || 'Failed to create your account. Please try again.');
            }
        } finally {
            jobSeekerAPI.setButtonLoading('#registerBtn', false);
        }
    }

    function validateForm() {
        let isValid = true;

        // Validate name
        const name = $('#name').val().trim();
        if (!name) {
            showFieldError('name', 'Full name is required.');
            isValid = false;
        } else if (name.length < 2) {
            showFieldError('name', 'Full name must be at least 2 characters long.');
            isValid = false;
        }

        // Validate email
        const email = $('#email').val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            showFieldError('email', 'Email is required.');
            isValid = false;
        } else if (!emailRegex.test(email)) {
            showFieldError('email', 'Please enter a valid email address.');
            isValid = false;
        }

        // Validate ID card number
        const idCard = $('#id_card_number').val().trim();
        if (!idCard) {
            showFieldError('id_card_number', 'ID Card number is required.');
            isValid = false;
        } else if (idCard.length !== 16) {
            showFieldError('id_card_number', 'ID Card number must be exactly 16 digits.');
            isValid = false;
        } else if (!/^\d+$/.test(idCard)) {
            showFieldError('id_card_number', 'ID Card number must contain only numbers.');
            isValid = false;
        }

        // Validate phone
        const phone = $('#phone').val().trim();
        if (!phone) {
            showFieldError('phone', 'Phone number is required.');
            isValid = false;
        } else if (phone.length < 10 || phone.length > 15) {
            showFieldError('phone', 'Phone number must be between 10-15 digits.');
            isValid = false;
        } else if (!/^\d+$/.test(phone)) {
            showFieldError('phone', 'Phone number must contain only numbers.');
            isValid = false;
        }

        // Validate password
        const password = $('#password').val();
        if (!password) {
            showFieldError('password', 'Password is required.');
            isValid = false;
        } else if (password.length < 8) {
            showFieldError('password', 'Password must be at least 8 characters long.');
            isValid = false;
        }

        // Validate password confirmation
        const passwordConfirmation = $('#password_confirmation').val();
        if (!passwordConfirmation) {
            showFieldError('password_confirmation', 'Password confirmation is required.');
            isValid = false;
        } else if (password !== passwordConfirmation) {
            showFieldError('password_confirmation', 'Passwords do not match.');
            isValid = false;
        }

        return isValid;
    }

    function displayValidationErrors(errors) {
        for (const field in errors) {
            const messages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
            showFieldError(field, messages[0]);
        }
    }

    function showFieldError(field, message) {
        const errorElement = $(`#${field}Error`);
        const inputElement = $(`#${field}`);

        errorElement.text(message).show();
        inputElement.addClass('is-invalid-ajax');
    }

    function clearFieldError(field) {
        const errorElement = $(`#${field}Error`);
        const inputElement = $(`#${field}`);

        errorElement.hide();
        inputElement.removeClass('is-invalid-ajax');
    }

    function clearAllErrors() {
        $('.invalid-feedback-ajax').hide();
        $('.is-invalid-ajax').removeClass('is-invalid-ajax');
    }
});
</script>

@endsection

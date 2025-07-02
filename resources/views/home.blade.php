@extends('layouts.app')

@section('title', 'Home - Job Seekers Platform')

@section('header')
    <div class="text-center">
        <h1 class="display-4">Job Seekers Platform</h1>
        <p class="lead">Find your dream job and build your career with us</p>
        @if(!Session::has('user_logged_in'))
            <div class="mt-4">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-3">Login</a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">Register</a>
            </div>
        @else
            <div class="mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg me-3">Go to Dashboard</a>
                <a href="{{ route('job-vacancies.index') }}" class="btn btn-outline-primary btn-lg">Browse Jobs</a>
            </div>
        @endif
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="text-center mb-5">
            <h2>How it Works</h2>
            <p class="text-muted">Simple steps to get your dream job</p>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-user-plus fa-3x text-primary"></i>
                </div>
                <h5 class="card-title">1. Register Account</h5>
                <p class="card-text">Create your account and complete your profile with all necessary information.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-check-circle fa-3x text-primary"></i>
                </div>
                <h5 class="card-title">2. Get Validated</h5>
                <p class="card-text">Request data validation to verify your skills and experience for better job matching.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-briefcase fa-3x text-primary"></i>
                </div>
                <h5 class="card-title">3. Apply for Jobs</h5>
                <p class="card-text">Browse available job vacancies and apply for positions that match your skills.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="text-center mb-4">
            <h2>Featured Companies</h2>
            <p class="text-muted">Join these amazing companies</p>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="card-title text-primary">PT. Maju Mundur Sejahtera</h6>
                <small class="text-muted">Technology Company</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="card-title text-primary">PT. Tech Innovation</h6>
                <small class="text-muted">Software Development</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="card-title text-primary">PT. Digital Solutions</h6>
                <small class="text-muted">Digital Agency</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="card-title text-primary">PT. Creative Agency</h6>
                <small class="text-muted">Design & Marketing</small>
            </div>
        </div>
    </div>
</div>

@if(!Session::has('user_logged_in'))
<div class="row">
    <div class="col-md-12">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3>Ready to Start Your Career?</h3>
                <p class="lead">Join thousands of job seekers who found their dream jobs through our platform.</p>
                <a href="{{ route('register') }}" class="btn btn-light btn-lg">Get Started Today</a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

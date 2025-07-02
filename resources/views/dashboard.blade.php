@extends('layouts.app')

@section('title', 'Dashboard - Job Seekers Platform')

@section('header')
    <h1 class="display-4">Dashboard</h1>
@endsection

@section('content')
<!-- Data Validation Section -->
<section class="validation-section mb-5">
    <div class="section-header mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="section-title text-muted">My Data Validation</h4>
            <button class="btn btn-outline-primary btn-sm" id="refreshValidations">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <div class="row" id="validationsContainer">
        <!-- Validations will be loaded here via AJAX -->
    </div>
</section>

<!-- Recent Job Applications Section -->
<section class="applications-section mb-5">
    <div class="section-header mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="section-title text-muted">Recent Job Applications</h4>
            <div>
                <button class="btn btn-outline-primary btn-sm" id="refreshApplications">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <a href="{{ route('job-applications.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-list"></i> View All
                </a>
            </div>
        </div>
    </div>

    <div id="applicationsContainer">
        <!-- Recent applications will be loaded here via AJAX -->
    </div>
</section>

<!-- Quick Actions Section -->
<section class="quick-actions-section mb-5">
    <div class="section-header mb-3">
        <h4 class="section-title text-muted">Quick Actions</h4>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card card-default">
                <div class="card-header">
                    <h5 class="mb-0">Data Validation</h5>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-clipboard-check fa-3x text-primary mb-3"></i>
                    <p>Request validation for your profile data</p>
                    <a href="{{ route('data-validation.create') }}" class="btn btn-primary w-100">
                        + Request Validation
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-default">
                <div class="card-header">
                    <h5 class="mb-0">Browse Jobs</h5>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-search fa-3x text-success mb-3"></i>
                    <p>Find and apply for available job vacancies</p>
                    <a href="{{ route('job-vacancies.index') }}" class="btn btn-success w-100">
                        <i class="fas fa-search"></i> Browse Jobs
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-default">
                <div class="card-header">
                    <h5 class="mb-0">My Applications</h5>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-briefcase fa-3x text-info mb-3"></i>
                    <p>Track your job application status</p>
                    <a href="{{ route('job-applications.index') }}" class="btn btn-info w-100">
                        <i class="fas fa-list"></i> View Applications
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>



    </div>
</section>
<!-- E: Data Validation Section -->

<!-- S: List Job Applications Section -->
<section class="validation-section mb-5">
    <div class="section-header mb-3">
        <div class="row">
            <div class="col-md-8">
                <h4 class="section-title text-muted">My Job Applications</h4>
            </div>
            <div class="col-md-4">
                <a href="{{ route('job-vacancies.index') }}" class="btn btn-primary btn-lg w-100">+ Add Job Applications</a>
            </div>
        </div>
    </div>
    <div class="section-body">
        <div class="row mb-4">

            <!-- S: Job Applications info -->
            <div class="col-md-12">
                <div class="alert alert-warning">
                    Your validation must be approved by validator to applying job.
                </div>
            </div>
            <!-- E: Job Applications info -->

            @if(isset($jobApplications) && $jobApplications->count() > 0)
                @foreach($jobApplications as $application)
                <div class="col-md-6">
                    <div class="card card-default">
                        <div class="card-header border-0">
                            <h5 class="mb-0">{{ $application->jobVacancy->company }}</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <tr>
                                    <th>Address</th>
                                    <td class="text-muted">{{ $application->jobVacancy->address }}</td>
                                </tr>
                                <tr>
                                    <th>Position</th>
                                    <td class="text-muted">
                                        <ul>
                                            @foreach($application->jobApplyPositions as $positionApp)
                                            <li>{{ $positionApp->availablePosition->position_name }}
                                                @if($application->status == 'pending')
                                                    <span class="badge bg-info">Pending</span>
                                                @elseif($application->status == 'accepted')
                                                    <span class="badge bg-success">Accepted</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Apply Date</th>
                                    <td class="text-muted">{{ $application->created_at->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td class="text-muted">{{ $application->notes ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <!-- Sample Job Application -->
                <div class="col-md-6">
                    <div class="card card-default">
                        <div class="card-header border-0">
                            <h5 class="mb-0">PT. Maju Mundur Sejahtera</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <tr>
                                    <th>Address</th>
                                    <td class="text-muted">Jln. HOS. Cjokroaminoto (Pasirkaliki) No. 900, DKI Jakarta</td>
                                </tr>
                                <tr>
                                    <th>Position</th>
                                    <td class="text-muted">
                                        <ul>
                                            <li>Desain Grafis <span class="badge bg-info">Pending</span></li>
                                            <li>Programmer <span class="badge bg-info">Pending</span></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Apply Date</th>
                                    <td class="text-muted">September 12, 2023</td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td class="text-muted">I was the better one</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-default">
                        <div class="card-header border-0">
                            <h5 class="mb-0">PT. Tech Innovation</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <tr>
                                    <th>Address</th>
                                    <td class="text-muted">Jln. Sudirman No. 123, Jakarta Pusat</td>
                                </tr>
                                <tr>
                                    <th>Position</th>
                                    <td class="text-muted">
                                        <ul>
                                            <li>Full Stack Developer <span class="badge bg-success">Accepted</span></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Apply Date</th>
                                    <td class="text-muted">October 15, 2023</td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td class="text-muted">Ready to start immediately</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</section>
<!-- E: List Job Applications Section -->
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load initial dashboard data
    loadValidations();
    loadRecentApplications();

    // Refresh buttons
    $('#refreshValidations').on('click', function() {
        loadValidations();
    });

    $('#refreshApplications').on('click', function() {
        loadRecentApplications();
    });

    async function loadValidations() {
        try {
            jobSeekerAPI.showLoading('#validationsContainer');

            // Show skeleton loading for validation cards
            $('#validationsContainer').html(renderValidationSkeletons());

            const response = await jobSeekerAPI.getValidations(1, { limit: 3 });

            if (response.success && response.data) {
                const validations = response.data.data || response.data;

                let validationsHtml = `
                    <!-- Request Validation Card -->
                    <div class="col-md-4">
                        <div class="card card-default">
                            <div class="card-header">
                                <h5 class="mb-0">Data Validation</h5>
                            </div>
                            <div class="card-body text-center">
                                <a href="/data-validation/create" class="btn btn-primary w-100">+ Request validation</a>
                            </div>
                        </div>
                    </div>
                `;

                if (validations.length > 0) {
                    validations.slice(0, 2).forEach(validation => {
                        validationsHtml += renderValidationCard(validation);
                    });
                } else {
                    // Show sample data when no validations exist
                    validationsHtml += `
                        <div class="col-md-4">
                            <div class="card card-default">
                                <div class="card-header border-0">
                                    <h5 class="mb-0">No Validations Yet</h5>
                                </div>
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Submit your first validation request to get started</p>
                                    <a href="/data-validation/create" class="btn btn-primary">
                                        Get Started
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                }

                $('#validationsContainer').html(validationsHtml);
            } else {
                throw new Error('Failed to load validations');
            }
        } catch (error) {
            console.error('Error loading validations:', error);
            $('#validationsContainer').html(`
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Unable to load validation data.
                        <button class="btn btn-sm btn-outline-primary ms-2" onclick="loadValidations()">
                            <i class="fas fa-redo"></i> Retry
                        </button>
                    </div>
                </div>
            `);
        } finally {
            jobSeekerAPI.hideLoading('#validationsContainer');
        }
    }

    async function loadRecentApplications() {
        try {
            jobSeekerAPI.showLoading('#applicationsContainer');

            const response = await jobSeekerAPI.getJobApplications(1, { limit: 3 });

            if (response.success && response.data) {
                const applications = response.data.data || response.data;

                if (applications.length > 0) {
                    let applicationsHtml = '<div class="row">';
                    applications.slice(0, 3).forEach(application => {
                        applicationsHtml += renderApplicationCard(application);
                    });
                    applicationsHtml += '</div>';

                    $('#applicationsContainer').html(applicationsHtml);
                } else {
                    $('#applicationsContainer').html(`
                        <div class="text-center py-4">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Applications Yet</h5>
                            <p class="text-muted">Start applying for jobs to see your applications here</p>
                            <a href="/job-vacancies" class="btn btn-primary">
                                <i class="fas fa-search"></i> Browse Jobs
                            </a>
                        </div>
                    `);
                }
            } else {
                throw new Error('Failed to load applications');
            }
        } catch (error) {
            console.error('Error loading applications:', error);

            if (error.status === 401) {
                $('#applicationsContainer').html(`
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i>
                        Please log in to view your recent applications.
                    </div>
                `);
            } else {
                $('#applicationsContainer').html(`
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Unable to load recent applications.
                        <button class="btn btn-sm btn-outline-primary ms-2" onclick="loadRecentApplications()">
                            <i class="fas fa-redo"></i> Retry
                        </button>
                    </div>
                `);
            }
        } finally {
            jobSeekerAPI.hideLoading('#applicationsContainer');
        }
    }

    function renderValidationCard(validation) {
        const statusConfig = getValidationStatusConfig(validation.status);

        return `
            <div class="col-md-4">
                <div class="card card-default">
                    <div class="card-header border-0">
                        <h5 class="mb-0">Data Validation #${validation.id}</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <tr>
                                <th>Status</th>
                                <td><span class="badge ${statusConfig.class}">${statusConfig.label}</span></td>
                            </tr>
                            <tr>
                                <th>Job Category</th>
                                <td class="text-muted">${validation.job_category?.name || '-'}</td>
                            </tr>
                            <tr>
                                <th>Job Position</th>
                                <td class="text-muted">${validation.position || '-'}</td>
                            </tr>
                            <tr>
                                <th>Validator</th>
                                <td class="text-muted">${validation.validator_name || '-'}</td>
                            </tr>
                            <tr>
                                <th>Submitted</th>
                                <td class="text-muted">${jobSeekerAPI.formatDate(validation.created_at)}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }

    function renderApplicationCard(application) {
        return `
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="company-name">${application.job_vacancy.company}</h6>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-map-marker-alt"></i> ${application.job_vacancy.address}
                        </p>
                        <span class="job-category small">${application.job_vacancy.job_category?.name || 'N/A'}</span>
                        <div class="mt-2">
                            <small class="text-muted">Applied: ${jobSeekerAPI.formatDate(application.created_at)}</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="/job-applications/${application.id}" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    function renderValidationSkeletons() {
        let skeletons = `
            <div class="col-md-4">
                <div class="card card-default">
                    <div class="card-header">
                        <h5 class="mb-0">Data Validation</h5>
                    </div>
                    <div class="card-body text-center">
                        <a href="/data-validation/create" class="btn btn-primary w-100">+ Request validation</a>
                    </div>
                </div>
            </div>
        `;

        for (let i = 0; i < 2; i++) {
            skeletons += `
                <div class="col-md-4">
                    <div class="card card-default">
                        <div class="card-header border-0">
                            <div class="bg-light rounded" style="height: 1.5rem; width: 60%;"></div>
                        </div>
                        <div class="card-body p-3">
                            <div class="bg-light rounded mb-2" style="height: 1rem;"></div>
                            <div class="bg-light rounded mb-2" style="height: 1rem;"></div>
                            <div class="bg-light rounded mb-2" style="height: 1rem;"></div>
                        </div>
                    </div>
                </div>
            `;
        }

        return skeletons;
    }

    function getValidationStatusConfig(status) {
        switch(status) {
            case 'pending':
                return { class: 'bg-info', label: 'Pending' };
            case 'accepted':
                return { class: 'bg-success', label: 'Accepted' };
            case 'rejected':
                return { class: 'bg-danger', label: 'Rejected' };
            default:
                return { class: 'bg-secondary', label: 'Unknown' };
        }
    }

    // Make functions globally accessible for retry buttons
    window.loadValidations = loadValidations;
    window.loadRecentApplications = loadRecentApplications;
});
</script>
@endpush

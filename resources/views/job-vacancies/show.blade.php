@extends('layouts.app')

@section('title', 'Job Details')

@section('header')
    <div class="text-center" id="jobHeader">
        <!-- Job header will be loaded via AJAX -->
    </div>
@endsection

@section('content')
<div id="jobVacancyContent">
    <!-- Job vacancy content will be loaded here via AJAX -->
</div>

<div class="text-center mb-4">
    <a href="{{ route('job-vacancies.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Job Vacancies
    </a>
</div>

<!-- Pass user login status to JavaScript -->
<script>
    window.userLoggedIn = {{ Session::has('user_logged_in') ? 'true' : 'false' }};
    window.userId = {{ Session::get('user_id', 'null') }};
    window.userName = '{{ addslashes(Session::get('user_name', '')) }}';
</script>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const vacancyId = {{ request()->route('id') }};

    loadJobVacancyDetails(vacancyId);

    // Handle apply button click
    $(document).on('click', '.apply-now-btn', function(e) {
        e.preventDefault();
        const vacancyId = $(this).data('vacancy-id');
        window.location.href = `/job-applications/create/${vacancyId}`;
    });

    // Handle login button click
    $(document).on('click', '.login-to-apply-btn', function(e) {
        e.preventDefault();
        window.location.href = '/login';
    });

    // Handle validation button click
    $(document).on('click', '.submit-validation-btn', function(e) {
        e.preventDefault();
        window.location.href = '/data-validation/create';
    });

    // Handle view applications button click
    $(document).on('click', '.view-applications-btn', function(e) {
        e.preventDefault();
        window.location.href = '/job-applications';
    });

    async function loadJobVacancyDetails(vacancyId) {
        try {
            jobSeekerAPI.showLoading('#jobVacancyContent');

            const response = await jobSeekerAPI.getJobVacancy(vacancyId);

            if (response.success && response.data) {
                const vacancy = response.data;

                // Update page title
                document.title = `Job Details - ${vacancy.company}`;

                // Render job header
                $('#jobHeader').html(`
                    <h1 class="display-4">${vacancy.company}</h1>
                    <span class="text-muted">${vacancy.address || 'Address not specified'}</span>
                    <br>
                    <span class="job-category mt-2">${vacancy.job_category?.name || 'N/A'}</span>
                `);

                // Get user status and render content
                const userStatus = await getUserStatusAsync(vacancy.id);
                const jobContent = renderJobVacancyContentSync(vacancy, userStatus);
                $('#jobVacancyContent').html(jobContent);

            } else {
                throw new Error(response.message || 'Failed to load job vacancy details');
            }
        } catch (error) {
            console.error('Error loading job vacancy:', error);
            $('#jobVacancyContent').html(`
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle"></i>
                    Failed to load job vacancy details. Please try again.
                    <br>
                    <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadJobVacancyDetails(${vacancyId})">
                        <i class="fas fa-redo"></i> Retry
                    </button>
                </div>
            `);
        } finally {
            jobSeekerAPI.hideLoading('#jobVacancyContent');
        }
    }

    function renderJobVacancyContentSync(vacancy, userStatus) {
        const applicationStatus = renderApplicationStatus(vacancy, userStatus);

        return `
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h3>Job Description</h3>
                            <p>${vacancy.description || 'Job description not available.'}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5>Quick Info</h5>
                            <p><strong>Company:</strong> ${vacancy.company}</p>
                            <p><strong>Category:</strong> ${vacancy.job_category?.name || 'N/A'}</p>
                            <p><strong>Address:</strong> ${vacancy.address}</p>
                            <p><strong>Positions Available:</strong> ${vacancy.available_positions?.length || 0}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>Available Positions</h3>
                        </div>
                        <div class="card-body">
                            ${renderAvailablePositions(vacancy.available_positions)}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Status and Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            ${applicationStatus}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    async function renderJobVacancyContent(vacancy) {
        const userStatus = await getUserStatusAsync(vacancy.id);
        return renderJobVacancyContentSync(vacancy, userStatus);
    }

    function renderAvailablePositions(positions) {
        if (!positions || positions.length === 0) {
            return `
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i> No positions available for this job vacancy.
                </div>
            `;
        }

        let tableHtml = `
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Position</th>
                            <th>Capacity</th>
                            <th>Applications</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        positions.forEach(position => {
            const applicationCount = position.application_count || 0;
            const applyCapacity = position.apply_capacity || position.capacity;
            const isFull = applicationCount >= applyCapacity;
            const progressPercent = (applicationCount / applyCapacity) * 100;

            tableHtml += `
                <tr>
                    <td><strong>${position.position}</strong></td>
                    <td>${position.capacity}</td>
                    <td>
                        ${applicationCount}/${applyCapacity}
                        <div class="progress mt-1" style="height: 5px;">
                            <div class="progress-bar ${isFull ? 'bg-danger' : 'bg-success'}"
                                 style="width: ${progressPercent}%">
                            </div>
                        </div>
                    </td>
                    <td>
                        ${isFull ?
                            '<span class="badge badge-danger">Full</span>' :
                            '<span class="badge badge-success">Available</span>'
                        }
                    </td>
                </tr>
            `;
        });

        tableHtml += `
                    </tbody>
                </table>
            </div>
        `;

        return tableHtml;
    }

    function getUserStatus() {
        // This function is now async and will be called differently
        // Keeping it synchronous for now by returning a promise-like structure
        return {
            isLoggedIn: false,
            hasApplied: false,
            validationAccepted: false
        };
    }

    async function getUserStatusAsync(vacancyId = null) {
        try {
            // Use server-side data if available
            if (typeof window.userLoggedIn !== 'undefined') {
                if (!window.userLoggedIn) {
                    return {
                        isLoggedIn: false,
                        hasApplied: false,
                        validationAccepted: false
                    };
                }

                // User is logged in, check other statuses via API
                const response = await jobSeekerAPI.getUserStatus(vacancyId);
                if (response.success) {
                    return response.data;
                }
            }

            // Fallback to API call
            const response = await jobSeekerAPI.getUserStatus(vacancyId);
            if (response.success) {
                return response.data;
            } else {
                throw new Error(response.message);
            }
        } catch (error) {
            console.error('Error getting user status:', error);
            return {
                isLoggedIn: window.userLoggedIn || false,
                hasApplied: false,
                validationAccepted: false
            };
        }
    }

    function renderApplicationStatus(vacancy, userStatus) {
        if (!userStatus.isLoggedIn) {
            return `
                <h5>Want to apply for this job?</h5>
                <p class="text-muted">Please login first to apply for this position.</p>
                <button class="btn btn-primary btn-lg login-to-apply-btn">
                    <i class="fas fa-sign-in-alt"></i> Login to Apply
                </button>
            `;
        }

        if (userStatus.hasApplied) {
            return `
                <div class="alert alert-info">
                    <h5><i class="fas fa-check-circle"></i> Application Submitted</h5>
                    <p class="mb-0">You have already applied for this job. Check your application status in your dashboard.</p>
                </div>
                <button class="btn btn-primary view-applications-btn">
                    <i class="fas fa-list"></i> View My Applications
                </button>
            `;
        }

        if (!userStatus.validationAccepted) {
            return `
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Validation Required</h5>
                    <p class="mb-0">Your data validation must be accepted by a validator before you can apply for jobs.</p>
                </div>
                <button class="btn btn-warning submit-validation-btn">
                    <i class="fas fa-clipboard-check"></i> Submit Data Validation
                </button>
            `;
        }

        return `
            <h5>Ready to apply?</h5>
            <p class="text-muted">Click the button below to start your application for this job.</p>
            <button class="btn btn-success btn-lg apply-now-btn" data-vacancy-id="${vacancy.id}">
                <i class="fas fa-paper-plane"></i> Apply Now
            </button>
        `;
    }

    // Make function globally accessible for retry button
    window.loadJobVacancyDetails = loadJobVacancyDetails;
});
</script>
@endpush

@extends('layouts.app')

@section('title', 'Application Details')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-file-alt"></i> Application Details</h4>
                </div>
                <div class="card-body" id="applicationContent">
                    <!-- Application content will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const applicationId = {{ request()->route('id') }};

    loadApplicationDetails(applicationId);

    // Handle back to applications button
    $(document).on('click', '.back-to-applications-btn', function(e) {
        e.preventDefault();
        window.location.href = '/job-applications';
    });

    // Handle view job button
    $(document).on('click', '.view-job-btn', function(e) {
        e.preventDefault();
        const vacancyId = $(this).data('vacancy-id');
        window.location.href = `/job-vacancies/${vacancyId}`;
    });

    async function loadApplicationDetails(applicationId) {
        try {
            jobSeekerAPI.showLoading('#applicationContent');

            // Since we don't have a specific API endpoint for single application,
            // we'll use the applications list endpoint and filter
            const response = await jobSeekerAPI.getJobApplications(1);

            if (response.success && response.data) {
                const applications = response.data.data || response.data;
                const application = applications.find(app => app.id == applicationId);

                if (application) {
                    $('#applicationContent').html(renderApplicationDetails(application));
                } else {
                    throw new Error('Application not found');
                }
            } else {
                throw new Error(response.message || 'Failed to load application details');
            }
        } catch (error) {
            console.error('Error loading application:', error);

            if (error.status === 401) {
                $('#applicationContent').html(`
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        You need to be logged in to view application details.
                        <br>
                        <a href="/login" class="btn btn-primary mt-2">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </div>
                `);
            } else {
                $('#applicationContent').html(`
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        Failed to load application details. Please try again.
                        <br>
                        <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadApplicationDetails(${applicationId})">
                            <i class="fas fa-redo"></i> Retry
                        </button>
                    </div>
                `);
            }
        } finally {
            jobSeekerAPI.hideLoading('#applicationContent');
        }
    }

    function renderApplicationDetails(application) {
        return `
            <!-- Job Vacancy Information -->
            <div class="job-vacancy-card mb-4">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="company-name">${application.job_vacancy.company}</h5>
                        <p class="text-muted mb-2">
                            <i class="fas fa-map-marker-alt"></i> ${application.job_vacancy.address}
                        </p>
                        <span class="job-category">${application.job_vacancy.job_category?.name || 'N/A'}</span>
                    </div>
                    <div class="col-md-4 text-md-right">
                        <p class="text-muted small">
                            <strong>Applied on:</strong><br>
                            ${jobSeekerAPI.formatDate(application.created_at)}<br>
                            <small>${formatTime(application.created_at)}</small>
                        </p>
                    </div>
                </div>
                <div class="mt-3">
                    <p><strong>Job Description:</strong></p>
                    <p>${application.job_vacancy.description || 'Job description not available.'}</p>
                </div>
            </div>

            <!-- Applied Positions -->
            <div class="mb-4">
                <h5><strong>Applied Positions:</strong></h5>
                <div class="row">
                    ${renderAppliedPositions(application.job_apply_positions)}
                </div>
            </div>

            ${application.notes ? `
                <!-- Application Notes -->
                <div class="mb-4">
                    <h5><strong>Application Notes:</strong></h5>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-0">${application.notes}</p>
                        </div>
                    </div>
                </div>
            ` : ''}

            <!-- Overall Status Summary -->
            <div class="mb-4">
                <h5><strong>Application Summary:</strong></h5>
                ${renderApplicationSummary(application.job_apply_positions)}
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between">
                <button class="btn btn-secondary back-to-applications-btn">
                    <i class="fas fa-arrow-left"></i> Back to Applications
                </button>
                <button class="btn btn-primary view-job-btn" data-vacancy-id="${application.job_vacancy.id}">
                    <i class="fas fa-eye"></i> View Original Job
                </button>
            </div>
        `;
    }

    function renderAppliedPositions(positions) {
        if (!positions || positions.length === 0) {
            return '<div class="col-12"><div class="alert alert-warning">No positions found</div></div>';
        }

        return positions.map(positionApp => {
            const status = positionApp.status || 'pending';
            const statusConfig = getStatusConfig(status);

            return `
                <div class="col-md-6 mb-3">
                    <div class="card border-${statusConfig.class}">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">${positionApp.available_position?.position || 'Unknown Position'}</h6>
                                <span class="text-${statusConfig.class}">
                                    <i class="fas fa-${statusConfig.icon}"></i>
                                </span>
                            </div>
                            <small class="text-muted">
                                Status:
                                <span class="badge badge-${statusConfig.class}">
                                    ${status.charAt(0).toUpperCase() + status.slice(1)}
                                </span>
                            </small>
                            <br>
                            <small class="text-muted">
                                Applied: ${jobSeekerAPI.formatDate(positionApp.created_at)}
                            </small>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderApplicationSummary(positions) {
        if (!positions || positions.length === 0) {
            return '<div class="alert alert-info">No positions to summarize</div>';
        }

        const totalPositions = positions.length;
        const acceptedPositions = positions.filter(p => p.status === 'accepted').length;
        const rejectedPositions = positions.filter(p => p.status === 'rejected').length;
        const pendingPositions = totalPositions - acceptedPositions - rejectedPositions;

        return `
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body p-3">
                            <h4 class="text-success mb-1">${acceptedPositions}</h4>
                            <small class="text-muted">Accepted</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-warning">
                        <div class="card-body p-3">
                            <h4 class="text-warning mb-1">${pendingPositions}</h4>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-danger">
                        <div class="card-body p-3">
                            <h4 class="text-danger mb-1">${rejectedPositions}</h4>
                            <small class="text-muted">Rejected</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function getStatusConfig(status) {
        switch(status) {
            case 'accepted':
                return { class: 'success', icon: 'check-circle' };
            case 'rejected':
                return { class: 'danger', icon: 'times-circle' };
            default:
                return { class: 'warning', icon: 'clock' };
        }
    }

    function formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }

    // Make function globally accessible for retry button
    window.loadApplicationDetails = loadApplicationDetails;
});
</script>

@endsection

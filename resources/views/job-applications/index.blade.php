@extends('layouts.app')

@section('title', 'My Job Applications')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-briefcase"></i> My Job Applications</h2>
                <a href="{{ route('job-vacancies.index') }}" class="btn btn-primary">
                    <i class="fas fa-search"></i> Browse Jobs
                </a>
            </div>

            <!-- Filter Container -->
            <div class="filter-container mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <select class="form-control" id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="search-input-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control search-input" id="searchInput" placeholder="Search by company...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" id="clearFilters">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-primary w-100" id="refreshBtn">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>

            <!-- Applications Container -->
            <div id="applicationsContainer" class="applications-container">
                <!-- Applications will be loaded here via AJAX -->
            </div>

            <!-- Pagination Container -->
            <div id="paginationContainer" class="d-flex justify-content-center mt-4">
                <!-- Pagination will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentPage = 1;
    let currentFilters = {};

    // Load initial data
    loadJobApplications();

    // Search functionality
    let searchTimeout;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentFilters.search = $(this).val();
            currentPage = 1;
            loadJobApplications();
        }, 500);
    });

    // Status filter
    $('#statusFilter').on('change', function() {
        currentFilters.status = $(this).val();
        currentPage = 1;
        loadJobApplications();
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#searchInput').val('');
        $('#statusFilter').val('');
        currentFilters = {};
        currentPage = 1;
        loadJobApplications();
    });

    // Refresh button
    $('#refreshBtn').on('click', function() {
        loadJobApplications();
    });

    // Pagination click handler
    $(document).on('click', '.pagination-ajax .page-link', function(e) {
        e.preventDefault();
        if (!$(this).parent().hasClass('disabled') && !$(this).parent().hasClass('active')) {
            currentPage = parseInt($(this).data('page'));
            loadJobApplications();
        }
    });

    // View job button handler
    $(document).on('click', '.view-job-btn', function(e) {
        e.preventDefault();
        const vacancyId = $(this).data('vacancy-id');
        window.location.href = `/job-vacancies/${vacancyId}`;
    });

    // View application details button handler
    $(document).on('click', '.view-application-btn', function(e) {
        e.preventDefault();
        const applicationId = $(this).data('application-id');
        window.location.href = `/job-applications/${applicationId}`;
    });

    async function loadJobApplications() {
        try {
            jobSeekerAPI.showLoading('#applicationsContainer');

            // Show skeleton loading
            $('#applicationsContainer').html(renderSkeletonLoading());

            const response = await jobSeekerAPI.getJobApplications(currentPage, currentFilters);

            if (response.success && response.data) {
                const applications = response.data.data || response.data;
                const pagination = response.data.pagination || {};

                if (applications.length > 0) {
                    let applicationsHtml = '<div class="row">';
                    applications.forEach(application => {
                        applicationsHtml += jobSeekerAPI.renderJobApplicationCard(application);
                    });
                    applicationsHtml += '</div>';

                    $('#applicationsContainer').html(applicationsHtml);

                    // Render pagination
                    if (pagination.total_pages > 1) {
                        const paginationHtml = jobSeekerAPI.renderPagination(
                            pagination.current_page || currentPage,
                            pagination.total_pages,
                            'loadJobApplications'
                        );
                        $('#paginationContainer').html(paginationHtml);
                    } else {
                        $('#paginationContainer').empty();
                    }
                } else {
                    $('#applicationsContainer').html(
                        jobSeekerAPI.renderEmptyState(
                            'No Job Applications Yet',
                            'You haven\'t applied for any jobs yet. Start browsing available positions!',
                            'Browse Job Vacancies',
                            '/job-vacancies'
                        )
                    );
                    $('#paginationContainer').empty();
                }
            } else {
                throw new Error(response.message || 'Failed to load job applications');
            }
        } catch (error) {
            console.error('Error loading job applications:', error);

            // Check if it's an authentication error
            if (error.status === 401 || error.message.includes('unauthorized')) {
                $('#applicationsContainer').html(`
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        You need to be logged in to view your job applications.
                        <br>
                        <a href="/login" class="btn btn-primary mt-2">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </div>
                `);
            } else {
                $('#applicationsContainer').html(`
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        Failed to load job applications. Please try again.
                        <br>
                        <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadJobApplications()">
                            <i class="fas fa-redo"></i> Retry
                        </button>
                    </div>
                `);
            }
            $('#paginationContainer').empty();
        } finally {
            jobSeekerAPI.hideLoading('#applicationsContainer');
        }
    }

    function renderSkeletonLoading() {
        let skeletonHtml = '<div class="row">';
        for (let i = 0; i < 4; i++) {
            skeletonHtml += `
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="bg-light rounded" style="height: 1.5rem; width: 60%;"></div>
                                <div class="bg-light rounded" style="height: 1rem; width: 80px;"></div>
                            </div>
                            <div class="bg-light rounded mb-2" style="height: 1rem; width: 80%;"></div>
                            <div class="bg-light rounded mb-3" style="height: 1.5rem; width: 40%;"></div>
                            <div class="bg-light rounded mb-2" style="height: 1rem; width: 70%;"></div>
                            <div class="bg-light rounded mb-2" style="height: 1rem; width: 50%;"></div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <div class="bg-light rounded" style="height: 2rem; width: 40%;"></div>
                            <div class="bg-light rounded" style="height: 2rem; width: 30%;"></div>
                        </div>
                    </div>
                </div>
            `;
        }
        skeletonHtml += '</div>';
        return skeletonHtml;
    }

    // Make function globally accessible for retry button
    window.loadJobApplications = loadJobApplications;
});
</script>
@endpush

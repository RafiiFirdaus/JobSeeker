@extends('layouts.app')

@section('title', 'Validation Results - Job Seekers Platform')

@section('header')
    <h1 class="display-4">Validation Results</h1>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>My Validation Results</h4>
            <div>
                <a href="{{ route('data-validation.progress') }}" class="btn btn-outline-secondary">View Progress</a>
                <a href="{{ route('data-validation.create') }}" class="btn btn-primary">+ Request New Validation</a>
                <button class="btn btn-outline-primary" id="refreshBtn">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Filter Container -->
        <div class="filter-container mb-4">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-control" id="statusFilter">
                        <option value="">All Results</option>
                        <option value="accepted">Accepted Only</option>
                        <option value="rejected">Rejected Only</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" id="categoryFilter">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="search-input-container">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control search-input" id="searchInput" placeholder="Search by position...">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" id="clearFilters">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Results Container -->
        <div id="resultsContainer" class="results-container">
            <!-- Results will be loaded here via AJAX -->
        </div>

        <!-- Pagination Container -->
        <div id="paginationContainer" class="d-flex justify-content-center mt-4">
            <!-- Pagination will be loaded here via AJAX -->
        </div>
    </div>
</div>
@endsection
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let currentPage = 1;
    let currentFilters = {};

    // Load initial data
    loadValidationResults();
    loadJobCategories();

    // Search functionality
    let searchTimeout;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentFilters.search = $(this).val();
            currentPage = 1;
            loadValidationResults();
        }, 500);
    });

    // Status filter
    $('#statusFilter').on('change', function() {
        currentFilters.status = $(this).val();
        currentPage = 1;
        loadValidationResults();
    });

    // Category filter
    $('#categoryFilter').on('change', function() {
        currentFilters.category_id = $(this).val();
        currentPage = 1;
        loadValidationResults();
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#searchInput').val('');
        $('#statusFilter').val('');
        $('#categoryFilter').val('');
        currentFilters = {};
        currentPage = 1;
        loadValidationResults();
    });

    // Refresh button
    $('#refreshBtn').on('click', function() {
        loadValidationResults();
    });

    // Pagination click handler
    $(document).on('click', '.pagination-ajax .page-link', function(e) {
        e.preventDefault();
        if (!$(this).parent().hasClass('disabled') && !$(this).parent().hasClass('active')) {
            currentPage = parseInt($(this).data('page'));
            loadValidationResults();
        }
    });

    async function loadValidationResults() {
        try {
            jobSeekerAPI.showLoading('#resultsContainer');

            // Show skeleton loading
            $('#resultsContainer').html(renderSkeletonLoading());

            // Only show accepted and rejected validations (not pending)
            const filters = { ...currentFilters };
            if (!filters.status) {
                filters.status = 'accepted,rejected'; // Show only completed validations
            }

            const response = await jobSeekerAPI.getValidations(currentPage, filters);

            if (response.success && response.data) {
                const validations = response.data.data || response.data;
                const pagination = response.data.pagination || {};

                // Filter only completed validations (accepted/rejected)
                const completedValidations = validations.filter(v =>
                    v.status === 'accepted' || v.status === 'rejected'
                );

                if (completedValidations.length > 0) {
                    let resultsHtml = '';
                    completedValidations.forEach(validation => {
                        resultsHtml += renderValidationResult(validation);
                    });

                    $('#resultsContainer').html(resultsHtml);

                    // Render pagination
                    if (pagination.total_pages > 1) {
                        $('#paginationContainer').html(renderPagination(pagination));
                    } else {
                        $('#paginationContainer').empty();
                    }
                } else {
                    $('#resultsContainer').html(`
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-file-contract fa-3x text-muted"></i>
                            </div>
                            <h5>No Validation Results</h5>
                            <p class="text-muted">You don't have any completed validation requests yet.</p>
                            <a href="/data-validation/create" class="btn btn-primary">Request Data Validation</a>
                        </div>
                    `);
                    $('#paginationContainer').empty();
                }
            } else {
                throw new Error('Failed to load validation results');
            }
        } catch (error) {
            console.error('Error loading validation results:', error);

            if (error.status === 401) {
                $('#resultsContainer').html(`
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        You need to be logged in to view your validation results.
                        <br>
                        <a href="/login" class="btn btn-primary mt-2">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </div>
                `);
            } else {
                $('#resultsContainer').html(`
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        Failed to load validation results. Please try again.
                        <br>
                        <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadValidationResults()">
                            <i class="fas fa-redo"></i> Retry
                        </button>
                    </div>
                `);
            }
            $('#paginationContainer').empty();
        } finally {
            jobSeekerAPI.hideLoading('#resultsContainer');
        }
    }

    async function loadJobCategories() {
        try {
            const response = await jobSeekerAPI.getJobCategories();
            if (response.success && response.data) {
                const categories = response.data;
                let optionsHtml = '<option value="">All Categories</option>';

                categories.forEach(category => {
                    optionsHtml += `<option value="${category.id}">${category.name}</option>`;
                });

                $('#categoryFilter').html(optionsHtml);
            }
        } catch (error) {
            console.error('Error loading job categories:', error);
        }
    }

    function renderValidationResult(validation) {
        const isAccepted = validation.status === 'accepted';
        const statusClass = isAccepted ? 'success' : 'danger';
        const statusIcon = isAccepted ? 'check-circle' : 'times-circle';
        const badgeClass = isAccepted ? 'bg-success' : 'bg-danger';

        return `
            <div class="col-md-12 mb-4">
                <div class="card border-${statusClass}">
                    <div class="card-header bg-${statusClass} text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-${statusIcon}"></i>
                                Validation Request #${validation.id} - ${validation.status.charAt(0).toUpperCase() + validation.status.slice(1)}
                            </h6>
                            <small>${new Date(validation.created_at).toLocaleDateString()}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Application Details</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Job Category:</th>
                                        <td>${validation.job_category?.name || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Position:</th>
                                        <td>${validation.position || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Has Experience:</th>
                                        <td>${validation.has_experience ? 'Yes' : 'No'}</td>
                                    </tr>
                                    <tr>
                                        <th>Domicile:</th>
                                        <td>${validation.domicile || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Expected Salary:</th>
                                        <td>${validation.expected_salary ? `Rp ${parseInt(validation.expected_salary).toLocaleString('id-ID')}` : '-'}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Validation Info</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Status:</th>
                                        <td><span class="badge ${badgeClass}">${validation.status.charAt(0).toUpperCase() + validation.status.slice(1)}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Submitted:</th>
                                        <td>${new Date(validation.created_at).toLocaleDateString()}</td>
                                    </tr>
                                    <tr>
                                        <th>Processed:</th>
                                        <td>${validation.updated_at ? new Date(validation.updated_at).toLocaleDateString() : '-'}</td>
                                    </tr>
                                    ${validation.validator_name ? `
                                        <tr>
                                            <th>Validated by:</th>
                                            <td>${validation.validator_name}</td>
                                        </tr>
                                    ` : ''}
                                    ${validation.validator_notes ? `
                                        <tr>
                                            <th>Validator Notes:</th>
                                            <td><small class="text-muted">${validation.validator_notes}</small></td>
                                        </tr>
                                    ` : ''}
                                </table>
                            </div>
                        </div>

                        ${validation.reason ? `
                            <div class="mt-3">
                                <h6>Your Application Notes</h6>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0">${validation.reason}</p>
                                </div>
                            </div>
                        ` : ''}

                        ${validation.status === 'accepted' ? `
                            <div class="mt-3">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <strong>Congratulations!</strong> Your validation has been accepted. You can now apply for jobs in the ${validation.job_category?.name || 'selected'} category.
                                    <div class="mt-2">
                                        <a href="/job-vacancies" class="btn btn-success btn-sm">
                                            <i class="fas fa-search"></i> Browse Job Vacancies
                                        </a>
                                    </div>
                                </div>
                            </div>
                        ` : ''}

                        ${validation.status === 'rejected' ? `
                            <div class="mt-3">
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle"></i>
                                    <strong>Validation Rejected</strong> Your validation request was not approved. You can submit a new request with updated information.
                                    <div class="mt-2">
                                        <a href="/data-validation/create" class="btn btn-danger btn-sm">
                                            <i class="fas fa-plus"></i> Submit New Request
                                        </a>
                                    </div>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    function renderSkeletonLoading() {
        let skeletonHtml = '';
        for (let i = 0; i < 3; i++) {
            skeletonHtml += `
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="bg-light rounded" style="height: 1.5rem; width: 60%;"></div>
                                <div class="bg-light rounded" style="height: 1rem; width: 80px;"></div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="bg-light rounded mb-2" style="height: 1rem; width: 40%;"></div>
                                    <div class="bg-light rounded mb-2" style="height: 1rem; width: 80%;"></div>
                                    <div class="bg-light rounded mb-2" style="height: 1rem; width: 60%;"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-light rounded mb-2" style="height: 1rem; width: 40%;"></div>
                                    <div class="bg-light rounded mb-2" style="height: 1rem; width: 70%;"></div>
                                    <div class="bg-light rounded mb-2" style="height: 1rem; width: 50%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        return skeletonHtml;
    }

    function renderPagination(pagination) {
        let paginationHtml = `<nav><ul class="pagination pagination-ajax">`;

        // Previous button
        if (pagination.current_page > 1) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a></li>`;
        } else {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">Previous</span></li>`;
        }

        // Page numbers
        for (let i = 1; i <= pagination.total_pages; i++) {
            if (i === pagination.current_page) {
                paginationHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            }
        }

        // Next button
        if (pagination.current_page < pagination.total_pages) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a></li>`;
        } else {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">Next</span></li>`;
        }

        paginationHtml += `</ul></nav>`;
        return paginationHtml;
    }

    // Make function globally accessible for retry button
    window.loadValidationResults = loadValidationResults;
});
</script>
@endpush

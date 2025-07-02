@extends('layouts.app')

@section('title', 'Validation Progress - Job Seekers Platform')

@section('header')
    <h1 class="display-4">Validation Progress</h1>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>My Data Validation Progress</h4>
            <div>
                <a href="{{ route('data-validation.create') }}" class="btn btn-primary">+ Request New Validation</a>
                <a href="{{ route('data-validation.results') }}" class="btn btn-outline-secondary">View Results</a>
                <button class="btn btn-outline-primary" id="refreshBtn">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
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
                    <select class="form-control" id="categoryFilter">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" id="clearFilters">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Validations Container -->
        <div id="validationsContainer" class="validations-container">
            <!-- Validations will be loaded here via AJAX -->
        </div>

        <!-- Pagination Container -->
        <div id="paginationContainer" class="d-flex justify-content-center mt-4">
            <!-- Pagination will be loaded here via AJAX -->
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let currentPage = 1;
    let currentFilters = {};

    // Load initial data
    loadValidations();
    loadJobCategories();

    // Status filter
    $('#statusFilter').on('change', function() {
        currentFilters.status = $(this).val();
        currentPage = 1;
        loadValidations();
    });

    // Category filter
    $('#categoryFilter').on('change', function() {
        currentFilters.category_id = $(this).val();
        currentPage = 1;
        loadValidations();
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#statusFilter').val('');
        $('#categoryFilter').val('');
        currentFilters = {};
        currentPage = 1;
        loadValidations();
    });

    // Refresh button
    $('#refreshBtn').on('click', function() {
        loadValidations();
    });

    // Pagination click handler
    $(document).on('click', '.pagination-ajax .page-link', function(e) {
        e.preventDefault();
        if (!$(this).parent().hasClass('disabled') && !$(this).parent().hasClass('active')) {
            currentPage = parseInt($(this).data('page'));
            loadValidations();
        }
    });

    async function loadValidations() {
        try {
            jobSeekerAPI.showLoading('#validationsContainer');

            // Show skeleton loading
            $('#validationsContainer').html(renderSkeletonLoading());

            const response = await jobSeekerAPI.getValidations(currentPage, currentFilters);

            if (response.success && response.data) {
                const validations = response.data.data || response.data;
                const pagination = response.data.pagination || {};

                if (validations.length > 0) {
                    let validationsHtml = '<div class="row">';
                    validations.forEach(validation => {
                        validationsHtml += renderValidationCard(validation);
                    });
                    validationsHtml += '</div>';

                    $('#validationsContainer').html(validationsHtml);

                    // Render pagination
                    if (pagination.total_pages > 1) {
                        $('#paginationContainer').html(renderPagination(pagination));
                    } else {
                        $('#paginationContainer').empty();
                    }
                } else {
                    $('#validationsContainer').html(`
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-file-alt fa-3x text-muted"></i>
                            </div>
                            <h5>No Validation Requests</h5>
                            <p class="text-muted">You haven't submitted any validation requests yet.</p>
                            <a href="/data-validation/create" class="btn btn-primary">Request Data Validation</a>
                        </div>
                    `);
                    $('#paginationContainer').empty();
                }
            } else {
                throw new Error('Failed to load validations');
            }
        } catch (error) {
            console.error('Error loading validations:', error);
            $('#validationsContainer').html(`
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Failed to load validations.
                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="loadValidations()">
                        Try Again
                    </button>
                </div>
            `);
        } finally {
            jobSeekerAPI.hideLoading('#validationsContainer');
        }
    }

    async function loadJobCategories() {
        try {
            const response = await jobSeekerAPI.getJobCategories();

            if (response.success && response.data) {
                const categories = response.data;
                const categorySelect = $('#categoryFilter');

                categories.forEach(category => {
                    categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
                });
            }
        } catch (error) {
            console.error('Error loading job categories:', error);
        }
    }

    function renderValidationCard(validation) {
        const statusConfig = getStatusConfig(validation.status);
        const categoryName = validation.job_category ? validation.job_category.name : 'Not specified';

        return `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">${validation.position}</h6>
                        <span class="badge ${statusConfig.class}">${statusConfig.label}</span>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th>Category:</th>
                                <td>${categoryName}</td>
                            </tr>
                            <tr>
                                <th>Experience:</th>
                                <td>${validation.has_experience ? 'Yes' : 'No'}</td>
                            </tr>
                            <tr>
                                <th>Submitted:</th>
                                <td>${new Date(validation.created_at).toLocaleDateString()}</td>
                            </tr>
                        </table>

                        ${validation.status === 'pending' ? `
                            <div class="alert alert-info small mb-2">
                                <i class="fas fa-clock"></i> Being reviewed by our team.
                            </div>
                        ` : ''}

                        ${validation.status === 'accepted' ? `
                            <div class="alert alert-success small mb-2">
                                <i class="fas fa-check-circle"></i> Validation accepted!
                            </div>
                        ` : ''}

                        ${validation.status === 'rejected' ? `
                            <div class="alert alert-danger small mb-2">
                                <i class="fas fa-times-circle"></i> Validation rejected.
                            </div>
                        ` : ''}

                        ${validation.validator_notes ? `
                            <div class="small">
                                <strong>Notes:</strong> ${validation.validator_notes}
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    function renderSkeletonLoading() {
        let skeletons = '';
        for (let i = 0; i < 6; i++) {
            skeletons += `
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="bg-light rounded" style="height: 1.5rem; width: 60%;"></div>
                        </div>
                        <div class="card-body">
                            <div class="bg-light rounded mb-2" style="height: 1rem;"></div>
                            <div class="bg-light rounded mb-2" style="height: 1rem;"></div>
                            <div class="bg-light rounded mb-2" style="height: 1rem;"></div>
                        </div>
                    </div>
                </div>
            `;
        }
        return `<div class="row">${skeletons}</div>`;
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

    function getStatusConfig(status) {
        switch(status) {
            case 'pending':
                return { class: 'bg-warning text-dark', label: 'Pending' };
            case 'accepted':
                return { class: 'bg-success', label: 'Accepted' };
            case 'rejected':
                return { class: 'bg-danger', label: 'Rejected' };
            default:
                return { class: 'bg-secondary', label: 'Unknown' };
        }
    }

    // Make functions globally accessible
    window.loadValidations = loadValidations;
});
</script>
@endpush

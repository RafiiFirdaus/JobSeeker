@extends('layouts.app')

@section('title', 'Job Vacancies - Job Seekers Platform')

@section('header')
    <h1 class="display-4">Job Vacancies</h1>
@endsection

@section('content')
<div class="mb-5">

    <div class="section-header mb-4">
        <h4 class="section-title text-muted font-weight-normal">List of Job Vacancies</h4>

        <!-- Filter Container -->
        <div class="filter-container mt-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="search-input-container">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control search-input" id="searchInput" placeholder="Search job vacancies...">
                    </div>
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
    </div>

    <div class="section-body job-vacancies-container" id="jobVacanciesContainer">
        <!-- Job vacancies will be loaded here via AJAX -->
    </div>

    <!-- Pagination Container -->
    <div id="paginationContainer" class="d-flex justify-content-center mt-4">
        <!-- Pagination will be loaded here via AJAX -->
    </div>

</div>


@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentPage = 1;
    let currentFilters = {};

    // Load initial data
    loadJobVacancies();
    loadJobCategories();

    // Search functionality
    let searchTimeout;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentFilters.search = $(this).val();
            currentPage = 1;
            loadJobVacancies();
        }, 500);
    });

    // Category filter
    $('#categoryFilter').on('change', function() {
        currentFilters.category_id = $(this).val();
        currentPage = 1;
        loadJobVacancies();
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#searchInput').val('');
        $('#categoryFilter').val('');
        currentFilters = {};
        currentPage = 1;
        loadJobVacancies();
    });

    // Pagination click handler
    $(document).on('click', '.pagination-ajax .page-link', function(e) {
        e.preventDefault();
        if (!$(this).parent().hasClass('disabled') && !$(this).parent().hasClass('active')) {
            currentPage = parseInt($(this).data('page'));
            loadJobVacancies();
        }
    });

    // Job vacancy card click handler
    $(document).on('click', '.vacancy-detail-btn', function(e) {
        e.preventDefault();
        const vacancyId = $(this).data('vacancy-id');
        if (!$(this).hasClass('disabled')) {
            window.location.href = `/job-vacancies/${vacancyId}`;
        }
    });

    async function loadJobVacancies() {
        try {
            jobSeekerAPI.showLoading('#jobVacanciesContainer');

            // Show skeleton loading
            $('#jobVacanciesContainer').html(
                jobSeekerAPI.renderJobVacancySkeleton() +
                jobSeekerAPI.renderJobVacancySkeleton() +
                jobSeekerAPI.renderJobVacancySkeleton()
            );

            const response = await jobSeekerAPI.getJobVacancies(currentPage, currentFilters);

            if (response.success && response.data) {
                const vacancies = response.data.data || response.data;
                const pagination = response.data.pagination || {};

                if (vacancies.length > 0) {
                    let vacanciesHtml = '';
                    vacancies.forEach(vacancy => {
                        vacanciesHtml += jobSeekerAPI.renderJobVacancyCard(vacancy);
                    });
                    $('#jobVacanciesContainer').html(vacanciesHtml);

                    // Render pagination
                    if (pagination.total_pages > 1) {
                        const paginationHtml = jobSeekerAPI.renderPagination(
                            pagination.current_page || currentPage,
                            pagination.total_pages,
                            'loadJobVacancies'
                        );
                        $('#paginationContainer').html(paginationHtml);
                    } else {
                        $('#paginationContainer').empty();
                    }
                } else {
                    $('#jobVacanciesContainer').html(
                        jobSeekerAPI.renderEmptyState(
                            'No Job Vacancies Found',
                            'There are no job vacancies matching your criteria.',
                            'Clear Filters',
                            '#'
                        )
                    );
                    $('#paginationContainer').empty();
                }
            } else {
                throw new Error(response.message || 'Failed to load job vacancies');
            }
        } catch (error) {
            console.error('Error loading job vacancies:', error);

            if (error.status === 401) {
                $('#jobVacanciesContainer').html(`
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        Authentication failed. Please login again.
                        <br>
                        <a href="/login" class="btn btn-primary mt-2">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </div>
                `);
            } else {
                $('#jobVacanciesContainer').html(`
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        Failed to load job vacancies. Please try again.
                        <br>
                        <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadJobVacancies()">
                            <i class="fas fa-redo"></i> Retry
                        </button>
                    </div>
                `);
            }
            $('#paginationContainer').empty();
        } finally {
            jobSeekerAPI.hideLoading('#jobVacanciesContainer');
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

    // Make loadJobVacancies globally accessible for retry button
    window.loadJobVacancies = loadJobVacancies;
});
</script>
@endpush

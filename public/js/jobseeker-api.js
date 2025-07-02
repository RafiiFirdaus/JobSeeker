/**
 * JobSeeker Platform AJAX Utilities
 * Centralized AJAX functions for API communication
 */

class JobSeekerAPI {
    constructor() {
        this.baseURL = '/api/v1';
        this.token = this.getStoredToken();
        this.setupDefaults();
    }

    setupDefaults() {
        // Set default headers for all requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
    }

    // Token management
    getStoredToken() {
        return localStorage.getItem('api_token') || sessionStorage.getItem('api_token');
    }

    setToken(token) {
        this.token = token;
        localStorage.setItem('api_token', token);
    }

    clearToken() {
        this.token = null;
        localStorage.removeItem('api_token');
        sessionStorage.removeItem('api_token');
    }

    // Loading indicators
    showLoading(element = 'body') {
        if (element === 'body') {
            $('body').append(`
                <div class="loading-overlay" id="globalLoading">
                    <div class="loading-spinner"></div>
                </div>
            `);
        } else {
            $(element).addClass('position-relative').append(`
                <div class="table-loading">
                    <div class="loading-spinner"></div>
                </div>
            `);
        }
    }

    hideLoading(element = 'body') {
        if (element === 'body') {
            $('#globalLoading').remove();
        } else {
            $(element).find('.table-loading').remove();
        }
    }

    // Button loading state
    setButtonLoading(button, loading = true) {
        const $btn = $(button);
        if (loading) {
            $btn.addClass('btn-loading').prop('disabled', true);
            $btn.data('original-text', $btn.html());
            $btn.html('<span class="me-2">Loading...</span>');
        } else {
            $btn.removeClass('btn-loading').prop('disabled', false);
            $btn.html($btn.data('original-text') || $btn.html());
        }
    }

    // Error handling
    showError(message, container = '.alert-container') {
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        if ($(container).length) {
            $(container).html(alertHtml);
        } else {
            $('main .container').prepend(alertHtml);
        }

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Success messages
    showSuccess(message, container = '.alert-container') {
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        if ($(container).length) {
            $(container).html(alertHtml);
        } else {
            $('main .container').prepend(alertHtml);
        }

        // Auto-dismiss after 3 seconds
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 3000);
    }

    // Enhanced alert method with custom styling
    showAlert(type, title, message, container = '.alert-container') {
        const alertClass = type === 'error' ? 'alert-danger' : `alert-${type}`;
        const iconClass = {
            'success': 'fas fa-check-circle',
            'error': 'fas fa-exclamation-circle',
            'warning': 'fas fa-exclamation-triangle',
            'info': 'fas fa-info-circle'
        }[type] || 'fas fa-info-circle';

        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show alert-ajax" role="alert">
                <div class="d-flex align-items-center">
                    <i class="${iconClass} me-2"></i>
                    <div>
                        <strong>${title}</strong><br>
                        <small>${message}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        if ($(container).length) {
            $(container).prepend(alertHtml);
        } else {
            $('main .container').prepend(alertHtml);
        }

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            $('.alert-ajax').fadeOut();
        }, 5000);
    }

    // Form validation errors
    showFormErrors(errors, form) {
        // Clear previous errors
        $(form).find('.is-invalid').removeClass('is-invalid');
        $(form).find('.invalid-feedback').remove();

        // Show field-specific errors
        for (const field in errors) {
            const $field = $(form).find(`[name="${field}"], [name="${field}[]"]`);
            $field.addClass('is-invalid');

            const errorHtml = `<div class="invalid-feedback">${errors[field][0]}</div>`;
            $field.closest('.form-group, .mb-3').append(errorHtml);
        }
    }

    // HTTP Headers helper
    getHeaders() {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        };

        if (this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }

        return headers;
    }

    // Session-based request method for web authentication
    async makeSessionRequest(url, options = {}) {
        const defaultOptions = {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'  // Include session cookies
        };

        const mergedOptions = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers
            }
        };

        return await fetch(url, mergedOptions);
    }

    // Response handler
    async handleResponse(response) {
        const responseData = await response.json().catch(() => ({}));

        if (!response.ok) {
            console.error('API Response Error:', {
                status: response.status,
                statusText: response.statusText,
                data: responseData
            });

            const error = new Error(responseData.message || `HTTP error! status: ${response.status}`);
            error.status = response.status;
            error.errors = responseData.errors || {};
            error.data = responseData;
            throw error;
        }

        return responseData;
    }

    // Error handler
    handleError(error, defaultMessage = 'An error occurred') {
        console.error('API Error:', error);

        // Handle validation errors
        if (error.errors && Object.keys(error.errors).length > 0) {
            const firstErrorKey = Object.keys(error.errors)[0];
            const firstError = error.errors[firstErrorKey][0];
            this.showError(firstError);
            return error;
        }

        // Handle HTTP errors
        if (error.status) {
            switch (error.status) {
                case 401:
                    this.showError('Authentication required. Please login again.');
                    this.clearToken();
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                    break;
                case 403:
                    this.showError('Access denied. You do not have permission to perform this action.');
                    break;
                case 404:
                    this.showError('The requested resource was not found.');
                    break;
                case 422:
                    this.showError(error.message || 'Validation failed. Please check your input.');
                    break;
                case 500:
                    this.showError('Server error. Please try again later.');
                    break;
                default:
                    this.showError(error.message || defaultMessage);
            }
        } else {
            // Handle network errors
            this.showError(error.message || defaultMessage);
        }

        return error;
    }

    // API Methods    // Authentication
    async login(credentials) {
        try {
            console.log('Attempting login with:', { nik: credentials.nik, password: '[HIDDEN]' });

            // Use traditional Laravel login for now
            const response = await fetch('/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                body: new URLSearchParams({
                    id_card_number: credentials.nik,  // Changed to 'id_card_number'
                    password: credentials.password
                })
            });

            console.log('Login response status:', response.status);

            if (response.ok) {
                // Check if it's a JSON response or redirect
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const result = await response.json();
                    console.log('JSON login response:', result);
                    return result;
                } else {
                    // Traditional form submission redirect - consider it successful
                    console.log('Traditional login successful (redirect response)');
                    return {
                        success: true,
                        message: 'Login successful',
                        redirect: response.url
                    };
                }
            } else {
                // Handle error response
                const errorData = await response.json().catch(() => ({
                    message: 'Login failed',
                    errors: { credentials: ['Invalid credentials'] }
                }));

                const error = new Error(errorData.message || 'Login failed');
                error.status = response.status;
                error.errors = errorData.errors || {};
                throw error;
            }
        } catch (error) {
            console.error('Login error details:', error);
            this.handleError(error, 'Login failed');
            throw error;
        }
    }

    async register(userData) {
        try {
            const response = await fetch(`${this.baseURL}/auth/register`, {
                method: 'POST',
                headers: this.getHeaders(),
                body: JSON.stringify(userData)
            });

            return await this.handleResponse(response);
        } catch (error) {
            this.handleError(error, 'Registration failed');
            throw error;
        }
    }

    async logout() {
        try {
            // Use session-based Laravel logout instead of API
            const response = await this.makeSessionRequest('/logout', {
                method: 'POST'
            });

            // Laravel logout typically redirects, so we consider it successful if no error
            this.clearToken();
            return {
                success: true,
                message: 'Logged out successfully'
            };
        } catch (error) {
            // Clear token even if logout fails
            this.clearToken();
            console.warn('Logout API failed, but clearing local session:', error);
            // Don't throw error for logout - just clear local data
            return {
                success: true,
                message: 'Logged out locally'
            };
        }
    }

    // User Status
    async getUserStatus(vacancyId = null) {
        try {
            const params = vacancyId ? `?vacancy_id=${vacancyId}` : '';
            const response = await this.makeSessionRequest(`/ajax/user-status${params}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            return await this.handleResponse(response);
        } catch (error) {
            this.handleError(error, 'Failed to get user status');
            throw error;
        }
    }

    // Data Validation
    async submitValidation(validationData) {
        try {
            // Use session-based web route instead of API
            const response = await this.makeSessionRequest('/ajax/validation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(validationData)
            });

            return await this.handleResponse(response);
        } catch (error) {
            this.handleError(error, 'Failed to submit validation');
            throw error;
        }
    }

    async getValidations(page = 1, filters = {}) {
        try {
            const params = new URLSearchParams({
                page: page,
                ...filters
            });

            // Use session-based web route instead of API
            const response = await this.makeSessionRequest(`/ajax/validations?${params}`, {
                method: 'GET'
            });

            return await this.handleResponse(response);
        } catch (error) {
            this.handleError(error, 'Failed to fetch validations');
            throw error;
        }
    }

    async getValidationHistory() {
        try {
            const response = await $.ajax({
                url: `${this.baseURL}/validations/history`,
                method: 'GET',
                data: { token: this.token }
            });

            return response;
        } catch (error) {
            throw this.handleError(error);
        }
    }

    async getValidationHistory(page = 1, filters = {}) {
        try {
            const params = new URLSearchParams({
                page: page,
                ...filters
            });

            const response = await fetch(`${this.baseURL}/validations/history?${params}`, {
                method: 'GET',
                headers: this.getHeaders()
            });

            return await this.handleResponse(response);
        } catch (error) {
            this.handleError(error, 'Failed to fetch validation history');
            throw error;
        }
    }

    // Job Categories
    async getJobCategories() {
        try {
            // Use session-based web route instead of API
            const response = await this.makeSessionRequest('/ajax/job-categories', {
                method: 'GET'
            });

            return await this.handleResponse(response);
        } catch (error) {
            this.handleError(error, 'Failed to fetch job categories');
            throw error;
        }
    }

    // Job Vacancies API
    async getJobVacancies(page = 1, filters = {}) {
        try {
            const params = new URLSearchParams({
                page: page,
                ...filters
            });

            // Use session-based web route instead of API
            const response = await this.makeSessionRequest(`/ajax/job-vacancies?${params}`, {
                method: 'GET'
            });

            return await this.handleResponse(response);
        } catch (error) {
            this.handleError(error, 'Failed to fetch job vacancies');
            throw error;
        }
    }

    async getJobVacancy(id) {
        try {
            // Use session-based web route instead of API
            const response = await this.makeSessionRequest(`/ajax/job-vacancies/${id}`, {
                method: 'GET'
            });

            return await this.handleResponse(response);
        } catch (error) {
            this.handleError(error, 'Failed to fetch job vacancy details');
            throw error;
        }
    }

    async getJobVacanciesByCategory(categoryId, page = 1) {
        try {
            const params = new URLSearchParams({
                page: page,
                category_id: categoryId
            });

            // Use session-based web route instead of API
            const response = await this.makeSessionRequest(`/ajax/job-vacancies?${params}`, {
                method: 'GET'
            });

            return await this.handleResponse(response);
        } catch (error) {
            this.handleError(error, 'Failed to fetch job vacancies by category');
            throw error;
        }
    }

    // Job Applications API
    async submitJobApplication(applicationData) {
        try {
            const response = await fetch(`${this.baseURL}/applications`, {
                method: 'POST',
                headers: this.getHeaders(),
                body: JSON.stringify(applicationData)
            });

            return await this.handleResponse(response);
        } catch (error) {
            this.handleError(error, 'Failed to submit job application');
            throw error;
        }
    }

    async getJobApplications(page = 1, filters = {}) {
        try {
            const params = new URLSearchParams({
                page: page,
                ...filters
            });

            // Use session-based web route instead of API
            const response = await this.makeSessionRequest(`/ajax/applications?${params}`, {
                method: 'GET'
            });

            return await this.handleResponse(response);
        } catch (error) {
            this.handleError(error, 'Failed to fetch job applications');
            throw error;
        }
    }

    async getJobApplication(id) {
        try {
            const response = await fetch(`${this.baseURL}/applications/${id}`, {
                method: 'GET',
                headers: this.getHeaders()
            });

            return await this.handleResponse(response);
        } catch (error) {
            this.handleError(error, 'Failed to fetch job application');
            throw error;
        }
    }

    // UI Helpers for Job Vacancies
    renderJobVacancyCard(vacancy) {
        const hasApplied = vacancy.has_applied || false;
        const buttonText = hasApplied ? 'Already Applied' : 'Detail / Apply';
        const buttonClass = hasApplied ? 'btn-success' : 'btn-danger';
        const buttonDisabled = hasApplied ? 'disabled' : '';

        return `
            <article class="spot job-vacancy-item" data-vacancy-id="${vacancy.id}">
                <div class="row">
                    <div class="col-5">
                        <h5 class="text-primary">${vacancy.company}</h5>
                        <span class="text-muted">${vacancy.address || 'Address not specified'}</span>
                    </div>
                    <div class="col-4">
                        <h5>Available Position (Capacity)</h5>
                        <span class="text-muted">
                            ${this.renderPositionsList(vacancy.available_positions)}
                        </span>
                    </div>
                    <div class="col-3">
                        <button class="btn ${buttonClass} btn-lg w-100 vacancy-detail-btn"
                                data-vacancy-id="${vacancy.id}" ${buttonDisabled}>
                            ${buttonText}
                        </button>
                    </div>
                </div>
            </article>
        `;
    }

    renderPositionsList(positions) {
        if (!positions || positions.length === 0) {
            return 'No positions available';
        }

        return positions.map(position =>
            `${position.position_name || position.position} (${position.capacity})`
        ).join(', ');
    }

    renderJobVacancySkeleton() {
        return `
            <article class="spot job-vacancy-skeleton">
                <div class="row">
                    <div class="col-5">
                        <div class="bg-light rounded" style="height: 1.5rem; margin-bottom: 0.5rem;"></div>
                        <div class="bg-light rounded" style="height: 1rem; width: 70%;"></div>
                    </div>
                    <div class="col-4">
                        <div class="bg-light rounded" style="height: 1.5rem; margin-bottom: 0.5rem;"></div>
                        <div class="bg-light rounded" style="height: 1rem;"></div>
                    </div>
                    <div class="col-3">
                        <div class="bg-light rounded" style="height: 3rem;"></div>
                    </div>
                </div>
            </article>
        `;
    }

    renderJobApplicationCard(application) {
        const statusColors = {
            'pending': 'warning',
            'accepted': 'success',
            'rejected': 'danger'
        };

        return `
            <div class="col-md-6 mb-4">
                <div class="card h-100 application-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="company-name">${application.job_vacancy.company}</h5>
                            <span class="badge badge-info">
                                ${this.formatDate(application.created_at)}
                            </span>
                        </div>

                        <p class="text-muted mb-2">
                            <i class="fas fa-map-marker-alt"></i> ${application.job_vacancy.address}
                        </p>

                        <span class="job-category mb-3">
                            ${application.job_vacancy.job_category?.name || 'N/A'}
                        </span>

                        <div class="mt-3">
                            <h6><strong>Applied Positions:</strong></h6>
                            ${this.renderApplicationPositions(application.job_apply_positions)}
                        </div>

                        ${application.notes ? `
                            <div class="mt-3">
                                <h6><strong>Notes:</strong></h6>
                                <p class="text-muted small">
                                    ${application.notes.substring(0, 100)}${application.notes.length > 100 ? '...' : ''}
                                </p>
                            </div>
                        ` : ''}
                    </div>

                    <div class="card-footer d-flex justify-content-between">
                        <button class="btn btn-outline-secondary btn-sm view-job-btn" data-vacancy-id="${application.job_vacancy.id}">
                            <i class="fas fa-eye"></i> View Job
                        </button>
                        <button class="btn btn-primary btn-sm view-application-btn" data-application-id="${application.id}">
                            <i class="fas fa-info-circle"></i> Details
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    renderApplicationPositions(positions) {
        if (!positions || positions.length === 0) {
            return '<span class="text-muted">No positions</span>';
        }

        return positions.map(positionApp => {
            const statusClass = positionApp.status === 'accepted' ? 'applied' :
                              (positionApp.status === 'rejected' ? 'full' : '');
            const statusIndicator = positionApp.status === 'accepted' ? 'applied' :
                                  (positionApp.status === 'rejected' ? 'full' : 'available');

            return `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="position-badge ${statusClass}">
                        ${positionApp.available_position?.position || 'Unknown'}
                    </span>
                    <span class="status-indicator ${statusIndicator}">
                        ${positionApp.status ? positionApp.status.charAt(0).toUpperCase() + positionApp.status.slice(1) : 'Pending'}
                    </span>
                </div>
            `;
        }).join('');
    }

    renderEmptyState(title, message, actionText = null, actionUrl = null) {
        return `
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h4 class="text-muted">${title}</h4>
                <p class="text-muted mb-4">${message}</p>
                ${actionText && actionUrl ? `
                    <a href="${actionUrl}" class="btn btn-primary">
                        <i class="fas fa-search"></i> ${actionText}
                    </a>
                ` : ''}
            </div>
        `;
    }

    renderPagination(currentPage, totalPages, onPageClick) {
        if (totalPages <= 1) return '';

        let pagination = '<nav aria-label="Page navigation"><ul class="pagination pagination-ajax">';

        // Previous button
        pagination += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            pagination += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        // Next button
        pagination += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
            </li>
        `;

        pagination += '</ul></nav>';

        return pagination;
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    // Template helpers
    renderStatusBadge(status) {
        const statusClasses = {
            'pending': 'badge-warning',
            'accepted': 'badge-success',
            'rejected': 'badge-danger'
        };

        return `<span class="badge ${statusClasses[status] || 'badge-secondary'}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
    }

    renderJobCategoryBadge(categoryName) {
        return `<span class="job-category">${categoryName}</span>`;
    }
}

// Initialize global API instance
window.jobSeekerAPI = new JobSeekerAPI();

// Document ready initialization
$(document).ready(function() {
    // Add alert container if not exists
    if (!$('.alert-container').length) {
        $('main .container').prepend('<div class="alert-container"></div>');
    }

    // Auto-dismiss alerts
    $(document).on('click', '.alert .btn-close', function() {
        $(this).closest('.alert').fadeOut();
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        $('.alert').not('.alert-permanent').fadeOut();
    }, 5000);
});

// Create global instance
window.jobSeekerAPI = new JobSeekerAPI();

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = JobSeekerAPI;
}

@extends('layouts.app')

@section('title', 'Apply for Job')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-briefcase"></i> Apply for Job</h4>
                </div>
                <div class="card-body">
                    <!-- Job Vacancy Information -->
                    <div id="jobVacancyInfo" class="job-vacancy-card mb-4">
                        <!-- Job vacancy info will be loaded via AJAX -->
                    </div>

                    <!-- Application Form -->
                    <form id="applicationForm" novalidate>
                        <input type="hidden" id="jobVacancyId" name="job_vacancy_id" value="{{ request()->route('vacancyId') }}">

                        <!-- Available Positions -->
                        <div class="form-group mb-4">
                            <label class="form-label"><strong>Available Positions:</strong></label>
                            <p class="text-muted small">Select the position(s) you want to apply for:</p>

                            <div id="positionsContainer" class="position-selection">
                                <!-- Positions will be loaded here via AJAX -->
                            </div>
                            <div class="invalid-feedback-ajax" id="positionsError" style="display: none;"></div>
                        </div>

                        <!-- Application Notes -->
                        <div class="form-group mb-4">
                            <label for="notes" class="form-label"><strong>Application Notes:</strong></label>
                            <textarea
                                class="form-control"
                                id="notes"
                                name="notes"
                                rows="5"
                                placeholder="Tell us why you're interested in this position and why you'd be a good fit..."
                                required></textarea>
                            <div class="invalid-feedback-ajax" id="notesError" style="display: none;"></div>
                            <small class="form-text text-muted">
                                Please provide a brief explanation of your interest and qualifications (required).
                            </small>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" id="cancelBtn">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-paper-plane"></i> Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const vacancyId = {{ request()->route('vacancyId') }};
    let selectedPositions = [];

    // Load job vacancy details
    loadJobVacancyInfo(vacancyId);

    // Form submission
    $('#applicationForm').on('submit', function(e) {
        e.preventDefault();
        submitApplication();
    });

    // Position selection handling
    $(document).on('change', '.position-checkbox input[type="checkbox"]', function() {
        const positionId = $(this).val();
        const positionCard = $(this).closest('.position-checkbox');

        if ($(this).is(':checked')) {
            selectedPositions.push(positionId);
            positionCard.addClass('selected');
        } else {
            selectedPositions = selectedPositions.filter(id => id !== positionId);
            positionCard.removeClass('selected');
        }

        // Clear validation error if positions are selected
        if (selectedPositions.length > 0) {
            clearFieldError('positions');
        }
    });

    // Cancel button
    $('#cancelBtn').on('click', function(e) {
        e.preventDefault();
        window.history.back();
    });

    async function loadJobVacancyInfo(vacancyId) {
        try {
            jobSeekerAPI.showLoading('#jobVacancyInfo');

            const response = await jobSeekerAPI.getJobVacancy(vacancyId);

            if (response.success && response.data) {
                const vacancy = response.data;

                // Update page title
                document.title = `Apply for Job - ${vacancy.company}`;

                // Render job info
                $('#jobVacancyInfo').html(renderJobVacancyInfo(vacancy));

                // Render available positions
                $('#positionsContainer').html(renderAvailablePositions(vacancy.available_positions));

            } else {
                throw new Error(response.message || 'Failed to load job vacancy details');
            }
        } catch (error) {
            console.error('Error loading job vacancy:', error);
            $('#jobVacancyInfo').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Failed to load job vacancy details. Please try again.
                </div>
            `);
            $('#positionsContainer').html(`
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Unable to load available positions.
                </div>
            `);
        } finally {
            jobSeekerAPI.hideLoading('#jobVacancyInfo');
        }
    }

    function renderJobVacancyInfo(vacancy) {
        return `
            <div class="row">
                <div class="col-md-8">
                    <h5 class="company-name">${vacancy.company}</h5>
                    <p class="text-muted mb-2">
                        <i class="fas fa-map-marker-alt"></i> ${vacancy.address || 'Address not specified'}
                    </p>
                    <span class="job-category">${vacancy.job_category?.name || 'N/A'}</span>
                </div>
            </div>
            <div class="mt-3">
                <p><strong>Description:</strong></p>
                <p>${vacancy.description || 'Job description not available.'}</p>
            </div>
        `;
    }

    function renderAvailablePositions(positions) {
        if (!positions || positions.length === 0) {
            return `
                <div class="alert alert-warning">
                    No positions available for this job vacancy.
                </div>
            `;
        }

        let positionsHtml = '<div class="row">';

        positions.forEach(position => {
            const applicationCount = position.application_count || 0;
            const applyCapacity = position.apply_capacity || position.capacity;
            const isFull = applicationCount >= applyCapacity;

            positionsHtml += `
                <div class="col-md-6 mb-3">
                    <div class="form-check position-checkbox p-3 border rounded ${isFull ? 'disabled' : ''}">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="selected_positions[]"
                            value="${position.id}"
                            id="position${position.id}"
                            ${isFull ? 'disabled' : ''}
                        >
                        <label class="form-check-label w-100" for="position${position.id}">
                            <strong>${position.position}</strong>
                            <br>
                            <small class="text-muted">
                                Capacity: ${position.capacity} |
                                Max Applications: ${applyCapacity}
                                <br>
                                Applied: ${applicationCount}/${applyCapacity}
                                ${isFull ? '<span class="text-danger">(Full)</span>' : ''}
                            </small>
                        </label>
                    </div>
                </div>
            `;
        });

        positionsHtml += '</div>';
        return positionsHtml;
    }

    async function submitApplication() {
        try {
            // Clear previous errors
            clearAllErrors();

            // Client-side validation
            if (!validateForm()) {
                return;
            }

            jobSeekerAPI.setButtonLoading('#submitBtn', true);

            const applicationData = {
                job_vacancy_id: parseInt(vacancyId),
                selected_positions: selectedPositions.map(id => parseInt(id)),
                notes: $('#notes').val().trim()
            };

            const response = await jobSeekerAPI.submitJobApplication(applicationData);

            if (response.success) {
                jobSeekerAPI.showAlert('success', 'Application Submitted Successfully!',
                    'Your job application has been submitted. You can track its status in your applications dashboard.');

                // Redirect to applications page after a short delay
                setTimeout(() => {
                    window.location.href = '/job-applications';
                }, 2000);

            } else {
                // Handle validation errors
                if (response.errors) {
                    displayValidationErrors(response.errors);
                } else {
                    throw new Error(response.message || 'Failed to submit application');
                }
            }
        } catch (error) {
            console.error('Error submitting application:', error);

            if (error.status === 422 && error.responseJSON?.errors) {
                displayValidationErrors(error.responseJSON.errors);
            } else if (error.status === 401) {
                jobSeekerAPI.showAlert('warning', 'Authentication Required',
                    'Please log in to submit your application.');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            } else {
                jobSeekerAPI.showAlert('error', 'Submission Failed',
                    error.message || 'Failed to submit your application. Please try again.');
            }
        } finally {
            jobSeekerAPI.setButtonLoading('#submitBtn', false);
        }
    }

    function validateForm() {
        let isValid = true;

        // Validate positions selection
        if (selectedPositions.length === 0) {
            showFieldError('positions', 'Please select at least one position to apply for.');
            isValid = false;
        }

        // Validate notes
        const notes = $('#notes').val().trim();
        if (!notes) {
            showFieldError('notes', 'Application notes are required.');
            isValid = false;
        } else if (notes.length < 10) {
            showFieldError('notes', 'Please provide more detailed application notes (at least 10 characters).');
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
        const fieldMap = {
            'selected_positions': 'positions',
            'notes': 'notes'
        };

        const mappedField = fieldMap[field] || field;
        const errorElement = $(`#${mappedField}Error`);
        const inputElement = mappedField === 'positions' ? $('#positionsContainer') : $(`#${mappedField}`);

        errorElement.text(message).show();
        inputElement.addClass('is-invalid-ajax');
    }

    function clearFieldError(field) {
        const errorElement = $(`#${field}Error`);
        const inputElement = field === 'positions' ? $('#positionsContainer') : $(`#${field}`);

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

@extends('layouts.app')

@section('title', 'Request Data Validation - Job Seekers Platform')

@section('header')
    <h1 class="display-4">Request Data Validation</h1>
@endsection

@section('content')
<form id="validationForm" class="ajax-form">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="form-group">
                <div class="d-flex align-items-center mb-3">
                    <label class="me-3 mb-0">Job Category</label>
                    <select class="form-select form-control-sm" name="job_category_id" id="jobCategorySelect" required>
                        <option value="">Select Category</option>
                        <!-- Options will be loaded via AJAX -->
                    </select>
                </div>
                <textarea class="form-control" name="job_position" cols="30" rows="5"
                          placeholder="Describe the job position you're interested in" required></textarea>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <div class="d-flex align-items-center mb-3">
                    <label class="me-3 mb-0">Work Experience</label>
                    <select class="form-select form-control-sm" name="work_experience" required>
                        <option value="">Select Option</option>
                        <option value="0-1 years">0-1 years</option>
                        <option value="1-3 years">1-3 years</option>
                        <option value="3-5 years">3-5 years</option>
                        <option value="5+ years">5+ years</option>
                    </select>
                </div>
                <textarea class="form-control" name="reason_accepted" cols="30" rows="5"
                          placeholder="Why should your application be accepted?" required>
                          {{ old('work_experiences') }}</textarea>
                @error('work_experiences')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <div class="d-flex align-items-center mb-3">
                    <label class="me-3 mb-0">Reason Accepted</label>
                </div>
                <textarea class="form-control @error('reason_accepted') is-invalid @enderror"
                          name="reason_accepted" cols="30" rows="6"
                          placeholder="Explain why you should be accepted" required>{{ old('reason_accepted') }}</textarea>
                @error('reason_accepted')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-primary me-2" id="submitValidationBtn">
            <i class="fas fa-paper-plane me-2"></i>Send Request
        </button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load job categories
    loadJobCategories();

    // Handle form submission
    $('#validationForm').on('submit', async function(e) {
        e.preventDefault();

        const $form = $(this);
        const $submitBtn = $('#submitValidationBtn');

        // Get form data
        const formData = {
            job_category_id: $form.find('[name="job_category_id"]').val(),
            job_position: $form.find('[name="job_position"]').val(),
            work_experience: $form.find('[name="work_experience"]').val(),
            reason_accepted: $form.find('[name="reason_accepted"]').val()
        };

        // Validate required fields
        const requiredFields = ['job_category_id', 'job_position', 'work_experience', 'reason_accepted'];
        let hasError = false;

        requiredFields.forEach(field => {
            if (!formData[field]) {
                $form.find(`[name="${field}"]`).addClass('invalid-field');
                hasError = true;
            } else {
                $form.find(`[name="${field}"]`).removeClass('invalid-field');
            }
        });

        if (hasError) {
            jobSeekerAPI.showError('Please fill in all required fields.');
            return;
        }

        try {
            // Set loading state
            jobSeekerAPI.setButtonLoading($submitBtn, true);

            // Call API
            const response = await jobSeekerAPI.submitValidation(formData);

            if (response.message) {
                jobSeekerAPI.showSuccess(response.message);

                // Reset form
                $form[0].reset();

                // Redirect to progress page after delay
                setTimeout(() => {
                    window.location.href = '/data-validation/progress';
                }, 2000);
            }

        } catch (error) {
            console.error('Validation submission error:', error);

            if (error.errors) {
                jobSeekerAPI.showFormErrors(error.errors, $form);
            }

            const message = error.message || 'Failed to submit validation request. Please try again.';
            jobSeekerAPI.showError(message);

        } finally {
            jobSeekerAPI.setButtonLoading($submitBtn, false);
        }
    });

    async function loadJobCategories() {
        try {
            const response = await jobSeekerAPI.getJobCategories();

            if (response.categories && Array.isArray(response.categories)) {
                const $select = $('#jobCategorySelect');
                $select.empty().append('<option value="">Select Category</option>');

                response.categories.forEach(category => {
                    $select.append(`<option value="${category.id}">${category.name}</option>`);
                });
            }

        } catch (error) {
            console.error('Failed to load job categories:', error);
            // Use fallback options
            const fallbackCategories = [
                {id: 1, name: 'Computing and ICT'},
                {id: 2, name: 'Construction and building'},
                {id: 3, name: 'Animals, land and environment'},
                {id: 4, name: 'Design, arts and crafts'},
                {id: 5, name: 'Education and training'}
            ];

            const $select = $('#jobCategorySelect');
            $select.empty().append('<option value="">Select Category</option>');

            fallbackCategories.forEach(category => {
                $select.append(`<option value="${category.id}">${category.name}</option>`);
            });
        }
    }
});
</script>
@endpush

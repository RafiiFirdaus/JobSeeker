@extends('layouts.app')

@section('title', 'Simple Test')

@section('content')
<div class="container">
    <h1>Simple Login & Job Vacancy Test</h1>

    <div class="row">
        <div class="col-md-6">
            <h3>Current Status</h3>
            <p><strong>Logged In:</strong> {{ Session::has('user_logged_in') ? 'Yes' : 'No' }}</p>
            @if(Session::has('user_logged_in'))
                <p><strong>User ID:</strong> {{ Session::get('user_id') }}</p>
                <p><strong>Name:</strong> {{ Session::get('user_name') }}</p>
                <p><strong>ID Card:</strong> {{ Session::get('user_id_card') }}</p>

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            @else
                <form method="POST" action="/login">
                    @csrf
                    <div class="mb-3">
                        <label>ID Card Number:</label>
                        <input type="text" name="id_card_number" value="20210001" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password:</label>
                        <input type="password" name="password" value="password123" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
            @endif
        </div>

        <div class="col-md-6">
            <h3>Job Vacancy Test</h3>
            <a href="/job-vacancies/1" class="btn btn-success">Go to Job Vacancy #1</a>

            <h4 class="mt-4">AJAX Test</h4>
            <button onclick="testUserStatus()" class="btn btn-info">Test User Status</button>
            <div id="result" class="mt-3"></div>
        </div>
    </div>
</div>

<script>
function testUserStatus() {
    $.get('/ajax/user-status?vacancy_id=1')
        .done(function(response) {
            $('#result').html('<div class="alert alert-success"><pre>' + JSON.stringify(response, null, 2) + '</pre></div>');
        })
        .fail(function(xhr) {
            $('#result').html('<div class="alert alert-danger">Error: ' + xhr.responseText + '</div>');
        });
}
</script>
@endsection

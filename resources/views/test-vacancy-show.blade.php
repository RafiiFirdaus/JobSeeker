@extends('layouts.app')

@section('title', 'Test Job Vacancy Show')

@section('content')
<div class="container">
    <h1>Test Job Vacancy Show</h1>

    <div class="alert alert-info">
        <h5>Server-side Status:</h5>
        <ul>
            <li>User Logged In: {{ Session::has('user_logged_in') ? 'Yes' : 'No' }}</li>
            <li>User ID: {{ Session::get('user_id', 'N/A') }}</li>
            <li>User Name: {{ Session::get('user_name', 'N/A') }}</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Login Form</div>
                <div class="card-body">
                    <form id="loginForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="id_card_number">ID Card Number</label>
                            <input type="text" class="form-control" id="id_card_number" name="id_card_number" value="123456789" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" value="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                        <button type="button" class="btn btn-secondary" onclick="logout()">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Test Results</div>
                <div class="card-body">
                    <div id="testResults"></div>
                    <button class="btn btn-info" onclick="testUserStatus()">Test User Status</button>
                    <button class="btn btn-success" onclick="testJobVacancy()">Test Job Vacancy #1</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Pass server data to client
window.userLoggedIn = {{ Session::has('user_logged_in') ? 'true' : 'false' }};
window.userId = {{ Session::get('user_id', 'null') }};
window.userName = '{{ addslashes(Session::get('user_name', '')) }}';

console.log('Server status:', {
    userLoggedIn: window.userLoggedIn,
    userId: window.userId,
    userName: window.userName
});

function addResult(title, data) {
    $('#testResults').append('<h6>' + title + '</h6><pre>' + JSON.stringify(data, null, 2) + '</pre><hr>');
}

function testUserStatus() {
    console.log('Testing user status...');
    $.get('/ajax/user-status')
        .done(function(response) {
            addResult('User Status API:', response);
        })
        .fail(function(xhr) {
            addResult('User Status API FAILED:', xhr.responseText);
        });
}

function testJobVacancy() {
    console.log('Testing job vacancy...');
    $.get('/ajax/job-vacancies/1')
        .done(function(response) {
            addResult('Job Vacancy API:', response);
        })
        .fail(function(xhr) {
            addResult('Job Vacancy API FAILED:', xhr.responseText);
        });
}

function logout() {
    $.post('/logout', {
        _token: $('input[name="_token"]').val()
    })
    .done(function(response) {
        addResult('Logout:', 'Success');
        location.reload();
    })
    .fail(function(xhr) {
        addResult('Logout FAILED:', xhr.responseText);
    });
}

$('#loginForm').on('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    $.post('/login', {
        id_card_number: formData.get('id_card_number'),
        password: formData.get('password'),
        _token: formData.get('_token')
    })
    .done(function(response) {
        addResult('Login:', 'Success');
        location.reload();
    })
    .fail(function(xhr) {
        addResult('Login FAILED:', xhr.responseText);
    });
});

$(document).ready(function() {
    addResult('Initial Status:', {
        serverSide: {
            userLoggedIn: window.userLoggedIn,
            userId: window.userId,
            userName: window.userName
        }
    });
});
</script>

@endsection

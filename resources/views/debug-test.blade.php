<!DOCTYPE html>
<html>
<head>
    <title>Test Debug</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Debug Test</h1>
    <div id="results"></div>

    <script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    function addResult(title, data) {
        $('#results').append('<h3>' + title + '</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>');
    }

    function testAll() {
        console.log('Testing all endpoints...');

        // Test debug-auth
        $.get('/debug-auth')
            .done(function(response) {
                addResult('Debug Auth:', response);
            })
            .fail(function(xhr) {
                addResult('Debug Auth FAILED:', xhr.responseText);
            });

        // Test user status
        $.get('/ajax/user-status')
            .done(function(response) {
                addResult('User Status:', response);
            })
            .fail(function(xhr) {
                addResult('User Status FAILED:', xhr.responseText);
            });

        // Test specific controller method
        $.get('/test-user-status')
            .done(function(response) {
                addResult('Test User Status Controller:', response);
            })
            .fail(function(xhr) {
                addResult('Test User Status Controller FAILED:', xhr.responseText);
            });
    }

    $(document).ready(function() {
        testAll();
    });
    </script>
</body>
</html>

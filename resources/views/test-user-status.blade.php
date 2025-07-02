<!DOCTYPE html>
<html>
<head>
    <title>Test User Status</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Test User Status Endpoint</h1>
    <div id="results"></div>

    <script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    function testUserStatus() {
        console.log('Testing user status endpoint...');

        // Test without vacancy ID
        $.get('/ajax/user-status')
            .done(function(response) {
                console.log('Test 1 (no vacancy):', response);
                $('#results').append('<h3>Test 1 (no vacancy):</h3><pre>' + JSON.stringify(response, null, 2) + '</pre>');
            })
            .fail(function(xhr) {
                console.error('Test 1 failed:', xhr);
                $('#results').append('<h3>Test 1 FAILED:</h3><pre>' + xhr.responseText + '</pre>');
            });

        // Test with vacancy ID
        $.get('/ajax/user-status?vacancy_id=1')
            .done(function(response) {
                console.log('Test 2 (with vacancy):', response);
                $('#results').append('<h3>Test 2 (with vacancy):</h3><pre>' + JSON.stringify(response, null, 2) + '</pre>');
            })
            .fail(function(xhr) {
                console.error('Test 2 failed:', xhr);
                $('#results').append('<h3>Test 2 FAILED:</h3><pre>' + xhr.responseText + '</pre>');
            });
    }

    $(document).ready(function() {
        testUserStatus();
    });
    </script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Cache Debug</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Freshservice Cache Debug</h1>
        
        <h2>Agents</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($agents as $key => $agent)
                    <tr>
                        <td>{{ $key }}</td>
                        <td><pre>{{ json_encode($agent, JSON_PRETTY_PRINT) }}</pre></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <h2>Groups</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groups as $key => $group)
                    <tr>
                        <td>{{ $key }}</td>
                        <td><pre>{{ json_encode($group, JSON_PRETTY_PRINT) }}</pre></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <h2>Departments</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($departments as $key => $department)
                    <tr>
                        <td>{{ $key }}</td>
                        <td><pre>{{ json_encode($department, JSON_PRETTY_PRINT) }}</pre></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <h2>Requesters</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requesters as $key => $requester)
                    <tr>
                        <td>{{ $key }}</td>
                        <td><pre>{{ json_encode($requester, JSON_PRETTY_PRINT) }}</pre></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>

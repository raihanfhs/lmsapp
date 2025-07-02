<!DOCTYPE html>
<html>
<head>
    <title>Executive Dashboard Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 20px; }
        h1, h2 { text-align: center; color: #333; }
        h2 { border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { text-align: right; font-size: 8px; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>Executive Dashboard Report</h1>
    <p style="text-align: center;">Generated on: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>

    <h2>1. Total Users by Role</h2>
    <table>
        <thead>
            <tr>
                <th>Role</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usersByRole as $item)
                <tr>
                    <td>{{ $item->role }}</td>
                    <td>{{ $item->count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>2. User Verification Status</h2>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $userVerificationStatus['labels'][0] }}</td>
                <td>{{ $userVerificationStatus['data'][0] }}</td>
            </tr>
            <tr>
                <td>{{ $userVerificationStatus['labels'][1] }}</td>
                <td>{{ $userVerificationStatus['data'][1] }}</td>
            </tr>
        </tbody>
    </table>

    <h2>3. New User Registrations Over Time</h2>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>New Users</th>
            </tr>
        </thead>
        <tbody>
            @foreach($userRegistrationTrends['labels'] as $key => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $userRegistrationTrends['data'][$key] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>4. Courses by Status</h2>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courseStatusData['labels'] as $key => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $courseStatusData['data'][$key] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        Page 1 of 1
    </div>
</body>
</html>
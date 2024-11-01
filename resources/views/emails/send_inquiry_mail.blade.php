<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $details['email'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #FF9948;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .content {
            padding: 20px;
        }
        .footer {
            text-align: center;
            padding: 10px;
            background-color: #FF9948;
            color: #fff;
        }
        table {
            width: 100%;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #FF9948;
            color: #fff;
        }
        .even {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ $details['title'] }}</h1>
    </div>
    <div class="content">
        <table>
            <tr>
                <th>Lead Field Names</th>
                <th>Lead Details</th>
            </tr>
            <tr>
                <td>Contact Name:</td>
                <td>{{ $details['name'] }}</td>
            </tr>
            <tr class="even">
                <td>Email:</td>
                <td>{{ $details['email'] }}</td>
            </tr>
            <tr>
                <td>Phone:</td>
                <td>{{ $details['phone'] }}</td>
            </tr>
            <tr class="even">
                <td>Details:</td>
                <td>{{ $details['details'] }}</td>
            </tr>
            <tr>
                <td>Lead Source:</td>
                <td>{{ $details['source'] }}</td>
            </tr>
            <tr class="even">
                <td>Lead Value:</td>
                <td>${{ $details['value'] }}/-</td>
            </tr>
            <tr>
                <td>Lead IP:</td>
                <td>{{ $details['lead_ip'] }}</td>
            </tr>
            <tr class="even">
                <td>Lead City:</td>
                <td>{{ $details['lead_city'] }}</td>
            </tr>
            <tr>
                <td>Lead State:</td>
                <td>{{ $details['lead_state'] }}</td>
            </tr>
            <tr class="even">
                <td>Lead Zip:</td>
                <td>{{ $details['lead_zip'] }}</td>
            </tr>
            <tr>
                <td>Lead Country:</td>
                <td>{{ $details['lead_country'] }}</td>
            </tr>
            <tr class="even">
                <td>Lead URL:</td>
                <td>{{ $details['lead_url'] }}</td>
            </tr>
            <tr>
                <td>Keyword:</td>
                <td>{{ $details['keyword'] }}</td>
            </tr>
            <tr class="even">
                <td>Match Type:</td>
                <td>{{ $details['matchtype'] }}</td>
            </tr>
            <tr>
                <td>MSCLKID:</td>
                <td>{{ $details['msclkid'] }}</td>
            </tr>
            <tr class="even">
                <td>GCLID:</td>
                <td>{{ $details['gclid'] }}</td>
            </tr>
            <tr>
                <td>Server Response:</td>
                <td>{{ $details['server_response'] }}</td>
            </tr>
            <tr class="even">
                <td>Brand Name:</td>
                <td>{{ is_string($details['brand_name'] ?? "") ? $details['brand_name'] : "" }}</td>
            </tr>
            <tr>
                <td>Team Name:</td>
                <td>{{ is_string($details['team_name'] ?? "") ? $details['team_name'] : "" }}</td>
            </tr>
            @foreach ($details as $key => $value)
                @if (in_array($key, $details['more_details']))
                    <tr>
                        <td>{{ ucwords($key) }}:</td>
                        <td>{{ $value }}</td>
                    </tr>
                @endif
            @endforeach
        </table>
    </div>
    <div class="footer">
        <p>Thank you for using our service!</p>
    </div>
</div>
</body>
</html>

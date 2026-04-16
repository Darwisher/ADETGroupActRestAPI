<?php
$result = null;
$error = null;
$apiUrlUsed = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Determine the API URL dynamically so it works anywhere it's hosted
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['REQUEST_URI']);
    $path = rtrim($path, '/\\');
    
    // Construct the endpoint URI for the GET request
    $apiUrl = sprintf("%s://%s%s/service.php?user=%s&pass=%s", 
        $protocol, 
        $host, 
        $path, 
        urlencode($username), 
        urlencode($password)
    );
    
    $apiUrlUsed = $apiUrl; // Keep track of the URL for display purposes

    // Fetch the response
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        
        // Handle cURL errors natively (e.g. timeout, DNS resolution failure)
        if (curl_errno($ch)) {
            $error = "cURL Request Error: " . curl_error($ch);
        }
        curl_close($ch);
    } else {
        // Fallback to file_get_contents if curl is not installed
        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 10
            ]
        ]);
        $response = @file_get_contents($apiUrl, false, $context);
        if ($response === false) {
            $error = "Request Error: Unable to reach the API endpoint. URL: " . htmlspecialchars($apiUrl);
        }
    }

    // Process the API response if we successfully obtained it
    if (!$error && isset($response)) {
        $result = json_decode($response, true);
        // Error handling if response is not valid JSON
        if ($result === null) {
            $error = "Failed to parse JSON response. The server might have returned an invalid response. Raw response: " . htmlspecialchars($response);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBridge Client Portal</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 480px;
        }
        h2 { 
            margin-top: 0; 
            margin-bottom: 25px; 
            color: #212529; 
            text-align: center; 
            font-weight: 600;
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        label { 
            display: block; 
            margin-bottom: 8px; 
            color: #495057; 
            font-weight: 500; 
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #4dabf7;
            outline: none;
            box-shadow: 0 0 0 3px rgba(77, 171, 247, 0.2);
        }
        button {
            width: 100%;
            padding: 14px;
            background-color: #339af0;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.1s;
            margin-top: 5px;
        }
        button:hover {
            background-color: #228be6;
        }
        button:active {
            transform: translateY(1px);
        }
        .alert {
            margin-top: 25px;
            padding: 20px;
            border-radius: 8px;
            border-left: 6px solid transparent;
        }
        .alert-error { background-color: #ffe3e3; color: #c92a2a; border-left-color: #fa5252; }
        .alert-success { background-color: #d3f9d8; color: #2b8a3e; border-left-color: #40c057; }
        .alert-unauthorized { background-color: #fff3bf; color: #e67700; border-left-color: #fcc419; }
        .alert-incomplete { background-color: #f1f3f5; color: #495057; border-left-color: #adb5bd; }
        .alert h4 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .alert p {
            margin: 8px 0;
            line-height: 1.5;
        }
        .user-data {
            background-color: rgba(255, 255, 255, 0.6);
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .user-data ul {
            margin: 0;
            padding-left: 20px;
        }
        .user-data li {
            margin-bottom: 5px;
        }
        h4.raw-response-title {
            margin-top: 25px;
            margin-bottom: 10px;
            color: #495057;
            font-size: 16px;
        }
        pre { 
            background: #212529; 
            color: #f8f9fa; 
            padding: 15px; 
            border-radius: 8px; 
            overflow-x: auto; 
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }
        .api-url {
            font-size: 13px;
            color: #868e96;
            word-break: break-all;
            margin-top: 20px;
            text-align: center;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            border: 1px dashed #ced4da;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>EduBridge Client Portal</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">API Username</label>
            <input type="text" id="username" name="username" placeholder="e.g. admin" required>
        </div>
        <div class="form-group">
            <label for="password">API Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>
        </div>
        <button type="submit">Submit Request</button>
    </form>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <h4 style="color: #c92a2a; margin-bottom: 10px;">Connection Error</h4>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($result): ?>
        <?php 
            $statusClass = 'alert-incomplete'; // Default fallback
            if (isset($result['status'])) {
                if ($result['status'] === 'success') $statusClass = 'alert-success';
                else if ($result['status'] === 'unauthorized') $statusClass = 'alert-unauthorized';
                else if ($result['status'] === 'incomplete') $statusClass = 'alert-incomplete';
            }
        ?>
        <div class="alert <?php echo $statusClass; ?>">
            <h4>API Response Output</h4>
            <p><strong>Response Status:</strong> <?php echo htmlspecialchars($result['status'] ?? 'Unknown'); ?></p>
            <p><strong>Message:</strong> <?php echo htmlspecialchars($result['message'] ?? ''); ?></p>
            
            <?php if (isset($result['user_data'])): ?>
                <div class="user-data">
                    <p style="margin-top: 0;"><strong>Parsed User Data:</strong></p>
                    <ul>
                        <li><strong>Username:</strong> <?php echo htmlspecialchars($result['user_data']['username']); ?></li>
                        <li><strong>Role:</strong> <?php echo htmlspecialchars($result['user_data']['role']); ?></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        
        <h4 class="raw-response-title">Raw JSON Display:</h4>
        <pre><?php echo htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)); ?></pre>
    <?php endif; ?>
    
    <?php if ($apiUrlUsed): ?>
        <div class="api-url">
            <strong>Endpoint called:</strong><br>
            <?php echo htmlspecialchars(strlen($apiUrlUsed) > 80 ? substr($apiUrlUsed, 0, 80) . '...' : $apiUrlUsed); ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>

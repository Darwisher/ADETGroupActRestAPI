<?php
header("Content-Type: application/json"); // Required for JSON response 
$conn = new mysqli("localhost", "root", "", "school_db");

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// Get parameters from the URL (GET request) 
$username = $_GET['user'] ?? '';
$password = $_GET['pass'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode([
        "status" => "incomplete",
        "message" => "Please provide both username and password."
    ]);
} else {
    // Check if user exists 
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Return multi-status response with data [cite: 35, 36]
        echo json_encode([
            "status" => "success",
            "message" => "Login successful!",
            "user_data" => [
                "username" => $user['username'],
                "role" => $user['role']
            ]
        ]);
    } else {
        echo json_encode([
            "status" => "unauthorized",
            "message" => "Invalid credentials."
        ]);
    }
}
?>
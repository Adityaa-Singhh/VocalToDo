<?php
require 'db_connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    try {
        // Validate inputs
        if (empty($username) || empty($email) || empty($password)) {
            throw new Exception("Please fill all fields");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        if (strlen($password) < 8) {
            throw new Exception("Password must be at least 8 characters");
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        
        if ($stmt->rowCount() > 0) {
            throw new Exception("Email or username already exists");
        }
        
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Signup successful! You can now login.'
        ]);
        
    } catch (Exception $e) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
<?php
require 'db_connection.php';
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

try {
    if (empty($email) || empty($password)) {
        throw new Exception("Email and password are required");
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful'
        ]);
    } else {
        throw new Exception("Invalid email or password");
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
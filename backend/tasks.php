<?php
require 'db_connection.php';
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$response = ['success' => false];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Cast completed to integer
        $stmt = $pdo->prepare("SELECT id, task_text, CAST(completed AS UNSIGNED) as completed FROM tasks WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = ['success' => true, 'tasks' => $tasks];
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['action'])) {
            switch ($input['action']) {
                case 'add':
                    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, task_text) VALUES (?, ?)");
                    $stmt->execute([$user_id, $input['task']]);
                    $response = ['success' => true, 'id' => $pdo->lastInsertId()];
                    break;
                    
                case 'toggle':
                    $completed = $input['completed'] ? 1 : 0;
                    $stmt = $pdo->prepare("UPDATE tasks SET completed = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$completed, $input['id'], $user_id]);
                    $response = ['success' => true];
                    break;
            }
        }
    }
} catch (PDOException $e) {
    $response['error'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response, JSON_NUMERIC_CHECK); // Force numbers to stay as numbers
?>
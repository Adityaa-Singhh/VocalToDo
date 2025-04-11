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
                    
                case 'delete':
                    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
                    $stmt->execute([$input['id'], $user_id]);
                    $response = ['success' => $stmt->rowCount() > 0];
                    break;
                    
                case 'edit':
                    $stmt = $pdo->prepare("UPDATE tasks SET task_text = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$input['newText'], $input['id'], $user_id]);
                    $response = ['success' => $stmt->rowCount() > 0];
                    break;
                    
                // New reminder system endpoints
                case 'get_uncompleted_tasks':
                    $stmt = $pdo->prepare("SELECT id, task_text FROM tasks WHERE user_id = ? AND completed = 0");
                    $stmt->execute([$user_id]);
                    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response = ['success' => true, 'tasks' => $tasks];
                    break;
                    
                case 'update_reminder_time':
                    // Check if reminders table exists, create if not
                    $pdo->exec("CREATE TABLE IF NOT EXISTS user_reminders (
                        user_id INT PRIMARY KEY,
                        last_reminder_time TIMESTAMP NULL,
                        FOREIGN KEY (user_id) REFERENCES users(id)
                    )");
                    
                    $stmt = $pdo->prepare("INSERT INTO user_reminders (user_id, last_reminder_time) 
                                         VALUES (?, NOW()) 
                                         ON DUPLICATE KEY UPDATE last_reminder_time = NOW()");
                    $stmt->execute([$user_id]);
                    $response = ['success' => true];
                    break;
                    
                default:
                    $response['error'] = 'Invalid action';
            }
        } else {
            $response['error'] = 'No action specified';
        }
    }
} catch (PDOException $e) {
    $response['error'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response, JSON_NUMERIC_CHECK);
?>
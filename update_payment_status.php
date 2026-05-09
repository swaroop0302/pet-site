<?php
session_start();
// Security headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header("Content-Type: application/json");

// Enable error reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['admin_loggedin'])) {
    http_response_code(403);
    die(json_encode(['success' => false, 'error' => 'Unauthorized access']));
}

$host = 'localhost';
$dbname = 'pet_shop';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log("DB Connection Error: " . $e->getMessage());
    die(json_encode(['success' => false, 'error' => 'Database connection failed']));
}

$input = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Invalid JSON data']));
}

if (empty($input['order_id']) || !isset($input['status'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Missing required fields']));
}

$orderId = (int)$input['order_id'];
$newStatus = in_array($input['status'], ['Paid', 'Unpaid']) ? $input['status'] : null;

if (!$newStatus) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Invalid status value']));
}

try {
    $stmt = $pdo->prepare("UPDATE orders SET payment_status = ? WHERE order_id = ?");
    $stmt->execute([$newStatus, $orderId]);
    
    if ($stmt->rowCount() === 0) {
        die(json_encode(['success' => false, 'error' => 'No records updated']));
    }
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    error_log("Update Error: " . $e->getMessage());
    http_response_code(500);
    die(json_encode(['success' => false, 'error' => 'Database update failed']));
}
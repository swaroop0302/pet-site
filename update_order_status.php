<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_loggedin'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

$host = 'localhost';
$dbname = 'pet_shop';
$user = 'root';
$pass = '';

$data = json_decode(file_get_contents('php://input'), true);
$order_id = $data['order_id'];
$status = $data['status'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update order status
    $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->execute([$status, $order_id]);
    
    // Get order amount - handle numeric conversion
    $stmt = $pdo->prepare(
        "SELECT total_amount
         FROM orders 
         WHERE order_id = ?"
    );
    $stmt->execute([$order_id]);
    $order_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Convert to float safely
    $order_amount = 0;
    if ($order_data && isset($order_data['total_amount'])) {
        $order_amount = (float) preg_replace('/[^0-9.]/', '', $order_data['total_amount']);
    }
    
    echo json_encode([
        'success' => true,
        'order_amount' => $order_amount
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
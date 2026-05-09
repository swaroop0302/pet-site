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

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get total sales safely
    $stmt = $pdo->query("SELECT total_amount FROM orders WHERE order_status = 'Delivered'");
    $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $totalSales = 0;
    foreach ($salesData as $order) {
        $totalSales += (float) preg_replace('/[^0-9.]/', '', $order['total_amount']);
    }
    
    $totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

    echo json_encode([
        'total_sales' => (float)$totalSales,
        'total_orders' => (int)$totalOrders
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
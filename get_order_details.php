<?php
session_start();
if (!isset($_SESSION['admin_loggedin'])) {
    die("Unauthorized access");
}

$host = 'localhost';
$dbname = 'pet_shop';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $orderId = $_GET['order_id'];
    $stmt = $pdo->prepare("SELECT products FROM orders WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $products = json_decode($order['products'], true);
        echo json_encode($products);
    } else {
        echo json_encode(['error' => 'Order not found']);
    }
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
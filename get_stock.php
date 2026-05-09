<?php
session_start();
$host = 'localhost';
$dbname = 'pet_shop';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Could not connect to database: " . $e->getMessage());
}

if (isset($_GET['title'])) {
    $title = $_GET['title'];
    
    try {
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE title = ?");
        $stmt->execute([$title]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            echo json_encode(['stock' => $product['stock']]);
        } else {
            echo json_encode(['error' => 'Product not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Database error']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
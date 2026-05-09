<?php
// check_stock.php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'pet_shop';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode(['errors' => ["Could not connect to database: " . $e->getMessage()]]));
}

$cart = json_decode(file_get_contents('php://input'), true);
$errors = [];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // First pass: Check stock availability
    foreach ($cart as $item) {
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE title = ?");
        $stmt->execute([$item['title']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $errors[] = "Product '{$item['title']}' not found";
        } else {
            $availableStock = $product['stock'];
            $requestedQuantity = $item['quantity'];
            
            if ($availableStock < $requestedQuantity) {
                $errors[] = "{$item['title']}: Only {$availableStock} available (You requested {$requestedQuantity})";
            }
        }
    }

    // Second pass: Update quantities if no errors
    if (empty($errors)) {
        foreach ($cart as $item) {
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE title = ?");
            $stmt->execute([$item['quantity'], $item['title']]);
        }
        $pdo->commit();
        echo json_encode(['success' => true]);
    } else {
        $pdo->rollBack();
        echo json_encode(['errors' => $errors]);
    }
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['errors' => ['Database error: ' . $e->getMessage()]]);
}
?>
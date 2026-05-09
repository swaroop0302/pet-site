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


if (!isset($_SESSION['admin_loggedin'])) {
    header("Location: adminlogin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $product_id = $_POST['product_id'];
    $new_stock = $_POST['new_stock'];
    
    $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE product_id = ?");
    $stmt->execute([$new_stock, $product_id]);
    
   header("Location: manage_product.php");
    exit();
}
?>
<?php
session_start();
// Add database connection
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

// Rest of your code...


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);
        $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $stock = filter_var($_POST['stock'], FILTER_SANITIZE_NUMBER_INT);
        $category = htmlspecialchars($_POST['category']);
        $image_url = filter_var($_POST['image_url'], FILTER_SANITIZE_URL);

        // Validation
        if (empty($title) || empty($description) || empty($price) || empty($stock) || empty($category) || empty($image_url)) {
            throw new Exception("All fields are required!");
        }

        

        $stmt = $pdo->prepare("INSERT INTO products 
            (title, description, price, stock, category, image_url, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())");

        $stmt->execute([$title, $description, $price, $stock, $category, $image_url]);

        $_SESSION['success'] = "Product added successfully!";
        header("Location: add_product.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: add_product.php");
        exit();
    }
}
?>
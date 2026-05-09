
<?php
session_start();
if (!isset($_SESSION['admin_loggedin'])) {
    header("Location: adminlogin.php");
    exit();
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pet_shop";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    // Basic validation
    if (empty($name) || empty($email) || empty($address) || empty($phone)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: add_supplier.php");
        exit();
    }

    // Insert into database using prepared statement
    $stmt = $conn->prepare("INSERT INTO suppliers (name, email, address, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $address, $phone);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Supplier added successfully!";
    } else {
        $_SESSION['error'] = "Error adding supplier: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
    
    header("Location: add_supplier.php");
    exit();
}
?>
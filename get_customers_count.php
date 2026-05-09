<?php
session_start();
if (!isset($_SESSION['admin_loggedin'])) {
    header("HTTP/1.1 401 Unauthorized");
    exit(json_encode(['count' => 0]));
}

$host = 'localhost';
$dbname = 'pet_shop';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $stmt = $pdo->query("SELECT COUNT(id) AS count FROM registor");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch(PDOException $e) {
    echo json_encode(['count' => 0]);
}
?>
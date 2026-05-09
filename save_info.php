<?php
$conn = new mysqli("localhost", "root", "", "pet_shop");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST["first_name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password=$_POST["confirm_password"];
    $address = $_POST["address"];
    $phone_no=$_POST["phone_no"];

    $stmt = $conn->prepare("INSERT INTO registor (first_name,email,password,confirm_password,address,phone_no) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss",$first_name, $email, $password, $confirm_password, $address,$phone_no);

    if ($stmt->execute()) {
        header("Location: registration.php");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

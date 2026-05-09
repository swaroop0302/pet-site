<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$database = "pet_shop";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($email) || empty($password)) {
        die("<script>alert('Please fill all fields!'); window.location='adminlogin.php'</script>");
    }

    // Prepare statement
    $stmt = $conn->prepare("SELECT id, password FROM adminlogin WHERE email = ?");
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $db_password);
        $stmt->fetch();

        // Plain text password comparison
        if ($password === $db_password) { // Changed here
            $_SESSION['admin_id'] = $user_id; // Changed session variables
            $_SESSION['admin_email'] = $email;
            $_SESSION['admin_loggedin'] = true;
            
            header("Location: admin.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password!'); window.location='adminlogin.php'</script>";
            exit();
        }
    } else {
        echo "<script>alert('Email not found!'); window.location='adminlogin.php'</script>";
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #121212;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #e0e0e0;
        }

        .login-container {
            background-color: #1e1e1e;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            width: 400px;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .subtitle {
            font-size: 16px;
            color: #a0a0a0;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #b0b0b0;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #333;
            border-radius: 4px;
            font-size: 16px;
            background-color: #2d2d2d;
            color: #e0e0e0;
        }

        input:focus {
            outline: none;
            border-color: #0066cc;
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
        }

        .remember-me input {
            width: auto;
            margin-right: 8px;
        }

        .forgot-password {
            color: #4a9cff;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-button {
            background-color: #0066cc;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 20px;
            transition: background-color 0.2s;
        }

        .login-button:hover {
            background-color: #005bb7;
        }

        .divider {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: #666;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #333;
        }

        .divider::before {
            margin-right: 10px;
        }

        .divider::after {
            margin-left: 10px;
        }

        .create-account {
            color: #4a9cff;
            text-decoration: none;
            font-size: 14px;
        }

        .create-account:hover {
            text-decoration: underline;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d; /* Neutral color for secondary actions */
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
            text-align: center;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>

<body>
   <form method="POST" action="" class="login-container">
        <h1>Admin Portal</h1>
        <p class="subtitle">Log in to your account</p>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="your@email.com" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="······" required>
        </div>
        <button type="submit" class="login-button">Login</button>
         <div class="button-container">
        <a href="home.php" class="back-button">Back to Home</a>
        </div>
    </form>
</body>

</html>
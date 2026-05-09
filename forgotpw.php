<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pet_shop";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $phone_no = trim($_POST['phoneno']);

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT phone_no FROM registor WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if email exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($correct_phone);
        $stmt->fetch();

        if (strtolower($phone_no) === strtolower($correct_phone)) {
            $_SESSION['reset_email'] = $email;
            header("Location: resetpassword.php");
            exit();
        } else {
            $error = "Incorrect mobile number.";
        }
    } else {
        $error = "Email not found.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forget Password</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100%;
            background-color: #fa9231;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .forget-password-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
        }
        .forget-password-box {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        label {
            font-size: 16px;
            display: block;
            margin-bottom: 5px;
            text-align: left;
        }
        input[type="email"], input[type="text"] {
            width: 95%;
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }
        input:focus {
            border-color: #007bff;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
        }
        button {
            width: 45%;
            padding: 12px;
            background-color: #ef860c;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #cb8905;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="forget-password-container">
    <div class="forget-password-box">
        <h2>Forget Password</h2>
        <?php if (!empty($error)) echo "<div class='error-message'>{$error}</div>"; ?>
        <form method="POST" action="">
            <label for="email">Email address:</label>
            <input type="email" id="email" name="email" placeholder="Enter email" required>

            <label for="phoneno">Mobile number:</label>
            <input type="text" id="phoneno" name="phoneno" placeholder="Enter mobile number" required>

            <div class="buttons">
                <button type="submit">Next</button>
                <button type="button" onclick="window.location.href='login.php'">Cancel</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

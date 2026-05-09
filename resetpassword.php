<?php
session_start();
$servername = "localhost"; // Change as needed
$username = "root"; // Change as needed
$password = ""; // Change as needed
$dbname = "pet_shop"; // Change as needed

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['reset_email'])) {
    echo "<p style='color:red;'>Unauthorized access!</p>";
    exit();
}

$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        // Hash the password before storing it

        
        // Update the password field correctly
        $stmt = $conn->prepare("UPDATE registor SET password = ?, confirm_password = ? WHERE email = ?");
        $stmt->bind_param("sss", $password, $confirm_password, $email);
        
        if ($stmt->execute()) {
            echo "<script>
                alert('Password has been successfully updated!');
                window.location.href = 'login.php';
            </script>";
            session_destroy(); // Clear session after password reset
            exit();
        } else {
            echo "<p style='color:red;'>Error updating password.</p>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Password and Confirm Password must be the same.');</script>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            background-color: #fa9231;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }
        .reset-box {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
            max-width: 90%;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        input[type="password"] {
            width: 95%;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #ef860c;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #cb8905;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
        }
        .error {
            color: #d9534f;
            background-color: #f2dede;
            border: 1px solid #ebccd1;
        }
        .success {
            color: #3c763d;
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
        }
        .password-rules {
            text-align: left;
            margin: 10px 0;
            font-size: 13px;
            color: #666;
        }
    </style>
</head>
<body>
<div class="reset-box">
    <h2>Reset Password</h2>
    <!-- <?php
    if (!empty($error)) {
        echo "<div class='message error'>".htmlspecialchars($error, ENT_QUOTES, 'UTF-8')."</div>";
    }
    if (!empty($success)) {
        echo "<div class='message success'>".htmlspecialchars($success, ENT_QUOTES, 'UTF-8')."</div>";
    }
    ?> -->
    <form method="POST" action="" onsubmit="return validatePassword()">
        <input type="password" name="password" id="password" placeholder="New Password" required minlength="6" maxlength="72">
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required minlength="6" maxlength="72">
        <!-- <div class="password-rules">
            <p>Password requirements:</p>
            <ul>
                <li>At least 6 characters</li>
                <li>Maximum 72 characters</li>
                <li>No special control characters</li>
            </ul> -->
        <!-- </div> -->
        <button type="submit">Reset Password</button>
    </form>
</div>

<!-- <script>
function validatePassword() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    // Check for non-printable characters
    if (!/^[\x20-\x7E]+$/.test(password)) {
        alert('Password contains invalid characters');
        return false;
    }
    
    if (password !== confirmPassword) {
        alert('Passwords do not match');
        return false;
    }
    
    return true;
}
</script> -->
</body>
</html>
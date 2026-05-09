<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "pet_shop";

// Establish a database connection
$conn = new mysqli($servername, $username, $password, $database);

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepare a statement to fetch the stored hashed password
    $stmt = $conn->prepare("SELECT password FROM registor WHERE email = ?");
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_password);
        $stmt->fetch();

        // Verify password using password_verify()
        if ($db_password === $password || password_verify($password, $db_password)) {
            $_SESSION['user_email'] = $email;
            header("Location: home.php"); // Redirect to the home page 
            exit();

        } else {
            echo "<script>alert('Incorrect password!');</script>";
        }


    } else {
        echo "<script>alert('Email not found!');</script>";
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
    <title>Login - Pet Care</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body background="log.jpg">
    <div class="login-container">
        <div class="login-box">
            <h2>Pet site</h2>
            <p class="subheading">Sign In To Continue</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> " method="POST">
                <div class="input-container">
                    <input type="email" placeholder="Email" name="email" required>
                </div>
                <div class="input-container">
                    <input type="password" placeholder="Password" name="password" required>
                </div>
                <div class="remember-forgot">
                    <a href="forgotpw.php">Forgot Password?</a>
                </div>
                <div class="button-container">
                    <button type="submit">Login</button>
                </div>
                <div class="signup-link">
                    <p>Don't have an account? <a href="registration.php">Sign up here</a></p>
                </div>
                <div class="button-container">
                <a href="home.php" class="back-button">Back to Home</a>
                </div>
            </form>
        </div>
        <div class="image-container">
            <img src="pet.jpg" alt="Pet site" />
        </div>
    </div>

</body>

</html>
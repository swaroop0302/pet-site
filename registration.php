<?php
// Connect to the database

$conn = new mysqli("localhost", "root", "", "pet_shop");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST["first_name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password=$_POST["confirm_password"];
    $address = $_POST["address"];
    $phone_no=$_POST["phone_no"];
    $latitude=$_POST["latitude"];
    $longitude=$_POST["longitude"];

    // Check if the email already exists
$sql_check_email = "SELECT * FROM registor WHERE email = ?";
$stmt = $conn->prepare($sql_check_email);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('Email already exists!');</script>";
} else {
     // Check if the phone number already exists
        $sql_check_phone = "SELECT * FROM registor WHERE phone_no = ?";
        $stmt_phone = $conn->prepare($sql_check_phone);
        $stmt_phone->bind_param("s", $phone_no);
        $stmt_phone->execute();
        $result_phone = $stmt_phone->get_result();

        if ($result_phone->num_rows > 0) {
            echo "<script>alert('Phone number already exists!');</script>";
        } else {
    // Insert new user
    if ($password === $confirm_password) {
        $sql_insert = "INSERT INTO registor (first_name,email,password,confirm_password,address,phone_no,latitude,longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("ssssssss",$first_name,$email,$password,$confirm_password,$address,$phone_no,$latitude,$longitude);

        if ($stmt->execute()) {
            header("Location: login.php"); // Permanent Redirect
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "<script>alert('Password do not match!');</script>";
    }
}
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
    <title>sign up</title>
    <link rel="stylesheet" type="text/css" href="regstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body>
    <div class="container">
        <h2>SIGN UP</h2>
        <form method="POST" action="">
        <div class="form-container">
                <div class="input-name">
                    <i class="fa fa-user lock"></i>
                    <input type="text" id="first_name" name="first_name" placeholder="First name" class="name" required>
                </div>

                <div class="input-name">
                    <i class="fa fa-envelope lock"></i>
                    <input type="email" id="email" name="email" placeholder=" Email" class="text-name" required>
                </div>

                <div class="input-name">
                    <i class="fa fa-lock lock"></i>
                    <input type="password" id="password" name="password" placeholder="password" class="text-name"
                        required>
                </div>

                <div class="input-name">
                    <i class="fa fa-lock lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password"
                        placeholder="Confirm your password" class="text-name" required>
                </div>

                <div class="input-name">
                    <i class="fa fa-map-marker lock"></i>
                    <input type="text" id="address" name="address"
                        placeholder="enter your address" class="text-name" required>
                </div>

                <div class="input-name">
                    <i class="fa fa-phone lock"></i>
                    <input type="text" id="phone_no" name="phone_no" pattern="[0-9]{10}"
                        placeholder="enter mobile number" class="text-name" required>
                </div>

                <!-- Auto-filled Location -->
             <input type="text" name="latitude" id="latitude" placeholder="Latitude" class="text-name1" required> 
             <input type="text" name="longitude" id="longitude" placeholder="Longitude" class="text-name1" required><br>
      
     
            <button type="button" style="margin-left:125px;" onclick="getLocation()" class="geo-btn">Get My Location</button>



                <button type="submit" class="signup-btn">Sign Up</button>
                <label class="text" style="margin-left: 110px;">Already have an account?</label>
                <a href="login.php">Sign in</a>
        </div>
        </form>
</div>

<script>
    function getLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function(position) {
            document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
            document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
          },
          function(error) {
            alert('Error getting location. Please allow location access.');
          }
        );
      } else {
        alert("Geolocation is not supported by this browser.");
      }
    }
  </script>

</body>

</html>
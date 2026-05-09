<?php
session_start();

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

// Redirect if not logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Handle Razorpay callback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'saveBooking') {
    try {
        $user_email = $_SESSION['user_email'];
        $payment_id = $_POST['payment_id'];
        $cart = json_decode($_POST['cart'], true);
        $total = floatval($_POST['totalFare']);

        // Validate inputs
        if (empty($cart) || $total <= 0) {
            throw new Exception("Invalid cart data or amount");
        }

        // Start transaction
        $conn->begin_transaction();

        // Check and deduct stock
        foreach ($cart as $item) {
            // Get current stock with row lock
            $stmt = $conn->prepare("SELECT stock FROM products WHERE title = ? FOR UPDATE");
            $stmt->bind_param("s", $item['title']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("Product {$item['title']} not found");
            }
            
            $product = $result->fetch_assoc();
            if ($product['stock'] < $item['quantity']) {
                throw new Exception("Insufficient stock for {$item['title']}");
            }
            
            // Deduct stock
            $updateStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE title = ?");
            $updateStmt->bind_param("is", $item['quantity'], $item['title']);
            $updateStmt->execute();
        }

        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, products, total_amount, payment_method, payment_id) VALUES (?, ?, ?, 'razorpay', ?)");
        $products_json = json_encode($cart);
        $stmt->bind_param("ssds", $user_email, $products_json, $total, $payment_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'expiry_time' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            'order_id' => $stmt->insert_id
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        die(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Shop Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Carter+One&display=swap" rel="stylesheet">
    <style>
        :root {
            --ff-carter_one: 'Carter One', cursive;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4f1f1;
        }

        nav {
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo {
            font-size: 2rem;
            color: #f9943b;
            font-family: var(--ff-carter_one);
            text-transform: uppercase;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        #payButton {
            background-color: #28a745;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            margin: 20px 0;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .success-message {
            display: none;
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .qr-section {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .continue-shopping-btn {
            display: inline-block;
            background-color: #f9943b;
            color: white !important;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            margin-top: 20px;
            font-weight: 500;
            transition: transform 0.3s ease;
        }
        .continue-shopping-btn:hover {
            background-color: #e68a2e;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-logo">Pet Site</div>
    </nav>

    <div class="container" id="paymentContainer">
    <h1>Pet Shop Payment</h1>
    <p><strong>Total Items:</strong> <span id="totalItems">0</span></p>
    <p><strong>Total Amount:</strong> ₹<span id="totalAmount">0.00</span></p>
    <button id="payButton">Pay with Razorpay</button>
    <center><a href="cart.php" style="color: #aaa;">Back to Cart</a></center>
</div>

    <div class="success-message" id="successMessage"></div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
   <script>
    // Get cart from localStorage
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Calculate totals
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const email = <?php echo json_encode($_SESSION['user_email']); ?>;

    // Update display
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('totalAmount').textContent = totalAmount.toFixed(2);

    const options = {
        "key": "rzp_test_1DP5mmOlF5G5ag",
        "amount": Math.round(totalAmount * 100),
        "currency": "INR",
        "name": "Pet Shop",
        "description": "Pet Products Purchase",
        "handler": function(response) {
        const payment_id = response.razorpay_payment_id;
        const formData = new FormData();
        formData.append('action', 'saveBooking');
        formData.append('payment_id', payment_id);
        formData.append('cart', JSON.stringify(cart));
        formData.append('totalFare', totalAmount);

        fetch('payment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                    document.getElementById("paymentContainer").style.display = 'none';
                    const successDiv = document.getElementById("successMessage");
                    successDiv.style.display = 'block';
                    successDiv.innerHTML = `
                        <h2>Payment Successful!</h2>
                        <div class="qr-section">
                            <div>
                                <p><strong>Order ID:</strong> ${res.order_id}</p>
                                <p><strong>Items Purchased:</strong> ${cart.length}</p>
                                <p><strong>Total Paid:</strong> ₹${totalAmount.toFixed(2)}</p>
                                <p><strong>Valid Until:</strong> ${res.expiry_time}</p>
                            </div>
                        </div>
                        <a href="home.php" class="continue-shopping-btn">Continue Shopping</a>
                    `;
                    localStorage.removeItem('cart');
                } else {
                    alert("Payment failed: " + res.message);
                }
            })
            .catch(error => {
                alert("Error processing payment: " + error.message);
            });
        },
            "prefill": { "email": email },
            "theme": { "color": "#f9943b" }
        };

        const rzp = new Razorpay(options);
        document.getElementById('payButton').addEventListener('click', function(e) {
            rzp.open();
            e.preventDefault();
        });
    </script>
</body>
</html>
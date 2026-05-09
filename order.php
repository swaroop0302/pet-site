<?php
session_start();
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

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}
// Add this at the top of order.php (if using PHP < 8.1)
if (!function_exists('array_is_list')) {
    function array_is_list(array $arr) {
        return array_keys($arr) === range(0, count($arr) - 1);
    }
}

// Fetch user's orders
$user_email = $_SESSION['user_email'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Pet Site</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Carter+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        /* Navigation styles */
        nav {
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo {
            font-size: 2rem;
            font-weight: 600;
            color: #f9943b;
            font-family: var(--ff-carter_one);
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .nav-links {
            display: flex;
            margin-right: auto;
            margin-left: 40px;
        }

        nav a {
            text-decoration: none;
            color: #333;
            margin: 0 20px;
            font-size: 18px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #ef952e;
        }

        /* Logout button styles */
        .navbar-action-btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 2px solid transparent;
        }

        .navbar-action-btn.logout {
            background-color: #f44336;
            color: white;
            border-color: #f44336;
        }

        .navbar-action-btn.logout:hover {
            background-color: #d32f2f;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(244, 67, 54, 0.3);
        }

        .navbar-action-btn.logout::before {
            content: "\f2f5";
            font-family: "Font Awesome 5 Free";
            margin-right: 8px;
        }

        /* Order history styles matching cart.php */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 10px;
        }

        .order-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .order-id {
            color: #f9943b;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .order-date {
            color: #666;
            font-size: 0.9rem;
        }

        .order-product {
            display: flex;
            align-items: center;
            padding: 15px;
            margin: 10px 0;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 20px;
        }

        .product-title {
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
        }

        .product-price {
            color: #ef952e;
            font-weight: 600;
        }

        .order-total {
            text-align: right;
            font-size: 1.2rem;
            font-weight: 600;
            color: #f9943b;
            margin-top: 15px;
        }

        @media (max-width: 768px) {
            .nav-links {
                margin-left: 20px;
            }
            
            nav a {
                margin: 0 10px;
                font-size: 16px;
            }
            
            .navbar-action-btn {
                padding: 6px 15px;
                font-size: 13px;
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .order-date {
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-logo">Pet Site</div>
        <div class="nav-links">
            <a href="home.php">Home</a>
        </div>
        <?php if(isset($_SESSION['user_email'])) : ?>
            <a href="logout.php" class="navbar-action-btn logout">Log Out</a>
        <?php endif; ?>
    </nav>

    <div class="container">
        <h1>Your Order History</h1>
<?php if ($result->num_rows > 0) : ?>
    <?php while($order = $result->fetch_assoc()) : 
        $products = json_decode($order['products'], true) ?? [];
        // Ensure products is always a list (indexed array)
        if (!empty($products) && !array_is_list($products)) {
            $products = [$products];
        }
    ?>
        <div class="order-container">
            <div class="order-header">
                <span class="order-id">Order #<?= htmlspecialchars($order['order_id']) ?></span>
                <span class="order-date"><?= date('M d, Y H:i', strtotime($order['order_date'])) ?></span>
            </div>
            
            <?php if(!empty($products)) : ?>
                <?php foreach($products as $product) : ?>
                    <div class="order-product">
                        <img src="<?= htmlspecialchars($product['image'] ?? '') ?>" 
                             class="product-image" 
                             alt="<?= htmlspecialchars($product['title'] ?? 'Product image') ?>">
                        <div class="product-info">
                            <div class="product-title">
                                <?= htmlspecialchars($product['title'] ?? 'Unknown Product') ?>
                            </div>
                            <div class="product-price">
                                Rs<?= number_format($product['price'] ?? 0, 2) ?>
                            </div>
                            <div>Quantity: <?= $product['quantity'] ?? 0 ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="order-product">
                    <p>No products found in this order</p>
                </div>
            <?php endif; ?>
            
            <div class="order-total">
                Total: Rs<?= number_format($order['total_amount'], 2) ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php else : ?>
    <div class="order-container">
        <p>No orders found.</p>
    </div>
<?php endif; ?>
    </div>
</body>
</html>
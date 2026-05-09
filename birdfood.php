<?php 
session_start();
$host = 'localhost';
$dbname = 'pet_shop';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Could not connect to database: " . $e->getMessage());
}
if (basename($_SERVER['PHP_SELF']) != 'login.php' && !isset($_SESSION['user_email'])) {
    //Optional: Redirect to login if you want protected pages
     header("Location: login.php");
     exit();
}
$stmt = $pdo->prepare("SELECT * FROM products WHERE category = 'birdfood'");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bird food</title>
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

        .nav-icons {
            display: flex;
            gap: 20px;
        }

        .nav-icon {
            font-size: 20px;
            color: #333;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .nav-icon:hover {
            color: #ef952e;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff6b6b;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 12px;
            font-weight: bold;
        }

        .icon-wrapper {
            position: relative;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        .span {
            color: rgb(243, 79, 20);
        }

        h2 {
            text-align: center;
            margin-bottom: 50px;
            font-size: 36px;
            color: #2c3e50;
            position: relative;
        }

        h2::after {
            content: '';
            width: 60px;
            height: 3px;
            background: #ef952e;
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            justify-items: center;
        }

        .product-card {
            background: white;
            width: 100%;
            max-width: 300px;
            padding: 15px;
            text-align: center;
            border-radius: 12px;
            transition: transform 0.3s ease;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-title {
            font-size: 16px;
            margin: 10px 0;
            color: #34495e;
            font-weight: 600;
        }

        .product-subtitle {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 18px;
            color: #ef952e;
            font-weight: 700;
            margin: 15px 0;
        }

        .best-seller {
            position: absolute;
            top: 15px;
            left: -30px;
            background: #ff6b6b;
            color: white;
            padding: 5px 35px;
            transform: rotate(-45deg);
            font-size: 12px;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .buy-button {
            background: #ef952e;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            opacity: 0;
            transform: translateY(10px);
        }

        .product-card:hover .buy-button {
            opacity: 1;
            transform: translateY(0);
        }

        .buy-button:hover {
            background: #d77a0f;
            transform: scale(1.05);
        }

        .category-bar {
            display: flex;
            overflow-x: auto;
            padding: 1.5rem 0.5rem;
            /* background: #fff; */
            border-bottom: 1px solid #eee;
        }

        .category-tab {
            flex: 0 0 auto;
            text-align: center;
            margin: 0 10px;
            cursor: pointer;
        }

        .category-tab .circle:hover {
            border-color: #d77a0f;
        }

        .category-tab .circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #f4f4f4;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 5px;
            border: 2px solid transparent;
            transition: border-color 0.3s;
        }

        .category-tab.active .circle {
            border-color: orange;
        }

        .category-tab img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .category-tab span {
            font-size: 0.85rem;
            color: #333;
        }

        /* Optional: Hide scrollbar on WebKit */
        .category-bar::-webkit-scrollbar {
            display: none;
        }
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
            /* Red color */
            color: white;
            border: 2px solid #f44336;
        }

        .navbar-action-btn.logout:hover {
            background-color: #d32f2f;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(244, 67, 54, 0.3);
        }

        /* Optional: Add icon using pseudo-elements */
        .navbar-action-btn.login::before {
            content: "\f2f6";
            /* FontAwesome user icon */
            font-family: "Font Awesome 5 Free";
            margin-right: 8px;
        }

        .navbar-action-btn.logout::before {
            content: "\f2f5";
            /* FontAwesome sign-out icon */
            font-family: "Font Awesome 5 Free";
            margin-right: 8px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .navbar-action-btn {
                padding: 6px 15px;
                font-size: 13px;
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
        <?php else : ?>
        <a href="login.php" class="navbar-action-btn login">Log In</a>
        <?php endif; ?>
        <a href="cart.php">
            <div class="icon-wrapper">
                <i class="fas fa-shopping-cart nav-icon"></i>
                <span class="cart-count">0</span>
            </div>
        </a>
        </div>
    </nav>
    <!-- category-bar -->
   <div class="category-bar">

        <div class="category-tab" data-category="CatFood" style="margin-left: 360px;">
            <a href="catfood.php">
            <div class="circle"><img src="catfood2.jpg" alt="All"></div>
            </a>
            <span>Cat Food</span>
        </div>

        <div class="category-tab" data-category="CatToy">
            <a href="cattoys.php">
                <div class="circle"><img src="cattoy3.jpg" alt="Puppy"></div>
            </a>
            <span>Cat Toys</span>
        </div>

        <div class="category-tab" data-category="DogFood">
            <a href="dogfood.php">
                <div class="circle"><img src="dog food7.jpg" alt="Adult"></div>
            </a>
            <span>Dog Food</span>
        </div>

        <div class="category-tab" data-category="DogToys">
            <a href="dogtoy.php">
                <div class="circle"><img src="dogtoy1.jpg" alt="Veg"></div>
            </a>
            <span>Dog Toys</span>
        </div>

        <div class="category-tab" data-category="Supply">
            <a href="supplyment.php">
                <div class="circle"><img src="supply11.jpg" alt="Drools"></div>
            </a>
            <span>Supplements</span>
        </div>

        <div class="category-tab" data-category="medicine">
            <a href="medicine.php">
                <div class="circle"><img src="medicine1.jpg" alt="Drools"></div>
            </a>
            <span>Medicine</span>
        </div>

        <div class="category-tab active" data-category="birdfood">
            <a href="birdfood.php">
                <div class="circle"><img src="birdfood1.jpg" alt="Drools"></div>
            </a>
            <span>Bird Food</span>
        </div>

        <div class="category-tab" data-category="fishfood">
            <a href="fishfood.php">
                <div class="circle"><img src="fishfood1.jpg" alt="Drools"></div>
            </a>
            <span>Fish Food</span>
        </div>

    </div>

    <!-- ends here -->



<div class="container">
        <h2 class="h2 section-title">
            <span class="span">Bird</span> product
        </h2>

        <div class="products-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="product-stock">
                    <?php if ($product['stock'] > 0): ?>
                        In Stock: <?= $product['stock'] ?>
                    <?php else: ?>
                        <span class="out-of-stock">Out of Stock</span>
                    <?php endif; ?>
                </div>
                
                <!-- <?php if ($product['is_bestseller']): ?>
                    <div class="best-seller">Bestseller</div>
                <?php endif; ?> -->

                <img src="<?= $product['image_url'] ?>" class="product-image" alt="<?= $product['title'] ?>">
                <div class="product-title"><?= $product['title'] ?></div>
                <div class="product-subtitle"><?= $product['description'] ?></div>
                <div class="product-price">Rs<?= number_format($product['price'], 2) ?></div>
                <button class="buy-button">Add to Cart</button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

     <script>
        // Cart functionality
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        function updateCartCount() {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.querySelectorAll('.cart-count').forEach(span => {
                span.textContent = totalItems;
            });
        }

        document.querySelectorAll('.buy-button').forEach(button => {
            button.addEventListener('click', function () {
                const productCard = this.closest('.product-card');
                
                // Check stock status
                const stockElement = productCard.querySelector('.product-stock');
                const isOutOfStock = stockElement.querySelector('.out-of-stock');
                
                if (isOutOfStock) {
                    alert('This product is out of stock and cannot be added to the cart.');
                    return; // Exit if out of stock
                }

                const title = productCard.querySelector('.product-title').textContent;
                const priceText = productCard.querySelector('.product-price').textContent;
                const price = parseFloat(priceText.replace('Rs', '').trim());
                const image = productCard.querySelector('.product-image').src;

                const existingItem = cart.find(item => item.title === title);
                if (existingItem) {
                    alert('This item is already in your cart!');
                    return;
                }

                cart.push({
                    title: title,
                    price: price,
                    quantity: 1,
                    image: image
                });

                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartCount();
            });
        });

        // Initial cart count update
        updateCartCount();
    </script>
</body>

</html>
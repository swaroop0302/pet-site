<?php 
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page != 'login.php' && !isset($_SESSION['user_email'])) {
    $_SESSION['redirect_url'] = $current_page;
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>shopping-cart</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Carter+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        #buy-now-btn {
            background-color: #ef952e;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        #buy-now-btn:hover {
            background-color: #e68a2e;
        }

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
            padding: 40px 10px;
        }

        .span {
            color: rgb(243, 79, 20);
        }

        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .cart-item-image img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }

        .cart-item-info {
            flex: 1;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }

        .quantity-controls button {
            padding: 5px 10px;
            border: none;
            background: #ef952e;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .remove-item {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .cart-item-total {
            font-weight: bold;
            margin-left: 20px;
            min-width: 100px;
            text-align: right;
        }

        .cart-total {
            text-align: right;
            font-size: 24px;
            margin-top: 20px;
        }
    /* Remove or rename these conflicting classes */
.navbar, .navbar.active {
    all: unset;
    margin-inline-end: auto;
}
.navbar-list {
    display: flex;
    gap: 10px;
}

  .navbar-link {
    color: var(--color);
    --fs-6: 1.7rem;
    font-weight: var(--fw-700);
    transition: var(--transition-1);
  }

  .header.active .navbar-link:is(:hover, :focus) {
    color: var(--portland-orange);
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
}

/* Login button specific styles */
.navbar-action-btn.login {
    background-color: #4CAF50; /* Green color */
    color: white;
    border: 2px solid #4CAF50;
}

.navbar-action-btn.login:hover {
    background-color: #45a049;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
}

/* Logout button specific styles */
.navbar-action-btn.logout {
    background-color: #f44336; /* Red color */
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
    content: "\f2f6"; /* FontAwesome user icon */
    font-family: "Font Awesome 5 Free";
    margin-right: 8px;
}

.navbar-action-btn.logout::before {
    content: "\f2f5"; /* FontAwesome sign-out icon */
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
</nav>
    <div class="container">
        <h2>Your Shopping Cart</h2>
        <div class="cart-items"></div>
        <div class="cart-total">
            <h3>Total: Rs<span id="total-amount">0.00</span></h3>
            <button id="buy-now-btn">Buy Now</button>
        </div>
    </div>

    <script>
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
       
        function updateCartCount() {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.querySelectorAll('.cart-count').forEach(span => {
                span.textContent = totalItems;
            });
        }

     function renderCart() {
    const container = document.querySelector('.cart-items');
    const totalAmount = document.getElementById('total-amount');
    container.innerHTML = '';
    let total = 0;

    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;

        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item';
        cartItem.innerHTML = `
            <div class="cart-item-image">
                <img src="${item.image}" alt="${item.title}">
            </div>
            <div class="cart-item-info">
                <h3>${item.title}</h3>
                <p>Price: Rs${item.price.toFixed(2)}</p>
                <div class="quantity-controls">
                    <button class="quantity-decrease" data-index="${index}">-</button>
                    <span class="quantity">${item.quantity}</span>
                    <button class="quantity-increase" data-index="${index}">+</button>
                </div>
                <button class="remove-item" data-index="${index}">Remove</button>
            </div>
            <div class="cart-item-total">
                Rs${itemTotal.toFixed(2)}
            </div>
        `;
        container.appendChild(cartItem);
    });

    totalAmount.textContent = total.toFixed(2);
}

document.querySelector('.cart-items').addEventListener('click', async (e) => {
    if (!e.target.classList.contains('quantity-increase') && 
        !e.target.classList.contains('quantity-decrease') && 
        !e.target.classList.contains('remove-item')) return;

    const index = e.target.dataset.index;
    const item = cart[index];

    if (e.target.classList.contains('quantity-increase')) {
        try {
            const response = await fetch(`get_stock.php?title=${encodeURIComponent(item.title)}`);
            const stockData = await response.json();

            if (stockData.error) {
                alert(stockData.error);
                return;
            }

            if (item.quantity + 1 > stockData.stock) {
                alert(`Out of Stock! Available: ${stockData.stock}`);
                return;
            }

            cart[index].quantity++;
        } catch (error) {
            console.error('Error checking stock:', error);
            return;
        }
    }
    else if (e.target.classList.contains('quantity-decrease')) {
        if (item.quantity > 1) {
            cart[index].quantity--;
        } else {
            cart.splice(index, 1);
        }
    }
    else if (e.target.classList.contains('remove-item')) {
        cart.splice(index, 1);
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
    updateCartCount();
});
        // Initial render
        renderCart();
        updateCartCount();

        // Add buy now button functionality with login check
         const isLoggedIn = <?php echo isset($_SESSION['user_email']) ? 'true' : 'false'; ?>;

// In cart.php, modify the buy-now-btn click handler:
document.getElementById('buy-now-btn').addEventListener('click', () => {
    if (cart.length === 0) {
        alert('Your cart is empty');
        return;
    }
    window.location.href = 'payment.php'; // Direct redirect without stock check
});
    </script>
</body>

</html>
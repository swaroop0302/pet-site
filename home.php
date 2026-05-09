
<?php 
session_start();

// Add database connection
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



if (basename($_SERVER['PHP_SELF']) != 'home.php' && !isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- 
    - primary meta tag
  -->
  <title> Hight Quality Pet Food</title>
  <meta name="title" content="Hight Quality Pet Food">


  <!-- 
    - favicon
  -->
  <link rel="shortcut icon" href="favicon.svg" type="image/svg+xml">

  <!-- 
    - custom css link
  -->
  <link rel="stylesheet" href="style.css">

  <!-- 
    - google font link
  -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Bangers&family=Carter+One&family=Nunito+Sans:wght@400;700&display=swap"
    rel="stylesheet">

  <!-- 
    - preload images
  -->
  <link rel="preload" as="image" href="hero-banner.jpg">

</head>

<body id="top">

  <!-- 
    - #HEADER
  -->

 <header class="header" data-header>
    <div class="container">

      <button class="nav-toggle-btn" aria-label="toggle menu" data-nav-toggler>
        <ion-icon name="menu-outline" aria-hidden="true" class="menu-icon"></ion-icon>
        <ion-icon name="close-outline" aria-label="true" class="close-icon"></ion-icon>
      </button>

      <a href="#" class="logo">Pet site</a>

      <nav class="navbar" data-navbar>
        <ul class="navbar-list">
          <li class="navbar-item">
            <a href="#home" class="navbar-link" data-nav-link>Home</a>
          </li>
          <li class="navbar-item">
            <a href="#contact" class="navbar-link" data-nav-link>Contact</a>
          </li>
          <li class="navbar-item">
            <a href="order.php" class="navbar-link" data-nav-link>orders</a>
          </li>
          <li class="navbar-item">
      <a href="adminlogin.php" class="navbar-link" data-nav-link>Admin</a>
    </li>
        </ul>
      </nav>

      <div class="header-actions">
        <!-- Login/Logout Button -->
        <?php if(isset($_SESSION['user_email'])) : ?>
          <a href="logout.php" class="action-btn user" aria-label="User">
            <ion-icon name="log-out-outline" aria-hidden="true"></ion-icon>
          </a>
        <?php else : ?>
          <a href="login.php" class="action-btn user" aria-label="User">
            <ion-icon name="person-outline" aria-hidden="true"></ion-icon>
          </a>
        <?php endif; ?>

        <!-- Cart Button -->
        <a href="cart.php" class="action-btn cart">
          <ion-icon name="bag-handle-outline" aria-hidden="true"></ion-icon>
          <span class="btn-badge">0</span>
        </a>
      </div>

    </div>
  </header>





  <main>
    <article>

      <!-- 
        - #HERO
      -->

      <section class="section hero has-bg-image" id="home" aria-label="home"
        style="background-image: url('./cathome.jpg') ">
        <div class="container">

          <h1 class="h1 hero-title">
            <span class="span">High Quality</span> Pet Food
          </h1>

          <!-- <p class="hero-text">Sale up to 40% off today</p> -->

          <!-- <a href="#" class="btn">Shop Now</a> -->

        </div>
      </section>





      <!-- 
        - #CATEGORY
      -->

      <section class="section category" aria-label="category">
        <div class="container">

          <h2 class="h2 section-title">
            <span class="span">Top</span> categories
          </h2>

          <ul class="has-scrollbar">

            <li class="scrollbar-item">
              <div class="category-card">
                <a href="catfood.php">
                  <figure class="card-banner img-holder" style="--width: 330; --height: 300;">
                    <img src="category-1.jpg" width="330" height="300" loading="lazy" alt="Cat Food" class="img-cover">
                  </figure>
                </a>

                <h3 class="h3">
                  <a href="#" class="card-title">Cat Food</a>
                </h3>

              </div>
            </li>

            <li class="scrollbar-item">
              <div class="category-card">
                <a href="cattoys.php">
                  <figure class="card-banner img-holder" style="--width: 330; --height: 300;">
                    <img src="category-2.jpg" width="330" height="300" loading="lazy" alt="Cat Toys" class="img-cover">
                  </figure>

                  <h3 class="h3">
                    <a href="#" class="card-title">Cat Toys</a>
                  </h3>
                </a>

              </div>
            </li>

            <li class="scrollbar-item">
              <div class="category-card">
                <a href="dogfood.php">
                  <figure class="card-banner img-holder" style="--width: 330; --height: 300;">
                    <img src="category-3.jpg" width="330" height="300" loading="lazy" alt="Dog Food" class="img-cover">
                </a>
                </figure>

                <h3 class="h3">
                  <a href="dogfood.html" class="card-title">Dog Food</a>
                </h3>

              </div>
            </li>

            <li class="scrollbar-item">
              <div class="category-card">
                <a href="dogtoy.php">
                  <figure class="card-banner img-holder" style="--width: 330; --height: 300;">
                    <img src="category-4.jpg" width="330" height="300" loading="lazy" alt="Dog Toys" class="img-cover">
                  </figure>

                  <h3 class="h3">
                    <a href="#" class="card-title">Dog Toys</a>
                  </h3>
                </a>
              </div>
            </li>

            <li class="scrollbar-item">
              <div class="category-card">
                <a href="supplyment.php">
                  <figure class="card-banner img-holder" style="--width: 330; --height: 300;">
                    <img src="category-5.jpg" width="330" height="300" loading="lazy" alt="Dog Sumpplements"
                      class="img-cover">
                  </figure>

                  <h3 class="h3">
                    <a href="#" class="card-title">Sumpplements</a>
                  </h3>
                </a>
              </div>
            </li>
            <li class="scrollbar-item">
              <div class="category-card">
                <a href="medicine.php">
                  <figure class="card-banner img-holder" style="--width: 330; --height: 300;">
                    <img src="med.jpg" width="330" height="300" loading="lazy" alt="Medicine" class="img-cover">
                  </figure>

                  <h3 class="h3">
                    <a href="#" class="card-title">Medicine</a>
                  </h3>
                </a>

              </div>
            </li>
            <li class="scrollbar-item">
              <div class="category-card">
                <a href="birdfood.php">
                  <figure class="card-banner img-holder" style="--width: 330; --height: 300;">
                    <img src="bird.jpg" width="330" height="300" loading="lazy" alt="Bird Food" class="img-cover">
                  </figure>

                  <h3 class="h3">
                    <a href="#" class="card-title">Bird Food</a>
                  </h3>
                </a>
              </div>
            </li>

            <li class="scrollbar-item">
              <div class="category-card">
                <a href="fishfood.php">
                  <figure class="card-banner img-holder" style="--width: 330; --height: 300;">
                    <img src="fish.jpg" width="330" height="300" loading="lazy" alt="Fish Food" class="img-cover">
                  </figure>
                </a>

                <h3 class="h3">
                  <a href="#" class="card-title">Fish Food</a>
                </h3>

              </div>
            </li>


          </ul>

        </div>
      </section>
      <!-- 
        - #SERVICE
      -->

      <section class="section service" aria-label="service">
        <div class="container">

          <img src="service-image.png" width="122" height="136" loading="lazy" alt="" class="img">

          <h2 class="h2 section-title">
            <span class="span">What your pet needs,</span> when they need it.
          </h2>

          <ul class="grid-list">

            <li>
              <div class="service-card">

                <figure class="card-icon">
                  <img src="service-icon-1.png" width="70" height="70" loading="lazy" alt="service icon">
                </figure>

                <h3 class="h3 card-title">Free Same-Day Delivery</h3>

                <p class="card-text">
                  Order by 2pm local time to get free delivery.
                </p>

              </div>
            </li>

            <!-- <li>
              <div class="service-card">

                <figure class="card-icon">
                  <img src="service-icon-2.png" width="70" height="70" loading="lazy" alt="service icon">
                </figure>

                <h3 class="h3 card-title">30 Day Return</h3>

                <p class="card-text">
                  35% off your first order plus 5% off all future orders.
                </p>

              </div>
            </li> -->

            <li>
              <div class="service-card">

                <figure class="card-icon">
                  <img src="service-icon-3.png" width="70" height="70" loading="lazy" alt="service icon">
                </figure>

                <h3 class="h3 card-title">Security payment</h3>

                <p class="card-text">
                  secure payment with UPI, Wallet, Card, Netbanking.
                </p>

              </div>
            </li>

            <li>
              <div class="service-card">

                <figure class="card-icon">
                  <img src="service-icon-4.png" width="70" height="70" loading="lazy" alt="service icon">
                </figure>

                <h3 class="h3 card-title">24/7 Support</h3>

                <p class="card-text">
                  Shop online to get orders upto 10% off on every product.
                </p>

              </div>
            </li>

          </ul>

        </div>
      </section>





      <!-- 
        - #CTA
      -->

      <section class="cta has-bg-image" aria-label="cta" style="background-image: url('./cta-bg.jpg')">
        <div class="container">

          <figure class="cta-banner">
            <img src="cta-banner.png" width="900" height="660" loading="lazy" alt="cat" class="w-100">
          </figure>

          <div class="cta-content">

            <img src="cta-icon.png" width="120" height="35" loading="lazy" alt="taste guarantee" class="img">

            <h2 class="h2 section-title">Taste it, love it or we’ll replace it… Guaranteed!</h2>

            <p class="section-text">
              At Petio, we believe your dog and cat will love their food so much that if they don’t … we’ll help you
              find a
              replacement. That’s our taste guarantee.
            </p>
          </div>
        </div>
      </section>

      <!-- 
        - #BRAND
      -->

      <section class="section brand" aria-label="brand">
        <div class="container">

          <h2 class="h2 section-title">
            <span class="span">Popular</span> Brands
          </h2>

          <ul class="has-scrollbar">

            <li class="scrollbar-item">
              <div class="brand-card img-holder" style="--width: 150; --height: 150;">
                <img src="brand-1.jpg" width="150" height="150" loading="lazy" alt="brand logo" class="img-cover">
              </div>
            </li>

            <li class="scrollbar-item">
              <div class="brand-card img-holder" style="--width: 150; --height: 150;">
                <img src="brand-2.jpg" width="150" height="150" loading="lazy" alt="brand logo" class="img-cover">
              </div>
            </li>

            <li class="scrollbar-item">
              <div class="brand-card img-holder" style="--width: 150; --height: 150;">
                <img src="brand-3.jpg" width="150" height="150" loading="lazy" alt="brand logo" class="img-cover">
              </div>
            </li>

            <li class="scrollbar-item">
              <div class="brand-card img-holder" style="--width: 150; --height: 150;">
                <img src="brand-4.jpg" width="150" height="150" loading="lazy" alt="brand logo" class="img-cover">
              </div>
            </li>

            <li class="scrollbar-item">
              <div class="brand-card img-holder" style="--width: 150; --height: 150;">
                <img src="brand-5.jpg" width="150" height="150" loading="lazy" alt="brand logo" class="img-cover">
              </div>
            </li>

          </ul>

        </div>
      </section>

    </article>
  </main>
  <!-- 
    - #FOOTER
  -->
  <section class="section footer" id="contact">
    <footer class="footer" style="background-image: url('./footer-bg.jpg')">

      <div class="footer-top section">
        <div class="container">

          <div class="footer-brand">

            <a href="#" class="logo">Pet site</a>

            <p class="footer-text">
              If you have any question, please contact us at <a href="mailto:support@gmail.com"
                class="link">petsitesupport@gmail.com</a>
            </p>

            <ul class="contact-list">

              <li class="contact-item">
                <ion-icon name="location-outline" aria-hidden="true"></ion-icon>

                <address class="address">
                  mandavi emerald, behind chai cafe,vidyaratna Nagar,Manipal,Karnataka 576104
                </address>
              </li>

              <li class="contact-item">
                <ion-icon name="call-outline" aria-hidden="true"></ion-icon>

                <a href="tel:+16234567891011" class="contact-link">(+1)-6234-56-789-1011</a>
              </li>

            </ul>

            <ul class="social-list">

              <li>
                <p class="social-link">
                  <ion-icon name="logo-facebook"></ion-icon>
        </p>
              </li>

              <li>
                <p class="social-link">
                  <ion-icon name="logo-twitter"></ion-icon>
        </p>
              </li>

              <li>
                <p class="social-link">
                  <ion-icon name="logo-pinterest"></ion-icon>
        </p>
              </li>

              <li>
                <p class="social-link">
                  <ion-icon name="logo-instagram"></ion-icon>
        </p>
              </li>

            </ul>

          </div>

          <ul class="footer-list">

            <li>
              <p class="footer-list-title">Corporate</p>
            </li>

            <li>
              <p class="footer-link">Careers</p>
            </li>

            <li>
              <p class="footer-link">About Us</p>
            </li>

            <li>
              <p class="footer-link">Contact Us</p>
            </li>
            <li>
              <p class="footer-link">Vendors</p>
            </li>
          </ul>

          <ul class="footer-list">

            <li>
              <p class="footer-list-title">Information</p>
            </li>

            <li>
              <p class="footer-link">Online Store</p>
            </li>

            <li>
              <p class="footer-link">Privacy Policy</p>
            </li>

            <li>
              <p class="footer-link">Terms of Service</p>
            </li>
          </ul>
        </div>
      </div>
  </section>
  <div class="footer-bottom">
    <div class="container">

      <p class="copyright">
        &copy; 2025 Made with ♥ by <a href="#" class="copyright-link">Poojary boys.</a>
      </p>

      <!-- <img src="payment.png" width="397" height="32" loading="lazy" alt="payment method" class="img"> -->

    </div>
  </div>
  </footer>
  <!-- 
    - #BACK TO TOP
  -->

  <a href="#top" class="back-top-btn" aria-label="back to top" data-back-top-btn>
    <ion-icon name="chevron-up" aria-hidden="true"></ion-icon>
  </a>





  <!-- 
    - custom js link
  -->
  <script src="script.js" defer></script>

  <!-- 
    - ionicon link
  -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <script>
    // Cart functionality
    document.addEventListener('DOMContentLoaded', function () {
      // Initialize cart from localStorage or create empty array
      let cart = JSON.parse(localStorage.getItem('cart')) || [];

      // Function to update cart count in the UI
      function updateCartCount() {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const cartBadge = document.querySelector('.btn-badge');
        if (cartBadge) {
          cartBadge.textContent = totalItems;
        }
      }

      // Function to add item to cart
      function addToCart(product) {
        // Check if product already exists in cart
        const existingItem = cart.find(item =>
          item.title === product.title && item.price === product.price
        );

        if (existingItem) {
          existingItem.quantity += 1;
        } else {
          cart.push({
            title: product.title,
            price: product.price,
            quantity: 1,
            image: product.image
          });
        }

        // Save to localStorage
        localStorage.setItem('cart', JSON.stringify(cart));

        // Update cart count
        updateCartCount();

        // Show feedback to user
        showNotification(`${product.title} has been added to your cart!`);
      }

      // Function to show notification
      function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'cart-notification';
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
          notification.classList.add('fade-out');
          setTimeout(() => notification.remove(), 500);
        }, 2000);
      }

      // Add click event to all "Add to Cart" buttons
      document.querySelectorAll('.card-action-btn').forEach(button => {
        button.addEventListener('click', function () {
          const productCard = this.closest('.product-card');
          const title = productCard.querySelector('.card-title').textContent;
          const price = parseFloat(productCard.querySelector('.card-price').getAttribute('value'));
          const image = productCard.querySelector('.card-banner img').src;

          addToCart({
            title: title,
            price: price,
            image: image
          });
        });
      });

      // Initialize cart count on page load
      updateCartCount();
    });
  </script>

</body>

</html>
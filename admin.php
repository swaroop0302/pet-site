<?php
session_start();
$host = 'localhost';
$dbname = 'pet_shop';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get recent orders (last 5 orders)
    $stmt = $pdo->query("SELECT 
        o.order_id,
        r.first_name AS customer,
        r.phone_no AS contact,
        DATE_FORMAT(o.order_date, '%Y-%m-%d %H:%i') AS order_date,
        o.order_status
    FROM orders o
    LEFT JOIN registor r ON o.user_id = r.email
    ORDER BY o.order_date DESC
    LIMIT 5");
    
    $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

     $stmtProducts = $pdo->query("SELECT COUNT(*) AS total_products FROM products");
    $totalProducts = $stmtProducts->fetchColumn();
} catch(PDOException $e) {
    die("Could not connect to database: " . $e->getMessage());
}
// Redirect to login if not authenticated
if (!isset($_SESSION['admin_loggedin'])) {
    header("Location: adminlogin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHaven Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            background-color: #121212;
            color: #e0e0e0;
        }

        .sidebar {
            width: 250px;
            background-color: #1e1e1e;
            padding: 20px;
            height: 100vh;
            border-right: 1px solid #333;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-bottom: 1px solid #333;
            color: #ffffff;
        }

        .menu-items {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .menu-item {
            padding: 12px;
            margin: 8px 0;
            cursor: pointer;
            border-radius: 8px;
            color: #ccc;
            display: block;
            padding: 12px;
            margin: 0;
        }

        .menu-item i {
            margin-right: 10px;
            width: 20px;
            color: #888;
        }

        .menu-item:hover {
            background-color: #2c2c2c;
        }

        .logout-container {
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid #333;
        }

        .logout-link {
            display: flex;
            align-items: center;
            padding: 12px;
            color: #ff6b6b;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.2s;
        }

        .logout-link:hover {
            background-color: #2c2c2c;
            color: #ff5252;
        }

        .logout-link i {
            margin-right: 10px;
            width: 20px;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            background-color: #121212;
        }

        h1,
        h2,
        h3 {
            color: #ffffff;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }


        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .login-icon {
            background-color: #2a2a2a;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #eee;
            transition: background-color 0.2s;
        }

        .login-icon:hover {
            background-color: #3a3a3a;
        }

        .header-divider {
            border: none;
            border-top: 1px solid #333;
            margin-bottom: 20px;
        }

        .dashboard-content {
            display: flex;
            flex-direction: column;
        }

        .metrics-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .metric-card {
            background-color: #1e1e1e;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #333;
            position: relative;
        }

        .metric-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .metric-icon {
            font-size: 28px;
            color: #888;
        }

        .metric-value {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
            color: #fff;
        }

        .metric-change,
        .product-stock {
            color: #aaa;
            font-size: 0.9em;
        }

        .orders-container {
            background-color: #1e1e1e;
            margin-top: 30px;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #333;
        }

        .orders-container h3 {
            margin-bottom: 15px;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1e1e1e;
            margin-top: 20px;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #333;
            color: #ddd;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9em;
        }

        .delivered {
            background-color: #2e7d32;
            color: #b9f6ca;
        }

        .processing {
            background-color: #ffb300;
            color: #fff8e1;
        }

        .shipped {
            background-color: #1565c0;
            color: #bbdefb;
        }

        .view-all {
            margin-top: 15px;
            text-align: center;
        }

        .view-all a {
            text-decoration: none;
            color: #64b5f6;
            font-weight: bold;
            transition: color 0.2s;
        }

        .view-all a:hover {
            color: #42a5f5;
        }
        .status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.9em;
    display: inline-block;
}

.status.delivered {
    background-color: #2e7d32;
    color: #b9f6ca;
}

.status.pending {
    background-color: #f44336;
    color: #ffebee;
}

.btn-details {
    background-color: #6a1b9a;
    color: #e1bee7;
    padding: 6px 12px;
    border-radius: 20px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-details:hover {
    background-color: #4a148c;
}
.dropdown-header i:first-child {
    color: #888;
    margin-right: 10px;
}

.dropdown-parent {
    position: relative;
    cursor: pointer;
}

.dropdown-header {
    padding: 12px;
    display: flex;
    align-items: center;
    border-radius: 8px;
}

.dropdown-header:hover {
    background-color: #2c2c2c;
}

.dropdown-arrow {
    font-size: 12px;
    transition: transform 0.3s ease;
    margin-left: auto;
}

.dropdown-items {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    padding-left: 36px;
    margin-top: 4px;
}

.dropdown-parent.active .dropdown-items {
    max-height: 200px;
}

.dropdown-parent.active .dropdown-arrow {
    transform: rotate(90deg);
}

.sub-item {
    padding: 6px 12px ;
    font-size: 0.9em;
    margin: 2px 0 ;
    border-radius: 0 ;
    display: block;
}

.sub-item:hover {
    background-color: #252525 !important;
}

.sub-item i {
    font-size: 0.9em;
    margin-right: 8px !important;
}
 .dashboard-header {
        margin-bottom: 30px;
        padding: 25px;
        background: linear-gradient(135deg, #6a1b9a 0%, #4a148c 100%);
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }

    .dashboard-header h2 {
        font-size: 28px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .dashboard-header h2::before {
        content: '';
        width: 4px;
        height: 100%;
        background: #e1bee7;
        position: absolute;
        left: 0;
        top: 0;
    }

    .dashboard-header p {
        color: #e1bee7;
        margin-top: 10px;
        font-size: 16px;
    }
 .dashboard-content {
        margin-top: -20px;
    }

    .metrics-container {
        margin-top: 20px;
    }
    /* Add this to your existing CSS */
.dropdown-items {
    overflow-y: auto; /* Changed from hidden */
    scrollbar-width: thin;
    scrollbar-color: #4a4a4a #1e1e1e;
}

/* Custom scrollbar styling */
.dropdown-items::-webkit-scrollbar {
    width: 8px;
}

.dropdown-items::-webkit-scrollbar-track {
    background: #1e1e1e;
    border-radius: 4px;
}

.dropdown-items::-webkit-scrollbar-thumb {
    background: #4a4a4a;
    border-radius: 4px;
}

.dropdown-items::-webkit-scrollbar-thumb:hover {
    background: #5a5a5a;
}

/* Increase max-height for scrolling */
.dropdown-parent.active .dropdown-items {
    max-height: 300px; /* Increased from 200px */
}
    </style>
</head>

<body>
  <div class="sidebar">
    <h2>Petsite Admin</h2>
    <div class="menu-items">
        <a href="admin.php" class="menu-item"><i class="fas fa-tachometer-alt"></i>Dashboard</a>

        <!-- Products Dropdown -->
        <div class="dropdown-parent">
            <div class="dropdown-header">
                <i class="fas fa-box"></i>Products
                <i class="fas fa-chevron-right dropdown-arrow"></i>
            </div>
            <div class="dropdown-items">
                <a href="add_product.php" class="menu-item sub-item">
                    <i class="fas fa-plus"></i>Add Product
                </a>
                <a href="manage_product.php" class="menu-item sub-item">
                    <i class="fas fa-cog"></i>manage product
                </a>
                
            </div>
        </div>

        <a href="orders.php" class="menu-item"><i class="fas fa-shopping-cart"></i>Orders</a>
        <a href="customers.php" class="menu-item"><i class="fas fa-users"></i>Customers</a>
        
        <!-- Existing Suppliers Dropdown -->
        <div class="dropdown-parent">
            <div class="dropdown-header">
                <i class="fas fa-truck"></i>Delivery partner
                <i class="fas fa-chevron-right dropdown-arrow"></i>
            </div>
            <div class="dropdown-items">
                <a href="add_supplier.php" class="menu-item sub-item">
                    <i class="fas fa-plus"></i>Add partner
                </a>
                <a href="manage_supplier.php" class="menu-item sub-item">
                    <i class="fas fa-cog"></i>partner detail
                </a>
            </div>
        </div>
    </div>
    
    <div class="logout-container">
        <a href="logout.php" class="logout-link" id="logout-link">
            <i class="fas fa-sign-out-alt"></i>Logout
        </a>
    </div>
</div>
   <div class="main-content">
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h2>
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </h2>
            <p>Welcome back to Petsite Admin Panel</p>
        </div>

            <!-- Moved metrics container above the table -->
            <div class="metrics-container">
                <div class="metric-card">
                    <div class="metric-header">
                        <h3>Total Sales</h3>
                        <i class="metric-icon fas fa-dollar-sign"></i>
                    </div>
                    <div class="metric-value"id="totalSales">Rs0</div>
                    
                </div>

                <div class="metric-card">
                    <div class="metric-header">
                        <h3>Total Orders</h3>
                        <i class="metric-icon fas fa-shopping-bag"></i>
                    </div>
                    <div class="metric-value"id="totalOrders">0</div>
                    
                </div>

                <div class="metric-card">
    <div class="metric-header">
        <h3>Products</h3>
        <i class="metric-icon fas fa-box-open"></i>
    </div>
    <div class="metric-value"><?= htmlspecialchars($totalProducts) ?></div>
</div>
                <div class="metric-card">
    <div class="metric-header">
        <h3>Customers</h3>
        <i class="metric-icon fas fa-user-friends"></i>
    </div>
    <div class="metric-value" id="customers-count">0</div>
</div>

                <!-- Add other product cards -->
            </div>

           <div class="orders-container">
    <h3>Recent Orders</h3>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Contact</th>
            <th>Order Date</th>
            <th>Status</th>
            <th>Details</th>
        </tr>
        <?php foreach ($recentOrders as $order): ?>
        <tr>
            <td><?= htmlspecialchars($order['order_id']) ?></td>
            <td><?= htmlspecialchars($order['customer']) ?></td>
            <td><?= htmlspecialchars($order['contact']) ?></td>
            <td><?= htmlspecialchars($order['order_date']) ?></td>
            <td>
                <span class="status <?= strtolower($order['order_status']) ?>">
                    <?= htmlspecialchars($order['order_status']) ?>
                </span>
            </td>
            <td>
                <button class="btn-details" 
                        onclick="viewOrderDetails('<?= $order['order_id'] ?>')">
                    View Details
                </button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <div class="view-all">
        <a href="orders.php">View All Orders</a>
    </div>
</div>

        </div>
    </div>
    <script>
        function viewOrderDetails(orderId) {
    // Implement your details view logic here
    console.log(`Viewing details for order ${orderId}`);
    window.location.href = `orders.php?order_id=${orderId}`;
}
document.addEventListener('DOMContentLoaded', function() {
    // Logout functionality
    const logoutLink = document.querySelector('.logout-link');
    
    logoutLink.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Confirm logout
        const confirmLogout = confirm('Are you sure you want to logout?');
        if (!confirmLogout) return;

        // Send logout request
        fetch('logout.php', {
            method: 'POST',
            credentials: 'same-origin'
        })
        .then(response => {
            if (response.ok) {
                window.location.href = 'adminlogin.php';
            } else {
                alert('Logout failed. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during logout.');
        });
    });
if (typeof BroadcastChannel !== 'undefined') {
    const metricsChannel = new BroadcastChannel('admin_updates');
    
    metricsChannel.onmessage = (event) => {
        if (event.data.type === 'metrics_update') {
            // Update Total Sales
            const salesElement = document.getElementById('totalSales');
            if (salesElement) {
                const currentSales = parseFloat(salesElement.textContent.replace('Rs', '')) || 0;
                const newAmount = parseFloat(event.data.order_amount) || 0;
                salesElement.textContent = `Rs${(currentSales + newAmount).toFixed(2)}`;
            }
            
            // Update Total Orders
            const ordersElement = document.getElementById('totalOrders');
            if (ordersElement) {
                const currentOrders = parseInt(ordersElement.textContent) || 0;
                ordersElement.textContent = currentOrders + 1;
            }
        }
    };
}

    // Polling fallback (optional)
    function updateMetrics() {
        fetch('get_metrics.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalSales').textContent = `Rs${data.total_sales.toFixed(2)}`;
                document.getElementById('totalOrders').textContent = data.total_orders;
            });
    }
    updateMetrics(); // Initial load
    setInterval(updateMetrics, 10000); // Refresh every 10 seconds
});
 
</script>
<script>
// Add this after the existing logout script
document.addEventListener('DOMContentLoaded', function() {
    // Fetch customer count
    fetch('get_customers_count.php')
        .then(response => response.json())
        .then(data => {
            const countElement = document.getElementById('customers-count');
            if (countElement && data.count !== undefined) {
                countElement.textContent = data.count;
            }
        })
        .catch(error => console.error('Error fetching customer count:', error));
});
</script>
<script>
// Updated dropdown script
document.addEventListener('DOMContentLoaded', function() {
    const dropdownParents = document.querySelectorAll('.dropdown-parent');
    
    dropdownParents.forEach(parent => {
        parent.querySelector('.dropdown-header').addEventListener('click', function(e) {
            parent.classList.toggle('active');
        });
    });
});
</script> 

</body>

</html>
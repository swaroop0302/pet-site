<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate"); // Add these
header("Pragma: no-cache");
header("Expires: 0");
if (!isset($_SESSION['admin_loggedin'])) {
    header("Location: adminlogin.php");
    exit();
}

$host = 'localhost';
$dbname = 'pet_shop';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Modified query to exclude status fields
   // Modified query in orders.php
  $stmt = $pdo->query("SELECT 
        o.order_id,
        o.user_id,
        r.first_name AS customer,
        r.phone_no AS contact,
        DATE_FORMAT(o.order_date, '%Y-%m-%d %H:%i') AS order_date,
        o.order_status,
        'Paid' AS payment_status,  -- Static value simulation
        r.latitude,  
        r.longitude  
    FROM orders o
    LEFT JOIN registor r ON o.user_id = r.email");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Could not connect to database: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
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
        .table-container {
            
        background-color: #1e1e1e;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #333;
        overflow-x: auto;
        max-height: 600px; /* Optional vertical scroll */
        overflow-y: auto; /* Optional vertical scroll */
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .orders-table th {
            background-color: #2a2a2a;
        }
      .status-btn {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.9em;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-delivered, .btn-completed {
        background-color: #2e7d32;
        color: #b9f6ca;
    }

    .btn-pending, .btn-unpaid {
        background-color: #f44336;
        color: #ffebee;
    }

    .btn-delivered:hover, .btn-completed:hover {
        background-color: #1b5e20;
    }

    .btn-pending:hover, .btn-unpaid:hover {
        background-color: #d32f2f;
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

    /* Adjust table padding for better spacing */
    .orders-table td, .orders-table th {
        padding: 12px;
        font-size: 0.9em;
    }

    /* Optional: Add hover effect for rows */
    .orders-table tbody tr:hover {
        background-color: #2a2a2a;
    }
    /* Modal Styles */
.details-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #2a2a2a;
    padding: 20px;
    border-radius: 10px;
    width: 80%;
    max-width: 600px;
    max-height: 80vh;
    z-index: 1000;
    box-shadow: 0 0 20px rgba(0,0,0,0.5);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 1px solid #333;
    padding-bottom: 10px;
}

.modal-close {
    cursor: pointer;
    font-size: 24px;
    color: #888;
}

.modal-close:hover {
    color: #fff;
}

.modal-content {
    overflow-y: auto;
    max-height: 60vh;
}

.product-detail {
    display: flex;
    align-items: center;
    padding: 10px;
    margin: 10px 0;
    background: #1e1e1e;
    border-radius: 8px;
}

.product-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 15px;
}

.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 999;
}
/* Add to existing styles */
.map-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 70%;
    height: 70vh;
    background: #2a2a2a;
    border-radius: 10px;
    z-index: 1001;
    padding: 20px;
}

#map-container {
    width: 100%;
    height: 100%;
    border-radius: 8px;
}

.btn-location {
            background-color: #4285f4;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
.btn-location:hover {
    background-color: #357abd;
}
.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 999;
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

.dropdown-header i:first-child {
    color: #888;
    margin-right: 10px;
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
}

.dropdown-parent.active .dropdown-items {
    max-height: 200px;
}

.dropdown-parent.active .dropdown-arrow {
    transform: rotate(90deg);
}

.sub-item {
    padding: 8px 12px !important;
    font-size: 0.9em;
    margin: 2px 0 !important;
    border-radius: 0 !important;
}

.sub-item:hover {
    background-color: #252525 !important;
}

.sub-item i {
    font-size: 0.9em;
    margin-right: 8px !important;
}
 .page-header {
        padding: 25px;
        background: linear-gradient(135deg, #6a1b9a 0%, #4a148c 100%);
        border-radius: 10px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .page-header h2 {
        font-size: 28px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .page-header h2::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: #e1bee7;
    }

    .page-header i {
        font-size: 24px;
        color: #e1bee7;
        margin-left: 10px;
    }

    /* Adjust content positioning */
    .dashboard-content {
        margin-top: -20px;
    }

    .table-container {
        margin-top: 20px;
    }
    .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .orders-table th,
        .orders-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        .orders-table th {
            background-color: #6a1b9a;
            color: #e1bee7;
            font-weight: bold;
        }

        .orders-table tr:hover {
            background-color: #2a2a2a;
        }

    </style>
</head>
<body>
    <!-- Sidebar (same as add_supplier.php) -->
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
                    <i class="fas fa-cog"></i>Manage partner
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
        <div class="page-header">
            <h2>
                <i class="fas fa-shopping-cart"></i>
                Orders Management
            </h2>
        </div>
              <div class="table-container">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Order Date</th>
                            <th>Order Status</th>
                            <th>Payment Status</th>
                            <th>Location</th>
                            <th>more details</th>
                        </tr>
                    </thead>
    <tbody><?php foreach ($orders as $order): ?>
<tr>
  <td><?= htmlspecialchars($order['order_id']) ?></td>
 <td><?= htmlspecialchars($order['customer'] )?></td>
<td><?= htmlspecialchars($order['contact'] ) ?></td>
                            <td><?= htmlspecialchars($order['order_date']) ?></td>
                          <td>
    <button class="status-btn <?= $order['order_status'] === 'Delivered' ? 'btn-delivered' : 'btn-pending' ?>" 
            data-order-id="<?= $order['order_id'] ?>"
            onclick="toggleOrderStatus(this)"
            <?= $order['order_status'] === 'Delivered' ? 'disabled' : '' ?>>
        <?= htmlspecialchars($order['order_status'] === 'Delivered' ? 'Delivered' : 'Pending') ?>
    </button>
</td>
      <td>
                <button class="status-btn <?= $order['payment_status'] === 'Paid' ? 'btn-completed' : 'btn-unpaid' ?>" 
                        disabled>
                    <?= htmlspecialchars($order['payment_status']) ?>
                </button>
            </td>
<!-- Add the location button in table rows -->
<td>
    <?php if (!empty($order['latitude']) && !empty($order['longitude'])): ?>
    <a class="btn-location" 
       href="https://www.google.com/maps?q=<?= $order['latitude'] ?>,<?= $order['longitude'] ?>" 
       target="_blank"
       rel="noopener noreferrer">
        View on Map
    </a>
    <?php else: ?>
        N/A
    <?php endif; ?>
</td>
 <td>
                <button class="btn-details" 
                        data-order-id="<?= $order['order_id'] ?>"
                        onclick="viewOrderDetails(this)">
                    View Details
                </button>
            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script>
function toggleOrderStatus(button) {
    const newStatus = 'Delivered';
    const orderId = button.dataset.orderId;
    
    fetch(`update_order_status.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: orderId, status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.textContent = newStatus;
            button.className = 'status-btn btn-delivered';
            button.disabled = true;

            // Broadcast update to admin dashboard
            if (typeof BroadcastChannel !== 'undefined') {
                const channel = new BroadcastChannel('admin_updates');
                channel.postMessage({ 
                    type: 'metrics_update',
                    order_amount: data.order_amount
                });
                channel.close();
            }

        } else {
            console.error('Update failed:', data.error);
            alert('Failed to update status.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    });
}
// Uncomment and update fetch URLs
function updateOrderStatus(orderId, newStatus) {
    fetch(`update_order_status.php?order_id=${orderId}&status=${newStatus}`, {
        method: 'POST'
    }).catch(error => console.error('Error:', error));
}

function togglePaymentStatus(button) {
    const orderId = button.dataset.orderId;
    const currentStatus = button.textContent.trim();
    const newStatus = currentStatus === 'Paid' ? 'Unpaid' : 'Paid';

    fetch(`update_payment_status.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            order_id: orderId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.textContent = newStatus;
            button.className = `status-btn ${newStatus === 'Paid' ? 'btn-completed' : 'btn-unpaid'}`;
            button.disabled = (newStatus === 'Paid'); // Disable if Paid
        } else {
            alert('Failed to update: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => console.error('Error:', error));
}
function viewOrderDetails(button) {
    const orderId = button.dataset.orderId;
    // Implement your details view logic here
    console.log(`Viewing details for order ${orderId}`);
    //window.location.href = `order.php?order_id=${orderId}`;
}
</script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutLink = document.querySelector('.logout-link');
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                const confirmLogout = confirm('Are you sure you want to logout?');
                if (!confirmLogout) return;
                fetch('logout.php', {
                    method: 'POST',
                    credentials: 'same-origin'
                }).then(response => {
                    if (response.ok) {
                        window.location.href = 'adminlogin.php';
                    } else {
                        alert('Logout failed.');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Logout error.');
                });
            });
        document.querySelectorAll('[onclick="togglePaymentStatus(this)"]').forEach(btn => {
        btn.textContent = 'Paid';
        btn.className = 'status-btn btn-completed';
        btn.disabled = true;
        btn.onclick = null;
    });

});
    </script>
    <div class="overlay" id="overlay"></div>
<div class="details-modal" id="detailsModal">
    <div class="modal-header">
        <h3>Order Details</h3>
        <span class="modal-close" onclick="closeModal()">&times;</span>
    </div>
    <div class="modal-content" id="modalContent"></div>
    <script>
        function viewOrderDetails(button) {
    const orderId = button.dataset.orderId;
    const modal = document.getElementById('detailsModal');
    const overlay = document.getElementById('overlay');
    const modalContent = document.getElementById('modalContent');

    // Show loading state
    modalContent.innerHTML = '<div class="loading">Loading details...</div>';
    modal.style.display = 'block';
    overlay.style.display = 'block';

    fetch(`get_order_details.php?order_id=${orderId}`)
        .then(response => response.json())
        .then(products => {
            if (products.error) {
                modalContent.innerHTML = `<div class="error">${products.error}</div>`;
                return;
            }

            let html = '<div class="products-list">';
            products.forEach(product => {
                html += `
                    <div class="product-detail">
                        <img src="${product.image}" class="product-image" alt="${product.title}">
                        <div>
                            <div class="product-title">${htmlSpecialChars(product.title)}</div>
                            <div class="product-price">₹${product.price.toFixed(2)}</div>
                            <div>Quantity: ${product.quantity}</div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            modalContent.innerHTML = html;
        })
        .catch(error => {
            modalContent.innerHTML = `<div class="error">Error loading details</div>`;
            console.error('Error:', error);
        });
}

function htmlSpecialChars(str) {
    return str.replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#039;');
}

function closeModal() {
    document.getElementById('detailsModal').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('overlay').addEventListener('click', closeModal);
    </script>
 <script>
// Ensure dropdown script matches admin.php
document.addEventListener('DOMContentLoaded', function() {
    const dropdownParents = document.querySelectorAll('.dropdown-parent');
    
    dropdownParents.forEach(parent => {
        parent.querySelector('.dropdown-header').addEventListener('click', function(e) {
            parent.classList.toggle('active');
        });
    });
});
</script>
</div>
</body>
</html>
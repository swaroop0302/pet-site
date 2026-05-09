<?php
session_start();
if (!isset($_SESSION['admin_loggedin'])) {
    header("Location: adminlogin.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pet_shop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$supplier = [];
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get supplier data
    $stmt = $conn->prepare("SELECT * FROM suppliers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $supplier = $result->fetch_assoc();
    }
    $stmt->close();
}

// Update supplier
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    
    // Validation
    if (empty($name) || empty($email) || empty($address) || empty($phone)) {
        $_SESSION['error'] = "All fields are required!";
    } else {
        $stmt = $conn->prepare("UPDATE suppliers SET name=?, email=?, address=?, phone=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $address, $phone, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Supplier updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating supplier: " . $conn->error;
        }
        $stmt->close();
    }
    $conn->close();
    header("Location: manage_supplier.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Copy all CSS from add_supplier.php -->
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

        /* Include all other styles from admin.php here */
        
        .form-container {
            flex: 1;
            padding: 30px;
            background-color: #121212;
        }

        .supplier-form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #1e1e1e;
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #333;
        }

        .form-title {
            color: #fff;
            margin-bottom: 30px;
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #ccc;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            background-color: #2a2a2a;
            border: 1px solid #333;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #555;
        }

        .button-group {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #6a1b9a;
            color: #e1bee7;
            border: none;
        }

        .btn-primary:hover {
            background-color: #4a148c;
        }

        .btn-secondary {
            background-color: #2a2a2a;
            color: #ccc;
            border: 1px solid #333;
        }

        .btn-secondary:hover {
            background-color: #3a3a3a;
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

        .search-bar {
            flex: 1;
            max-width: 400px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 12px 20px 12px 40px;
            border: 1px solid #333;
            border-radius: 25px;
            font-size: 16px;
            background-color: #1e1e1e;
            color: #fff;
        }

        .search-input:focus {
            outline: none;
            border-color: #555;
            background-color: #1e1e1e;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
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
        
        .table-container {
            background-color: #1e1e1e;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #333;
        }
        
        .customers-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .customers-table th {
            background-color: #2a2a2a;
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
   .main-header {
            display: flex;
            align-items: center;
            padding: 20px 30px;
            background: linear-gradient(135deg, #6a1b9a 0%, #4a148c 100%);
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .header-title {
            font-size: 24px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-icon {
            font-size: 28px;
            color: #e1bee7;
        }

        /* Enhanced Form Styling */
        .supplier-form {
            max-width: 600px;
            margin: 0 auto;
            background: linear-gradient(145deg, #1e1e1e 0%, #2a2a2a 100%);
            padding: 40px;
            border-radius: 15px;
            border: 1px solid #333;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
        }

        .supplier-form:hover {
            transform: translateY(-2px);
        }

        .form-group input {
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #6a1b9a;
            box-shadow: 0 0 8px rgba(106,27,154,0.4);
        }

        .btn-primary {
            background: linear-gradient(135deg, #6a1b9a 0%, #4a148c 100%);
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            bottom: -50%;
            left: -50%;
            background: linear-gradient(45deg, 
                transparent 25%, 
                rgba(255,255,255,0.1) 50%, 
                transparent 75%);
            transform: rotateZ(60deg) translate(-5em, 7.5em);
            animation: buttonShine 3s infinite;
        }

        @keyframes buttonShine {
            100% {
                transform: rotateZ(60deg) translate(5em, -7.5em);
            }
        }

        .form-title {
            font-size: 28px;
            letter-spacing: 0.5px;
            position: relative;
            padding-left: 20px;
        }

        .form-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 70%;
            width: 4px;
            background: #6a1b9a;
            border-radius: 2px;
        }
        .alert {
    padding: 15px;
    margin: 20px auto;
    max-width: 600px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert.success {
    background-color: #2e7d32;
    color: #b9f6ca;
    border: 1px solid #1b5e20;
}

.alert.error {
    background-color: #c62828;
    color: #ffcdd2;
    border: 1px solid #b71c1c;
}

.alert i {
    font-size: 20px;
}
    </style>
</head>
<body>
   <div class="sidebar">
        <h2>Petsite Admin</h2>
        <div class="menu-items">
            <a href="admin.php" class="menu-item"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
            <div class="menu-item"><i class="fas fa-box"></i>Products</div>
            <a href="orders.php" class="menu-item"><i class="fas fa-shopping-cart"></i>Orders</a>
            <a href="customers.php" class="menu-item"><i class="fas fa-users"></i>Customers</a>
            <div class="dropdown-parent">
                <div class="dropdown-header">
                    <i class="fas fa-truck"></i>Suppliers
                    <i class="fas fa-chevron-right dropdown-arrow"></i>
                </div>
                <div class="dropdown-items">
                    <a href="add_supplier.php" class="menu-item sub-item">
                        <i class="fas fa-plus"></i>Add Supplier
                    </a>
                    <a href="manage_supplier.php" class="menu-item sub-item">
                        <i class="fas fa-cog"></i>Manage Supplier
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


    <div class="form-container">
        <nav class="main-header">
            <div class="header-title">
                <i class="fas fa-truck-moving header-icon"></i>
                <span>Edit Supplier</span>
            </div>
        </nav>

        <div class="supplier-form">
    <h1 class="form-title">Edit Supplier</h1>
    <?php if (!empty($supplier)): ?>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $supplier['id'] ?>">
        
        <div class="form-group">
            <label for="name">Supplier Name *</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($supplier['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Supplier Email *</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($supplier['email']) ?>" required>
        </div>

        <div class="form-group">
            <label for="address">Supplier Address *</label>
            <input type="text" id="address" name="address" value="<?= htmlspecialchars($supplier['address']) ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Supplier Phone *</label>
            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($supplier['phone']) ?>" required>
        </div>

        <div class="button-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Supplier
            </button>
        </div>
    </form>
    <?php else: ?>
        <div class="alert error">Supplier not found!</div>
    <?php endif; ?>
</div>
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert success">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert error">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>
    </div>
</body>
</html>
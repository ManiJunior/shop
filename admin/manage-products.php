
<?php 
// 1. Start session to show messages
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

// 🔒 [نیا چوکیدار کوڈ] اگر بندہ لاگ ان ہی نہیں ہے، تو اسے لاگ ان پیج پر بھگاؤ
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 🔒 [نیا چوکیدار کوڈ] اگر لاگ ان ہے لیکن لیول 3 (اسٹاف) ہے، تو اسے یہ پیج مت دکھاؤ
if ((int)$_SESSION['admin_level'] === 3) {
    header("Location: index.php"); 
    exit();
}

// 2. Include Database Connection
include '../db_connect.php'; 
// --- SEARCH & FILTER LOGIC ---
$search_query = "";
$category_filter = "";

// ہم ڈیٹا بیس سے صرف وہی چیزیں نکال رہے ہیں جن کی ضرورت ہے
$query = "SELECT product_id, product_name, category FROM products WHERE 1=1";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $query .= " AND product_name LIKE '%$search_query%'";
}

if (isset($_GET['category_filter']) && !empty($_GET['category_filter'])) {
    $category_filter = mysqli_real_escape_string($conn, $_GET['category_filter']);
    $query .= " AND category = '$category_filter'";
}

$query .= " ORDER BY product_id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - GreenHub Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { display: flex; background-color: #f4f6f9; min-height: 100vh; }
        
        /* Sidebar Styling */
        .sidebar { width: 260px; background-color: #2c3e50; color: white; padding: 20px 0; display: flex; flex-direction: column; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; font-size: 24px; color: #2ecc71; }
        .sidebar a { padding: 15px 25px; color: #ecf0f1; text-decoration: none; display: block; }
        .sidebar a:hover { background-color: #34495e; color: #2ecc71; }
        
        /* Main Content Styling */
        .main-content { flex: 1; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: white; padding: 15px 20px; border-radius: 8px; }
        
        /* Filter Form Styling */
        .search-container { background: white; padding: 20px; border-radius: 8px; margin-bottom: 25px; display: flex; gap: 15px; }
        .search-box { flex: 2; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .filter-select { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .btn-search { background-color: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        
        /* Table Card Styling */
        .table-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .product-table { width: 100%; border-collapse: collapse; }
        .product-table th, .product-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        .product-table th { background-color: #f8f9fa; font-weight: 600; color: #333; }
        
        /* Edit Button Styling */
        .btn-edit { background-color: #3498db; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px; display: inline-block; transition: 0.3s; }
        .btn-edit:hover { background-color: #2980b9; }
        
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 4px; font-weight: bold; text-align: center; background-color: #d4edda; color: #155724; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>Product Inventory</h1>
            <span>Welcome, Admin</span>
        </div>

        <form method="GET" action="manage-products.php" class="search-container">
            <input type="text" name="search" class="search-box" placeholder="Search product by name..." value="<?php echo htmlspecialchars($search_query); ?>">
            <select name="category_filter" class="filter-select">
                <option value="">All Categories</option>
                <option value="Pesticides" <?php if($category_filter == 'Pesticides') echo 'selected'; ?>>Pesticides</option>
                <option value="Fertilizers" <?php if($category_filter == 'Fertilizers') echo 'selected'; ?>>Fertilizers</option>
                <option value="Seeds" <?php if($category_filter == 'Seeds') echo 'selected'; ?>>Seeds</option>
            </select>
            <button type="submit" class="btn-search">🔍 Filter</button>
            <?php if(!empty($search_query) || !empty($category_filter)): ?>
                <a href="manage-products.php" style="background:#95a5a6; color:white; padding:10px 15px; border-radius:4px; text-decoration:none; font-weight:bold;">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-card">
            <h3>Products Control List</h3><br>

            <?php
            if (isset($_SESSION['stock_update'])) {
                echo '<div class="alert">' . $_SESSION['stock_update'] . '</div>';
                unset($_SESSION['stock_update']);
            }
            ?>

            <table class="product-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $p_id = $row['product_id'];
                            
                            echo "<tr>";
                            // 1. Name Column
                            echo "<td><strong>" . htmlspecialchars($row['product_name']) . "</strong></td>";
                            // 2. Category Column
                            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            // 3. Action Edit Column
                            echo "<td><a href='edit-product.php?id=" . $p_id . "' class='btn-edit'>✏️ Edit Product</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center; color:#999; padding: 20px;'>No products found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
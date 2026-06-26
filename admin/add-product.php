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
if ((int)$_SESSION['admin_level'] === 2) {
    header("Location: index.php"); 
    exit();
}

// 2. Include Database Connection
include '../db_connect.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - GreenHub Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; background-color: #f4f6f9; min-height: 100vh; }
        
        /* Sidebar Styling */
        .sidebar { width: 260px; background-color: #2c3e50; color: white; padding: 20px 0; display: flex; flex-direction: column; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; font-size: 24px; color: #2ecc71; letter-spacing: 1px; }
        .sidebar a { padding: 15px 25px; color: #ecf0f1; text-decoration: none; display: block; font-size: 16px; transition: 0.3s; }
        .sidebar a:hover { background-color: #34495e; color: #2ecc71; border-left: 4px solid #2ecc71; }
        
        /* Main Content Styling */
        .main-content { flex: 1; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: white; padding: 15px 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .header h1 { font-size: 24px; color: #333; }
        
        /* Form Card Styling */
        .form-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
        .form-card h3 { margin-bottom: 20px; color: #2c3e50; border-bottom: 2px solid #f4f6f9; padding-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 14px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 15px; }
        .form-control:focus { border-color: #2ecc71; outline: none; }
        textarea.form-control { height: 100px; resize: vertical; }
        .row { display: flex; gap: 20px; }
        .col { flex: 1; }
        
        /* Button & Alerts */
        .btn-submit { background-color: #2ecc71; color: white; padding: 12px 20px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-submit:hover { background-color: #27ae60; }
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 4px; font-weight: bold; text-align: center; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- MAIN CONTENT AREA -->
    <div class="main-content">
        <div class="header">
            <h1>Product Management</h1>
            <span>Welcome, Admin | <a href="#" style="color: #e74c3c; text-decoration: none; font-weight: bold;">Logout</a></span>
        </div>

        <div class="form-card">
            <h3>Add New Product</h3>

            <!-- NEW PHP Session Messages Logic -->
            <?php
            if (isset($_SESSION['status_msg'])) {
                if ($_SESSION['status_msg'] == 'success') {
                    echo '<div class="alert alert-success">Product and Image uploaded successfully!</div>';
                } elseif ($_SESSION['status_msg'] == 'error') {
                    echo '<div class="alert alert-error">Database Error: Could not add product.</div>';
                } elseif ($_SESSION['status_msg'] == 'upload_error') {
                    echo '<div class="alert alert-error">File Error: Failed to upload product image.</div>';
                }
                
                // CRITICAL LINE: Destroys the message from memory so it won't show on refresh!
                unset($_SESSION['status_msg']);
            }
            ?>

            <form action="insert-product.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="product_name" class="form-control" required placeholder="e.g., Nativo Pesticide">
                </div>

                <div class="row">
                    <div class="col class=form-group">
                        <label>Category</label>
                        <select name="category" class="form-control" required>
                            <option value="Pesticides">Pesticides</option>
                            <option value="Fertilizers">Fertilizers</option>
                            <option value="Seeds">Seeds</option>
                        </select>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col class=form-group">
                        <label>Old Price (PKR)</label>
                        <input type="number" name="old_price" class="form-control" required placeholder="e.g., 1500">
                    </div>
                    <div class="col class=form-group">
                        <label>Current Price (PKR)</label>
                        <input type="number" name="current_price" class="form-control" required placeholder="e.g., 1200">
                    </div>
                </div>
                <br>

                <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" name="product_image" class="form-control" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label>Product Description</label>
                    <textarea name="description" class="form-control" placeholder="Write product main usage here..."></textarea>
                </div>

                <div class="form-group">
                    <label>How to Use</label>
                    <textarea name="how_to_use" class="form-control" placeholder="Instructions for farmers..."></textarea>
                </div>

                <div class="form-group">
                    <label>Precautions</label>
                    <textarea name="precautions" class="form-control" placeholder="Keep away from children..."></textarea>
                </div>

                <button type="submit" class="btn-submit">Publish Product</button>
            </form>
        </div>
    </div>

</body>
</html>
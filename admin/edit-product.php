<?php
// 1. سیشن شروع کریں تاکہ لسٹ والے پیج پر کامیابی کا میسج بھیج سکیں
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 2. ڈیٹا بیس کنکشن فائل شامل کریں
include '../db_connect.php';

// 3. لسٹ پیج سے آنے والی ID کے ذریعے ڈیٹا بیس سے پرانا ڈیٹا نکالیں
if (isset($_GET['id'])) {
    $product_id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT * FROM products WHERE product_id = '$product_id'";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        die("پروڈکٹ ڈیٹا بیس میں نہیں ملی۔");
    }
} else {
    header("Location: manage-products.php");
    exit();
}

// 4. جب ایڈمن فارم بدل کر "Save Changes" کا بٹن دبائے گا (POST Logic)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $p_id          = mysqli_real_escape_string($conn, $_POST['product_id']);
    $product_name  = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category      = mysqli_real_escape_string($conn, $_POST['category']);
    $old_price     = mysqli_real_escape_string($conn, $_POST['old_price']);
    $current_price = mysqli_real_escape_string($conn, $_POST['current_price']);
    $description   = mysqli_real_escape_string($conn, $_POST['description']);
    $how_to_use    = mysqli_real_escape_string($conn, $_POST['how_to_use']);
    $precautions   = mysqli_real_escape_string($conn, $_POST['precautions']);
    $stock_status  = mysqli_real_escape_string($conn, $_POST['stock_status']);

    // 🖼️ تصویر ہینڈلنگ کا لاجک
    if (!empty($_FILES['product_image']['name'])) {
        // اگر ایڈمن نے نئی تصویر سلیکٹ کی ہے
        $image_name = $_FILES['product_image']['name'];
        $image_tmp  = $_FILES['product_image']['tmp_name'];
        $target_folder = "../Pictures/" . $image_name;
        
        move_uploaded_file($image_tmp, $target_folder);
        
        // کیوری: تصویر کے کالم سمیت سب کچھ اپڈیٹ کریں
        $update_query = "UPDATE products SET 
            product_name='$product_name', category='$category', product_image='$image_name', 
            old_price='$old_price', current_price='$current_price', description='$description', 
            how_to_use='$how_to_use', precautions='$precautions', stock_status='$stock_status' 
            WHERE product_id='$p_id'";
    } else {
        // کیوری: اگر تصویر نہیں بدلی تو پرانی تصویر کو چھیڑے بغیر باقی ڈیٹا اپڈیٹ کریں
        $update_query = "UPDATE products SET 
            product_name='$product_name', category='$category', 
            old_price='$old_price', current_price='$current_price', description='$description', 
            how_to_use='$how_to_use', precautions='$precautions', stock_status='$stock_status' 
            WHERE product_id='$p_id'";
    }

    // کیوری چلانے کا رزلٹ
    if ($conn->query($update_query) === TRUE) {
        $_SESSION['stock_update'] = "🚀 پروڈکٹ '" . $product_name . "' کا ڈیٹا کامیابی سے اپڈیٹ ہو گیا ہے!";
        header("Location: manage-products.php");
        exit();
    } else {
        $error_msg = "ڈیٹا بیس اپڈیٹ فیل ہو گیا: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - GreenHub Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { display: flex; background-color: #f4f6f9; min-height: 100vh; }
        
        /* Sidebar Styling */
        .sidebar { width: 260px; background-color: #2c3e50; color: white; padding: 20px 0; display: flex; flex-direction: column; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; font-size: 24px; color: #2ecc71; }
        .sidebar a { padding: 15px 25px; color: #ecf0f1; text-decoration: none; display: block; }
        .sidebar a:hover { background-color: #34495e; color: #2ecc71; }
        
        /* Content Styling */
        .main-content { flex: 1; padding: 30px; }
        .form-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
        .form-card h3 { margin-bottom: 15px; color: #2c3e50; border-bottom: 2px solid #f4f6f9; padding-bottom: 10px; }
        
        .meta-info { font-size: 13px; color: #7f8c8d; margin-bottom: 20px; background: #f8f9fa; padding: 12px; border-radius: 4px; border-left: 4px solid #3498db; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 15px; }
        textarea.form-control { height: 110px; resize: vertical; }
        
        .row { display: flex; gap: 20px; margin-bottom: 5px; }
        .col { flex: 1; }
        
        .btn-submit { background-color: #2ecc71; color: white; padding: 12px 20px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; width: 48%; transition: 0.3s; }
        .btn-submit:hover { background-color: #27ae60; }
        .btn-cancel { background-color: #e74c3c; color: white; padding: 12px 20px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; text-decoration: none; display: inline-block; text-align: center; width: 48%; float: right; transition: 0.3s; }
        .btn-cancel:hover { background-color: #c0392b; }
        
        .current-img { width: 100px; height: 100px; object-fit: cover; border-radius: 4px; margin-top: 10px; border: 1px solid #ddd; display: block; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="form-card">
            <h3>✏️ Edit Product Specifications</h3>
            
            <div class="meta-info">
                <strong>Product ID (Primary Key):</strong> <?php echo $product['product_id']; ?> &nbsp;|&nbsp; 
                <strong>Created At (Date/Time):</strong> <?php echo $product['created_at']; ?>
            </div>

            <?php if(isset($error_msg)) echo "<div style='color:red; margin-bottom:15px; font-weight:bold;'>$error_msg</div>"; ?>

            <form action="edit-product.php?id=<?php echo $product['product_id']; ?>" method="POST" enctype="multipart/form-data">
                
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                </div>

                <div class="row">
                    <div class="col class=form-group">
                        <label>Category</label>
                        <select name="category" class="form-control" required>
                            <option value="Pesticides" <?php if($product['category'] == 'Pesticides') echo 'selected'; ?>>Pesticides</option>
                            <option value="Fertilizers" <?php if($product['category'] == 'Fertilizers') echo 'selected'; ?>>Fertilizers</option>
                            <option value="Seeds" <?php if($product['category'] == 'Seeds') echo 'selected'; ?>>Seeds</option>
                        </select>
                    </div>
                    <div class="col class=form-group">
                        <label>Stock Status</label>
                        <select name="stock_status" class="form-control" required>
                            <option value="In Stock" <?php if($product['stock_status'] == 'In Stock') echo 'selected'; ?>>In Stock</option>
                            <option value="Out of Stock" <?php if($product['stock_status'] == 'Out of Stock') echo 'selected'; ?>>Out of Stock</option>
                        </select>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col class=form-group">
                        <label>Old Price (PKR)</label>
                        <input type="number" name="old_price" class="form-control" value="<?php echo $product['old_price']; ?>" required>
                    </div>
                    <div class="col class=form-group">
                        <label>Current Price (PKR)</label>
                        <input type="number" name="current_price" class="form-control" value="<?php echo $product['current_price']; ?>" required>
                    </div>
                </div>
                <br>

                <div class="form-group">
                    <label>Product Image (Leave blank to keep current image)</label>
                    <input type="file" name="product_image" class="form-control" accept="image/*">
                    <img src="../Pictures/<?php echo $product['product_image']; ?>" class="current-img" alt="Current Image">
                </div>

                <div class="form-group">
                    <label>Product Description</label>
                    <textarea name="description" class="form-control"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>How to Use (طریقہ استعمال)</label>
                    <textarea name="how_to_use" class="form-control"><?php echo htmlspecialchars($product['how_to_use']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Precautions (احتیاطی تدابیر)</label>
                    <textarea name="precautions" class="form-control"><?php echo htmlspecialchars($product['precautions']); ?></textarea>
                </div>

                <button type="submit" class="btn-submit">💾 Save Changes</button>
                <a href="manage-products.php" class="btn-cancel">Cancel</a>
            </form>
        </div>
    </div>

</body>
</html>
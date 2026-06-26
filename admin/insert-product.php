<?php
// ۱۔ سب سے پہلے ڈیٹا بیس کنکشن فائل شامل کریں
include '../db_connect.php';

// ۲۔ اب چیک کریں کہ کیا سیشن پہلے سے شروع ہے؟ اگر نہیں تو شروع کریں
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // ۳۔ فارم کا ڈیٹا محفوظ طریقے سے حاصل کریں
    $product_name  = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category      = mysqli_real_escape_string($conn, $_POST['category']);
    $old_price     = mysqli_real_escape_string($conn, $_POST['old_price']);
    $current_price = mysqli_real_escape_string($conn, $_POST['current_price']);
    $description   = mysqli_real_escape_string($conn, $_POST['description']);
    $how_to_use    = mysqli_real_escape_string($conn, $_POST['how_to_use']);
    $precautions   = mysqli_real_escape_string($conn, $_POST['precautions']);
    $stock_status  = "In Stock"; 

    // ۴۔ تصویر ہینڈلنگ
    $image_name = $_FILES['product_image']['name'];
    $image_tmp  = $_FILES['product_image']['tmp_name'];
    $target_folder = "../Pictures/" . $image_name;

    // ۵۔ لاجک: اگر تصویر چلی جائے تو کیوری چلاؤ
    if (move_uploaded_file($image_tmp, $target_folder)) {
        
        $query = "INSERT INTO products (product_name, category, product_image, old_price, current_price, description, how_to_use, precautions, stock_status) 
                  VALUES ('$product_name', '$category', '$image_name', '$old_price', '$current_price', '$description', '$how_to_use', '$precautions', '$stock_status')";

        if ($conn->query($query) === TRUE) {
            // سیشن میں کامیابی کا میسج سیو کریں
            $_SESSION['status_msg'] = "success";
            header("Location: add-product.php");
            exit();
        } else {
            // اگر کیوری فیل ہو تو عارضی طور پر اصل وجہ دیکھنے کے لیے یہ بندوبست:
            die("MySQL Query Failed: " . $conn->error);
        }
    } else {
        $_SESSION['status_msg'] = "upload_error";
        header("Location: add-product.php");
        exit();
    }
}
?>
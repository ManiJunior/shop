<?php
// سیشن چیک کریں تاکہ ایڈمن کا لیول پڑھا جا سکے
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// اگر کسی وجہ سے لیول سیٹ نہیں ہے، تو عارضی طور پر اسے 3 (سب سے کم) مان لیتے ہیں تاکہ سیکیورٹی رسک نہ ہو
$current_level = isset($_SESSION['admin_level']) ? (int)$_SESSION['admin_level'] : 3; 
?>

<div class="sidebar">
    <h2>GreenHub Admin</h2>
    
    <!-- یہ ہوم پیج کا بٹن ہے، یہ سب ایڈمنز کو ہمیشہ دیکھے گا -->
    <a href="index.php">📊 Dashboard Home</a>

    <!-- 🔒 ➕ Add Product (صرف ایڈمن 1 اور 3 کے لیے) -->
    <?php if ($current_level === 1 || $current_level === 3): ?>
        <a href="add-product.php">➕ Add Product</a>
    <?php endif; ?>

    <!-- 🔒 📦 Manage Products (صرف ایڈمن 1 اور 2 کے لیے) -->
    <?php if ($current_level === 1 || $current_level === 2): ?>
        <a href="manage-products.php">📦 Manage Products (Stock)</a>
    <?php endif; ?>

    <!-- 🔒 باقی تینوں ایپس (صرف ایڈمن 1 اور 3 کے لیے) -->
    <?php if ($current_level === 1 || $current_level === 3): ?>
        <a href="#">🌦️ Weather Alerts</a>
        <a href="#">📰 Manage Articles</a>
        <a href="#">🛒 View Orders</a>
    <?php endif; ?>
</div>
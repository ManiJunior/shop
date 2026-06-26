<?php
// ۱۔ جو ڈیٹا بیس فائل ہم نے ابھی بنائی تھی، اسے یہاں شامل کیا
include 'db_connect.php';

// ۲۔ یو آر ایل سے پروڈکٹ کی آئی ڈی حاصل کی (جیسے product-detail.php?id=1)
// اگر لنک میں کوئی آئی ڈی نہ ہو، تو یہ خود بخود پہلی پروڈکٹ (1) کا ڈیٹا اٹھائے گا
$p_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// ۳۔ آپ کی ٹیبل کے کالم 'product_id' کے مطابق ڈیٹا بیس سے پروڈکٹ کا ڈیٹا نکالا
$query = "SELECT * FROM products WHERE product_id = $p_id";
$result = $conn->query($query);

// ۴۔ چیک کیا کہ اس آئی ڈی کی پروڈکٹ ڈیٹا بیس میں موجود ہے یا نہیں
if ($result && $result->num_rows > 0) {
    // اگر پروڈکٹ مل گئی، تو اس کا سارا ڈیٹا $product نامی ویری ایبل میں سیو کر لیا
    $product = $result->fetch_assoc();
} else {
    // اگر ڈیٹا بیس میں یہ آئی ڈی موجود نہ ہو تو یہ میسج دیکھے گا
    die("<h2 style='text-align:center; padding:50px; font-family:sans-serif;'>معذرت! یہ پروڈکٹ موجود نہیں ہے۔</h2>");
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پروڈکٹ کی تفصیل - GreenHub</title>
    <!-- Font Awesome Icons کے لیے لنک -->
    <link rel="stylesheet" href="fonts/css/all.min.css">
    <!-- سی ایس ایس فائل کا لنک -->
    <link rel="stylesheet" href="product-detail.css">
    <link rel="stylesheet" href=".css">
</head>
<body>
    <header class="main-header">
    <div class="header-top">
        <div class="logo-area">
            <img src="Pictures/logo.png" alt="Brand Logo" class="brand-logo-img">
        </div>
        <a href="cart.html" class="header-icons">
            <div class="icon-btn cart-btn">
                <i class="fas fa-shopping-cart"></i>
                <span class="cartCount">0</span>
            </div>
        </a>
    </div>
    <!-- سرچ بار والا کنٹینر -->
<div class="search-container" style="position: relative;"> <!-- اس کے پیرنٹ پر ریلیٹیو پوزیشن لازمی ہونی چاہیے -->
    <input type="text" id="headerSearchInput" placeholder="Search products...">
    <i class="fa-solid fa-magnifying-glass search-icon"></i>
    
    <!-- 🌟 یہ وہ خالی ڈو ہے جو آپ نے دوبارہ رکھنی ہے -->
    <div id="searchSuggestionsBar" class="search-suggestions-bar"></div>
</div>
</header>

<div class="container">
    
    <div class="product-wrapper">
        
        <div class="product-image-section">
            <div class="main-img-box">
                <img id="prod-image" src="Pictures/<?php echo $product['product_image']; ?>" alt="<?php echo $product['product_name']; ?>">
            </div>
        </div>
        
        <div class="product-info-section">
            <span id="prod-cat" class="category-badge"><?php echo $product['category']; ?></span>
            
            <h1 id="prod-title" class="product-title"><?php echo $product['product_name']; ?></h1>
            
            <div class="price-container">
                <span id="prod-old-price" class="old-price">RS <?php echo number_format($product['old_price']); ?></span>
                
                <span id="prod-price" class="current-price">RS <?php echo number_format($product['current_price']); ?></span>
            </div>

            <?php if(strtolower($product['stock_status']) == 'in stock' || $product['stock_status'] == 'دستیاب ہے'): ?>
                <div id="prod-stock" class="stock-status in-stock-tag">
                    <i class="fa-solid fa-circle-check"></i> دستیاب ہے (In Stock)
                </div>
            <?php else: ?>
                <div id="prod-stock" class="stock-status out-of-stock-tag" style="color: red; border-color: red;">
                    <i class="fa-solid fa-circle-xmark"></i> دستیاب نہیں ہے (Out of Stock)
                </div>
            <?php endif; ?>

            <div class="quantity-section">
                <span>مقدار:</span>
                <button type="button" class="qty-btn minus-btn">-</button>
                <span id="qty-count">1</span>
                <button type="button" class="qty-btn plus-btn">+</button>
            </div>

            <div class="action-buttons">
    <?php 
    // چیک کریں کہ کیا پروڈکٹ ان اسٹاک ہے؟
    if ($product['stock_status'] == 'In Stock') { 
        // ✅ اگر ان اسٹاک ہے تو بٹنز بالکل نارمل دیکھیں گے اور کام کریں گے
        ?>
        <button id="add-to-cart-btn" class="btn btn-cart">
            <i class="fa-solid fa-cart-shopping"></i> کارٹ میں شامل کریں
        </button>
        <button id="whatsapp-btn" class="btn btn-whatsapp">
            <i class="fa-brands fa-whatsapp"></i> واٹس ایپ پر آرڈر کریں
        </button>
        <?php 
    } else { 
        // ❌ اگر آؤٹ آف اسٹاک ہے تو بٹنز کو ڈس ایبل (Lock) کر دیں گے اور رنگ گرے کر دیں گے
        ?>
        <button id="add-to-cart-btn" class="btn btn-cart" style="background-color: #bdc3c7; color: #7f8c8d; cursor: not-allowed;" disabled>
            <i class="fa-solid fa-lock"></i> ختم ہو چکا ہے (Out of Stock)
        </button>
        <button id="whatsapp-btn" class="btn btn-whatsapp" style="background-color: #95a5a6; color: #7f8c8d; cursor: not-allowed;" disabled>
            <i class="fa-brands fa-whatsapp"></i> واٹس ایپ آرڈر بند ہے
        </button>
        <?php 
    } 
    ?>
</div>
        </div>

    </div>

    <div class="tabs-container">
        <div class="tabs-header">
            <button class="tab-link active" onclick="openTab(event, 'tab-desc')">📋 تفصیل</button>
            <button class="tab-link" onclick="openTab(event, 'tab-use')">🌾 استعمال کا طریقہ</button>
            <button class="tab-link" onclick="openTab(event, 'tab-caution')">⚠️ احتیاطی تدابیر</button>
        </div>

        <div id="tab-desc" class="tab-content active">
            <p id="prod-desc"><?php echo $product['description']; ?></p>
        </div>

        <div id="tab-use" class="tab-content">
            <p id="prod-use"><?php echo $product['how_to_use']; ?></p>
        </div>

        <div id="tab-caution" class="tab-content">
            <p id="prod-caution"><?php echo $product['precautions']; ?></p>
        </div>
    </div>

    <div class="trust-badges">
        <div><i class="fa-solid fa-shield-halved"></i> ۱۰۰٪ اصل برانڈڈ پروڈکٹ</div>
        <div><i class="fa-solid fa-truck-fast"></i> تیز ترین ہوم ڈلیوری</div>
        <div><i class="fa-solid fa-user-doctor"></i> زرعی ماہرین کی تصدیق شدہ</div>
    </div>

</div>

    <!-- موبائل ایپ جیسا باٹم نیویگیشن بار -->
    <nav class="bottom-nav">
        <a href="index.html" class="nav-item active">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="shop.php" class="nav-item">
            <i class="fas fa-shopping-bag"></i>
            <span>Shop</span>
        </a>
        <a href="social.html" class="nav-item">
            <i class="fas fa-users"></i>
            <span>Social+</span>
        </a>
        <a href="cart.html" class="nav-item">
            <i class="fas fa-shopping-cart"></i>
            <span>Your Cart</span>
            <span class="cartCount">0</span>
        </a>
        <a href="account.html" class="nav-item">
            <i class="fas fa-user-circle"></i>
            <span>Account</span>
        </a>
    </nav>
    <footer class="site-footer">
    <div class="footer-container">
        
        <!-- کالم ۱: برانڈ کا نام اور سوشل لنکس -->
        <div class="footer-col about-col">
            <h3 class="footer-logo">Green<span>Hub</span></h3>
            <p>Your trusted partner for premium seeds, pesticides, and household gardening solutions.</p>
            <div class="footer-socials">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
                <a href="#"><i class="fab fa-x"></i></a>
                <a href="https://wa.me/923056416902" target="_blank"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>

        <!-- کالم ۲: ضروری لنکس -->
        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="shop.html">Shop All</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Contact Us</a></li>
            </ul>
        </div>

        <!-- کالم ۳: کسٹمر پالیسیز -->
        <div class="footer-col">
            <h4>Our Policies</h4>
            <ul>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms & Conditions</a></li>
                <li><a href="#">Return & Refund</a></li>
                <li><a href="#">Shipping Info</a></li>
            </ul>
        </div>

        <!-- کالم ۴: رابطہ -->
        <div class="footer-col">
            <h4>Contact Info</h4>
            <ul class="contact-list">
                <li><i class="fas fa-map-marker-alt"></i> Lahore, Pakistan</li>
                <li><i class="fas fa-phone-alt"></i> +92 305 6416902</li>
                <li><i class="fas fa-envelope"></i> info@greenhub.com</li>
                <p>&copy; 2026 GreenHub. All Rights Reserved.</p>
            </ul>
        </div>

    </div>
    <!-- <i class="fas fa-truck"></i> -->
</footer>

<!-- جاوا اسکرپٹ فائل کا لنک -->
<script>
    // یہ لائن پی ایچ پی کی آئی ڈی کو جاوا اسکرپٹ کے گلوبل ویری ایبل میں ڈال دے گی
    window.currentProductId = "<?php echo $product['product_id']; ?>";
</script>
<script src="product-detail.js"></script>
</body>
</html>
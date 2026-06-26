<?php
// ۱۔ ڈیٹا بیس کنکشن فائل شامل کریں
include 'db_connect.php';

// ۲۔ ڈیٹا بیس سے تمام پروڈکٹس نکالنے کی کیوری
$query = "SELECT * FROM products";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دکان - تمام پروڈکٹس</title>
    <link rel="stylesheet" href="fonts/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="shop.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- ==========================================
       ۱. مین ہیڈر (سیم لک)
       ========================================== -->
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

    <!-- ==========================================
       ۲. شاپ ناؤ بینر پٹی
       ========================================== -->
    <div class="shop-premium-banner">
        <a href="#" class="banner-link">Shop Now</a>
    </div>

    <!-- شاپ پیج کا مین مواد -->
    <main class="shop-container">

        <!-- ==========================================
           ۳. کیٹیگری فلٹر سیکشن
           ========================================== -->
        <div class="category-filter-bar">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="kitchen-gardening">Kitchen Gardening</button>
            <button class="filter-btn" data-filter="pesticides">Pesticides</button>
            <button class="filter-btn" data-filter="seeds">Seeds</button>
            <button class="filter-btn" data-filter="hybrid-seeds">Hybrid Seeds</button>
            <button class="filter-btn" data-filter="house-Hold">House Hold</button>            
            <button class="filter-btn" data-filter="home-decor">Home Decors</button>            
            <button class="filter-btn" data-filter="fertilizers">Fertilizers</button>            
            <button class="filter-btn" data-filter="livestock">Live Stock</button>            
            <button class="filter-btn" data-filter="feed">Feed</button>
            <button class="filter-btn" data-filter="veterinary-medicine">Veterinary Medicine</button>
            <button class="filter-btn" data-filter="spray-machinary">Spray Machinary</button>
            <button class="filter-btn" data-filter="tunnel-farming">Tunnel Farming</button>
            <button class="filter-btn" data-filter="agriculture-tools">Agriculture Tools</button>
        </div>






























        <!-- ==========================================
           ۴. پروڈکٹ گرڈ (3D فلوٹنگ لک)
           ========================================== -->
        <section class="products-grid">
    <?php
    // ۳۔ چیک کریں کہ ڈیٹا بیس میں پروڈکٹس ہیں یا نہیں
    if ($result && $result->num_rows > 0) {
        // ۴۔ جبکہ (while) ڈیٹا بیس میں پروڈکٹس ملتی رہیں، یہ لوپ چلتا رہے
        while ($row = $result->fetch_assoc()) {
            ?>
            <a href="product-detail.php?id=<?php echo $row['product_id']; ?>" 
               class="product-card" 
               data-id="<?php echo $row['product_id']; ?>" 
               data-cat="<?php echo $row['category']; ?>">
               
                <div class="img-box">
                    <img src="Pictures/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_name']; ?>">
                </div>
                
                <h4 class="product-name"><?php echo $row['product_name']; ?></h4>
                
                <div class="product-footer">
                    <div class="price-box">
                        <span class="product-old-price">RS <?php echo number_format($row['old_price']); ?></span>
                        <span class="product-price">RS <?php echo number_format($row['current_price']); ?></span>
                    </div>

                    <?php if ($row['stock_status'] == 'In Stock') { ?>
                        <button class="minimal-add-btn"></button>
                    <?php } else { ?>
                        <span style="background-color: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; white-space: nowrap;">Out of Stock</span>
                    <?php } ?>

                </div>
            </a>
            <?php
        }
    } else {
        // اگر ڈیٹا بیس بالکل خالی ہو
        echo "<p style='grid-column: 1/-1; text-align: center; padding: 20px;'>فی الحال کوئی پروڈکٹ دستیاب نہیں ہے۔</p>";
    }
    ?>
</section>
            






































    </main>

    <!-- ==========================================
       ۵. باٹم نیویگیشن پٹی
       ========================================== -->
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

    <!-- شاپ پیج کی جاوا اسکرپٹ فائل جو اسی فولڈر میں ہے -->
    <script src="shop.js"></script>

</body>
</html>
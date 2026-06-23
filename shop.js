document.addEventListener("DOMContentLoaded", function () {
    const addButtons = document.querySelectorAll(".minimal-add-btn");
    const cartCountElements = document.querySelectorAll(".cartCount");

    // 🔄 ہیڈر کاؤنٹ اپڈیٹ کرنے کا فنکشن
    function updateShopCartCount() {
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        let totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCountElements.forEach(el => {
            el.textContent = totalCount;
        });
    }

    // ⚡ "Add to Cart" بٹن ایکشن
    addButtons.forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            const productCard = this.closest(".product-card");
            if (!productCard) return;

            // پروڈکٹ کا ڈیٹا نکالنا
            const productId = productCard.getAttribute("data-id");
            const productCat = productCard.getAttribute("data-cat") || "Pesticides";
            const productName = productCard.querySelector(".product-name").textContent.trim();
            const productImg = productCard.querySelector(".img-box img").getAttribute("src");
            
            const priceText = productCard.querySelector(".product-price").textContent;
            const productPrice = parseInt(priceText.replace(/[^0-9]/g, '')) || 0;

            // لوکل اسٹوریج میں سیو کرنا
            let cart = JSON.parse(localStorage.getItem("cart")) || [];
            let existingItem = cart.find(item => item.id === productId);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    image: productImg,
                    category: productCat,
                    quantity: 1
                });
            }

            localStorage.setItem("cart", JSON.stringify(cart));
            updateShopCartCount(); // ہیڈر کاؤنٹ فوراً اپڈیٹ کرو

            // ==========================================================================
            // 🌟 فلوٹنگ سنیک بار (Bottom Notification) کا لاجک
            // ==========================================================================
            
            // اگر پہلے سے کوئی نوٹیفکیشن اسکرین پر ہے تو اسے فوراً ہٹاؤ
            const oldSnackbar = document.querySelector('.cart-snackbar');
            if (oldSnackbar) oldSnackbar.remove();

            // ۱۔ نیا نوٹیفکیشن باکس بنائیں
            const snackbar = document.createElement('div');
            snackbar.className = 'cart-snackbar';
            
            // ۲۔ اس کے اندر کا مال سیٹ کریں
            snackbar.innerHTML = `
                <div class="snackbar-tick">✓</div>
                <span style="font-size: 14px; font-family: sans-serif;">آئٹم شامل ہو گیا!</span>
                <a href="cart.html" class="snackbar-btn">Checkout کریں</a>
            `;

            // ۳۔ اسے پیج پر دکھائیں
            document.body.appendChild(snackbar);

            // ۴۔ اینیمیشن کے ساتھ اوپر لائیں
            setTimeout(() => {
                snackbar.classList.add('show');
            }, 50);

            // ۵۔ ۳ سیکنڈ بعد غائب کر دیں
            setTimeout(() => {
                snackbar.classList.remove('show');
                setTimeout(() => { snackbar.remove(); }, 400);
            }, 3000);
        });
    });

    // ⚡ چوکیدار: اگر ہوم یا کارٹ پیج پر کچھ بدلے، تو شاپ پیج کا کاؤنٹ لائیو بدلے
    window.addEventListener("storage", function (e) {
        if (e.key === "cart") {
            updateShopCartCount();
        }
    });

    // پیج لوڈ ہوتے ہی کاؤنٹ شو کرنا
    updateShopCartCount();
});
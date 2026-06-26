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
                <span style="font-size: 14px; font-family: sans-serif;">Successfully</span>
                <a href="cart.html" class="snackbar-btn">Checkout</a>
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


document.addEventListener("DOMContentLoaded", function () {
    // ۱۔ تمام فلٹر بٹنز اور پروڈکٹ کارڈز کو پکڑو
    const filterButtons = document.querySelectorAll(".filter-btn");
    const products = document.querySelectorAll(".product-card");

    // ۲۔ ہر بٹن پر کلک کا جادو سیٹ کرو
    filterButtons.forEach(button => {
        button.addEventListener("click", function () {
            
            // الف: پہلے پرانے ایکٹو بٹن سے 'active' کلاس ہٹاؤ اور نئے پر لگاؤ
            document.querySelector(".filter-btn.active")?.classList.remove("active");
            this.classList.add("active");

            // ب: بٹن کی ویلیو پکڑو (جیسے All, Pesticides وغیرہ) اور اسپیس ختم کرو
            const selectedFilter = this.getAttribute("data-filter").toLowerCase().trim();

            // ج: اب پروڈکٹس کو فلٹر کرو
            products.forEach(product => {
                const productCat = product.getAttribute("data-cat");
                const currentProductCat = productCat ? productCat.toLowerCase().trim() : "";

                // اگر 'all' کلک ہوا ہے یا پروڈکٹ کی کیٹیگری بٹن سے میچ کر گئی ہے
                if (selectedFilter === "all" || currentProductCat === selectedFilter) {
                    product.style.display = ""; 
                } else {
                    product.style.display = "none"; // باقیوں کو چھپا دو
                }
            });
        });
    });
});


document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("headerSearchInput");
    const products = document.querySelectorAll(".product-card");

    if (!searchInput || products.length === 0) return;

    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // سیدھا پروڈکٹ کارڈز کو فلٹر اور ہائی لائٹ کرنے کا لاجک
    searchInput.addEventListener("input", function () {
        const query = this.value.trim().toLowerCase();

        products.forEach(product => {
            const nameElement = product.querySelector(".product-name");
            if (!nameElement) return;

            if (!nameElement.getAttribute("data-original-text")) {
                nameElement.setAttribute("data-original-text", nameElement.textContent);
            }
            const originalText = nameElement.getAttribute("data-original-text");

            if (query === "") {
                product.style.display = "";
                nameElement.innerHTML = originalText;
                return;
            }

            const regex = new RegExp(`(${escapeRegExp(query)})`, "gi");
            const hasExactOrPartialMatch = originalText.toLowerCase().includes(query);
            const productCat = product.getAttribute("data-cat") ? product.getAttribute("data-cat").toLowerCase() : "";
            const hasCategoryMatch = productCat.includes(query);

            if (hasExactOrPartialMatch || hasCategoryMatch) {
                product.style.display = "";
                if (hasExactOrPartialMatch) {
                    nameElement.innerHTML = originalText.replace(regex, `<mark class="search-highlight">$1</mark>`);
                } else {
                    nameElement.innerHTML = originalText;
                }
            } else {
                product.style.display = "none";
            }
        });
    });

    // ==========================================================================
    // 🌟 🚀 ہوم پیج سے آنے والی کیٹیگری کو آٹو فلٹر کرنے کا نیا کوڈ (یہاں سیٹ کر دیا)
    // ==========================================================================
    const urlParams = new URLSearchParams(window.location.search);
    const categoryFromUrl = urlParams.get('category');

    if (categoryFromUrl) {
        const targetButton = document.querySelector(`.filter-btn[data-filter="${categoryFromUrl}"]`);
        if (targetButton) {
            setTimeout(() => {
                targetButton.click();
            }, 100); // ہلکا سا وقفہ تاکہ بٹنز ریڈی ہو جائیں
        }
    }
});
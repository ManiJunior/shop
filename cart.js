document.addEventListener("DOMContentLoaded", function () {
    const DEFAULT_DELIVERY_CHARGE = 200;
    const FREE_DELIVERY_THRESHOLD = 1500;

    const cartItemsList = document.getElementById("cartItemsList");
    const subtotalPriceEl = document.getElementById("subtotalPrice");
    const deliveryChargesEl = document.getElementById("deliveryCharges");
    const grandTotalPriceEl = document.getElementById("grandTotalPrice");
    const cartCountElements = document.querySelectorAll(".cartCount");

    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    // 🔄 ہیڈر کاؤنٹ اپڈیٹ کرنا
    function updateCartPageCount() {
        let totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCountElements.forEach(el => {
            el.textContent = totalCount;
        });
    }

    // 🖥️ کارٹ لسٹ دکھانا
    function renderCartItems() {
        if (!cartItemsList) return;
        
        if (cart.length === 0) {
            cartItemsList.innerHTML = `
                <div style="text-align:center; padding: 40px; font-family:sans-serif;">
                    <i class="fas fa-shopping-basket" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                    <p style="font-size: 18px; color: #666;">آپ کا کارٹ ابھی خالی ہے!</p>
                    <a href="main.html" style="display:inline-block; margin-top:15px; padding:10px 20px; background:#2e7d32; color:white; text-decoration:none; border-radius:5px; font-weight:bold;">شاپنگ کریں</a>
                </div>
            `;
            updateCartTotals(0);
            return;
        }

        let cartHTML = "";
        cart.forEach(item => {
            let itemTotal = item.price * item.quantity;
            cartHTML += `
                <div class="cart-item" data-id="${item.id}">
                    <img src="${item.image || 'images/placeholder.jpg'}" alt="${item.name}" class="item-img">
                    <div class="item-details">
                        <h3>${item.name}</h3>
                        <p class="item-category">${item.category || 'Kitchen Garden'}</p>
                    </div>
                    <div class="item-quantity">
                        <button class="qty-btn minus" data-id="${item.id}">-</button>
                        <input type="number" value="${item.quantity}" class="qty-input" readonly>
                        <button class="qty-btn plus" data-id="${item.id}">+</button>
                    </div>
                    <div class="item-price">Rs ${itemTotal.toLocaleString()}</div>
                    <button class="delete-btn" data-id="${item.id}"><i class="fas fa-trash-alt"></i></button>
                </div>
            `;
        });
        
        cartItemsList.innerHTML = cartHTML;
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        updateCartTotals(subtotal);
    }

    function updateCartTotals(subtotal) {
        let deliveryCharge = DEFAULT_DELIVERY_CHARGE;
        if (subtotal >= FREE_DELIVERY_THRESHOLD || subtotal === 0) {
            deliveryCharge = 0;
        }
        let grandTotal = subtotal + deliveryCharge;

        subtotalPriceEl.textContent = `Rs ${subtotal.toLocaleString()}`;
        deliveryChargesEl.textContent = deliveryCharge === 0 && subtotal > 0 ? "FREE" : `Rs ${deliveryCharge}`;
        grandTotalPriceEl.textContent = `Rs ${grandTotal.toLocaleString()}`;
    }

    // پلس، مائنس، ڈیلیٹ کلک ایکشنز
    if (cartItemsList) {
        cartItemsList.addEventListener("click", function (e) {
            let target = e.target;
            let id = target.getAttribute("data-id") || target.closest("button")?.getAttribute("data-id");
            if (!id) return;

            let itemIndex = cart.findIndex(item => item.id == id);
            if (itemIndex === -1) return;

            if (target.classList.contains("plus")) {
                cart[itemIndex].quantity += 1;
            } else if (target.classList.contains("minus")) {
                if (cart[itemIndex].quantity > 1) {
                    cart[itemIndex].quantity -= 1;
                }
            } else if (target.closest(".delete-btn")) {
                if (confirm("کیا آپ اس آئٹم کو کارٹ سے ہٹانا چاہتے ہیں؟")) {
                    cart.splice(itemIndex, 1);
                } else {
                    return;
                }
            }

            localStorage.setItem("cart", JSON.stringify(cart));
            renderCartItems();
            updateCartPageCount();
        });
    }

    // ⚡ چوکیدار: ہوم یا شاپ پیج سے نیا مال آنے پر کارٹ پیج کو لائیو رینڈر کرنا
    window.addEventListener("storage", function (e) {
        if (e.key === "cart") {
            cart = JSON.parse(localStorage.getItem("cart")) || [];
            renderCartItems();
            updateCartPageCount();
        }
    });

    renderCartItems();
    updateCartPageCount();
});
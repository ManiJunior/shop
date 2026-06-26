// کوانٹیٹی کم یا زیادہ کرنے کا فنکشن
function changeQty(amount) {
    let qtyCount = document.getElementById('qty-count');
    let currentQty = parseInt(qtyCount.innerText);
    currentQty += amount;
    
    if (currentQty < 1) {
        currentQty = 1; // مقدار ۱ سے کم نہیں ہو سکتی
    }
    qtyCount.innerText = currentQty;
}

// ٹیبز تبدیل کرنے کا فنکشن
function openTab(evt, tabId) {
    let tabContents = document.getElementsByClassName("tab-content");
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove("active");
    }

    let tabLinks = document.getElementsByClassName("tab-link");
    for (let i = 0; i < tabLinks.length; i++) {
        tabLinks[i].classList.remove("active");
    }

    document.getElementById(tabId).classList.add("active");
    evt.currentTarget.classList.add("active");
}




// کوانٹیٹی کم یا زیادہ کرنے کا فنکشن
function changeQty(amount) {
    let qtyCount = document.getElementById('qty-count');
    let currentQty = parseInt(qtyCount.innerText);
    currentQty += amount;
    
    if (currentQty < 1) {
        currentQty = 1;
    }
    qtyCount.innerText = currentQty;
}

// ٹیبز تبدیل کرنے کا فنکشن
function openTab(evt, tabId) {
    let tabContents = document.getElementsByClassName("tab-content");
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove("active");
    }

    let tabLinks = document.getElementsByClassName("tab-link");
    for (let i = 0; i < tabLinks.length; i++) {
        tabLinks[i].classList.remove("active");
    }

    document.getElementById(tabId).classList.add("active");
    evt.currentTarget.classList.add("active");
}

/* ========================================================================= */
/* 🛒 کارٹ کا فائنل لاجک (ہیڈر اور نیو بار دونوں کے لیے) */
/* ========================================================================= */

/* ========================================================================= */
/* ➕ اور ➖ بٹنز کا لاجک (مقدار کم یا زیادہ کرنے کے لیے) */
/* ========================================================================= */

// کوانٹیٹی کم یا زیادہ کرنے کا فنکشن
function changeQty(amount) {
    let qtyCount = document.getElementById('qty-count');
    if (!qtyCount) return; // اگر پیج پر ایلیمنٹ نہ ملے تو کوڈ نہ رکے

    let currentQty = parseInt(qtyCount.innerText) || 1;
    currentQty += amount;
    
    if (currentQty < 1) {
        currentQty = 1; // مقدار ۱ سے کم نہیں ہو سکتی
    }
    qtyCount.innerText = currentQty;
}

// ٹیبز تبدیل کرنے کا فنکشن
function openTab(evt, tabId) {
    let tabContents = document.getElementsByClassName("tab-content");
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove("active");
    }

    let tabLinks = document.getElementsByClassName("tab-link");
    for (let i = 0; i < tabLinks.length; i++) {
        tabLinks[i].classList.remove("active");
    }

    let targetTab = document.getElementById(tabId);
    if (targetTab) targetTab.classList.add("active");
    if (evt && evt.currentTarget) evt.currentTarget.classList.add("active");
}

/* ========================================================================= */
/* 🛒 کارٹ کا فائنل لاجک (ہیڈر اور نیو بار دونوں کے لیے) */
/* ========================================================================= */

// ٹیبز تبدیل کرنے کا فنکشن (یہ ویسے ہی رہے گا)
function openTab(evt, tabId) {
    let tabContents = document.getElementsByClassName("tab-content");
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove("active");
    }

    let tabLinks = document.getElementsByClassName("tab-link");
    for (let i = 0; i < tabLinks.length; i++) {
        tabLinks[i].classList.remove("active");
    }

    let targetTab = document.getElementById(tabId);
    if (targetTab) targetTab.classList.add("active");
    if (evt && evt.currentTarget) evt.currentTarget.classList.add("active");
}

/* ========================================================================= */
/* 🛒 کارٹ کا کاؤنٹ ہیڈر اور نیو بار میں دکھانے کا فنکشن */
/* ========================================================================= */
function updateProductPageCartCount() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let totalItems = cart.reduce((total, item) => total + item.quantity, 0);
    
    let countElements = document.querySelectorAll('.cartCount');
    if (countElements.length > 0) {
        countElements.forEach(element => {
            element.innerText = totalItems;
        });
    }
}

/* ========================================================================= */
/* 🚀 پیج لوڈ ہوتے ہی سارے ایکشنز ایک ساتھ ایکٹیو کریں */
/* ========================================================================= */
document.addEventListener('DOMContentLoaded', function() {
    
    // ۱۔ سب سے پہلے پرانا کارٹ کاؤنٹ لوڈ کریں
    updateProductPageCartCount();

    // ۲۔ پلس اور مائنس بٹنز کا نیا اور پکا لاجک
    let qtyCount = document.getElementById('qty-count');
    let plusBtn = document.querySelector('.plus-btn');
    let minusBtn = document.querySelector('.minus-btn');

    if (plusBtn && minusBtn && qtyCount) {
        // پلس بٹن پر کلک کا جادو
        plusBtn.addEventListener('click', function() {
            let currentQty = parseInt(qtyCount.innerText) || 1;
            qtyCount.innerText = currentQty + 1;
        });

        // مائنس بٹن پر کلک کا جادو
        minusBtn.addEventListener('click', function() {
            let currentQty = parseInt(qtyCount.innerText) || 1;
            if (currentQty > 1) {
                qtyCount.innerText = currentQty - 1;
            }
        });
    }

    // ۳۔ ایڈ ٹو کارٹ بٹن کا لاجک
    let addToCartBtn = document.getElementById('add-to-cart-btn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            let productId = window.currentProductId || "1"; 
            let qtyToAdd = qtyCount ? parseInt(qtyCount.innerText) : 1;

            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let existingProduct = cart.find(item => item.id === productId);

            if (existingProduct) {
                existingProduct.quantity += qtyToAdd; 
            } else {
                cart.push({ id: productId, quantity: qtyToAdd }); 
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            updateProductPageCartCount();
            
            alert("پروڈکٹ کارٹ میں شامل کر دی گئی ہے!");
        });
    }
});

// دوسرے پیجز سے لائیو سنک کرنے کے لیے
window.addEventListener("storage", function (e) {
    if (e.key === "cart") {
        updateProductPageCartCount();
    }
});
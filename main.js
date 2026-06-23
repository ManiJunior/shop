// ==========================================
// 1. کارٹ مینیجمنٹ سسٹم (Cart Counter)
// ==========================================
let globalCartCount = 0;

// پیج پر موجود تمام "Add to Cart" بٹنز کو سلیکٹ کریں
document.querySelectorAll('.minimal-add-btn').forEach(button => {
    button.addEventListener('click', function(event) {
        // اگلے پیج (shop.html) پر جانے سے پکا روکنے کے لیے
        event.preventDefault();
        event.stopPropagation();
        
        // کارٹ کی گنتی میں اضافہ اور ڈسپلے اپڈیٹ
        globalCartCount++;
        document.querySelectorAll('.cartCount').forEach(countSpan => {
            countSpan.textContent = globalCartCount;
        });
        
        // console.log("Product Added! Total items in cart: " + globalCartCount);
    });
});
document.querySelectorAll('.minimal-add-btn').forEach(button => {
    button.addEventListener('click', function(event) {
        // اگلے پیج پر جانے سے پکا روکنے کے لیے
        event.preventDefault();
        event.stopPropagation();
        
        // کارٹ کی گنتی میں اضافہ اور ڈسپلے اپڈیٹ
        globalCartCount++;
        document.querySelectorAll('.cartCount').forEach(countSpan => {
            countSpan.textContent = globalCartCount;
        });
        
        // ==========================================================================
        // 🌟 صرف گرین ٹک (✓) نشان دکھانے کا لاجک
        // ==========================================================================
        const originalContent = this.innerHTML; // بٹن کا اصل مال (ٹیکسٹ یا آئیکن) سنبھال لو
        
        this.innerHTML = "✓"; // بٹن کے اندر صرف ٹک کا نشان ڈالیں
        this.style.backgroundColor = "#2e7d32"; // بیک گراؤنڈ کا رنگ ہرا کر دیں
        this.style.color = "white"; // ٹک کا رنگ سفید
        this.style.pointerEvents = "none"; // ۱ سیکنڈ کے لیے بٹن فریز

        // ٹھیک ۱ سیکنڈ بعد بٹن کو واپس پہلی حالت میں لائیں
        setTimeout(() => {
            this.innerHTML = originalContent; // اصل ٹیکسٹ یا آئیکن واپس
            this.style.backgroundColor = ""; // پرانا رنگ واپس
            this.style.color = "";
            this.style.pointerEvents = "auto"; // بٹن دوبارہ ایکٹو
        }, 1000);

        // console.log("Product Added! Total items in cart: " + globalCartCount);
    });
});
// ==========================================
// 2. لائیو موسم اپڈیٹ (Live Weather Integration)
// ==========================================
async function fetchLiveWeather() {
    const city = "Multan"; 
    const apiKey = "b713444458d927c3bc63a483e589df46"; // آپ کی اوپن ویدر API کی
    const url = `https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&appid=${apiKey}`;

    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error('Weather network error');
        
        const data = await response.json();
        const weatherTitle = document.querySelector('.weather-text h3');
        const weatherDesc = document.querySelector('.weather-text p');
        
        if (weatherTitle && weatherDesc) {
            const temp = Math.round(data.main.temp);
            const condition = data.weather[0].main.toLowerCase();
            
            // اردو میں بنیادی ترجمہ کا لاجک
            let urduCondition = "صاف موسم";
            if (condition.includes('rain')) urduCondition = "بارش کا امکان";
            else if (condition.includes('cloud')) urduCondition = "بادل چھائے ہیں";
            else if (condition.includes('clear')) urduCondition = "صاف اور چمکدار موسم";

            weatherTitle.innerText = `${city}: ${temp}°C`;
            weatherDesc.innerText = `${urduCondition} اور ہلکی ہوا`;
        }
    } catch (error) {
        console.log("انٹرنیٹ یا ویدر API کا مسئلہ ہے، ڈیفالٹ ڈیٹا شو ہو رہا ہے:", error);
    }
}

// ==========================================
// 3. دکان کے پیج کے لیے کیٹیگری فلٹر
// ==========================================
// function filterCategory(category) {
//     const products = document.querySelectorAll('.product-card');
    
//     products.forEach(product => {
//         const productCat = product.getAttribute('data-cat');
//         if (category === 'all' || productCat === category) {
//             product.style.display = 'flex';
//         } else {
//             product.style.display = 'none';
//         }
//     });
// }

// ==========================================
// 4. پریمیم سلائیڈر فکس اور سوائپ لاجک (RTL سپورٹڈ)
// ==========================================
const wrapper = document.getElementById('sliderWrapper');
const dots = document.querySelectorAll('.dot');
const totalSlides = document.querySelectorAll('.slide-card').length;
let currentIndex = 0;
let autoPlayTimer;

function showSlide(index) {
    if (index >= totalSlides) currentIndex = 0;
    else if (index < 0) currentIndex = totalSlides - 1;
    else currentIndex = index;

    if (wrapper) {
        // 🎯 جادوئی فکس: اردو پیج (RTL) پر پلس (+) لگانے سے سلائیڈر بالکل سیدھا موو کرے گا اور کارڈ نہیں کٹے گا
        wrapper.style.transform = `translateX(${currentIndex * 100}%)`;  
    }

    // ڈاٹس کو ایکٹو کرنا
    dots.forEach((dot, idx) => {
        if (idx === currentIndex) dot.classList.add('active');
        else dot.classList.remove('active');
    });
}

// ڈاٹس کلک ایونٹ
dots.forEach((dot, idx) => {
    dot.addEventListener('click', () => {
        showSlide(idx);
        resetAutoPlay();
    });
});

function startAutoPlay() {
    autoPlayTimer = setInterval(() => { 
        showSlide(currentIndex + 1); 
    }, 4000);
}

function resetAutoPlay() {
    clearInterval(autoPlayTimer);
    startAutoPlay();
}

// سوائپ اور ماؤس ڈریگ مینیجمنٹ
let startX = 0; 
let isDragging = false;

function handleStart(e) { 
    isDragging = true; 
    startX = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX; 
    clearInterval(autoPlayTimer); 
}

function handleEnd(e) {
    if (!isDragging) return; 
    isDragging = false;
    
    const endX = e.type.includes('mouse') ? e.pageX : e.changedTouches[0].clientX;
    const diffX = startX - endX;
    
    // 50px سے زیادہ سوائپ ہونے پر سلائیڈ بدلے گی
    if (Math.abs(diffX) > 50) { 
        if (diffX > 0) showSlide(currentIndex + 1); 
        else showSlide(currentIndex - 1); 
    } 
    resetAutoPlay();
}

// سلائیڈر ایونٹس رجسٹریشن (اگر پیج پر سلائیڈر موجود ہو)
if (wrapper) {
    wrapper.addEventListener('touchstart', handleStart, { passive: true }); 
    wrapper.addEventListener('touchend', handleEnd, { passive: true });
    wrapper.addEventListener('mousedown', handleStart); 
    wrapper.addEventListener('mouseup', handleEnd);
    wrapper.addEventListener('mouseleave', () => { isDragging = false; }); // اگر ماؤس باہر چلا جائے تو ایرر نہ آئے
}

// ==========================================
// 5. پیج لوڈ پر ایگزیکیوشن (Initialization)
// ==========================================
window.addEventListener('DOMContentLoaded', () => {
    fetchLiveWeather();
    if (wrapper && totalSlides > 0) {
        startAutoPlay();
    }
});

// ہوم پیج پر کارٹ میں آئٹم ایڈ کرنے کا نمونہ فنکشن
function addToCart(id, name, price, image, category) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    
    // چیک کرنا کہ آئٹم پہلے سے تو موجود نہیں
    let existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({ id, name, price, image, category, quantity: 1 });
    }
    localStorage.setItem("cart", JSON.stringify(cart));
    alert(`${name} کارٹ میں ایڈ ہو گئی ہے!`);
}

// ٹیسٹ کرنے کے لیے آپ ہوم پیج کے کسی بھی بٹن پر یہ لگا کر کلک کر سکتے ہیں:
// onclick="addToCart(1, 'Nativo Premium Seeds (1kg)', 4500, 'images/nativo.jpg', 'Kitchen Garden')"


document.addEventListener("DOMContentLoaded", function () {
    const addButtons = document.querySelectorAll(".minimal-add-btn");
    const cartCountElements = document.querySelectorAll(".cartCount");

    // 🔄 ہیڈر کاؤنٹ اپڈیٹ کرنے کا فنکشن
    function updateHeaderCartCount() {
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

            const productId = productCard.getAttribute("data-id");
            const productCat = productCard.getAttribute("data-cat") || "Pesticides";
            const productName = productCard.querySelector(".product-name").textContent.trim();
            const productImg = productCard.querySelector(".img-box img").getAttribute("src");
            
            const priceText = productCard.querySelector(".product-price").textContent;
            const productPrice = parseInt(priceText.replace(/[^0-9]/g, '')) || 0;

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
            updateHeaderCartCount(); // اپنے پیج کا کاؤنٹ فوراً بدلو

            // alert(`${productName} کارٹ میں شامل کر دی گئی ہے!`);
        });
    });

    // ⚡ چوکیدار: اگر شاپ یا کارٹ پیج پر کچھ بدلے، تو ہوم پیج کا کاؤنٹ لائیو بدلے
    window.addEventListener("storage", function (e) {
        if (e.key === "cart") {
            updateHeaderCartCount();
        }
    });

    // پیج لوڈ ہوتے ہی کاؤنٹ شو کرنا
    updateHeaderCartCount();
});



document.querySelectorAll('.minimal-add-btn').forEach(button => {
    button.addEventListener('click', function(event) {
        // اگلے پیج پر جانے سے پکا روکنے کے لیے
        event.preventDefault();
        event.stopPropagation();
        
        // کارٹ کی گنتی میں اضافہ اور ڈسپلے اپڈیٹ
        globalCartCount++;
        document.querySelectorAll('.cartCount').forEach(countSpan => {
            countSpan.textContent = globalCartCount;
        });

        // ==========================================================================
        // 🌟 فلوٹنگ سنیک بار (Bottom Notification) بنانے کا لاجک
        // ==========================================================================
        
        // اگر پہلے سے کوئی نوٹیفکیشن اسکرین پر ہے تو اسے ہٹاؤ
        const oldSnackbar = document.querySelector('.cart-snackbar');
        if (oldSnackbar) oldSnackbar.remove();

        // ۱۔ نیا نوٹیفکیشن باکس (Div) بنائیں
        const snackbar = document.createElement('div');
        snackbar.className = 'cart-snackbar';
        
        // ۲۔ اس کے اندر کا مال سیٹ کریں (ٹک کا نشان، ٹیکسٹ اور چیک آؤٹ لنک)
        snackbar.innerHTML = `
            <div class="snackbar-tick">✓</div>
            <span style="font-size: 14px;">آئٹم شامل ہو گیا!</span>
            <a href="cart.html" class="snackbar-btn">Checkout کریں</a>
        `;

        // ۳۔ اسے پیج کے اندر شامل (Append) کریں
        document.body.appendChild(snackbar);

        // ۴۔ صرف ۵۰ ملی سیکنڈ کا وقفہ دے کر 'show' کلاس لگا دیں تاکہ اینیمیشن کے ساتھ اوپر آئے
        setTimeout(() => {
            snackbar.classList.add('show');
        }, 50);

        // ۵۔ ٹھیک ۳ سیکنڈ (3000 مائیکرو سیکنڈ) بعد پٹی کو واپس نیچے چھپا کر ڈیلیٹ کر دیں
        setTimeout(() => {
            snackbar.classList.remove('show');
            // اینیمیشن ختم ہونے کے بعد اسے ایچ ٹی ایم ایل سے بالکل صاف کر دو
            setTimeout(() => { snackbar.remove(); }, 400);
        }, 3000);
    });
});
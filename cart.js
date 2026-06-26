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
                    <p style="font-size: 18px; color: #666;">Your Cart Is Empty Now</p>
                    <a href="shop.html" style="display:inline-block; margin-top:15px; padding:10px 20px; background:#2e7d32; color:white; text-decoration:none; border-radius:5px; font-weight:bold;">SHOP NOW</a>
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
document.addEventListener("DOMContentLoaded", function () {
    const provinceSelect = document.getElementById("custProvince");
    const districtSelect = document.getElementById("custDistrict");
    const citySelect = document.getElementById("custCity");

    // اگر پیج پر یہ فیلڈز موجود نہ ہوں تو کوڈ آگے نہ چلے (بگ سے بچنے کے لیے)
    if (!provinceSelect || !districtSelect || !citySelect) return;

    // 🗺️ پاکستان کا ڈیٹا (صوبہ -> اضلاع -> شہر)
    const pakistanData = {
        "Punjab": {
            "Lahore": ["Lahore City", "Raiwind", "Kahna Nau"],
            "Faisalabad": ["Faisalabad City", "Jaranwala", "Sammundri"],
            "Rawalpindi": ["Rawalpindi City", "Gujar Khan", "Taxila"],
            "Multan": ["Multan City", "Shujabad", "Jalampur Pirwala"],
            "Gujranwala": ["Gujranwala City", "Kamoke", "Wazirabad"],
            "Sargodha": ["Sargodha City", "Bhalwal", "Shahpur"],
            "Sahiwal": ["Sahiwal City", "Chichawatni"],
            "Sialkot": ["Sialkot City", "Daska", "Sambrial"]
        },
        "Sindh": {
            "Karachi": ["Karachi Central", "Karachi East", "Karachi South", "Karachi West", "Malir", "Korangi"],
            "Hyderabad": ["Hyderabad City", "Latifabad", "Qasimabad"],
            "Sukkur": ["Sukkur City", "Rohri", "Pano Aqil"],
            "Larkana": ["Larkana City", "Ratodero", "Dokri"]
        },
        "KPK": {
            "Peshawar": ["Peshawar City", "Hayatabad"],
            "Mardan": ["Mardan City", "Takht Bhai"],
            "Abbottabad": ["Abbottabad City", "Havelian"],
            "Swat": ["Mingora", "Barikot", "Khwazakhela"]
        },
        "Balochistan": {
            "Quetta": ["Quetta City", "Kuchlak"],
            "Gwadar": ["Gwadar City", "Pasni", "Ormara"],
            "Khuzdar": ["Khuzdar City", "Wadh"]
        },
        "Islamabad": {
            "Islamabad": ["Islamabad Sector Area", "Bara Kahu", "Tarnol", "Sihala"]
        }
    };

    // ۱. جب صوبہ سلیکٹ ہو تو ضلعے ایکٹو کرو
    provinceSelect.addEventListener("change", function () {
        const selectedProvince = this.value;
        
        districtSelect.innerHTML = '<option value="">ضلع (Select District)</option>';
        citySelect.innerHTML = '<option value="">شہر (Select City)</option>';
        citySelect.disabled = true;

        if (selectedProvince && pakistanData[selectedProvince]) {
            districtSelect.disabled = false;
            Object.keys(pakistanData[selectedProvince]).forEach(district => {
                const opt = document.createElement("option");
                opt.value = district;
                opt.textContent = district;
                districtSelect.appendChild(opt);
            });
        } else {
            districtSelect.disabled = true;
        }
    });

    // ۲. جب ضلع سلیکٹ ہو تو شہر ایکٹو کرو
    districtSelect.addEventListener("change", function () {
        const selectedProvince = provinceSelect.value;
        const selectedDistrict = this.value;

        citySelect.innerHTML = '<option value="">شہر (Select City)</option>';

        if (selectedDistrict && pakistanData[selectedProvince] && pakistanData[selectedProvince][selectedDistrict]) {
            citySelect.disabled = false;
            pakistanData[selectedProvince][selectedDistrict].forEach(city => {
                const opt = document.createElement("option");
                opt.value = city;
                opt.textContent = city;
                citySelect.appendChild(opt);
            });
        } else {
            citySelect.disabled = true;
        }
    });
});



document.addEventListener("DOMContentLoaded", function () {
    const provinceSelect = document.getElementById("custProvince");
    const districtSelect = document.getElementById("custDistrict");
    const citySelect = document.getElementById("custCity");
    
    // "Other" والی ان پٹ فیلڈز کے کنٹینرز
    const otherDistrictWrapper = document.getElementById("otherDistrictWrapper");
    const otherCityWrapper = document.getElementById("otherCityWrapper");
    const custDistrictOther = document.getElementById("custDistrictOther");
    const custCityOther = document.getElementById("custCityOther");

    if (!provinceSelect || !districtSelect || !citySelect) return;

    // 🗺️ پاکستان کا ڈیٹا (صوبہ -> اضلاع -> شہر)
    const pakistanData = {
        "Punjab": {
            "Lahore": ["Lahore City", "Raiwind", "Kahna Nau"],
            "Faisalabad": ["Faisalabad City", "Jaranwala", "Sammundri"],
            "Rawalpindi": ["Rawalpindi City", "Gujar Khan", "Taxila"],
            "Multan": ["Multan City", "Shujabad"],
            "Gujranwala": ["Gujranwala City", "Kamoke", "Wazirabad"],
            "Sargodha": ["Sargodha City", "Bhalwal"],
            "Sahiwal": ["Sahiwal City", "Chichawatni"],
            "Sialkot": ["Sialkot City", "Daska"]
        },
        "Sindh": {
            "Karachi": ["Karachi Central", "Karachi East", "Karachi South", "Karachi West", "Malir", "Korangi"],
            "Hyderabad": ["Hyderabad City", "Latifabad"],
            "Sukkur": ["Sukkur City", "Rohri"],
            "Larkana": ["Larkana City"]
        },
        "KPK": {
            "Peshawar": ["Peshawar City", "Hayatabad"],
            "Mardan": ["Mardan City"],
            "Abbottabad": ["Abbottabad City"],
            "Swat": ["Mingora"]
        },
        "Balochistan": {
            "Quetta": ["Quetta City"],
            "Gwadar": ["Gwadar City"],
            "Khuzdar": ["Khuzdar City"]
        },
        "Islamabad": {
            "Islamabad": ["Islamabad Sector Area", "Bara Kahu", "Tarnol"]
        }
    };

    // ۱. جب صوبہ سلیکٹ ہو تو ضلعے لوڈ کرو
    provinceSelect.addEventListener("change", function () {
        const selectedProvince = this.value;
        
        districtSelect.innerHTML = '<option value="">Select District</option>';
        citySelect.innerHTML = '<option value="">Select City</option>';
        citySelect.disabled = true;
        
        // "Other" والی فیلڈز کو چھپائیں اور ان کی ویلیو بھی خالی کریں
        otherDistrictWrapper.style.display = "none";
        custDistrictOther.required = false;
        custDistrictOther.value = ""; 
        
        otherCityWrapper.style.display = "none";
        custCityOther.required = false;
        custCityOther.value = "";

        if (selectedProvince && pakistanData[selectedProvince]) {
            districtSelect.disabled = false;
            Object.keys(pakistanData[selectedProvince]).forEach(district => {
                const opt = document.createElement("option");
                opt.value = district;
                opt.textContent = district;
                districtSelect.appendChild(opt);
            });
            
            // آخر میں "Other" کا آپشن جوڑیں
            const otherOpt = document.createElement("option");
            otherOpt.value = "other";
            otherOpt.textContent = "Other District";
            districtSelect.appendChild(otherOpt);
        } else {
            districtSelect.disabled = true;
        }
    });

    // ۲. جب ضلع سلیکٹ ہو تو شہر لوڈ کرو
    districtSelect.addEventListener("change", function () {
        const selectedProvince = provinceSelect.value;
        const selectedDistrict = this.value;

        citySelect.innerHTML = '<option value="">Select City</option>';
        
        // پہلے سے اوپن سٹی "Other" فیلڈ کو ری سیٹ کریں
        otherCityWrapper.style.display = "none";
        custCityOther.required = false;
        custCityOther.value = "";

        // اگر ضلع میں "Other" سلیکٹ ہوا ہو
        if (selectedDistrict === "other") {
            otherDistrictWrapper.style.display = "block";
            custDistrictOther.required = true;
            
            // شہر والا ڈراپ ڈاؤن ڈائریکٹ "Other" کر دیں
            citySelect.innerHTML = '<option value="other">Other City</option>';
            citySelect.value = "other";
            citySelect.disabled = false;
            
            otherCityWrapper.style.display = "block";
            custCityOther.required = true;
            return;
        } else {
            // یہاں فکس کیا ہے: اگر "Other" سے ہٹ کر کوئی اور ضلع چنا تو فیلڈ چھپائیں اور ریکوائرڈ ختم کریں
            otherDistrictWrapper.style.display = "none";
            custDistrictOther.required = false;
            custDistrictOther.value = "";
        }

        if (selectedDistrict && pakistanData[selectedProvince] && pakistanData[selectedProvince][selectedDistrict]) {
            citySelect.disabled = false;
            pakistanData[selectedProvince][selectedDistrict].forEach(city => {
                const opt = document.createElement("option");
                opt.value = city;
                opt.textContent = city;
                citySelect.appendChild(opt);
            });

            // آخر میں "Other" کا آپشن جوڑیں
            const otherCityOpt = document.createElement("option");
            otherCityOpt.value = "other";
            otherCityOpt.textContent = "Other City";
            citySelect.appendChild(otherCityOpt);
        } else {
            citySelect.disabled = true;
        }
    });

    // ۳. جب شہر میں "Other" سلیکٹ ہو
    citySelect.addEventListener("change", function () {
        if (this.value === "other") {
            otherCityWrapper.style.display = "block";
            custCityOther.required = true;
        } else {
            otherCityWrapper.style.display = "none";
            custCityOther.required = false;
            custCityOther.value = ""; // ویلیو صاف کریں
        }
    });
});


function processOrder(buttonType) {
    // 1. آپ کے HTML فارم کی اصل IDs سے ڈیٹا اٹھانا
    let name = document.getElementById("custName").value.trim();
    let phone = document.getElementById("custPhone").value.trim();
    let province = document.getElementById("custProvince").value.trim();
    
    // ضلع چیک کرنا (اگر لسٹ والا خالی ہے تو ٹائپ کرنے والا اٹھائے)
    let district = document.getElementById("custDistrict").value.trim();
    if (!district) {
        district = document.getElementById("custDistrictOther").value.trim();
    }

    // شہر چیک کرنا (اگر لسٹ والا خالی ہے تو ٹائپ کرنے والا اٹھائے)
    let city = document.getElementById("custCity").value.trim();
    if (!city) {
        city = document.getElementById("custCityOther").value.trim();
    }
    
    let address = document.getElementById("custAddress").value.trim();
    
    // 2. پیمنٹ کا طریقہ چنا (payMethod ڈراپ ڈاؤن سے)
    let paymentSelect = document.querySelector('select[name="payMethod"]');
    let paymentMethod = paymentSelect ? paymentSelect.value : "COD";

    // 3. گرینڈ ٹوٹل رقم اٹھانا (Rs 4,700 میں سے صرف نمبر الگ کرنا)
    let totalAmountText = document.getElementById("grandTotalPrice").innerText; 
    let totalCartAmount = totalAmountText.replace(/[^\d]/g, ''); 

    // 4. کارٹ کی پروڈکٹس لوکل اسٹوریج سے اٹھانا
    let cartItemsList = "";
    let localCart = JSON.parse(localStorage.getItem("cart")) || [];
    if (localCart.length > 0) {
        cartItemsList = localCart.map(item => `${item.name} (x${item.quantity})`).join(", ");
    } else {
        cartItemsList = "کارٹ کی پروڈکٹس"; 
    }

    // فارم کی ویلیڈیشن (ضروری فیلڈز چیک کرنا)
    if (!name || !phone || !city || !address) {
        alert("براہ کرم تمام ضروری معلومات (نام، فون، شہر اور پتہ) درج کریں!");
        return;
    }

    // 5. پی ایچ پی فائل کو بھیجنے کے لیے ڈیٹا پیک کرنا
    let formData = new FormData();
    formData.append("customer_name", name);
    formData.append("phone_number", phone);
    formData.append("province", province);
    formData.append("district", district);
    formData.append("city", city);
    formData.append("address", address);
    formData.append("cart_items", cartItemsList);
    formData.append("payment_method", paymentMethod);
    formData.append("total_amount", totalCartAmount);

    // 6. AJAX کے ذریعے 'save_order.php' کو ڈیٹا بھیجنا
    fetch("save_order.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            
            // واٹس ایپ میسج کا ٹیکسٹ
            let whatsappMessage = `*نیا آرڈر موصول ہوا ہے!*\n\n` +
                                  `*نام:* ${name}\n` +
                                  `*فون:* ${phone}\n` +
                                  `*صوبہ:* ${province}\n` +
                                  `*ضلع:* ${district}\n` +
                                  `*شہر:* ${city}\n` +
                                  `*پتہ:* ${address}\n` +
                                  `*آئٹمز:* ${cartItemsList}\n` +
                                  `*ٹوٹل رقم:* Rs ${totalCartAmount}\n` +
                                  `*پیمنٹ کا طریقہ:* ${paymentMethod}\n` +
                                  `*آرڈر اسٹیٹس:* ${data.order_status}`;

            let whatsappNumber = "923056416902"; // آپ کا فوٹر والا نمبر لگا دیا ہے
            let whatsappUrl = `https://api.whatsapp.com/send?phone=${whatsappNumber}&text=${encodeURIComponent(whatsappMessage)}`;

            // بٹن کلک کا لاجک چیک کرنا
            if (buttonType === "whatsapp") {
                window.open(whatsappUrl, "_blank");
                clearCartAndReload();
            } 
            else if (buttonType === "place_order") {
                if (paymentMethod === "JazzCash" || paymentMethod === "EasyPaisa" || paymentMethod === "HBL") {
                    alert(`آپ کا آرڈر محفوظ کر لیا گیا ہے۔ اسٹیٹس: پینڈنگ (${data.order_status})۔ ایڈوانس پیمنٹ کی تصدیق کے لیے آپ کو واٹس ایپ پر ری ڈائریکٹ کیا جا رہا ہے۔`);
                    window.open(whatsappUrl, "_blank");
                    clearCartAndReload();
                } else {
                    alert("شکریہ! آپ کا کیش آن ڈیلیوری (COD) آرڈر کامیابی سے موصول ہو گیا ہے اور ڈیٹا بیس میں سیو ہو چکا ہے۔");
                    clearCartAndReload();
                }
            }
        } else {
            alert("ڈیٹا بیس ایرر: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("سرور یا پی ایچ پی فائل سے رابطہ نہیں ہو سکا!");
    });
}

function clearCartAndReload() {
    localStorage.removeItem("cart"); 
    location.reload();
}
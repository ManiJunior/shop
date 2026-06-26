<?php
// 1. ڈیٹا بیس کنکشن (پہلے کی طرح)
$host = "localhost";
$username = "root";
$password = "";
$database = "greenhub_db"; 

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database Connection Failed"]));
}

// 2. جاوا اسکرپٹ کے نئے ناموں کے مطابق ڈیٹا وصول کرنا (بالکل میچنگ!)
$customer_name   = $_POST['custName'] ?? '';
$phone_number    = $_POST['custPhone'] ?? '';
$province        = $_POST['custProvince'] ?? '';
$district        = $_POST['custDistrict'] ?? '';
$city            = $_POST['custCity'] ?? '';
$address         = $_POST['custAddress'] ?? '';
$cart_items      = $_POST['cart_items'] ?? '';
$payment_method  = $_POST['payMethod'] ?? ''; // جاوا اسکرپٹ کا payMethod یہاں آیا
$total_amount    = $_POST['grandTotalPrice'] ?? ''; // جاوا اسکرپٹ کا grandTotalPrice یہاں آیا

// 3. آرڈر اسٹیٹس کا لاجک
$order_status = "Approved"; 
if ($payment_method == "JazzCash" || $payment_method == "EasyPaisa" || $payment_method == "HBL") {
    $order_status = "Pending";
}

// 4. ڈیٹا بیس کی ٹیبل میں ڈالنا (ٹیبل کے کالم کے نام وہی رہیں گے جو تصویر میں تھے)
$sql = "INSERT INTO orders (customer_name, phone_number, province, district, city, address, cart_items, payment_method, order_status, total_amount) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssss", $customer_name, $phone_number, $province, $district, $city, $address, $cart_items, $payment_method, $order_status, $total_amount);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success", 
        "message" => "Order saved successfully",
        "order_status" => $order_status
    ]);
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Failed to save order: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
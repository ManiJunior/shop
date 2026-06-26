<?php
$host = "localhost";
$user = "root";         // اگر آپ XAMPP استعمال کر رہے ہیں تو یہ 'root' ہی رہے گا
$password = "";         // اگر کوئی پاس ورڈ نہیں رکھا تو اسے خالی چھوڑ دیں
$dbname = "greenhub_db"; // ⚠️ یہاں اپنے ڈیٹا بیس کا اصل نام لکھیں جو آپ نے بنایا ہے

// کنکشن بنانے کا طریقہ
$conn = new mysqli($host, $user, $password, $dbname);

// چیک کریں کہ کنکشن صحیح بنا یا کوئی ایرر آیا
if ($conn->connect_error) {
    die("ڈیٹا بیس سے کنکشن فیل ہو گیا: " . $conn->connect_error);
}

// اردو فونٹ اور ٹیکسٹ کو صحیح دکھانے کے لیے یہ لائن ضروری ہے
$conn->set_charset("utf8");
?>
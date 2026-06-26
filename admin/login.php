<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../db_connect.php'; // اپنے ڈیٹا بیس کنکشن کا صحیح راستہ چیک کر لیں

// اگر ایڈمن پہلے سے لاگ ان ہے، تو اسے سیدھا مین ڈیش بورڈ پر بھیج دیں
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit();
}

$error = "";

// 🔒 سیکیورٹی چیک 1: لاگ ان بلاک ٹائمر چیک کریں
if (isset($_SESSION['login_blocked_until']) && time() < $_SESSION['login_blocked_until']) {
    $remaining_time = ceil(($_SESSION['login_blocked_until'] - time()) / 60);
    $error = "❌ بہت زیادہ غلط کوششیں۔ آپ کا اکاؤنٹ $remaining_time منٹ کے لیے لاک ہے۔";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($error)) {
    
    // 🔒 سیکیورٹی چیک 2: ہنی پاٹ (روبوٹ کا جال)
    if (!empty($_POST['email_verify'])) {
        die("Robot detected! Access Denied.");
    }

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // 🗄️ لائیو میچنگ: یہاں ہم نے ٹیبل کا نام 'admin_users' رکھا ہے (اگر آپ کا 'admins' ہے تو اسے بدل لیں)
    $query = "SELECT * FROM admin_users WHERE username = '$username'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // سادہ پاس ورڈ میچنگ 
        if (password_verify($password, $row['password'])) {
            
            // ✅ لاگ ان کامیاب! چابی (سیشن) تیار
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $row['username'];
            
            // 🎯 جادوئی لائن: اب یہ ڈیٹا بیس سے لائیو لیول (1، 2 یا 3) اٹھائے گا!
            $_SESSION['admin_level'] = (int)$row['admin_level']; 
            
            unset($_SESSION['login_attempts']);
            unset($_SESSION['login_blocked_until']);

            // 🚀 اب یہ سیدھا مین ہوم پیج پر جائے گا جہاں سائیڈ بار لگی ہے
            header("Location: index.php");
            exit();
        }
    }

    // ❌ غلط پاس ورڈ پر کاؤنٹر
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 1;
    } else {
        $_SESSION['login_attempts']++;
    }

    if ($_SESSION['login_attempts'] >= 5) {
        $_SESSION['login_blocked_until'] = time() + (15 * 60);
        $error = "❌ بہت زیادہ غلط کوششیں۔ آپ کا اکاؤنٹ 15 منٹ کے لیے لاک کر دیا گیا ہے۔";
    } else {
        $remaining_attempts = 5 - $_SESSION['login_attempts'];
        $error = "❌ غلط یوزر نیم یا پاس ورڈ! آپ کے پاس $remaining_attempts کوششیں باقی ہیں۔";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - GreenHub</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 400px; }
        .login-card h2 { text-align: center; color: #2c3e50; margin-bottom: 10px; }
        .login-card p { text-align: center; color: #7f8c8d; margin-bottom: 30px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 15px; }
        .form-control:focus { border-color: #2ecc71; outline: none; }
        .verify-field { display: none !important; visibility: hidden !important; }
        .btn-login { background-color: #2ecc71; color: white; padding: 12px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; transition: 0.3s; margin-top: 10px; }
        .btn-login:hover { background-color: #27ae60; }
        .error-msg { color: #e74c3c; background: #fce4e4; padding: 10px; border-radius: 4px; text-align: center; margin-bottom: 20px; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>GreenHub Admin</h2>
    <p>Sign in to manage your store</p>

    <?php if(!empty($error)) { echo "<div class='error-msg'>$error</div>"; } ?>

    <form action="login.php" method="POST">
        <input type="text" name="email_verify" class="verify-field" autocomplete="off">

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" placeholder="Enter username" required autocomplete="off">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter password" required>
        </div>

        <button type="submit" class="btn-login">🔒 Secure Log In</button>
    </form>
</div>

</body>
</html>
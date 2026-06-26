
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔑 چوکیدار چیک: اگر بندہ لاگ ان نہیں ہے، تو اسے واپس لاگ ان پیج پر بھگاؤ
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// لاگ ان ہوئے ایڈمن کا لیول اس ویری ایبل میں سیو ہو گیا (1, 2، یا 3)
$current_level = $_SESSION['admin_level']; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AgriShop</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; background-color: #f4f6f9; min-height: 100vh; }
        
        /* Sidebar Styling */
        .sidebar { width: 260px; background-color: #2c3e50; color: white; padding: 20px 0; display: flex; flex-direction: column; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; font-size: 24px; color: #2ecc71; letter-spacing: 1px; }
        .sidebar a { padding: 15px 25px; color: #ecf0f1; text-decoration: none; display: block; font-size: 16px; transition: 0.3s; }
        .sidebar a:hover { background-color: #34495e; color: #2ecc71; border-left: 4px solid #2ecc71; }
        
        /* Main Content Styling */
        .main-content { flex: 1; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: white; padding: 15px 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .header h1 { font-size: 24px; color: #333; }
        
        /* Welcome Card */
        .welcome-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; }
        .welcome-card h2 { color: #2c3e50; margin-bottom: 10px; }
        .welcome-card p { color: #7f8c8d; font-size: 16px; }
    </style>
</head>
<body>

    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- MAIN CONTENT AREA -->
    <div class="main-content">
        <div class="header">
            <h1>Dashboard Home</h1>
            <span>Welcome, Admin |<a href="logout.php" style="color: #e74c3c; text-decoration: none; font-weight: bold;">Logout</a></span>
        </div>

        <div class="welcome-card">
            <h2>Welcome to GreenHub Control Room</h2>
            <p>Select any option from the left sidebar menu to manage your website content.</p>
        </div>
    </div>
<script>
// 🛡️ یہ کوڈ براؤزر کی ہسٹری کو کنٹرول کرے گا اور بیک بٹن پر پیج کو زبردستی ریفریش کرے گا
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

window.addEventListener('pageshow', function (event) {
    // اگر پیج براؤزر کی ہسٹری/کیشے سے لوڈ ہوا ہے، تو یہ اسے فوراً ریفریش کر دے گا
    if (event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2)) {
        window.location.reload();
    }
});
</script>
</body>
</html>
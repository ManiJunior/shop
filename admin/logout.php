<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔑 چابی کو میموری سے بالکل مٹا دو
session_unset();
session_destroy();

// واپس لاگ ان پیج پر بھیج دو
header("Location: login.php");
exit();
?>
<?php
// logout.php
session_start(); // Memulai sesi jika belum dimulai

// Hapus semua data sesi
$_SESSION = array();

// Hapus cookie sesi jika ada
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Hancurkan sesi
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit();
?>
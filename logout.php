<?php
session_start();

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session
session_destroy();

// Redirect ke halaman login (misalnya login.php)
header("Location: login.php"); // Tanpa slash di depan // <--- Ubah baris ini
exit;
?>
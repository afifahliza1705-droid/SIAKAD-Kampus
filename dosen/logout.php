<?php
session_start();

// Hapus semua session
$_SESSION = [];

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header("Location: ../auth/login.php");
exit;
?>
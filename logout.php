<?php
session_start();

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session
session_destroy();

// Redirect ke halaman login (Path sudah diperbaiki)
header("Location: login.php");
exit;
?>
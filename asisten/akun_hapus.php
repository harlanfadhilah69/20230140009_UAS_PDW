<?php
session_start();
require_once '../config.php';

// Validasi admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$id_user = $_GET['id'] ?? null;
$admin_id = $_SESSION['user_id'];

// Cegah admin menghapus dirinya sendiri
if ($id_user && $id_user != $admin_id) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id_user);
    if ($stmt->execute()) {
        header("Location: manajemen_akun.php?status=hapus_sukses");
    } else {
        header("Location: manajemen_akun.php?status=hapus_gagal");
    }
    $stmt->close();
} else {
    header("Location: manajemen_akun.php");
}

$conn->close();
exit();
?>
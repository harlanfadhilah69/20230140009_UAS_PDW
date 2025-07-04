<?php
require_once '../config.php';
session_start();

// Cek jika pengguna bukan asisten atau tidak login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM mata_praktikum WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: manajemen_praktikum.php?status=hapus_sukses");
    } else {
        header("Location: manajemen_praktikum.php?status=hapus_gagal");
    }
    $stmt->close();
} else {
    header("Location: manajemen_praktikum.php");
}

$conn->close();
exit();
?>
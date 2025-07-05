<?php
session_start();
require_once '../config.php';

// Validasi sesi asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if ($id) {
    // Nonaktifkan sementara pemeriksaan foreign key untuk memastikan bisa menghapus
    $conn->query("SET FOREIGN_KEY_CHECKS=0");

    // Hapus mata praktikum utama. 
    // ON DELETE CASCADE di database akan menangani penghapusan data terkait di tabel lain.
    $stmt = $conn->prepare("DELETE FROM mata_praktikum WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    // Aktifkan kembali pemeriksaan foreign key
    $conn->query("SET FOREIGN_KEY_CHECKS=1");
    
    // Redirect dengan pesan sukses
    header("Location: manajemen_praktikum.php?status=hapus_sukses");

} else {
    // Redirect jika tidak ada ID
    header("Location: manajemen_praktikum.php");
}

$conn->close();
exit();
?>
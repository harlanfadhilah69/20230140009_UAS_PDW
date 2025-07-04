<?php
require_once '../config.php';
session_start();

// Validasi sesi asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$id_modul = $_GET['id'] ?? null;

if ($id_modul) {
    // 1. Ambil informasi modul untuk mendapatkan nama file dan id_praktikum
    $stmt_select = $conn->prepare("SELECT id_praktikum, file_materi FROM modul WHERE id = ?");
    $stmt_select->bind_param("i", $id_modul);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $modul = $result->fetch_assoc();
    $stmt_select->close();

    if ($modul) {
        $id_praktikum = $modul['id_praktikum'];
        $file_materi = $modul['file_materi'];

        // 2. Hapus file fisik dari folder uploads jika ada
        if (!empty($file_materi) && file_exists('../uploads/' . $file_materi)) {
            unlink('../uploads/' . $file_materi);
        }

        // 3. Hapus record dari database
        $stmt_delete = $conn->prepare("DELETE FROM modul WHERE id = ?");
        $stmt_delete->bind_param("i", $id_modul);
        
        if ($stmt_delete->execute()) {
            // Redirect kembali ke halaman daftar modul dengan pesan sukses
            header("Location: modul.php?id_praktikum=" . $id_praktikum . "&status=hapus_sukses");
        } else {
            header("Location: modul.php?id_praktikum=" . $id_praktikum . "&status=hapus_gagal");
        }
        $stmt_delete->close();

    } else {
        // Jika modul tidak ditemukan, kembali ke halaman utama
        header("Location: manajemen_praktikum.php");
    }

} else {
    header("Location: manajemen_praktikum.php");
}

$conn->close();
exit();
?>
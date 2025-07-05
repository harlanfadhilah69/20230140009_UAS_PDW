<?php
session_start();
require_once '../config.php';

// Validasi sesi asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$id_modul = $_GET['id'] ?? null;

if ($id_modul) {
    // Ambil info modul untuk redirect dan hapus file
    $stmt_get = $conn->prepare("SELECT id_praktikum, file_materi FROM modul WHERE id = ?");
    $stmt_get->bind_param("i", $id_modul);
    $stmt_get->execute();
    $modul = $stmt_get->get_result()->fetch_assoc();
    $stmt_get->close();

    if ($modul) {
        $id_praktikum = $modul['id_praktikum'];
        $file_materi = $modul['file_materi'];

        // Hapus file fisik dari folder uploads jika ada
        if (!empty($file_materi) && file_exists('../uploads/' . $file_materi)) {
            unlink('../uploads/' . $file_materi);
        }

        // Nonaktifkan foreign key check, hapus, lalu aktifkan kembali
        $conn->query("SET FOREIGN_KEY_CHECKS=0");
        $stmt_delete = $conn->prepare("DELETE FROM modul WHERE id = ?");
        $stmt_delete->bind_param("i", $id_modul);
        $stmt_delete->execute();
        $stmt_delete->close();
        $conn->query("SET FOREIGN_KEY_CHECKS=1");

        header("Location: modul.php?id_praktikum=" . $id_praktikum . "&status=hapus_sukses");
    } else {
        header("Location: manajemen_praktikum.php");
    }
} else {
    header("Location: manajemen_praktikum.php");
}

$conn->close();
exit();
?>
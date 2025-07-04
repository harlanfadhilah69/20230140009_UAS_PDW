<?php
session_start();
require_once '../config.php';

// Pastikan mahasiswa sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$id_mahasiswa = $_SESSION['user_id'];
$id_praktikum = $_GET['id_praktikum'] ?? null;

if (!$id_praktikum) {
    header("Location: courses.php?status=gagal");
    exit();
}

// Cek apakah sudah terdaftar
$stmt_check = $conn->prepare("SELECT * FROM pendaftaran WHERE id_mahasiswa = ? AND id_praktikum = ?");
$stmt_check->bind_param("ii", $id_mahasiswa, $id_praktikum);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Jika sudah terdaftar, arahkan ke halaman praktikum saya
    header("Location: my_courses.php?status=sudah_terdaftar");
} else {
    // Jika belum, daftarkan
    $stmt_insert = $conn->prepare("INSERT INTO pendaftaran (id_mahasiswa, id_praktikum) VALUES (?, ?)");
    $stmt_insert->bind_param("ii", $id_mahasiswa, $id_praktikum);

    if ($stmt_insert->execute()) {
        header("Location: my_courses.php?status=daftar_sukses");
    } else {
        header("Location: courses.php?status=gagal");
    }
    $stmt_insert->close();
}
$stmt_check->close();
$conn->close();
exit();
?>
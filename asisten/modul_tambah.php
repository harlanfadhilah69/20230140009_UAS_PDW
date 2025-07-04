<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') { header("Location: ../login.php"); exit(); }

$id_praktikum = $_GET['id_praktikum'] ?? null;
if (!$id_praktikum) { echo "ID Praktikum tidak valid."; exit; }

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_modul = trim($_POST['nama_modul']);
    $deskripsi = trim($_POST['deskripsi']);
    $file_materi_path = null;

    if (empty($nama_modul)) {
        $error = "Nama modul wajib diisi!";
    } else {
        if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == UPLOAD_ERR_OK) {
            $file_info = $_FILES['file_materi'];
            $file_ext = strtolower(pathinfo($file_info['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['pdf', 'doc', 'docx', 'zip', 'rar'];
            
            if (!in_array($file_ext, $allowed_ext)) {
                $error = "Format file tidak diizinkan.";
            } else {
                $upload_dir = '../uploads/';
                $new_file_name = uniqid('modul_', true) . '.' . $file_ext;
                $destination = $upload_dir . $new_file_name;
                if (move_uploaded_file($file_info['tmp_name'], $destination)) {
                    $file_materi_path = $new_file_name;
                } else { $error = "Gagal mengunggah file."; }
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO modul (id_praktikum, nama_modul, deskripsi, file_materi) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $id_praktikum, $nama_modul, $deskripsi, $file_materi_path);
            if ($stmt->execute()) {
                header("Location: modul.php?id_praktikum=" . $id_praktikum . "&status=tambah_sukses");
                exit();
            } else { $error = "Gagal menyimpan ke database."; }
            $stmt->close();
        }
    }
}

$pageTitle = 'Tambah Modul';
$activePage = 'manajemen_praktikum';
require_once 'templates/header.php';
?>

<div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6 border-b dark:border-gray-700 pb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Modul Baru</h2>
        <a href="modul.php?id_praktikum=<?php echo $id_praktikum; ?>" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">&larr; Kembali</a>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 dark:bg-red-900/50 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg mb-4"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="modul_tambah.php?id_praktikum=<?php echo $id_praktikum; ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label for="nama_modul" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Modul</label>
            <input type="text" name="nama_modul" id="nama_modul" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg" required>
        </div>
        <div>
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi Singkat</label>
            <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg"></textarea>
        </div>
        <div>
            <label for="file_materi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">File Materi (Opsional)</label>
            <input type="file" id="file_materi" name="file_materi" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-100 dark:file:bg-gray-600 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-gray-200 dark:hover:file:bg-gray-500">
        </div>
        <div class="flex justify-end pt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg">Simpan Modul</button>
        </div>
    </form>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>
<?php
session_start();
require_once '../config.php';

// Validasi sesi asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$error = '';
$id_modul = $_GET['id'] ?? null;

if (!$id_modul) {
    header("Location: manajemen_praktikum.php");
    exit();
}

// Ambil data modul yang akan diedit untuk mendapatkan id_praktikum
$stmt_get = $conn->prepare("SELECT * FROM modul WHERE id = ?");
$stmt_get->bind_param("i", $id_modul);
$stmt_get->execute();
$result_get = $stmt_get->get_result();
$modul = $result_get->fetch_assoc();
$stmt_get->close();

if (!$modul) {
    echo "Modul tidak ditemukan.";
    exit;
}
$id_praktikum = $modul['id_praktikum'];


// Proses form SEBELUM mencetak HTML
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_modul = trim($_POST['nama_modul']);
    $deskripsi = trim($_POST['deskripsi']);
    $file_materi_path = $modul['file_materi']; // Gunakan file lama sebagai default

    if (empty($nama_modul)) {
        $error = "Nama modul wajib diisi!";
    } else {
        // Cek jika ada file baru yang diunggah
        if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == UPLOAD_ERR_OK) {
            // Hapus file lama jika ada
            if (!empty($modul['file_materi']) && file_exists('../uploads/' . $modul['file_materi'])) {
                unlink('../uploads/' . $modul['file_materi']);
            }

            $file_info = $_FILES['file_materi'];
            $file_name = $file_info['name'];
            $file_tmp_name = $file_info['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $upload_dir = '../uploads/';
            $new_file_name = uniqid('modul_', true) . '.' . $file_ext;
            $destination = $upload_dir . $new_file_name;
            
            $allowed_ext = ['pdf', 'doc', 'docx', 'zip', 'rar'];
            if (!in_array($file_ext, $allowed_ext)) {
                $error = "Format file tidak diizinkan.";
            } else if (move_uploaded_file($file_tmp_name, $destination)) {
                $file_materi_path = $new_file_name; // Set nama file baru
            } else {
                $error = "Terjadi kesalahan saat mengunggah file baru.";
            }
        }

        // Jika tidak ada error, update database
        if (empty($error)) {
            $stmt_update = $conn->prepare("UPDATE modul SET nama_modul = ?, deskripsi = ?, file_materi = ? WHERE id = ?");
            $stmt_update->bind_param("sssi", $nama_modul, $deskripsi, $file_materi_path, $id_modul);
            
            if ($stmt_update->execute()) {
                header("Location: modul.php?id_praktikum=" . $id_praktikum . "&status=edit_sukses");
                exit();
            } else {
                $error = "Gagal memperbarui data: " . $stmt_update->error;
            }
            $stmt_update->close();
        }
    }
}

// Setelah semua logika selesai, baru panggil header
$pageTitle = 'Edit Modul';
$activePage = 'manajemen_praktikum';
require_once 'templates/header.php';
?>

<div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6 border-b dark:border-gray-700 pb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit Modul</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui detail modul di bawah ini.</p>
        </div>
        <a href="modul.php?id_praktikum=<?php echo $id_praktikum; ?>" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
            &larr; Kembali
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bg-red-100 dark:bg-red-900/50 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg mb-4">
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <form action="modul_edit.php?id=<?php echo $id_modul; ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label for="nama_modul" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Modul</label>
            <input type="text" id="nama_modul" name="nama_modul" value="<?php echo htmlspecialchars($modul['nama_modul']); ?>" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm" required>
        </div>
        <div>
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi Singkat</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm"><?php echo htmlspecialchars($modul['deskripsi']); ?></textarea>
        </div>
        <div>
            <label for="file_materi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ganti File Materi (Opsional)</label>
            <?php if (!empty($modul['file_materi'])): ?>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-2">File saat ini: <a href="../uploads/<?php echo htmlspecialchars($modul['file_materi']); ?>" target="_blank" class="text-blue-500 hover:underline"><?php echo htmlspecialchars($modul['file_materi']); ?></a></p>
            <?php endif; ?>
            <input type="file" id="file_materi" name="file_materi" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-50 dark:file:bg-gray-700 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-gray-100">
        </div>
        <div class="flex items-center justify-end pt-2">
            <button type="submit" class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2.5 px-6 rounded-lg shadow-md">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php 
$conn->close();
require_once 'templates/footer.php'; 
?>
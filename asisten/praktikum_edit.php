<?php
session_start();
require_once '../config.php';

// Validasi sesi asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$error = '';
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manajemen_praktikum.php");
    exit();
}

// Proses form SEBELUM mencetak HTML
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_praktikum = trim($_POST['kode_praktikum']);
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($kode_praktikum) || empty($nama)) {
        $error = "Kode dan Nama Praktikum tidak boleh kosong!";
    } else {
        $stmt = $conn->prepare("UPDATE mata_praktikum SET kode_praktikum = ?, nama = ?, deskripsi = ? WHERE id = ?");
        $stmt->bind_param("sssi", $kode_praktikum, $nama, $deskripsi, $id);
        
        if ($stmt->execute()) {
            header("Location: manajemen_praktikum.php?status=edit_sukses");
            exit();
        } else {
            $error = "Gagal memperbarui data: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Ambil data praktikum yang akan diedit
$stmt_select = $conn->prepare("SELECT * FROM mata_praktikum WHERE id = ?");
$stmt_select->bind_param("i", $id);
$stmt_select->execute();
$result = $stmt_select->get_result();
$praktikum = $result->fetch_assoc();
$stmt_select->close();

if (!$praktikum) {
    echo "Data tidak ditemukan.";
    exit;
}

// Setelah semua logika selesai, baru panggil header
$pageTitle = 'Edit Praktikum';
$activePage = 'manajemen_praktikum';
require_once 'templates/header.php';
?>

<div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6 border-b dark:border-gray-700 pb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit Mata Praktikum</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui detail untuk praktikum ini.</p>
        </div>
        <a href="manajemen_praktikum.php" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
            &larr; Kembali
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bg-red-100 dark:bg-red-900/50 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg mb-4" role="alert">
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <form action="praktikum_edit.php?id=<?php echo $id; ?>" method="POST" class="space-y-6">
        <div>
            <label for="kode_praktikum" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Praktikum</label>
            <input type="text" id="kode_praktikum" name="kode_praktikum" value="<?php echo htmlspecialchars($praktikum['kode_praktikum']); ?>" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Praktikum</label>
            <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($praktikum['nama']); ?>" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($praktikum['deskripsi']); ?></textarea>
        </div>
        <div class="flex items-center justify-end pt-2">
            <button type="submit" class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-all">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>
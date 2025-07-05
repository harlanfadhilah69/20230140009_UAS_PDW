<?php
session_start();
require_once '../config.php';

// Validasi sesi asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$error = '';

// Proses form SEBELUM mencetak HTML
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_praktikum = trim($_POST['kode_praktikum']);
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($kode_praktikum) || empty($nama)) {
        $error = "Kode dan Nama Praktikum wajib diisi!";
    } else {
        $stmt = $conn->prepare("INSERT INTO mata_praktikum (kode_praktikum, nama, deskripsi) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $kode_praktikum, $nama, $deskripsi);

        if ($stmt->execute()) {
            header("Location: manajemen_praktikum.php?status=tambah_sukses");
            exit();
        } else {
            if ($conn->errno == 1062) {
                $error = "Gagal menyimpan: Kode Praktikum '" . htmlspecialchars($kode_praktikum) . "' sudah ada.";
            } else {
                $error = "Terjadi kesalahan saat menyimpan data: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

// Setelah semua logika selesai, baru panggil header
$pageTitle = 'Tambah Praktikum';
$activePage = 'manajemen_praktikum';
require_once 'templates/header.php';
?>

<div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6 border-b dark:border-gray-700 pb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Buat Praktikum Baru</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Isi detail di bawah untuk menambahkan mata praktikum.</p>
        </div>
        <a href="manajemen_praktikum.php" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
            &larr; Kembali
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bg-red-100 dark:bg-red-900/50 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg mb-4" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form action="praktikum_tambah.php" method="POST" class="space-y-6">
        <div>
            <label for="kode_praktikum" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Praktikum</label>
            <input type="text" id="kode_praktikum" name="kode_praktikum" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: PW-01" required>
        </div>
        <div>
            <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Praktikum</label>
            <input type="text" id="nama" name="nama" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Pemrograman Web" required>
        </div>
        <div>
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Jelaskan singkat mengenai mata praktikum ini"></textarea>
        </div>
        <div class="flex items-center justify-end pt-2">
            <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-all">
                Buat Praktikum
            </button>
        </div>
    </form>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>
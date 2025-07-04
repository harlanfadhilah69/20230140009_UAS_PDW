<?php
session_start();
require_once '../config.php';

// Validasi sesi asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$id_laporan = $_GET['id'] ?? null;
if (!$id_laporan) {
    echo "ID Laporan tidak valid.";
    exit;
}

// Proses update nilai
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = $_POST['nilai'];
    $feedback = $_POST['feedback'];
    $stmt = $conn->prepare("UPDATE laporan SET nilai = ?, feedback = ?, status = 'Dinilai' WHERE id = ?");
    $stmt->bind_param("isi", $nilai, $feedback, $id_laporan);
    $stmt->execute();
    $stmt->close();
    header("Location: laporan.php?status=Terkumpul&pesan=nilai_sukses");
    exit();
}

// Ambil data laporan
$stmt = $conn->prepare("SELECT l.*, u.nama as nama_mahasiswa, m.nama_modul 
                        FROM laporan l 
                        JOIN users u ON l.id_mahasiswa = u.id 
                        JOIN modul m ON l.id_modul = m.id 
                        WHERE l.id = ?");
$stmt->bind_param("i", $id_laporan);
$stmt->execute();
$laporan = $stmt->get_result()->fetch_assoc();
$stmt->close();


// Setelah semua logika selesai, baru panggil header
$pageTitle = 'Penilaian Laporan';
$activePage = 'laporan';
require_once 'templates/header.php';
?>

<div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6 border-b dark:border-gray-700 pb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Detail Laporan</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Beri nilai dan feedback untuk laporan ini.</p>
        </div>
        <a href="laporan.php" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
            &larr; Kembali
        </a>
    </div>

    <div class="space-y-4 mb-6 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg">
        <div class="flex items-center">
            <span class="w-24 text-sm font-semibold text-gray-600 dark:text-gray-400">Mahasiswa</span>
            <span class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($laporan['nama_mahasiswa']); ?></span>
        </div>
        <div class="flex items-center">
            <span class="w-24 text-sm font-semibold text-gray-600 dark:text-gray-400">Modul</span>
            <span class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($laporan['nama_modul']); ?></span>
        </div>
        <div class="flex items-center">
            <span class="w-24 text-sm font-semibold text-gray-600 dark:text-gray-400">File Laporan</span>
            <a href="../laporan/<?php echo htmlspecialchars($laporan['file_laporan']); ?>" download class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold py-1 px-3 rounded-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Unduh Laporan
            </a>
        </div>
    </div>

    <form action="laporan_nilai.php?id=<?php echo $id_laporan; ?>" method="POST">
        <div class="mb-4">
            <label for="nilai" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nilai (0-100)</label>
            <input type="number" name="nilai" id="nilai" min="0" max="100" value="<?php echo htmlspecialchars($laporan['nilai'] ?? ''); ?>" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
        </div>
        <div class="mb-6">
            <label for="feedback" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Feedback (Opsional)</label>
            <textarea name="feedback" id="feedback" rows="4" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"><?php echo htmlspecialchars($laporan['feedback'] ?? ''); ?></textarea>
        </div>
        <div class="flex items-center justify-end">
            <button type="submit" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-all">
                Simpan Nilai
            </button>
        </div>
    </form>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>
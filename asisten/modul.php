<?php
$pageTitle = 'Manajemen Modul';
$activePage = 'manajemen_praktikum';
require_once 'templates/header.php';
require_once '../config.php';

$id_praktikum = $_GET['id_praktikum'] ?? null;
if (!$id_praktikum) {
    echo "ID Praktikum tidak valid.";
    exit;
}

$stmt_praktikum = $conn->prepare("SELECT nama FROM mata_praktikum WHERE id = ?");
$stmt_praktikum->bind_param("i", $id_praktikum);
$stmt_praktikum->execute();
$result_praktikum = $stmt_praktikum->get_result();
$praktikum = $result_praktikum->fetch_assoc();
$nama_praktikum = $praktikum['nama'] ?? 'Tidak Ditemukan';

$stmt_modul = $conn->prepare("SELECT * FROM modul WHERE id_praktikum = ? ORDER BY created_at ASC");
$stmt_modul->bind_param("i", $id_praktikum);
$stmt_modul->execute();
$result_modul = $stmt_modul->get_result();
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Daftar Modul untuk "<?php echo htmlspecialchars($nama_praktikum); ?>"</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Kelola materi pertemuan untuk praktikum ini.</p>
    </div>
    <div class="flex items-center space-x-4">
         <a href="manajemen_praktikum.php" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">&larr; Kembali</a>
        <a href="modul_tambah.php?id_praktikum=<?php echo $id_praktikum; ?>" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <span>Tambah Modul</span>
        </a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md">
    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
        <?php if ($result_modul->num_rows > 0): ?>
            <?php while($modul = $result_modul->fetch_assoc()): ?>
                <li class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex-shrink-0 flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($modul['nama_modul']); ?></p>
                            <?php if (!empty($modul['file_materi'])): ?>
                                <a href="../uploads/<?php echo htmlspecialchars($modul['file_materi']); ?>" target="_blank" class="text-xs text-blue-500 hover:underline"><?php echo htmlspecialchars($modul['file_materi']); ?></a>
                            <?php else: ?>
                                <p class="text-xs text-gray-400">Materi belum diunggah</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="modul_edit.php?id=<?php echo $modul['id']; ?>" class="text-sm bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-3 rounded-lg">Edit</a>
                        <a href="modul_hapus.php?id=<?php echo $modul['id']; ?>" class="text-sm bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-3 rounded-lg" onclick="return confirm('Yakin ingin menghapus modul ini?');">Hapus</a>
                    </div>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="text-center py-10">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum Ada Modul</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Silakan tambahkan modul pertama untuk praktikum ini.</p>
            </li>
        <?php endif; ?>
    </ul>
</div>

<?php
$stmt_praktikum->close();
$stmt_modul->close();
$conn->close();
require_once 'templates/footer.php';
?>
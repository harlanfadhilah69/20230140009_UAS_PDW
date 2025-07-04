<?php
$pageTitle = 'Manajemen Praktikum';
$activePage = 'manajemen_praktikum';
require_once 'templates/header.php';
require_once '../config.php';

// Ambil semua data praktikum dari database
$result = $conn->query("SELECT * FROM mata_praktikum ORDER BY nama ASC");
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <?php if(isset($_GET['status'])): ?>
            <span class="text-sm text-green-600 dark:text-green-400">
                <?php 
                    if($_GET['status'] == 'tambah_sukses') echo 'Praktikum baru berhasil ditambahkan!';
                    if($_GET['status'] == 'edit_sukses') echo 'Data praktikum berhasil diperbarui!';
                    if($_GET['status'] == 'hapus_sukses') echo 'Praktikum berhasil dihapus!';
                ?>
            </span>
        <?php endif; ?>
    </div>
    <a href="praktikum_tambah.php" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors shadow-lg">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        <span>Tambah Praktikum</span>
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <?php
                // Hitung jumlah modul untuk praktikum ini
                $stmt_modul = $conn->prepare("SELECT COUNT(*) as total FROM modul WHERE id_praktikum = ?");
                $stmt_modul->bind_param("i", $row['id']);
                $stmt_modul->execute();
                $count_modul = $stmt_modul->get_result()->fetch_assoc()['total'];
                $stmt_modul->close();

                // Hitung jumlah mahasiswa terdaftar untuk praktikum ini
                $stmt_mahasiswa = $conn->prepare("SELECT COUNT(*) as total FROM pendaftaran WHERE id_praktikum = ?");
                $stmt_mahasiswa->bind_param("i", $row['id']);
                $stmt_mahasiswa->execute();
                $count_mahasiswa = $stmt_mahasiswa->get_result()->fetch_assoc()['total'];
                $stmt_mahasiswa->close();
            ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden flex flex-col transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="p-6 flex-grow">
                    <div class="flex justify-between items-start">
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 text-xs font-semibold rounded-full"><?php echo htmlspecialchars($row['kode_praktikum']); ?></span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-3"><?php echo htmlspecialchars($row['nama']); ?></h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 h-12"><?php echo htmlspecialchars($row['deskripsi']); ?></p>

                    <div class="mt-4 flex space-x-4 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                            <span><?php echo $count_modul; ?> Modul</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197m0 0A4 4 0 119.646 3.646 4 4 0 0112 4.354v0z"></path></svg>
                            <span><?php echo $count_mahasiswa; ?> Mahasiswa</span>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 border-t dark:border-gray-700 flex justify-end space-x-2">
                    <a href="modul.php?id_praktikum=<?php echo $row['id']; ?>" class="text-sm bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-3 rounded-lg transition-colors">Modul</a>
                    <a href="praktikum_edit.php?id=<?php echo $row['id']; ?>" class="text-sm bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-3 rounded-lg transition-colors">Edit</a>
                    <a href="praktikum_hapus.php?id=<?php echo $row['id']; ?>" class="text-sm bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-3 rounded-lg transition-colors" onclick="return confirm('Menghapus praktikum akan menghapus semua modul dan laporan terkait. Yakin?');">Hapus</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-span-full text-center py-12 bg-white dark:bg-gray-800 rounded-xl shadow-md">
             <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
             <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Belum Ada Praktikum</h3>
             <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Silakan tambahkan mata praktikum baru untuk memulai.</p>
        </div>
    <?php endif; ?>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>
<?php
$pageTitle = 'Praktikum Saya';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php'; // Memanggil header yang sudah gacor
require_once '../config.php';

$id_mahasiswa = $_SESSION['user_id'];

// Query untuk mengambil praktikum yang diikuti
$stmt = $conn->prepare("
    SELECT mp.id, mp.nama, mp.deskripsi 
    FROM mata_praktikum mp 
    JOIN pendaftaran p ON mp.id = p.id_praktikum 
    WHERE p.id_mahasiswa = ? 
    ORDER BY mp.nama ASC
");
$stmt->bind_param("i", $id_mahasiswa);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="space-y-8">
    
    <?php if (isset($_GET['status']) && $_GET['status'] == 'daftar_sukses'): ?>
        <div class="bg-green-100 dark:bg-green-900/50 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl relative" role="alert">
            <strong class="font-bold">Pendaftaran Berhasil!</strong>
            <span class="block sm:inline">Selamat, Anda sudah bisa memulai praktikum baru.</span>
        </div>
    <?php endif; ?>

    <div class="bg-white/70 dark:bg-gray-800/60 backdrop-blur-sm border border-white/20 dark:border-gray-700 p-6 rounded-2xl shadow-lg">
        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Praktikum yang Anda Ikuti</h3>
        
        <div class="space-y-6">
            <?php if ($result->num_rows > 0): ?>
                <?php while($praktikum = $result->fetch_assoc()): ?>
                    <?php
                        // Hitung progres untuk setiap praktikum
                        $stmt_modul_count = $conn->prepare("SELECT COUNT(*) as total FROM modul WHERE id_praktikum = ?");
                        $stmt_modul_count->bind_param("i", $praktikum['id']);
                        $stmt_modul_count->execute();
                        $total_m = $stmt_modul_count->get_result()->fetch_assoc()['total'];
                        $stmt_modul_count->close();
                        
                        $stmt_laporan_count = $conn->prepare("SELECT COUNT(l.id) as total FROM laporan l JOIN modul m ON l.id_modul = m.id WHERE l.id_mahasiswa = ? AND m.id_praktikum = ?");
                        $stmt_laporan_count->bind_param("ii", $id_mahasiswa, $praktikum['id']);
                        $stmt_laporan_count->execute();
                        $total_l = $stmt_laporan_count->get_result()->fetch_assoc()['total'];
                        $stmt_laporan_count->close();

                        $progress = ($total_m > 0) ? floor(($total_l / $total_m) * 100) : 0;
                    ?>
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-5 rounded-xl border border-gray-200 dark:border-gray-700 transform hover:shadow-xl hover:border-blue-400 transition-all duration-300">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                            <div class="flex-grow mb-4 sm:mb-0">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($praktikum['nama']); ?></h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1"><?php echo htmlspecialchars($praktikum['deskripsi']); ?></p>
                            </div>
                            <div class="flex-shrink-0 ml-0 sm:ml-6">
                                <a href="course_detail.php?id_praktikum=<?php echo $praktikum['id']; ?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-lg transition-colors duration-300 shadow-md hover:shadow-lg">
                                    Lihat Detail Tugas
                                </a>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progres</span>
                                <span class="text-sm font-medium text-blue-700 dark:text-blue-400"><?php echo $progress; ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5">
                                <div class="bg-blue-500 h-2.5 rounded-full transition-all duration-500" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-10">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M12 21a9 9 0 110-18 9 9 0 010 18z" /></svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Anda Belum Mengambil Praktikum</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jelajahi katalog dan daftarkan diri Anda pada praktikum yang tersedia.</p>
                    <div class="mt-6">
                        <a href="courses.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                            Cari Praktikum Sekarang
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
require_once 'templates/footer_mahasiswa.php';
?>
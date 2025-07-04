<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header.php';
require_once '../config.php';

// 1. Hitung Total Mata Praktikum yang diajarkan asisten (jika ada relasi)
// Untuk sekarang, kita hitung semua praktikum
$count_praktikum = $conn->query("SELECT COUNT(*) as total FROM mata_praktikum")->fetch_assoc()['total'];

// 2. Hitung Total Laporan Masuk
$count_laporan_total = $conn->query("SELECT COUNT(*) as total FROM laporan")->fetch_assoc()['total'];

// 3. Hitung Laporan yang Belum Dinilai
$count_laporan_menunggu = $conn->query("SELECT COUNT(*) as total FROM laporan WHERE status = 'Terkumpul'")->fetch_assoc()['total'];

// 4. Ambil 5 Laporan Terbaru yang Belum Dinilai
$result_aktivitas = $conn->query("
    SELECT l.id, u.nama as nama_mahasiswa, m.nama_modul, l.tgl_kumpul
    FROM laporan l
    JOIN users u ON l.id_mahasiswa = u.id
    JOIN modul m ON l.id_modul = m.id
    WHERE l.status = 'Terkumpul'
    ORDER BY l.tgl_kumpul DESC
    LIMIT 5
");
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg flex items-center space-x-4">
        <div class="bg-blue-100 dark:bg-blue-900/50 p-4 rounded-xl">
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Total Praktikum</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $count_praktikum; ?></p>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg flex items-center space-x-4">
        <div class="bg-green-100 dark:bg-green-900/50 p-4 rounded-xl">
            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Total Laporan Masuk</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $count_laporan_total; ?></p>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg flex items-center space-x-4">
        <div class="bg-yellow-100 dark:bg-yellow-900/50 p-4 rounded-xl">
            <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Laporan Belum Dinilai</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $count_laporan_menunggu; ?></p>
        </div>
    </div>
</div>

<div class="mt-8 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Perlu Dinilai</h3>
        <a href="laporan.php?status=Terkumpul" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">Lihat Semua</a>
    </div>
    <div class="space-y-4">
        <?php if ($result_aktivitas->num_rows > 0): ?>
            <?php while($aktivitas = $result_aktivitas->fetch_assoc()): ?>
                <div class="p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex-shrink-0 flex items-center justify-center mr-4">
                            <span class="font-bold text-gray-600 dark:text-gray-300"><?php echo strtoupper(substr($aktivitas['nama_mahasiswa'], 0, 1)); ?></span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                <span class="font-bold"><?php echo htmlspecialchars($aktivitas['nama_mahasiswa']); ?></span> mengumpulkan laporan untuk
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($aktivitas['nama_modul']); ?></p>
                        </div>
                    </div>
                    <a href="laporan_nilai.php?id=<?php echo $aktivitas['id']; ?>" class="bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-bold py-2 px-4 rounded-lg transition-colors">
                        Beri Nilai
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center py-6">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Kerja Bagus!</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Semua laporan sudah selesai dinilai.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>
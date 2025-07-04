<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// Panggil header GACOR PARAH
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

$id_mahasiswa = $_SESSION['user_id'];

// 1. STATISTIK UTAMA (Kode ini sama seperti sebelumnya, sudah dinamis)
$stmt_praktikum = $conn->prepare("SELECT COUNT(*) as total FROM pendaftaran WHERE id_mahasiswa = ?");
$stmt_praktikum->bind_param("i", $id_mahasiswa);
$stmt_praktikum->execute();
$count_praktikum = $stmt_praktikum->get_result()->fetch_assoc()['total'];
$stmt_praktikum->close();

$stmt_selesai = $conn->prepare("SELECT COUNT(*) as total FROM laporan WHERE id_mahasiswa = ? AND status = 'Dinilai'");
$stmt_selesai->bind_param("i", $id_mahasiswa);
$stmt_selesai->execute();
$count_selesai = $stmt_selesai->get_result()->fetch_assoc()['total'];
$stmt_selesai->close();

$stmt_total_modul = $conn->prepare("SELECT COUNT(m.id) as total FROM modul m JOIN pendaftaran p ON m.id_praktikum = p.id_praktikum WHERE p.id_mahasiswa = ?");
$stmt_total_modul->bind_param("i", $id_mahasiswa);
$stmt_total_modul->execute();
$total_modul = $stmt_total_modul->get_result()->fetch_assoc()['total'];
$stmt_total_modul->close();

$stmt_terkumpul = $conn->prepare("SELECT COUNT(*) as total FROM laporan WHERE id_mahasiswa = ?");
$stmt_terkumpul->bind_param("i", $id_mahasiswa);
$stmt_terkumpul->execute();
$count_terkumpul = $stmt_terkumpul->get_result()->fetch_assoc()['total'];
$stmt_terkumpul->close();

$count_menunggu = $total_modul - $count_terkumpul;


// 2. DATA UNTUK KONTEN BARU: Progres Praktikum & Aktivitas
$praktikum_diikuti = [];
$stmt_progres = $conn->prepare("SELECT mp.id, mp.nama FROM mata_praktikum mp JOIN pendaftaran p ON mp.id = p.id_praktikum WHERE p.id_mahasiswa = ?");
$stmt_progres->bind_param("i", $id_mahasiswa);
$stmt_progres->execute();
$result_progres = $stmt_progres->get_result();
while($row = $result_progres->fetch_assoc()) {
    // Hitung progres untuk setiap praktikum
    $stmt_modul_count = $conn->prepare("SELECT COUNT(*) as total FROM modul WHERE id_praktikum = ?");
    $stmt_modul_count->bind_param("i", $row['id']);
    $stmt_modul_count->execute();
    $total_m = $stmt_modul_count->get_result()->fetch_assoc()['total'];
    $stmt_modul_count->close();
    
    $stmt_laporan_count = $conn->prepare("SELECT COUNT(l.id) as total FROM laporan l JOIN modul m ON l.id_modul = m.id WHERE l.id_mahasiswa = ? AND m.id_praktikum = ?");
    $stmt_laporan_count->bind_param("ii", $id_mahasiswa, $row['id']);
    $stmt_laporan_count->execute();
    $total_l = $stmt_laporan_count->get_result()->fetch_assoc()['total'];
    $stmt_laporan_count->close();

    $row['progress'] = ($total_m > 0) ? floor(($total_l / $total_m) * 100) : 0;
    $praktikum_diikuti[] = $row;
}
$stmt_progres->close();

$stmt_notif = $conn->prepare("SELECT m.nama_modul, mp.nama as nama_praktikum, l.tgl_kumpul FROM laporan l JOIN modul m ON l.id_modul = m.id JOIN mata_praktikum mp ON m.id_praktikum = mp.id WHERE l.id_mahasiswa = ? AND l.status = 'Dinilai' ORDER BY l.tgl_kumpul DESC LIMIT 4");
$stmt_notif->bind_param("i", $id_mahasiswa);
$stmt_notif->execute();
$notifikasi = $stmt_notif->get_result();
$stmt_notif->close();
?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white/70 dark:bg-gray-800/60 backdrop-blur-sm border border-white/20 dark:border-gray-700 p-6 rounded-2xl shadow-lg flex items-center space-x-4 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
        <div class="bg-blue-500 p-4 rounded-xl shadow-md"><svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
        <div>
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Praktikum Diikuti</p>
            <p class="text-3xl font-bold text-gray-800 dark:text-white"><?php echo $count_praktikum; ?></p>
        </div>
    </div>
    <div class="bg-white/70 dark:bg-gray-800/60 backdrop-blur-sm border border-white/20 dark:border-gray-700 p-6 rounded-2xl shadow-lg flex items-center space-x-4 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
        <div class="bg-green-500 p-4 rounded-xl shadow-md"><svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
        <div>
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Tugas Selesai</p>
            <p class="text-3xl font-bold text-gray-800 dark:text-white"><?php echo $count_selesai; ?></p>
        </div>
    </div>
    <div class="bg-white/70 dark:bg-gray-800/60 backdrop-blur-sm border border-white/20 dark:border-gray-700 p-6 rounded-2xl shadow-lg flex items-center space-x-4 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
        <div class="bg-yellow-500 p-4 rounded-xl shadow-md"><svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
        <div>
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Tugas Menunggu</p>
            <p class="text-3xl font-bold text-gray-800 dark:text-white"><?php echo $count_menunggu; ?></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white/70 dark:bg-gray-800/60 backdrop-blur-sm border border-white/20 dark:border-gray-700 p-6 rounded-2xl shadow-lg">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Progres Praktikum Anda</h3>
            <div class="space-y-5">
                <?php if (empty($praktikum_diikuti)): ?>
                    <p class="text-center text-gray-500 dark:text-gray-400 py-4">Anda belum mengikuti praktikum apapun.</p>
                <?php else: ?>
                    <?php foreach ($praktikum_diikuti as $p): ?>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-base font-medium text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($p['nama']); ?></span>
                                <span class="text-sm font-medium text-blue-700 dark:text-blue-400"><?php echo $p['progress']; ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="bg-blue-500 h-2.5 rounded-full" style="width: <?php echo $p['progress']; ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1">
         <div class="bg-white/70 dark:bg-gray-800/60 backdrop-blur-sm border border-white/20 dark:border-gray-700 p-6 rounded-2xl shadow-lg">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Aktivitas Terbaru</h3>
            <ul class="space-y-4">
                <?php if ($notifikasi->num_rows > 0): ?>
                    <?php while($item = $notifikasi->fetch_assoc()): ?>
                        <li class="flex items-start">
                            <div class="flex-shrink-0"><div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/50 flex items-center justify-center"><span class="text-lg">✅</span></div></div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Nilai untuk <span class="font-bold"><?php echo htmlspecialchars($item['nama_modul']); ?></span> telah diberikan.</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('d M Y', strtotime($item['tgl_kumpul'])); ?></p>
                            </div>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li class="text-center text-sm text-gray-500 dark:text-gray-400 py-3">Belum ada nilai yang masuk.</li>
                <?php endif; ?>
                <li class="flex items-start">
                    <div class="flex-shrink-0"><div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center"><span class="text-lg">👋</span></div></div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Selamat datang di <span class="font-bold">SIMPRAK</span>!</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Semoga harimu menyenangkan.</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php
$conn->close();
// Panggil footer GACOR PARAH
require_once 'templates/footer_mahasiswa.php';
?>
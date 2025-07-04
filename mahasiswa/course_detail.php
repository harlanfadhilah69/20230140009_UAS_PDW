<?php
$pageTitle = 'Detail Praktikum';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php'; // Memanggil header yang sudah gacor
require_once '../config.php';

$id_praktikum = $_GET['id_praktikum'] ?? null;
$id_mahasiswa = $_SESSION['user_id'];
if (!$id_praktikum) {
    echo "ID Praktikum tidak valid.";
    exit;
}

// Logika untuk upload laporan
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_modul'])) {
    $id_modul = $_POST['id_modul'];
    
    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] == UPLOAD_ERR_OK) {
        $file_info = $_FILES['file_laporan'];
        $file_name = basename($file_info['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Validasi ekstensi file
        $allowed_ext = ['pdf', 'doc', 'docx', 'zip', 'rar'];
        if (!in_array($file_ext, $allowed_ext)) {
            $error = "Format file tidak diizinkan. Harap unggah PDF, DOC, DOCX, ZIP, atau RAR.";
        } else {
            $upload_dir = '../laporan/';
            $new_file_name = 'laporan_' . $id_mahasiswa . '_' . $id_modul . '.' . $file_ext;
            $destination = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_info['tmp_name'], $destination)) {
                $stmt_insert = $conn->prepare("INSERT INTO laporan (id_modul, id_mahasiswa, file_laporan) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE file_laporan = VALUES(file_laporan), tgl_kumpul = CURRENT_TIMESTAMP, status = 'Terkumpul', nilai = NULL, feedback = NULL");
                $stmt_insert->bind_param("iis", $id_modul, $id_mahasiswa, $new_file_name);
                if ($stmt_insert->execute()) {
                    $message = "Laporan berhasil diunggah!";
                } else {
                    $error = "Gagal menyimpan data laporan.";
                    unlink($destination);
                }
                $stmt_insert->close();
            } else {
                $error = "Gagal memindahkan file.";
            }
        }
    } else {
        $error = "Tidak ada file yang diunggah atau terjadi error.";
    }
}


// Ambil info praktikum
$stmt_praktikum = $conn->prepare("SELECT nama, deskripsi FROM mata_praktikum WHERE id = ?");
$stmt_praktikum->bind_param("i", $id_praktikum);
$stmt_praktikum->execute();
$result_praktikum = $stmt_praktikum->get_result();
$praktikum = $result_praktikum->fetch_assoc();
$namaPraktikum = $praktikum['nama'] ?? 'Tidak Ditemukan';
$deskripsiPraktikum = $praktikum['deskripsi'] ?? '';

// Ambil modul terkait
$stmt_modul = $conn->prepare("SELECT m.*, l.id as id_laporan, l.file_laporan, l.nilai, l.status FROM modul m LEFT JOIN laporan l ON m.id = l.id_modul AND l.id_mahasiswa = ? WHERE m.id_praktikum = ? ORDER BY m.created_at ASC");
$stmt_modul->bind_param("ii", $id_mahasiswa, $id_praktikum);
$stmt_modul->execute();
$result_modul = $stmt_modul->get_result();
?>

<div class="space-y-8">

    <div class="bg-white/70 dark:bg-gray-800/60 backdrop-blur-sm border border-white/20 dark:border-gray-700 p-6 rounded-2xl shadow-lg">
        <a href="my_courses.php" class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline mb-4">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Praktikum Saya
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($namaPraktikum); ?></h1>
        <p class="text-gray-600 dark:text-gray-300 mt-1"><?php echo htmlspecialchars($deskripsiPraktikum); ?></p>
    </div>

    <?php if ($message): ?> <div class="bg-green-100 dark:bg-green-900/50 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl" role="alert"><?php echo $message; ?></div> <?php endif; ?>
    <?php if ($error): ?> <div class="bg-red-100 dark:bg-red-900/50 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded-xl" role="alert"><?php echo $error; ?></div> <?php endif; ?>

    <div class="bg-white/70 dark:bg-gray-800/60 backdrop-blur-sm border border-white/20 dark:border-gray-700 p-6 rounded-2xl shadow-lg">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-5">Daftar Modul</h2>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <?php if ($result_modul->num_rows > 0): ?>
                <?php $modul_index = 0; ?>
                <?php while($modul = $result_modul->fetch_assoc()): ?>
                    <?php $modul_index++; ?>
                    <details class="p-4 group" <?php echo $modul_index === 1 ? 'open' : '' ?>>
                        <summary class="flex items-center justify-between cursor-pointer list-none">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex-shrink-0 flex items-center justify-center mr-4">
                                    <span class="text-blue-600 dark:text-blue-400 font-bold"><?php echo str_pad($modul_index, 2, '0', STR_PAD_LEFT); ?></span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white"><?php echo htmlspecialchars($modul['nama_modul']); ?></h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($modul['deskripsi']); ?></p>
                                </div>
                            </div>
                            <div class="text-gray-500 transform group-open:rotate-180 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </summary>

                        <div class="mt-6 pl-14">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                
                                <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-300 mb-2">Materi</h4>
                                    <?php if (!empty($modul['file_materi'])): ?>
                                        <a href="../uploads/<?php echo htmlspecialchars($modul['file_materi']); ?>" download class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold py-2 px-3 rounded-lg">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            <span>Unduh Materi</span>
                                        </a>
                                    <?php else: ?>
                                        <p class="text-sm text-gray-400 dark:text-gray-500">Tidak tersedia</p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-300 mb-2">Laporan</h4>
                                    <?php if ($modul['id_laporan']): ?>
                                        <div class="flex items-center text-green-600 dark:text-green-400 text-sm">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Terkumpul: <?php echo basename($modul['file_laporan']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <form action="course_detail.php?id_praktikum=<?php echo $id_praktikum; ?>" method="POST" enctype="multipart/form-data" class="mt-2">
                                        <input type="hidden" name="id_modul" value="<?php echo $modul['id']; ?>">
                                        <input type="file" name="file_laporan" required class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100">
                                        <button type="submit" class="mt-2 text-xs bg-gray-600 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded-full"><?php echo $modul['id_laporan'] ? 'Upload Ulang' : 'Upload'; ?></button>
                                    </form>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-300 mb-2">Nilai</h4>
                                    <?php if ($modul['status'] == 'Dinilai'): ?>
                                        <span class="text-3xl font-bold text-blue-600 dark:text-blue-400"><?php echo htmlspecialchars($modul['nilai']); ?></span>
                                    <?php else: ?>
                                        <p class="text-sm text-gray-400 dark:text-gray-500">Belum Dinilai</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </details>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center text-gray-500 dark:text-gray-400 py-6">Belum ada modul yang ditambahkan untuk praktikum ini.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$conn->close();
require_once 'templates/footer_mahasiswa.php';
?>
<?php
$pageTitle = 'Cari Praktikum';
$activePage = 'courses';
require_once 'templates/header_mahasiswa.php'; // Memanggil header yang sudah gacor
require_once '../config.php';

$id_mahasiswa = $_SESSION['user_id'];

// Ambil daftar ID praktikum yang sudah diikuti mahasiswa
$pendaftaran_ids = [];
$stmt_pendaftaran = $conn->prepare("SELECT id_praktikum FROM pendaftaran WHERE id_mahasiswa = ?");
$stmt_pendaftaran->bind_param("i", $id_mahasiswa);
$stmt_pendaftaran->execute();
$result_pendaftaran = $stmt_pendaftaran->get_result();
while ($row = $result_pendaftaran->fetch_assoc()) {
    $pendaftaran_ids[] = $row['id_praktikum'];
}
$stmt_pendaftaran->close();

// Query dasar untuk mengambil semua praktikum
// Kita juga akan mengambil nama asisten jika ada (LEFT JOIN)
$sql = "SELECT mp.*, u.nama as nama_asisten 
        FROM mata_praktikum mp
        LEFT JOIN users u ON mp.id_asisten = u.id AND u.role = 'asisten'";

// Logika untuk fitur pencarian
$search_term = $_GET['search'] ?? '';
if (!empty($search_term)) {
    $sql .= " WHERE mp.nama LIKE ? OR mp.deskripsi LIKE ?";
}
$sql .= " ORDER BY mp.nama ASC";

$stmt = $conn->prepare($sql);
if (!empty($search_term)) {
    $search_like = "%" . $search_term . "%";
    $stmt->bind_param("ss", $search_like, $search_like);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="space-y-8">
    
    <div class="bg-white/70 dark:bg-gray-800/60 backdrop-blur-sm border border-white/20 dark:border-gray-700 p-6 rounded-2xl shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Katalog Praktikum</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Jelajahi dan temukan praktikum yang menarik untukmu.</p>
            </div>
            <form action="courses.php" method="GET" class="mt-4 md:mt-0 w-full md:w-auto">
                <div class="relative">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Cari nama praktikum..." class="w-full md:w-64 pl-4 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($result->num_rows > 0): ?>
            <?php while($praktikum = $result->fetch_assoc()): ?>
                <?php $is_registered = in_array($praktikum['id'], $pendaftaran_ids); ?>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md overflow-hidden transform hover:-translate-y-1 transition-transform duration-300 flex flex-col">
                    <div class="p-6 flex-grow">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($praktikum['nama']); ?></h4>
                        <?php if(!empty($praktikum['nama_asisten'])): ?>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Oleh: <?php echo htmlspecialchars($praktikum['nama_asisten']); ?></p>
                        <?php endif; ?>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-3 h-20"><?php echo htmlspecialchars($praktikum['deskripsi']); ?></p>
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                        <?php if ($is_registered): ?>
                            <a href="my_courses.php" class="w-full text-center bg-green-500 text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Sudah Terdaftar</span>
                            </a>
                        <?php else: ?>
                            <a href="daftar_action.php?id_praktikum=<?php echo $praktikum['id']; ?>" class="w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300 block">
                                Daftar Sekarang
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-10 bg-white dark:bg-gray-800 rounded-2xl shadow-md">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Tidak Ditemukan</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada praktikum yang cocok dengan pencarian "<?php echo htmlspecialchars($search_term); ?>".</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
require_once 'templates/footer_mahasiswa.php';
?>
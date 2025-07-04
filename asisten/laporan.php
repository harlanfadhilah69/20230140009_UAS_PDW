<?php
$pageTitle = 'Laporan Masuk';
$activePage = 'laporan';
require_once 'templates/header.php';
require_once '../config.php';

// --- LOGIC TO FETCH AND FILTER DATA (No changes needed here) ---
$sql = "SELECT l.id, l.tgl_kumpul, l.status, u.nama as nama_mahasiswa, m.nama_modul 
        FROM laporan l 
        JOIN users u ON l.id_mahasiswa = u.id 
        JOIN modul m ON l.id_modul = m.id";

$whereClauses = [];
$params = [];
$types = '';

if (!empty($_GET['id_mahasiswa'])) {
    $whereClauses[] = "l.id_mahasiswa = ?";
    $params[] = $_GET['id_mahasiswa'];
    $types .= 'i';
}
if (!empty($_GET['id_modul'])) {
    $whereClauses[] = "l.id_modul = ?";
    $params[] = $_GET['id_modul'];
    $types .= 'i';
}
if (!empty($_GET['status'])) {
    $whereClauses[] = "l.status = ?";
    $params[] = $_GET['status'];
    $types .= 's';
}

if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}
$sql .= " ORDER BY l.tgl_kumpul DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result_laporan = $stmt->get_result();

$mahasiswas = $conn->query("SELECT id, nama FROM users WHERE role = 'mahasiswa' ORDER BY nama");
$moduls = $conn->query("SELECT id, nama_modul FROM modul ORDER BY nama_modul");
// --- END OF LOGIC ---

?>

<div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md mb-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filter Laporan</h3>
    
    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'nilai_sukses'): ?>
        <div class="bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 p-3 rounded-lg mb-4 text-center text-sm">
            Nilai berhasil disimpan!
        </div>
    <?php endif; ?>

    <form action="laporan.php" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label for="id_mahasiswa" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mahasiswa</label>
            <select name="id_mahasiswa" id="id_mahasiswa" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">Semua Mahasiswa</option>
                <?php while($row = $mahasiswas->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>" <?php echo (($_GET['id_mahasiswa'] ?? '') == $row['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['nama']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label for="id_modul" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Modul</label>
            <select name="id_modul" id="id_modul" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">Semua Modul</option>
                 <?php while($row = $moduls->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>" <?php echo (($_GET['id_modul'] ?? '') == $row['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['nama_modul']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
            <select name="status" id="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">Semua Status</option>
                <option value="Terkumpul" <?php echo (($_GET['status'] ?? '') == 'Terkumpul') ? 'selected' : ''; ?>>Belum Dinilai</option>
                <option value="Dinilai" <?php echo (($_GET['status'] ?? '') == 'Dinilai') ? 'selected' : ''; ?>>Sudah Dinilai</option>
            </select>
        </div>
        <div class="self-end">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md">Filter</button>
        </div>
    </form>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md">
    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
        <?php if ($result_laporan->num_rows > 0): ?>
            <?php while($laporan = $result_laporan->fetch_assoc()): ?>
                <li class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex-shrink-0 flex items-center justify-center mr-4">
                            <span class="font-bold text-gray-600 dark:text-gray-300"><?php echo strtoupper(substr($laporan['nama_mahasiswa'], 0, 1)); ?></span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($laporan['nama_mahasiswa']); ?></p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Mengumpulkan <span class="font-semibold"><?php echo htmlspecialchars($laporan['nama_modul']); ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500 dark:text-gray-400 hidden md:block"><?php echo date('d M Y, H:i', strtotime($laporan['tgl_kumpul'])); ?></span>
                        <?php if ($laporan['status'] == 'Dinilai'): ?>
                            <span class="capitalize px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">Dinilai</span>
                        <?php else: ?>
                            <span class="capitalize px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300">Menunggu</span>
                        <?php endif; ?>
                        <a href="laporan_nilai.php?id=<?php echo $laporan['id']; ?>" class="bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold py-2 px-4 rounded-lg transition-colors">
                            <?php echo ($laporan['status'] == 'Dinilai') ? 'Detail' : 'Nilai'; ?>
                        </a>
                    </div>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="text-center py-10">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Tidak Ada Laporan</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada laporan yang cocok dengan filter yang Anda pilih.</p>
            </li>
        <?php endif; ?>
    </ul>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>
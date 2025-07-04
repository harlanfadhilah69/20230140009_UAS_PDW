<?php
$pageTitle = 'Manajemen Akun';
$activePage = 'manajemen_akun';
require_once 'templates/header.php';
require_once '../config.php';

// Ambil semua pengguna dari database, kecuali admin yang sedang login untuk sementara
$current_admin_id = $_SESSION['user_id'];
$result = $conn->query("SELECT id, nama, email, role FROM users ORDER BY nama ASC");
?>

<div class="flex justify-between items-center mb-6">
     <div>
        <?php if(isset($_GET['status'])): ?>
            <span class="text-sm text-green-600 dark:text-green-400">
                <?php 
                    if($_GET['status'] == 'tambah_sukses') echo 'Akun baru berhasil dibuat!';
                    if($_GET['status'] == 'edit_sukses') echo 'Data akun berhasil diperbarui!';
                    if($_GET['status'] == 'hapus_sukses') echo 'Akun berhasil dihapus!';
                ?>
            </span>
        <?php endif; ?>
    </div>
    <a href="akun_tambah.php" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors shadow-lg">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
        <span>Tambah Akun</span>
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md">
    <div class="p-4 border-b dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Semua Pengguna Terdaftar</h3>
    </div>
    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
        <?php while($user = $result->fetch_assoc()): ?>
            <li class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex-shrink-0 flex items-center justify-center mr-4">
                        <span class="font-bold text-gray-600 dark:text-gray-300"><?php echo strtoupper(substr($user['nama'], 0, 1)); ?></span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($user['nama']); ?></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="capitalize px-3 py-1 text-xs font-semibold rounded-full <?php echo ($user['role'] == 'asisten') ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300'; ?>">
                        <?php echo htmlspecialchars($user['role']); ?>
                    </span>
                    <div class="flex items-center space-x-2">
                         <a href="akun_edit.php?id=<?php echo $user['id']; ?>" class="text-sm bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-3 rounded-lg transition-colors">Edit</a>
                        <?php if ($current_admin_id != $user['id']): // Cegah admin menghapus dirinya sendiri ?>
                            <a href="akun_hapus.php?id=<?php echo $user['id']; ?>" class="text-sm bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-3 rounded-lg transition-colors" onclick="return confirm('Yakin ingin menghapus akun ini?');">Hapus</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>
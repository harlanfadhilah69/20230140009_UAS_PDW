<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$id_user = $_GET['id'] ?? null;
if (!$id_user) {
    header("Location: manajemen_akun.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $password = trim($_POST['password']);

    $sql = "UPDATE users SET nama = ?, email = ?, role = ?";
    $types = "sss";
    $params = [$nama, $email, $role];

    if (!empty($password)) {
        $sql .= ", password = ?";
        $types .= "s";
        $params[] = password_hash($password, PASSWORD_BCRYPT);
    }

    $sql .= " WHERE id = ?";
    $types .= "i";
    $params[] = $id_user;

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        header("Location: manajemen_akun.php?status=edit_sukses");
        exit();
    } else {
        $error = "Gagal memperbarui akun: " . $stmt->error;
    }
    $stmt->close();
}

$stmt_get = $conn->prepare("SELECT nama, email, role FROM users WHERE id = ?");
$stmt_get->bind_param("i", $id_user);
$stmt_get->execute();
$user = $stmt_get->get_result()->fetch_assoc();
$stmt_get->close();

$pageTitle = 'Edit Akun';
$activePage = 'manajemen_akun';
require_once 'templates/header.php';
?>

<div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6 border-b dark:border-gray-700 pb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit Akun Pengguna</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui detail akun di bawah ini.</p>
        </div>
        <a href="manajemen_akun.php" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
            &larr; Kembali
        </a>
    </div>

    <?php if ($error): ?> 
        <div class="bg-red-100 dark:bg-red-900/50 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg mb-4" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="akun_edit.php?id=<?php echo $id_user; ?>" method="POST" class="space-y-6">
        <div>
            <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat Email</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password Baru (Opsional)</label>
            <input type="password" name="password" id="password" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm" placeholder="Kosongkan jika tidak ingin diubah">
        </div>
        <div>
            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
            <select name="role" id="role" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                <option value="mahasiswa" <?php echo ($user['role'] == 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                <option value="asisten" <?php echo ($user['role'] == 'asisten') ? 'selected' : ''; ?>>Asisten</option>
            </select>
        </div>
        <div class="flex justify-end pt-2">
            <button type="submit" class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2.5 px-6 rounded-lg shadow-md">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>
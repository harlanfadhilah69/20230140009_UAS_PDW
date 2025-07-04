<?php
session_start();
require_once 'config.php';

// Jika sudah login, redirect ke halaman yang sesuai
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'asisten') {
        header("Location: asisten/dashboard.php");
    } elseif ($_SESSION['role'] == 'mahasiswa') {
        header("Location: mahasiswa/dashboard.php");
    }
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "Email dan password harus diisi!";
    } else {
        $sql = "SELECT id, nama, email, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 'asisten') {
                    header("Location: asisten/dashboard.php");
                } elseif ($user['role'] == 'mahasiswa') {
                    header("Location: mahasiswa/dashboard.php");
                }
                exit();
            } else {
                $message = "Password yang Anda masukkan salah.";
            }
        } else {
            $message = "Akun dengan email tersebut tidak ditemukan.";
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SIMPRAK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 to-indigo-700 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h1 class="text-center text-4xl font-extrabold text-white">SIMPRAK</h1>
                <h2 class="mt-2 text-center text-lg text-blue-200">Sistem Informasi Manajemen Praktikum</h2>
            </div>

            <div class="bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl p-8 space-y-6">
                <h3 class="text-center text-2xl font-bold text-gray-900">Selamat Datang!</h3>
                
                <?php 
                    if (isset($_GET['status']) && $_GET['status'] == 'registered') {
                        echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert"><p>Registrasi berhasil! Silakan login.</p></div>';
                    }
                    if (!empty($message)) {
                        echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert"><p>' . htmlspecialchars($message) . '</p></div>';
                    }
                ?>

                <form class="space-y-6" action="login.php" method="POST">
                    <div>
                        <label for="email" class="text-sm font-bold text-gray-600 block">Email</label>
                        <input id="email" name="email" type="email" autocomplete="email" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="password" class="text-sm font-bold text-gray-600 block">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300">
                            Masuk
                        </button>
                    </div>
                </form>
                
                <p class="text-center text-sm text-gray-600">
                    Belum punya akun?
                    <a href="register.php" class="font-medium text-blue-600 hover:text-blue-500">
                        Daftar di sini
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
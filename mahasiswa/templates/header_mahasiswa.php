<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Cek jika pengguna belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
// Ambil data dari session
$nama = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));
// Pesan selamat datang berdasarkan waktu (WIB)
date_default_timezone_set('Asia/Jakarta');
$jam = date('G');
if ($jam >= 5 && $jam < 11) {
    $salam = "Selamat Pagi";
} elseif ($jam >= 11 && $jam < 15) {
    $salam = "Selamat Siang";
} elseif ($jam >= 15 && $jam < 18) {
    $salam = "Selamat Sore";
} else {
    $salam = "Selamat Malam";
}
?>
<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPRAK - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .dark body { background-color: #111827; }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen">
        <div class="bg-blue-600 dark:bg-gray-800 pb-32">
            <nav class="bg-transparent">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="border-b border-blue-500 border-opacity-25 lg:border-none">
                        <div class="h-16 flex items-center justify-between">
                            <div class="flex items-center">
                                <a href="dashboard.php" class="text-white text-2xl font-extrabold tracking-wider">SIMPRAK</a>
                                <div class="hidden lg:block ml-10">
                                    <div class="flex space-x-4">
                                        <?php $activeClass = 'bg-blue-700 dark:bg-gray-900 text-white'; $inactiveClass = 'text-blue-100 hover:bg-blue-500 hover:bg-opacity-75'; ?>
                                        <a href="dashboard.php" class="<?php echo ($activePage == 'dashboard') ? $activeClass : $inactiveClass; ?> rounded-md py-2 px-3 text-sm font-medium">Dashboard</a>
                                        <a href="my_courses.php" class="<?php echo ($activePage == 'my_courses') ? $activeClass : $inactiveClass; ?> rounded-md py-2 px-3 text-sm font-medium">Praktikum Saya</a>
                                        <a href="courses.php" class="<?php echo ($activePage == 'courses') ? $activeClass : $inactiveClass; ?> rounded-md py-2 px-3 text-sm font-medium">Cari Praktikum</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="hidden lg:flex items-center space-x-4">
                                <button id="dark-mode-toggle" class="p-1 rounded-full text-blue-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                    <svg id="sun-icon" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                    <svg id="moon-icon" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                                </button>
                                <div class="w-8 h-8 rounded-full bg-pink-500 flex items-center justify-center ring-2 ring-white/75">
                                    <span class="text-white font-bold text-base"><?php echo $inisial; ?></span>
                                </div>
                                <a href="../logout.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg text-sm">Logout</a>
                            </div>
                            <div class="-mr-2 flex lg:hidden">
                                <button type="button" id="mobile-menu-button" class="p-2 rounded-md inline-flex items-center justify-center text-blue-200 hover:text-white hover:bg-blue-500/75"><svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hidden lg:hidden" id="mobile-menu">
                    <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                        <a href="dashboard.php" class="text-white block rounded-md py-2 px-3 text-base font-medium">Dashboard</a>
                        <a href="my_courses.php" class="text-white block rounded-md py-2 px-3 text-base font-medium">Praktikum Saya</a>
                        <a href="courses.php" class="text-white block rounded-md py-2 px-3 text-base font-medium">Cari Praktikum</a>
                        <a href="../logout.php" class="text-white block rounded-md py-2 px-3 text-base font-medium">Logout</a>
                    </div>
                </div>
            </nav>
            <header class="py-10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold text-white"><?php echo $salam; ?>, <?php echo explode(' ', $nama)[0]; ?>!</h1>
                    <p class="text-blue-200 mt-1">Ini ringkasan aktivitas praktikummu.</p>
                </div>
            </header>
        </div>
        <main class="-mt-32">
            <div class="max-w-7xl mx-auto pb-12 px-4 sm:px-6 lg:px-8">
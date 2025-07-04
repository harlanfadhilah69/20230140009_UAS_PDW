<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}
// Ambil data dari session untuk personalisasi
$nama = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));
?>
<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="UTF-8">
    <title>Panel Asisten - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .dark body { background-color: #111827; }
        .sidebar-link-active { background-color: #1F2937; color: #fff; }
        .sidebar-link { color: #D1D5DB; }
        .sidebar-link:hover { background-color: #374151; color: #fff; }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900">

<div class="flex h-screen bg-gray-100 dark:bg-gray-900">
    <aside class="w-64 bg-gray-800 dark:bg-gray-900 text-white flex-col hidden md:flex">
        <div class="h-16 flex items-center justify-center border-b border-gray-700 dark:border-gray-800 px-4">
             <a href="dashboard.php" class="text-white text-2xl font-extrabold tracking-wider">SIMPRAK</a>
        </div>
        <nav class="flex-grow p-4 space-y-2">
            <?php $activeClass = 'sidebar-link-active'; $inactiveClass = 'sidebar-link'; ?>
            <a href="dashboard.php" class="<?php echo ($activePage == 'dashboard') ? $activeClass : $inactiveClass; ?> flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </a>
            <a href="manajemen_praktikum.php" class="<?php echo ($activePage == 'manajemen_praktikum') ? $activeClass : $inactiveClass; ?> flex items-center px-3 py-2.5 rounded-lg transition-colors">
                 <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                <span>Praktikum</span>
            </a>
             <a href="manajemen_akun.php" class="<?php echo ($activePage == 'manajemen_akun') ? $activeClass : $inactiveClass; ?> flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197m0 0A4 4 0 119.646 3.646 4 4 0 0112 4.354v0z" /></svg>
                <span>Akun</span>
            </a>
            <a href="laporan.php" class="<?php echo ($activePage == 'laporan') ? $activeClass : $inactiveClass; ?> flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                <span>Laporan</span>
            </a>
        </nav>
        <div class="p-4 mt-auto border-t border-gray-700 dark:border-gray-800">
             <a href="../logout.php" class="sidebar-link flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white dark:bg-gray-800 border-b dark:border-gray-700 flex items-center justify-between px-6">
            <div class="flex items-center">
                <button id="mobile-menu-button" class="md:hidden mr-4 text-gray-500 dark:text-gray-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
            </div>
            <div class="flex items-center space-x-4">
                 <button id="dark-mode-toggle" class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg id="sun-icon" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <svg id="moon-icon" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                </button>
                <div class="flex items-center">
                    <span class="text-right mr-3 hidden sm:block">
                        <span class="font-semibold text-gray-800 dark:text-white"><?php echo htmlspecialchars($nama); ?></span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Asisten</span>
                    </span>
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center ring-2 ring-white dark:ring-gray-700">
                        <span class="text-white font-bold text-lg"><?php echo $inisial; ?></span>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6 lg:p-8">
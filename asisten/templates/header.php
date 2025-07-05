<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') { header("Location: ../login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Asisten - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800;900&display=swap" rel="stylesheet">
    <style>body {font-family: 'Nunito', sans-serif;}</style>
</head>
<body class="bg-slate-100">
<div class="flex h-screen">
    <aside class="w-64 bg-red-800 text-white flex flex-col">
        <div class="p-6 text-center border-b-2 border-red-900/50">
            <h3 class="text-2xl font-black">Panel Asisten</h3>
            <p class="text-sm text-red-300 mt-1 font-bold"><?php echo htmlspecialchars($_SESSION['nama']); ?></p>
        </div>
        <nav class="flex-grow p-4">
            <ul class="space-y-2">
                <?php 
                    $activeClass = 'bg-red-900 text-white font-extrabold';
                    $inactiveClass = 'text-red-200 hover:bg-red-700 hover:text-white font-bold';
                ?>
                <li><a href="dashboard.php" class="<?php echo ($activePage == 'dashboard') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-xl transition-colors"><span>Dashboard</span></a></li>
                <li><a href="kelola_praktikum.php" class="<?php echo ($activePage == 'praktikum') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-xl transition-colors"><span>Kelola Praktikum</span></a></li>
                <li><a href="kelola_modul.php" class="<?php echo ($activePage == 'modul') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-xl transition-colors"><span>Kelola Modul</span></a></li>
                <li><a href="laporan_masuk.php" class="<?php echo ($activePage == 'laporan') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-xl transition-colors"><span>Laporan Masuk</span></a></li>
                <li><a href="kelola_pengguna.php" class="<?php echo ($activePage == 'pengguna') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-xl transition-colors"><span>Kelola Pengguna</span></a></li>
            </ul>
        </nav>
        <div class="p-4 border-t-2 border-red-900/50">
             <a href="../logout.php" class="w-full text-center bg-slate-700 hover:bg-slate-600 text-white font-bold py-2 px-4 rounded-xl transform hover:-translate-y-0.5 transition-transform">Logout</a>
        </div>
    </aside>
    <main class="flex-1 p-6 lg:p-10 overflow-y-auto">
        <h1 class="text-3xl font-extrabold text-slate-800 mb-8"><?php echo $pageTitle ?? 'Halaman'; ?></h1>
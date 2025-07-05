<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') { header("Location: ../login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Mahasiswa - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800;900&display=swap" rel="stylesheet">
    <style>body {font-family: 'Nunito', sans-serif;}</style>
</head>
<body class="bg-red-50">
<div class="flex h-screen">
    <aside class="w-64 bg-red-800 text-white flex flex-col">
        <div class="p-6 text-center border-b-2 border-red-900/50">
            <h3 class="text-2xl font-black">Panel Mahasiswa</h3>
            <p class="text-sm text-red-300 mt-1 font-bold"><?php echo htmlspecialchars($_SESSION['nama']); ?></p>
        </div>
        <nav class="flex-grow p-4">
            <ul class="space-y-2">
                <?php 
                    $activeClass = 'bg-red-900 text-white font-extrabold';
                    $inactiveClass = 'text-red-200 hover:bg-red-700 hover:text-white font-bold';
                ?>
                <li><a href="dashboard.php" class="<?php echo ($activePage == 'dashboard') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-xl transition-colors"><span>Dashboard</span></a></li>
                <li><a href="katalog.php" class="<?php echo ($activePage == 'katalog') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-xl transition-colors"><span>Katalog Praktikum</span></a></li>
                <li><a href="praktikum_saya.php" class="<?php echo ($activePage == 'praktikum_saya') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-3 rounded-xl transition-colors"><span>Praktikum Saya</span></a></li>
            </ul>
        </nav>
        <div class="p-4 border-t-2 border-red-900/50">
             <a href="../logout.php" class="w-full text-center bg-slate-700 hover:bg-slate-600 text-white font-bold py-2 px-4 rounded-xl transform hover:-translate-y-0.5 transition-transform">Logout</a>
        </div>
    </aside>
    <main class="flex-1 p-6 lg:p-10 overflow-y-auto">
        
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-black text-slate-800"><?php echo $pageTitle ?? 'Halaman'; ?></h1>
            
            <form action="katalog.php" method="GET" class="relative w-1/3">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="none"><path d="M21 21L15.803 15.803M15.803 15.803C17.2092 14.3968 18 12.5185 18 10.5C18 6.35786 14.6421 3 10.5 3C6.35786 3 3 6.35786 3 10.5C3 14.6421 6.35786 18 10.5 18C12.5185 18 14.3968 17.2092 15.803 15.803V15.803Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                </span>
                <input type="text" name="search" placeholder="Cari mata praktikum..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="w-full py-2 pl-10 pr-4 text-gray-700 bg-white border-2 border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300">
            </form>
        </div>
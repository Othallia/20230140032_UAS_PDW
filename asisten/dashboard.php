<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once '../config.php';
require_once 'templates/header.php';

$result_modul = $conn->query("SELECT COUNT(*) AS total FROM modul");
$total_modul = $result_modul->fetch_assoc()['total'] ?? 0;
$result_laporan = $conn->query("SELECT COUNT(*) AS total FROM laporan");
$total_laporan = $result_laporan->fetch_assoc()['total'] ?? 0;
$result_belum_dinilai = $conn->query("SELECT COUNT(*) AS total FROM laporan WHERE status = 'dikumpulkan'");
$laporan_belum_dinilai = $result_belum_dinilai->fetch_assoc()['total'] ?? 0;
$aktivitas_terbaru = $conn->query("SELECT u.nama AS nama_mahasiswa, m.judul_modul, l.tanggal_kumpul FROM laporan l JOIN users u ON l.mahasiswa_id = u.id JOIN modul m ON l.modul_id = m.id ORDER BY l.tanggal_kumpul DESC LIMIT 5");
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-blue-500 flex items-center gap-5">
        <div class="bg-blue-100 text-blue-600 p-4 rounded-full">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
        </div>
        <div>
            <p class="font-semibold text-slate-500 text-sm">Total Modul Diajarkan</p>
            <p class="text-3xl font-extrabold text-slate-800 mt-1"><?php echo $total_modul; ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-green-500 flex items-center gap-5">
         <div class="bg-green-100 text-green-600 p-4 rounded-full">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <div>
            <p class="font-semibold text-slate-500 text-sm">Total Laporan Masuk</p>
            <p class="text-3xl font-extrabold text-slate-800 mt-1"><?php echo $total_laporan; ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-yellow-500 flex items-center gap-5">
        <div class="bg-yellow-100 text-yellow-600 p-4 rounded-full">
           <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="font-semibold text-slate-500 text-sm">Laporan Belum Dinilai</p>
            <p class="text-3xl font-extrabold text-slate-800 mt-1"><?php echo $laporan_belum_dinilai; ?></p>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-2xl shadow-sm">
    <h3 class="text-xl font-extrabold text-slate-800 mb-5">Aktivitas Laporan Terbaru 🚀</h3>
    <div class="space-y-4">
        <?php if ($aktivitas_terbaru->num_rows > 0): while ($aktivitas = $aktivitas_terbaru->fetch_assoc()): ?>
            <div class="flex items-center bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="w-11 h-11 rounded-full bg-red-200 text-red-700 flex items-center justify-center font-bold flex-shrink-0">
                    <?php echo strtoupper(substr($aktivitas['nama_mahasiswa'], 0, 2)); ?>
                </div>
                <div class="ml-4">
                    <p class="text-slate-800 font-bold text-sm">
                        <?php echo htmlspecialchars($aktivitas['nama_mahasiswa']); ?>
                        <span class="font-normal text-slate-600">mengumpulkan laporan untuk</span>
                        <?php echo htmlspecialchars($aktivitas['judul_modul']); ?>
                    </p>
                    <p class="text-xs text-slate-500 font-semibold mt-1"><?php echo date('d F Y, H:i', strtotime($aktivitas['tanggal_kumpul'])); ?></p>
                </div>
            </div>
        <?php endwhile; else: ?>
            <p class="text-center text-slate-500 font-semibold py-8">Tidak ada aktivitas laporan terbaru.</p>
        <?php endif; ?>
    </div>
</div>
<?php require_once 'templates/footer.php'; ?>
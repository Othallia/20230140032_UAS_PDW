<?php
// PHP logic di atas tetap sama
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once '../config.php';
require_once 'templates/header_mahasiswa.php';
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }
$mahasiswa_id = $_SESSION['user_id'];
$stmt_praktikum = $conn->prepare("SELECT COUNT(*) AS total FROM pendaftaran_praktikum WHERE mahasiswa_id = ?"); $stmt_praktikum->bind_param("i", $mahasiswa_id); $stmt_praktikum->execute();
$praktikum_diikuti = $stmt_praktikum->get_result()->fetch_assoc()['total'] ?? 0; $stmt_praktikum->close();
$stmt_selesai = $conn->prepare("SELECT COUNT(*) AS total FROM laporan WHERE mahasiswa_id = ? AND status = 'dinilai'"); $stmt_selesai->bind_param("i", $mahasiswa_id); $stmt_selesai->execute();
$tugas_selesai = $stmt_selesai->get_result()->fetch_assoc()['total'] ?? 0; $stmt_selesai->close();
$stmt_menunggu = $conn->prepare("SELECT COUNT(*) AS total FROM laporan WHERE mahasiswa_id = ? AND status = 'dikumpulkan'"); $stmt_menunggu->bind_param("i", $mahasiswa_id); $stmt_menunggu->execute();
$tugas_menunggu = $stmt_menunggu->get_result()->fetch_assoc()['total'] ?? 0; $stmt_menunggu->close();
$stmt_notif = $conn->prepare("SELECT m.judul_modul, mp.nama_praktikum, l.tanggal_kumpul FROM laporan l JOIN modul m ON l.modul_id = m.id JOIN mata_praktikum mp ON m.mata_praktikum_id = mp.id WHERE l.mahasiswa_id = ? AND l.status = 'dinilai' ORDER BY l.tanggal_kumpul DESC LIMIT 5");
$stmt_notif->bind_param("i", $mahasiswa_id); $stmt_notif->execute(); $notifikasi_terbaru = $stmt_notif->get_result();
?>

<div class="bg-gradient-to-r from-red-700 to-red-500 text-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-4xl font-black">Hai, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
    <p class="mt-2 font-semibold opacity-90">Semangat terus praktikumnya ya! 🔥</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
    <div class="bg-white p-6 rounded-xl shadow-md transition-transform transform hover:-translate-y-1">
        <p class="font-semibold text-slate-500">Praktikum Diikuti</p>
        <p class="text-5xl font-extrabold text-red-600 mt-2"><?php echo $praktikum_diikuti; ?></p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md transition-transform transform hover:-translate-y-1">
        <p class="font-semibold text-slate-500">Tugas Selesai</p>
        <p class="text-5xl font-extrabold text-green-500 mt-2"><?php echo $tugas_selesai; ?></p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md transition-transform transform hover:-translate-y-1">
        <p class="font-semibold text-slate-500">Tugas Menunggu</p>
        <p class="text-5xl font-extrabold text-orange-500 mt-2"><?php echo $tugas_menunggu; ?></p>
    </div>
</div>

<div class="bg-white p-8 rounded-xl shadow-md">
    <h3 class="text-2xl font-extrabold text-slate-800 mb-6">Aktivitas & Notifikasi Terbaru</h3>
    <div class="relative">
        <div class="absolute left-4 top-1 h-full border-l-2 border-dashed border-slate-300"></div>

        <?php if ($notifikasi_terbaru->num_rows > 0): ?>
            <?php while($notif = $notifikasi_terbaru->fetch_assoc()): ?>
            <div class="relative pl-12 pb-8">
                <div class="absolute left-0 top-0 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white ring-8 ring-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                </div>
                <div class="p-4 bg-red-50 rounded-xl border border-red-200">
                    <p class="font-bold text-slate-700">Nilai untuk <span class="font-black text-red-600"><?php echo htmlspecialchars($notif['judul_modul']); ?></span> telah diberikan!</p>
                    <p class="text-sm text-slate-500 mt-1">Praktikum: <?php echo htmlspecialchars($notif['nama_praktikum']); ?></p>
                    <p class="text-xs text-slate-400 font-semibold mt-2"><?php echo date('d F Y, H:i', strtotime($notif['tanggal_kumpul'])); ?></p>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="relative pl-12">
                 <div class="absolute left-0 top-0 flex h-8 w-8 items-center justify-center rounded-full bg-slate-300 text-white ring-8 ring-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" /></svg>
                </div>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <p class="font-bold text-slate-600">Tidak ada notifikasi baru.</p>
                    <p class="text-sm text-slate-500 mt-1">Semua tugasmu sudah dinilai atau belum ada yang dikumpulkan. Tetap semangat!</p>
                </div>
            </div>
        <?php endif; $stmt_notif->close(); ?>
    </div>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>
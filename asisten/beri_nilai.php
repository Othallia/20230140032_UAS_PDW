<?php
$pageTitle = 'Beri Nilai Laporan';
$activePage = 'laporan';
require_once '../config.php';
require_once 'templates/header.php';

$laporan_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$error = '';

if ($laporan_id == 0) {
    header("Location: laporan_masuk.php");
    exit();
}

// Logika Form (tidak ada perubahan)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nilai = intval($_POST['nilai']);
    $feedback = trim($_POST['feedback']);
    if ($nilai < 0 || $nilai > 100) {
        $error = "Nilai harus di antara 0 dan 100.";
    } else {
        $sql = "UPDATE laporan SET nilai = ?, feedback = ?, status = 'dinilai' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $nilai, $feedback, $laporan_id);
        if ($stmt->execute()) {
            $message = "Nilai berhasil disimpan.";
        } else {
            $error = "Gagal menyimpan nilai.";
        }
        $stmt->close();
    }
}

// Mengambil data detail laporan (tidak ada perubahan)
$sql_detail = "SELECT l.*, u.nama AS nama_mahasiswa, m.judul_modul, mp.nama_praktikum FROM laporan l JOIN users u ON l.mahasiswa_id = u.id JOIN modul m ON l.modul_id = m.id JOIN mata_praktikum mp ON m.mata_praktikum_id = mp.id WHERE l.id = ?";
$stmt_detail = $conn->prepare($sql_detail);
$stmt_detail->bind_param("i", $laporan_id);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();
$laporan = $result_detail->fetch_assoc();

if (!$laporan) {
    echo "<p class='text-red-500 font-bold'>Laporan tidak ditemukan.</p>";
    require_once 'templates/footer.php';
    exit();
}
?>

<?php if ($message): ?><div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg font-bold" role="alert"><p><?php echo $message; ?></p></div><?php endif; ?>
<?php if ($error): ?><div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg font-bold" role="alert"><p><?php echo $error; ?></p></div><?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

    <div class="bg-white p-8 rounded-2xl shadow-md">
        <h2 class="text-2xl font-extrabold text-slate-800 mb-6">Detail Pengumpulan</h2>
        <div class="space-y-4 text-sm">
            <div class="flex justify-between">
                <span class="font-bold text-slate-500">Mahasiswa:</span>
                <span class="font-semibold text-slate-800 text-right"><?php echo htmlspecialchars($laporan['nama_mahasiswa']); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="font-bold text-slate-500">Praktikum:</span>
                <span class="font-semibold text-slate-800 text-right"><?php echo htmlspecialchars($laporan['nama_praktikum']); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="font-bold text-slate-500">Modul:</span>
                <span class="font-semibold text-slate-800 text-right"><?php echo htmlspecialchars($laporan['judul_modul']); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="font-bold text-slate-500">Tanggal Kumpul:</span>
                <span class="font-semibold text-slate-800 text-right"><?php echo date('d M Y, H:i', strtotime($laporan['tanggal_kumpul'])); ?></span>
            </div>
        </div>
        <div class="mt-8 border-t pt-6">
            <a href="../uploads/laporan/<?php echo htmlspecialchars($laporan['file_laporan']); ?>" download class="w-full text-center block bg-slate-800 text-white font-bold py-3 px-5 rounded-lg hover:bg-slate-900 transition-all">
                Unduh Laporan
            </a>
        </div>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-md">
         <h2 class="text-2xl font-extrabold text-slate-800 mb-6">Form Penilaian</h2>
        <form action="beri_nilai.php?id=<?php echo $laporan_id; ?>" method="POST">
            <div class="mb-5">
                <label for="nilai" class="block text-sm font-bold text-slate-700 mb-2">Nilai (0-100)</label>
                <input type="number" name="nilai" id="nilai" min="0" max="100" value="<?php echo htmlspecialchars($laporan['nilai'] ?? ''); ?>" class="w-full p-3 border border-slate-300 rounded-lg text-lg font-bold focus:ring-2 focus:ring-green-400 focus:border-green-500" required>
            </div>
            <div class="mb-6">
                <label for="feedback" class="block text-sm font-bold text-slate-700 mb-2">Feedback (Opsional)</label>
                <textarea name="feedback" id="feedback" rows="6" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-green-500"><?php echo htmlspecialchars($laporan['feedback'] ?? ''); ?></textarea>
            </div>
            <div>
                <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-700 transition-all">Simpan Nilai</button>
            </div>
        </form>
    </div>
</div>

<div class="mt-8">
    <a href="laporan_masuk.php" class="font-bold text-red-600 hover:text-red-800 transition-colors">&larr; Kembali ke Daftar Laporan</a>
</div>

<?php
$stmt_detail->close();
require_once 'templates/footer.php';
?>
<?php
$pageTitle = 'Detail Praktikum';
$activePage = 'praktikum_saya';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

$mahasiswa_id = $_SESSION['user_id'];
$praktikum_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$message_type = '';

if ($praktikum_id == 0) {
    // Redirect jika ID praktikum tidak valid
    header("Location: praktikum_saya.php");
    exit();
}

// Logika untuk handle upload laporan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kumpul_laporan'])) {
    $modul_id = intval($_POST['modul_id']);
    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] == 0) {
        $target_dir = "../uploads/laporan/";
        $file_name = time() . '_' . basename($_FILES["file_laporan"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($file_type != "pdf" && $file_type != "doc" && $file_type != "docx") {
            $message = 'Maaf, hanya file PDF, DOC, & DOCX yang diizinkan.';
            $message_type = 'gagal';
        } else {
            if (move_uploaded_file($_FILES["file_laporan"]["tmp_name"], $target_file)) {
                $sql_upsert = "INSERT INTO laporan (modul_id, mahasiswa_id, file_laporan, status) VALUES (?, ?, ?, 'dikumpulkan') ON DUPLICATE KEY UPDATE file_laporan = VALUES(file_laporan), tanggal_kumpul = NOW(), status = 'dikumpulkan', nilai = NULL, feedback = NULL";
                $stmt = $conn->prepare($sql_upsert);
                $stmt->bind_param("iis", $modul_id, $mahasiswa_id, $file_name);
                if ($stmt->execute()) {
                    $message = 'Laporan berhasil diunggah. Mantap!';
                    $message_type = 'sukses';
                } else {
                    $message = 'Gagal menyimpan data laporan.';
                    $message_type = 'gagal';
                }
                $stmt->close();
            } else {
                $message = 'Error saat mengunggah file.';
                $message_type = 'gagal';
            }
        }
    } else {
        $message = 'Pilih file dulu ya.';
        $message_type = 'gagal';
    }
}

// Mengambil data utama praktikum
$sql_praktikum = "SELECT nama_praktikum, deskripsi FROM mata_praktikum WHERE id = ?";
$stmt_praktikum = $conn->prepare($sql_praktikum);
$stmt_praktikum->bind_param("i", $praktikum_id);
$stmt_praktikum->execute();
$result_praktikum = $stmt_praktikum->get_result();
$praktikum = $result_praktikum->fetch_assoc();

if (!$praktikum) {
    echo "<p>Praktikum tidak ditemukan.</p>";
    require_once 'templates/footer_mahasiswa.php';
    exit();
}

// Tampilkan pesan status jika ada
if ($message) {
    echo '<div class="mb-4 p-4 rounded-xl font-bold ' . ($message_type == 'sukses' ? 'bg-green-100 text-green-800 border-2 border-green-300' : 'bg-red-100 text-red-800 border-2 border-red-300') . '">' . htmlspecialchars($message) . '</div>';
}
?>

<div class="bg-white p-8 rounded-2xl shadow-sm mb-8 border-l-8 border-red-500">
    <h2 class="text-3xl font-black text-slate-800"><?php echo htmlspecialchars($praktikum['nama_praktikum']); ?></h2>
    <p class="text-slate-600 mt-2 font-semibold"><?php echo htmlspecialchars($praktikum['deskripsi']); ?></p>
</div>

<h3 class="text-2xl font-bold text-slate-700 mb-6">Daftar Modul</h3>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    <?php
    $sql_modul = "SELECT m.id, m.judul_modul, m.deskripsi, m.file_materi, l.file_laporan, l.tanggal_kumpul, l.status, l.nilai, l.feedback FROM modul m LEFT JOIN laporan l ON m.id = l.modul_id AND l.mahasiswa_id = ? WHERE m.mata_praktikum_id = ? ORDER BY m.id ASC";
    $stmt_modul = $conn->prepare($sql_modul);
    $stmt_modul->bind_param("ii", $mahasiswa_id, $praktikum_id);
    $stmt_modul->execute();
    $result_modul = $stmt_modul->get_result();

    if ($result_modul->num_rows > 0):
        while($modul = $result_modul->fetch_assoc()):
            $status_laporan = $modul['status'] ?? 'belum_kumpul';
    ?>
    <div class="bg-white rounded-2xl shadow-md flex flex-col transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
        <div class="p-6 flex-grow">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <?php if (!empty($modul['file_materi'])): ?>
                    <a href="../uploads/materi/<?php echo htmlspecialchars($modul['file_materi']); ?>" download class="bg-red-500 text-white font-bold py-2 px-4 rounded-lg text-sm hover:bg-red-600 transition-all">Unduh Materi</a>
                <?php endif; ?>
            </div>
            <h4 class="text-xl font-extrabold text-slate-800"><?php echo htmlspecialchars($modul['judul_modul']); ?></h4>
            <p class="text-slate-500 text-sm mt-1 mb-4 font-semibold"><?php echo htmlspecialchars($modul['deskripsi']); ?></p>
        </div>

        <div class="bg-slate-50 p-5 rounded-b-2xl border-t border-slate-200">
             <?php if ($status_laporan == 'dinilai'): ?>
                <div class="bg-green-100 p-4 rounded-xl border border-green-200">
                    <p class="font-black text-2xl text-green-700">Nilai: <?php echo htmlspecialchars($modul['nilai']); ?></p>
                    <p class="font-bold mt-2 text-slate-600">Feedback:</p>
                    <p class="text-slate-700 whitespace-pre-wrap text-sm"><?php echo !empty($modul['feedback']) ? htmlspecialchars($modul['feedback']) : '<i>Tidak ada feedback.</i>'; ?></p>
                </div>
            <?php elseif ($status_laporan == 'dikumpulkan'): ?>
                <div class="bg-yellow-100 text-yellow-800 p-4 rounded-xl font-bold border border-yellow-200 text-center">
                    Menunggu Penilaian
                </div>
            <?php endif; ?>
            
             <form action="detail_praktikum.php?id=<?php echo $praktikum_id; ?>" method="post" enctype="multipart/form-data" class="mt-4">
                <input type="hidden" name="modul_id" value="<?php echo $modul['id']; ?>">
                <input type="file" name="file_laporan" id="file_laporan_<?php echo $modul['id']; ?>" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100" required>
                <button type="submit" name="kumpul_laporan" class="w-full mt-3 bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 px-4 rounded-lg transition-all text-sm">
                    <?php echo $modul['file_laporan'] ? 'Kumpul Ulang' : 'Kumpul Laporan'; ?>
                </button>
            </form>
        </div>
    </div>
    <?php endwhile; else: ?>
        <div class="col-span-full text-center py-12 bg-white rounded-2xl shadow-sm">
            <p class="text-slate-500 font-bold text-xl">Belum ada modul untuk praktikum ini.</p>
        </div>
    <?php endif; $stmt_modul->close(); $conn->close(); ?>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>
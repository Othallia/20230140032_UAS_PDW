<?php
// Set variabel untuk judul halaman dan link aktif di sidebar
$pageTitle = 'Katalog Praktikum';
$activePage = 'katalog';

// Memanggil template header mahasiswa
require_once 'templates/header_mahasiswa.php';
require_once '../config.php'; // Koneksi ke database

// --- Logika untuk menangani ID praktikum yang sudah didaftar ---
$pendaftaran_ids = [];
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'mahasiswa') {
    $mahasiswa_id = $_SESSION['user_id'];
    $sql_pendaftaran = "SELECT mata_praktikum_id FROM pendaftaran_praktikum WHERE mahasiswa_id = ?";
    $stmt_pendaftaran = $conn->prepare($sql_pendaftaran);
    $stmt_pendaftaran->bind_param("i", $mahasiswa_id);
    $stmt_pendaftaran->execute();
    $result_pendaftaran = $stmt_pendaftaran->get_result();
    while ($row_pendaftaran = $result_pendaftaran->fetch_assoc()) {
        $pendaftaran_ids[] = $row_pendaftaran['mata_praktikum_id'];
    }
    $stmt_pendaftaran->close();
}

// --- Logika untuk menangani PENCARIAN ---
$search_term = $_GET['search'] ?? '';
$params = [];
$sql = "SELECT * FROM mata_praktikum";
if (!empty($search_term)) {
    $sql .= " WHERE nama_praktikum LIKE ? OR deskripsi LIKE ?";
    $like_term = "%" . $search_term . "%";
    $params[] = $like_term;
    $params[] = $like_term;
}
$sql .= " ORDER BY nama_praktikum ASC";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Array warna dan fungsi untuk ikon inisial
$initialColors = [
    ['bg' => 'bg-red-100', 'text' => 'text-red-600'],
    ['bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
    ['bg' => 'bg-green-100', 'text' => 'text-green-600'],
    ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
    ['bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
    ['bg' => 'bg-pink-100', 'text' => 'text-pink-600'],
];
function getInitialsWithColor($string, $index) {
    global $initialColors;
    $color = $initialColors[$index % count($initialColors)];
    $words = explode(" ", $string);
    $initials = "";
    foreach ($words as $w) {
        $initials .= mb_substr($w, 0, 1);
    }
    return ['initials' => strtoupper(mb_substr($initials, 0, 2)), 'bg' => $color['bg'], 'text' => $color['text']];
}
?>

<div class="space-y-4">
    <?php if ($result->num_rows > 0): $i = 0; while($row = $result->fetch_assoc()): $initialsData = getInitialsWithColor($row['nama_praktikum'], $i); ?>
    <div class="bg-white p-4 rounded-xl border border-slate-200 hover:border-slate-300 hover:shadow-sm transition-all flex items-center space-x-5">
        <div class="flex-shrink-0 w-20 h-20 rounded-lg <?php echo $initialsData['bg']; ?> flex items-center justify-center">
            <span class="text-2xl font-extrabold <?php echo $initialsData['text']; ?>"><?php echo $initialsData['initials']; ?></span>
        </div>
        
        <div class="flex-grow">
            <h3 class="text-xl font-bold text-slate-800"><?php echo htmlspecialchars($row['nama_praktikum']); ?></h3>
            <p class="text-slate-500 text-sm mt-1"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
        </div>
        
        <div class="flex-shrink-0">
            <?php if (in_array($row['id'], $pendaftaran_ids)): ?>
                <div class="text-center text-sm text-green-700 font-bold py-2 px-4 rounded-lg bg-green-100">
                    ✓ Terdaftar
                </div>
            <?php else: ?>
                <a href="daftar_praktikum.php?id=<?php echo $row['id']; ?>" class="block text-center bg-slate-800 text-white font-bold py-2 px-5 rounded-lg hover:bg-slate-900 transition-all">
                    Daftar
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php $i++; endwhile; else: ?>
        <div class="text-center bg-white rounded-xl p-12 border border-slate-200">
            <p class="text-slate-500 font-bold text-lg">Waduh, praktikum yang Anda cari tidak ditemukan. 😢</p>
        </div>
    <?php endif; ?>
</div>

<?php
// Memanggil template footer mahasiswa
require_once 'templates/footer_mahasiswa.php';
$stmt->close();
$conn->close();
?>